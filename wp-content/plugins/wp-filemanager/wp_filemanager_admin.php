<h1>WP-Filemanager Admin panel</h1>
<div>
<?php if (isset($_GET['settings-updated']) && ($_GET['settings-updated'] == 'true') ) : ?>
<div class="updated fade"><p><strong><?php _e('Your options have been saved'); ?></strong></p></div>
<?php endif; ?>
<form action="options.php" method="post">
<?php wp_nonce_field('update-options'); 
?>
<label>Filemanager Default Home location : </label><input type="text" name="wp_fileman_home" value="<?php 
if (get_option('wp_fileman_home') != '')
{
	echo get_option('wp_fileman_home'); 
}
else
{
	echo $home_directory;
}
?>" width="100px"/><br />
<?php
$str = "Create_File,Create_Folder,Allow_Download,Allow_Rename,Allow_Upload,Allow_Delete,Allow_View,Allow_Edit,Show_Extension";
$str_ar = explode(',',$str);
foreach ($str_ar as $st)
{
	$val = explode("_",$st);
	$st = 'wp_fileman_' . $st;
	echo "<input name='" . $st . "' value='checked' type='checkbox' " . get_option($st) . " /><label>" . $val[0] . '&nbsp;' . $val[1] . "</label><br>\n";
	$str_final = $str_final . $st . ',';
}
	$str_final = $str_final . $st . ',';
?>
<label>Editable Extension list : </label><input type="text" name="wp_fileman_editable_ext" value="<?php 
if (get_list_ext('wp_fileman_editable_ext') != '')
{
	echo get_list_ext('wp_fileman_editable_ext');
}
else
{
	echo $EditableFiles;
}
 ?>" size="120"/><br />
<label>Viewable Extension list : </label><input type="text" name="wp_fileman_viewable_ext" value="<?php echo get_list_ext('wp_fileman_viewable_ext') ?>" size="119"/><br />
<label>Hidden File String : </label><input type="text" name="wp_fileman_hidden_file" value="<?php echo get_list_ext('wp_fileman_hidden_file') ?>" size="100"/><br />
<label>Hidden File extension : </label><input type="text" name="wp_fileman_hidden_extension" value="<?php echo get_list_ext('wp_fileman_hidden_extension') ?>" size="100"/><br />
<label>Hidden Directory List : </label><input type="text" name="wp_fileman_hidden_dir" value="<?php echo get_list_ext('wp_fileman_hidden_dir') ?>" size="100"/><br />
<input type="submit" value="<?php _e('Save Changes') ?>" />
<input type="hidden" name="action" value="update" />
 <input type="hidden" name="page_options" value="<?php echo $str_final; ?>wp_fileman_home,wp_fileman_editable_ext,wp_fileman_viewable_ext,wp_fileman_hidden_file,wp_fileman_hidden_extension,wp_fileman_hidden_dir" />

</form>
<!--
<pre>
options to be implemented on this section includes


hide_file_extension
hide_file_string
hide_directory_string

Language setting

DAte Format for file modification
mime type
</pre>
-->
</div>
