<?php
/**
 * Core CST class
 *
 * Class that contains all of the methods needed to create connections, push files, etc.
 *
 * @author Ollie Armstrong
 * @package CST
 * @copyright All rights reserved 2011
 * @license GNU GPLv2
 */
class Cst {
	protected $cdnConnection, $connectionType, $fileTypes, $ftpHome;

	function __construct() {
		$this->connectionType = get_option('cst-cdn');
		$this->createConnection();
		add_action('admin_menu', array($this, 'createPages'));

		// Create nonce
		add_action('init', array($this, 'createNonce'));

		// Enqueue files
		add_action('admin_init', array($this, 'enqueueFiles'));
	}

	/**
	 * Initialises the connection to the CDN
	 * 
	 */
	private function createConnection() {
		require_once CST_DIR.'lib/pages/Options.php';
		if ($this->connectionType == 'S3') {
			require_once CST_DIR.'lib/api/S3.php';
			$awsAccessKey = get_option('cst-s3-accesskey');
			$awsSecretKey = get_option('cst-s3-secretkey');
			$this->cdnConnection = new S3($awsAccessKey, $awsSecretKey);
		} else if ($this->connectionType == 'FTP') {
			$this->cdnConnection = ftp_connect(get_option('cst-ftp-server'), get_option('cst-ftp-port'));
			if ($this->cdnConnection === false) {
				CST_Page::$messages[] = 'FTP connection error, please check details.';
			} else {
				if (ftp_login($this->cdnConnection, get_option('cst-ftp-username'), get_option('cst-ftp-password')) === false) {
					CST_Page::$messages[] = 'FTP login error, please check details.';
				}
				$this->ftpHome = ftp_pwd($this->cdnConnection);
			}
		} else if ($this->connectionType == 'Cloudfiles') {
			require_once CST_DIR.'/lib/api/cloudfiles.php';
			try {
				$cfAuth = new CF_Authentication(get_option('cst-cf-username'), get_option('cst-cf-api'));
				$cfAuth->authenticate();
				$this->cdnConnection = new CF_Connection($cfAuth);
				$this->cdnConnection = $this->cdnConnection->create_container(get_option('cst-cf-container'));
			} catch (Exception $e) {
				CST_Page::$messages[] = 'Cloudfiles connection error, please check details.';
			}
		}
	}

	/**
	 * Pushes a file to the CDN
	 * 
	 * @param $file string path to the file to push
	 * @param $remotePath string path to the remote file
	 */
	private function pushFile($file, $remotePath) {
		if ($this->connectionType == 'S3') {
			// Puts a file to the bucket
			// putObjectFile(localName, bucketName, remoteName, ACL)
			$bucketName = get_option('cst-s3-bucket');
			$buckets = $this->cdnConnection->listBuckets();
			if (!in_array($bucketName, $buckets)) {
				$this->cdnConnection->putBucket($bucketName);
			}
			$this->cdnConnection->putObjectFile($file, $bucketName, $remotePath, S3::ACL_PUBLIC_READ);
		} else if ($this->connectionType == 'FTP') {
			$initDir = get_option('cst-ftp-dir');
			if ($initDir[0] != '/') {
				update_option('cst-ftp-dir', '/'.$initDir);
				$initDir = get_option('cst-ftp-dir');
			}
			// Creates the directories
			ftp_chdir($this->cdnConnection, $this->ftpHome.$initDir);
			$remotePathExploded = explode('/', $remotePath);
			$filename = array_pop($remotePathExploded);
			foreach($remotePathExploded as $dir) {
				$rawlist = ftp_rawlist($this->cdnConnection, $dir);
				if (empty($rawlist)) {
					ftp_mkdir($this->cdnConnection, $dir);
				}
				ftp_chdir($this->cdnConnection, $dir);
			}
			// Uploads files
			ftp_put($this->cdnConnection, $filename, $file, FTP_ASCII);
		} else if ($this->connectionType == 'Cloudfiles') {
			require_once CST_DIR.'etc/mime.php';
			global $mime_types;
			$object = $this->cdnConnection->create_object($remotePath);
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			$object->content_type = $mime_types[$extension];
			$result = $object->load_from_filename($file);
		}
	}

