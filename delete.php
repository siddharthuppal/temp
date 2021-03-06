<?php
require_once("include/functions.php");
require_once("include/config.php");

dbconn();

standardheader('Delete Torrents');

$id = mysql_escape_string($_GET["info_hash"]);

if (!isset($id) || !$id)
    die("Error ID");

$res = mysql_query("SELECT namemap.info_hash, namemap.uploader, namemap.filename, namemap.url, UNIX_TIMESTAMP(namemap.data) as data, namemap.size, namemap.comment, categories.name as cat_name, summary.seeds, summary.leechers, summary.finished, summary.speed FROM namemap LEFT JOIN categories ON categories.id=namemap.category LEFT JOIN summary ON summary.info_hash=namemap.info_hash WHERE namemap.info_hash ='" . $id . "'") or die(mysql_error());
$row = mysql_fetch_array($res);

if ($CURUSER["delete_torrents"]!="yes" && $CURUSER["uid"]!=$row["uploader"])
   {
   err_msg(SORRY,CANT_DELETE_TORRENT);
   stdfoot();
   exit();
}

$scriptname = htmlspecialchars($_SERVER["PHP_SELF"]);

$link = urlencode($_GET["returnto"]);
$hash = AddSlashes($_GET["info_hash"]);

if ($link=="")
   $link="torrents.php";

if (isset($_POST["action"])) {

   if ($_POST["action"]==DELETE) {

      $ris = mysql_query("SELECT info_hash,filename,url FROM namemap WHERE info_hash=\"$hash\"") or die(mysql_error());
      if (mysql_num_rows($ris)==0)
            {
            err_msg("Sorry!", "torrent $hash not found.");
            exit();
            }
      else
            {
            list($torhash,$torname,$torurl)=mysql_fetch_array($ris);
            }
      write_log("Deleted torrent $torname ($torhash)","delete");

      @mysql_query("DELETE FROM summary WHERE info_hash=\"$hash\"");
      @mysql_query("DELETE FROM namemap WHERE info_hash=\"$hash\"");
      @mysql_query("DELETE FROM timestamps WHERE info_hash=\"$hash\"");
      @mysql_query("DELETE FROM comments WHERE info_hash=\"$hash\"");
      @mysql_query("DELETE FROM ratings WHERE infohash=\"$hash\"");
      @mysql_query("DELETE FROM peers WHERE infohash=\"$hash\"");
      @mysql_query("DELETE FROM history WHERE infohash=\"$hash\"");

      unlink($TORRENTSDIR."/$hash.btf");

      print("<script LANGUAGE=\"javascript\">window.location.href=\"$link\"</script>");
      exit();

   }

   else {

   print("<script LANGUAGE=\"javascript\">window.location.href=\"$link\"</script>");
   exit();

   }

}

block_begin(DELETE_TORRENT);

print("<table width=100% class=\"lista\" border=\"0\" cellspacing=\"5\" cellpadding=\"5\">\n");
print("<tr><td align=right class=\"header\">".FILE_NAME.":</td><td class=\"lista\" >" . $row["filename"]. "</td></tr>");
print("<tr><td align=right class=\"header\">".INFO_HASH.":</td><td class=\"lista\" >" . $row["info_hash"]. "</td></tr>");
if (!empty($row["comment"]))
   print("<tr><td align=right class=\"header\">".DESCRIPTION.":</td><td align=left class=\"lista\" >" . format_comment($row["comment"]) . "</td></tr>");
if (isset($row["cat_name"]))
   print("<tr><td align=right class=\"header\">".CATEGORY_FULL.":</td><td class=\"lista\" >" . $row["cat_name"]. "</td></tr>");
else
    print("<tr><td align=right class=\"header\">".CATEGORY_FULL.":</td><td class=\"lista\" >(nessuno)</td></tr>");
print("<tr><td align=right class=\"header\">".SIZE.":</td><td class=\"lista\" >" . $row["size"]. "</td></tr>");
print("<tr><td align=right class=\"header\">".ADDED.":</td><td class=\"lista\" >" . date("d/m/Y",$row["data"]). "</td></tr>");
if ($row["speed"] < 0) {
  $speed = "N/D";
}
else if ($row["speed"] > 2097152) {
  $speed = round($row["speed"]/1048576,2) . " MB/sec";
}
else {
  $speed = round($row["speed"] / 1024, 2) . " KB/sec";
}
print("<tr><td align=right class=\"header\">".SPEED.":</td><td class=\"lista\" >" . $speed . "</td></tr>");
print("<tr><td align=right class=\"header\">".DOWNLOADED.":</td><td class=\"lista\" >" . $row["finished"] . "</td></tr>");
print("<tr><td align=right class=\"header\">".PEERS.":</td><td class=\"lista\" >". SEEDERS .": " .$row["seeds"].",".LEECHERS .": ". $row["leechers"]."=". ($row["leechers"]+$row["seeds"]). " ". PEERS."</td></tr>");
print("</table>\n");
print ("<form action=\"$scriptname?info_hash=$id&returnto=$link\" name=\"delete\" method=\"post\">");
print ("<center><input type=\"submit\" name=\"action\" value=\"".DELETE."\" />");
print ("&nbsp;&nbsp;<input type=\"submit\" name=\"action\" value=\"".FRM_CANCEL."\" /></center>");
print ("</form>");

block_end();
stdfoot();
?>
