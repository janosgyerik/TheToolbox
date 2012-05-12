<?php

list($seconds, $microseconds) = explode(" ", microtime());
$time_end = $seconds + $microseconds;
$total_time = round($time_end-$time_start, 4);

print "<br /><br />";
print "<table cellpadding=0 cellspacing=0>";
  print "<tr><td align='center'><small><a href='http://johannesries.de/webwork/wp-filemanager/' target='_new'>WP-FileManager Plugin</a><br />powered by <a href='http://phpfm.zalon.dk/' target='_new' class='bottom'>PHPFM</a> - <a href='http://www.silvestre.com.ar/' target='_new'>Gion Icons</a> - <a href='http://mudbomb.com/archives/2004/05/27/wordpress-file-manager-hack/' target='_new'>WordPress FM Hack</a></small></td></tr>";
  print "<tr><td>&nbsp;</td></tr>";
  print "<tr><td align='center'>";
  print "</td></tr>";
  print "<tr><td>&nbsp;</td></tr>";
print "</table>";

print "</center>";
print "</body>";
print "</html>";

?>