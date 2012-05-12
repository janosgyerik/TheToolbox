<?php

print "<table class='index' width=500 cellpadding=0 cellspacing=0>";
 print "<tr>";
  print "<td class='iheadline' align='center' height=21>";
   print "<font class='iheadline'>$StrLoginSystem</font>";
  print "</td>";
 print "</tr>";
 print "<tr>";
  print "<td valign='top'>";

   print "<center><br />";

   print "$StrLoginInfo<br />";
   print "<form action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>";

   print "<table class='upload'>";
    print "<tr><td>$StrUsername</td><td><input name='input_username' size=20></td></tr>";
    print "<tr><td>$StrPassword</td><td><input type='password' name='input_password' size=20></td></tr>";
    print "<tr><td>&nbsp;</td><td><input class='button' type='submit' value='$StrLogIn'></td></tr>";
   print "</table>";

   print "<input type='hidden' name=path value=\"".htmlentities($path)."\">";
   print "</form>";

   print "<br /><br /></center>";

  print "</td>";
 print "</tr>";
print "</table>";

?>