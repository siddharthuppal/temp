<?php
if (!defined("IN_ACP"))
    die("No direct access!");

$action=(isset($_GET["action"])?$_GET["action"]:"");
$days=(isset($_POST["days"])?max(0,$_POST["days"]):"");

if ($action=="prune")
   {
   if (!isset($_POST["hash"]))
          redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=prunet");
   $count=0;
   foreach($_POST["hash"] as $selected=>$hash)
          {
           @mysql_query("DELETE FROM summary WHERE info_hash=\"$hash\"");
           @mysql_query("DELETE FROM namemap WHERE info_hash=\"$hash\"");
           @mysql_query("DELETE FROM timestamps WHERE info_hash=\"$hash\"");
           @mysql_query("DELETE FROM comments WHERE info_hash=\"$hash\"");
           @mysql_query("DELETE FROM ratings WHERE infohash=\"$hash\"");
           @mysql_query("DELETE FROM peers WHERE infohash=\"$hash\"");
           @mysql_query("DELETE FROM history WHERE infohash=\"$hash\"");

           @unlink($TORRENTSDIR."/$hash.btf");
           $count++;
           }
   block_begin("Pruned torrents");
   echo "<p align=center>n.$count torrents pruned!</p>";
   block_end();
   echo "<br />\n";
   exit;
   }
elseif ($action=="view")
    {
    // 30 DAYS
    if ($days==0)
        {
        // days not set!!
        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=prunet");
        exit;
        }
    $timeout=(60*60*24)*$days;

      $res=mysql_query("SELECT namemap.info_hash, filename, summary.lastspeedcycle AS lastupdate, summary.seeds, ".
            " summary.leechers FROM namemap LEFT JOIN summary ON summary.info_hash=namemap.info_hash WHERE external='no' AND summary.lastspeedcycle<(UNIX_TIMESTAMP()-$timeout) ORDER BY lastspeedcycle");

    block_begin("Prune torrents");
    if (!$res)
       {
       print("<p align=center>No torrents to prune...<p>");
    }
    elseif (mysql_num_rows($res)>0)
       {
       print("<script type=\"text/javascript\">
       <!--
       function SetAllCheckBoxes(FormName, FieldName, CheckValue)
       {
         if(!document.forms[FormName])
           return;
         var objCheckBoxes = document.forms[FormName].elements[FieldName];
         if(!objCheckBoxes)
           return;
         var countCheckBoxes = objCheckBoxes.length;
         if(!countCheckBoxes)
           objCheckBoxes.checked = CheckValue;
         else
           // set the check value for all check boxes
           for(var i = 0; i < countCheckBoxes; i++)
             objCheckBoxes[i].checked = CheckValue;
       }
       // -->
       </script>
       ");
       print("\n<form action=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=prunet&action=prune\" name=\"prune\" method=\"post\">");
       print("\n<table class=\"lista\" width=\"100%\">");
       print("\n<tr><td class=\"header\" align=\"center\">".TORRENT."</td>");
       print("\n<td class=\"header\" align=\"center\">".LAST_UPDATE."</td>");
       print("\n<td class=\"header\" align=\"center\">".SEEDERS."</td>");
       print("\n<td class=\"header\" align=\"center\">".LEECHERS."</td>");
       print("\n<td class=\"header\" align=\"center\"><input type=\"checkbox\" name=\"all\" onclick=\"SetAllCheckBoxes('prune','hash[]',this.checked)\" /></td></tr>");
       $count=0;
       include("include/offset.php");
       while ($rtorrent=mysql_fetch_array($res))
             {
             print("\n<tr>\n<td class=\"lista\" align=\"left\">".$rtorrent["filename"]."</td>");
             print("\n<td class=\"lista\" align=\"left\">".date("d/m/Y H:i",$rtorrent["lastupdate"]-$offset)."</td>");
             print("\n<td class=\"lista\" align=\"right\">".$rtorrent["seeds"]."</td>");
             print("\n<td class=\"lista\" align=\"right\">".$rtorrent["leechers"]."</td>");
             print("\n<td class=\"lista\" align=\"center\"><input type=\"checkbox\" name=\"hash[]\" value=\"".$rtorrent["info_hash"]."\" /></td></tr>");
             $count++;
             }

        // external
         $res=mysql_query("SELECT namemap.info_hash, filename, UNIX_TIMESTAMP(namemap.lastupdate) AS lastupdate, summary.seeds, ".
              " summary.leechers FROM namemap LEFT JOIN summary ON summary.info_hash=namemap.info_hash WHERE external='yes' AND UNIX_TIMESTAMP(namemap.lastupdate)<(UNIX_TIMESTAMP()-$timeout) ORDER BY lastupdate");

         if (mysql_num_rows($res)>0)
              {
               while ($rtorrent=mysql_fetch_array($res))
                     {
                     print("\n<tr>\n<td class=\"lista\" align=\"left\">".$rtorrent["filename"]."</td>");
                     print("\n<td class=\"lista\" align=\"left\">".date("d/m/Y H:i",$rtorrent["lastupdate"]-$offset)."</td>");
                     print("\n<td class=\"lista\" align=\"right\">".$rtorrent["seeds"]."</td>");
                     print("\n<td class=\"lista\" align=\"right\">".$rtorrent["leechers"]."</td>");
                     print("\n<td class=\"lista\" align=\"center\"><input type=\"checkbox\" name=\"hash[]\" value=\"".$rtorrent["info_hash"]."\" /></td></tr>");
                     $count++;
                     }
        }
       print("\n<tr>\n<td class=\"lista\" align=\"right\" colspan=\"5\"><input type=\"submit\" name=\"action\" value=\"GO\" /></td></tr>");
       print("\n</table>\n</form>");
    }
    else
       {
       print("<p align=center>No torrents to prune...<p>");
    }


    block_end();
    print("<br />\n");
}
else
{
    block_begin("Prune torrents");
    print("\n<form action=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=prunet&action=view\" name=\"prune\" method=\"post\">");
    print("<p align=\"center\">Imput the number of days which the torrents are to be considered as \"dead\"&nbsp;<input type=\"text\" name=\"days\" value=\"$days\" size=\"10\" maxlength=\"3\" />");
    print("\n<input type=\"submit\" name=\"action\" value=\"View\" /></td></tr>");
    print("\n</p></form>");
    block_end();
    print("<br />\n");
}
?>