	/**
	 * Sends $file to Google Closure Compiler
	 * 
	 * @param $file string absolute path to file to be minified
	 * @return $response string the resulting minified code or an error
	 */
	private function minifyFile($file) {
		$js = file_get_contents($file);
		$data = 'output_info=compiled_code&js_code='.$js;
		$url = 'http://closure-compiler.appspot.com/compile';
		$optional_headers = NULL;
		$params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
		if ($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return $response;
	}

	/**
	 * Checks if the file is in the excluded files array
	 * 
	 * @param $type string either 'js' or 'css'
	 * @param $file string path of file relative to site root
	 * @return boolean
	 */
	private function checkIfExcluded($type, $file) {
		$excludedFiles = get_option('cst-'.$type.'-exclude');
		$excludedFiles = explode(",", $excludedFiles);
		foreach ($excludedFiles as &$excludedFile) {
			$excludedFile = ABSPATH . $excludedFile;
		}
		if (in_array($file, $excludedFiles)) {
			return true;
		} else {
			return false;
		}
	}

	/**
 	* Concatenates the passed files and saves to specified file
 	* 
 	* @param $files array of file paths to combine
 	* @param $type string file extension
 	* @param $savePath string path to folder of where to save the combined file
 	*/
	private function combineFiles($files, $type, $savePath) {
		$savePath = ABSPATH.$savePath.'/cst-combined.'.$type;
		if (file_exists($savePath)) {
			unlink($savePath);
		}
		foreach ($files as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == $type && !self::checkIfExcluded($type, $file)) {
				file_put_contents($savePath, file_get_contents($file)."\r\n", FILE_APPEND);
			}
		}
	}

	/**
	 * Finds all the files that need syncing and add to database
	 * 
	 */
	private function findFiles() {
		$files = array();
		if (isset($_POST['cst-options']['syncfiles']['cssjs']))
			$files[] = get_stylesheet_directory();
		if (isset($_POST['cst-options']['syncfiles']['theme']))
			$files[] = get_template_directory();
		if (isset($_POST['cst-options']['syncfiles']['media'])) {
			$files[] = ABSPATH.'wp-includes';
			$mediaFiles = $this->getMediaFiles();
		}

		$files = $this->getDirectoryFiles($files);

		// Combine files if required
		if (get_option('cst-js-combine') == 'yes') {
			$this->combineFiles($files, 'js', get_option('cst-js-savepath'));
		}

		if (get_option('cst-css-combine') == 'yes') {
			$this->combineFiles($files, 'css', get_option('cst-css-savepath'));
		}

		if (get_option('cst-js-combine') == 'yes' || get_option('cst-css-combine') == 'yes') {
			if (get_option('cst-css-combine') != get_option('cst-js-combine')) {
				$combinedCssJs[] = ABSPATH.get_option('cst-js-savepath');
				$combinedCssJs[] = ABSPATH.get_option('cst-css-savepath');
			} else {
				$combinedCssJs[] = ABSPATH.get_option('cst-js-savepath');
			}
			$combinedCssJs = $this->getDirectoryFiles($combinedCssJs);
		}

		if (isset($_POST['cst-options']['syncfiles']['media'])) {
			$files = array_merge($files, $mediaFiles);
			if (isset($combinedCssJs) && !empty($combinedCssJs))
				$files = array_merge($files, $combinedCssJs);
		}

		if (get_option('cst-js-minify') == 'yes') {
			if (get_option('cst-js-combine') == 'yes' && file_exists(ABSPATH.get_option('cst-js-savepath').'/cst-combined.js')) {
				// If JS is combined then only bother minifying that one
				$this->minifyFile(ABSPATH.get_option('cst-js-savepath').'/cst-combined.js');
			} else {
				foreach ($files as $file) {
					if (pathinfo($file, PATHINFO_EXTENSION) == 'js' && !self::checkIfExcluded('js', $file)) {
						file_put_contents(ABSPATH.get_option('cst-js-savepath').'/'.pathinfo($file, PATHINFO_FILENAME).'.min.js', $this->minifyFile($file));
						$files[] = ABSPATH.get_option('cst-js-savepath').'/'.pathinfo($file, PATHINFO_FILENAME).'.min.js';
					}
				}
			}
		}
		self::_addFilesToDb($files);
	}

