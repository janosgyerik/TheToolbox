<?php
if (!@include_once(WP_CONTENT_DIR . "/plugins/wp-filemanager/incl/auth.inc.php"))
 include_once(WP_CONTENT_DIR . "/plugins/wp-filemanager/incl/auth.inc.php");
include_once(WP_CONTENT_DIR . "/plugins/wp-filemanager/lang/$language.inc.php");
include_once(WP_CONTENT_DIR . "/plugins/wp-filemanager/incl/header.inc.php");
if ($AllowRename && isset($_GET['directory_name']) || $AllowRename && isset($_GET['filename']) || $AllowRename && isset($_POST['directory_name']) || $AllowRename && isset($_POST['filename']))
{
 if (isset($_GET['rename']) && isset($_POST['directory_name']))
 {
  if (!is_valid_name(substr($new_directory_name, 0, -1)))
   print "<font color='#CC0000'>$StrFolderInvalidName</font>";
  else if (@file_exists($home_directory.$path.$new_directory_name))
   print "<font color='#CC0000'>$StrAlreadyExists</font>";
  else if (@rename($home_directory.$path.$directory_name, $home_directory.$path.$new_directory_name))
   print "<font color='#009900'>$StrRenameFolderSuccess</font>";
  else
  {
   print "<font color='#CC0000'>$StrRenameFolderFail</font><br /><br />";
   print $StrRenameFolderFailHelp;
  }
 }

 else if (isset($_GET['rename']) && isset($_POST['filename']))
 {
  if (!is_valid_name($new_filename))
   print "<font color='#CC0000'>$StrFileInvalidName</font>";
  else if (@file_exists($home_directory.$path.$new_filename))
   print "<font color='#CC0000'>$StrAlreadyExists</font>";
  else if (@rename($home_directory.$path.$filename, $home_directory.$path.$new_filename))
   print "<font color='#009900'>$StrRenameFileSuccess</font>";
  else
  {
   print "<font color='#CC0000'>$StrRenameFileFail</font><br /><br />";
   print $StrRenameFileFailHelp;
  }
 }

 else
 {
  print "<table class='index' width=350 cellpadding=0 cellspacing=0 border=0>";
 /*  print "<tr>";
    print "<td class='iheadline' height=21>";
     if (isset($_GET['directory_name'])) print "<font class='iheadline'>&nbsp;$StrRenameFolder \"".htmlentities(basename($directory_name))."\"</font>";
     else if (isset($_GET['filename'])) print "<font class='iheadline'>&nbsp;$StrRenameFile \"".htmlentities($filename)."\"</font>";
    print "</td>";
    print "<td class='iheadline' align='right' height=21>";
//     print "<font class='iheadline'><a href='$base_url&amp;path=".htmlentities(rawurlencode($path))."'><img src='" . WP_CONTENT_URL . "/plugins/wp-filemanager/icon/back.gif' border=0 alt='$StrBack'></a></font>";
    print "</td>";
   print "</tr>";
 */  print "<tr>";
    print "<td valign='top' colspan=2>";

    print "<center><br />";

    if (isset($_GET['directory_name'])) print "$StrRenameFolderQuestion<br /><br />";
    else if (isset($_GET['filename'])) print "$StrRenameFileQuestion<br /><br />";
    print "<form action='$base_url&amp;output=rename&amp;rename=true' method='post'>";
    if (isset($_GET['directory_name'])) print "<input name='new_directory_name' value=\"".htmlentities(basename($directory_name))."\" size=40>&nbsp;";
    else if (isset($_GET['filename'])) print "<input name='new_filename' value=\"".htmlentities($filename)."\" size=40>&nbsp;";
    print "<input class='bigbutton' type='submit' value='$StrRename'>";
    if (isset($_GET['directory_name'])) print "<input type='hidden' name=directory_name value=\"".htmlentities($directory_name)."\">";
    else if (isset($_GET['filename'])) print "<input type='hidden' name=filename value=\"".htmlentities($filename)."\">";
    print "<input type='hidden' name=path value=\"".htmlentities($path)."\">";
    print "</form>";

    print "<br /><br /></center>";

    print "</td>";
   print "</tr>";
  print "</table>";
 }
}
else
 print "<font color='#CC0000'>$StrAccessDenied</font>";

?>