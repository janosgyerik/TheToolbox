<?php
include_once(WP_CONTENT_DIR . "/plugins/wp-filemanager/fm.php");
if (!@include_once("auth.inc.php"))
 include_once("auth.inc.php");
include("../conf/config.inc.php");
include("./functions.inc.php");
include("../lang/$language.inc.php");
//echo "Download : " . $AllowDownload; 
//if (function_exists('get_option'))
//{
//	echo "Exists";
//}
//else
//{
//	echo "Sorry";
//}
if (isset($_GET['action']) && $_GET['action'] == "download")
{
    session_cache_limiter("public, post-check=50");
    header("Cache-Control: private");
}
if (isset($session_save_path)) session_save_path($session_save_path);

if (isset($_GET['path'])) $path = validate_path($_GET['path']);
if (!isset($path)) $path = FALSE;
if ($path == "./" || $path == ".\\" || $path == "/" || $path == "\\") $path = FALSE;
if (isset($_GET['filename'])) $filename = basename(stripslashes($_GET['filename']));
/*echo "<pre>";
print_r($_GET); 
echo "</pre>";*/
if ($AllowDownload || $AllowView)
{
//echo "Download Allowed";
/* if (is_file("../../../" . $home_directory . $path.$filename))
 {
	echo "File Found";
 }
 else
 {
	echo "Path : " . $home_directory . " & ".$path . " & " .$filename;
 }
 */
 if (isset($_GET['filename']) && isset($_GET['action']) && is_file($home_directory.$path.$filename) || is_file("../../../".$home_directory.$path.$filename))
 {
// echo "file found";
	if (is_file($home_directory.$path.$filename) && !strstr($home_directory, "./") && !strstr($home_directory, ".\\"))
   $fullpath = $home_directory.$path.$filename;
  else if (is_file("../../../".$home_directory.$path.$filename))
   $fullpath = "../../../".$home_directory.$path.$filename;
//echo $fullpath;
  if (!$AllowDownload && $AllowView && !is_viewable_file($filename))
  {
   print "<font color='#CC0000'>$StrAccessDenied</font>";
   exit();
  }
  header("Content-Type: ".get_mimetype($filename));
  header("Content-Length: ".filesize($fullpath));
  if ($_GET['action'] == "download");
   header("Content-Disposition: attachment; filename=$filename");
  readfile($fullpath);
 }
 else
  print "<font color='#CC0000'>$StrDownloadFail</font>";
}
?>