	/**
	 * Adds the files to the database
	 * 
	 * @param $files array of file paths
	 */
	private function _addFilesToDb($files) {
		global $wpdb;

		// Adds file to db
		foreach($files as $file) {

			if (stristr($file, 'wp-content')) {
				$remotePath = preg_split('$wp-content$', $file);
				$remotePath = 'wp-content'.$remotePath[1];
			} else if (stristr($file, 'wp-includes')) {
				$remotePath = preg_split('$wp-includes$', $file);
				$remotePath = 'wp-includes'.$remotePath[1];
			} else if (stristr($file, ABSPATH)) {
				$remotePath = preg_split('$'.ABSPATH.'$', $file);
				$remotePath = $remotePath[1];
			}

			$row = $wpdb->get_row("SELECT * FROM `".CST_TABLE_FILES."` WHERE `remote_path` = '".$remotePath."'");

			$changedate = filemtime($file);

			if ((!empty($row) && $changedate != $row->changedate) || (isset($_POST['cst-options']['syncall']) && $row != NULL)) {
				$wpdb->update(
					CST_TABLE_FILES,
					array('changedate' => $changedate, 'synced' => '0'),
					array('remote_path' => $remotePath)
				);
			} else if (!isset($row) || empty($row)) {
				$wpdb->insert(
					CST_TABLE_FILES,
					array(
	        			'file_dir' => $file,
	      			    'remote_path' => $remotePath,
	    			    'changedate' => filemtime($file),
	   	 			    'synced' => '0'
					)
				);
			}
		}
	}

	/**
	 * Syncs all required files to CDN
	 * 
	 */
	public function syncFiles() {
		global $wpdb;

		if ($this->connectionType == 'Origin') {
			echo '<div class="cst-progress">Sync not required on origin pull CDNs.';
		} else {
			$this->findFiles();
			
			$filesToSync = $wpdb->get_results("SELECT * FROM `".CST_TABLE_FILES."` WHERE `synced` = '0'", ARRAY_A);
			$total = count($filesToSync);
			$i = 1;
			echo '<div class="cst-progress">';	
			echo '<h2>Syncing Files..</h2>';
			foreach($filesToSync as $file) {
				$this->pushFile($file['file_dir'], $file['remote_path']);
				$padstr = str_pad("", 512, " ");
				echo $padstr;
				echo 'Syncing ['.$i.'/'.$total.'] '.$file['remote_path'].'<br />';
				flush();
				$i++;
				$wpdb->update(
					CST_TABLE_FILES,
					array(
						'synced' => '1'
					),
					array(
						'id' => $file['id']
					)
				);
			}
			echo 'All files synced.';
		}
		echo '<br /><br />Return to <a href="'.CST_URL.'?page=cst">CST Options</a>.</div>';
	}

	/**
	 * Sync a specified directory to the CDN
	 * 
	 * @param $dirs array of directories to sync relative to site root
	 */
	public function syncCustomDirectory($dirs) {
		update_option('cst-custom-directories', serialize($dirs));
		foreach ($dirs as &$dir) {
			$dir = ABSPATH.$dir;
		}
		$files = self::getDirectoryFiles($dirs);
		self::_addFilesToDb($files);
		self::syncFiles();
	}

	/**
 	 * Gets all media files
 	 * 
	 */
	private function getMediaFiles() {
		global $wpdb;
		$mediaFiles = array();
		$files = $wpdb->get_results("SELECT pmo.meta_value AS filename , pmt.meta_value AS meta 
							 FROM ".$wpdb->postmeta." as pmo 
							 INNER JOIN ".$wpdb->postmeta." as pmt 
							 ON pmt.post_id = pmo.post_id 
							 AND pmt.meta_key = '_wp_attachment_metadata'   
							 WHERE pmo.meta_key = '_wp_attached_file'",ARRAY_A );
		$uploadDir = wp_upload_dir();
		$uploadDir = $uploadDir['basedir'].'/';
		foreach($files as $file) {
			$mediaFiles[] = $uploadDir.$file['filename'];
		}
		return $mediaFiles;
	}

	/**
	 * Loops through a directory checking file types
	 * 
	 * @param array directories to loop through
	 * @return array of file directories
	 */
	private function getDirectoryFiles($dirs) {
		$files = array();
		foreach ($dirs as $dir) {
			if ($handle = opendir($dir)) {
				while (false !== ($entry = readdir($handle))) {
					if (preg_match('$\.(css|js|jpe?g|gif|png)$', $entry)) {
						$files[] = $dir.'/'.$entry;
					}
				}
				closedir($handle);
			}
		}
		return $files;
	}

	public function createNonce() {
		$GLOBALS['nonce'] = wp_create_nonce('cst-nonce');
	}

	/**
	 * Enqueues the JS/CSS
	 * 
	 */
	public function enqueueFiles() {
		wp_enqueue_script('cst-generic-js', plugins_url('/js/cst-js.js', CST_FILE));
		wp_enqueue_style('cst-generic-style', plugins_url('/css/cst-style.css', CST_FILE));
	}

	/**
	 * Creates the admin page(s) required
	 * 
	 */
	public function createPages() {
		require_once CST_DIR.'lib/pages/Options.php';
		add_options_page('CST Options', 'CDN Sync Tool', 'manage_options', 'cst', array('CST_Page_Options', 'page'));
	}
}
