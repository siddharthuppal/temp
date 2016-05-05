<?php

require_once ("include/functions.php");
require_once ("include/config.php");

dbconn(true);
// Additional admin check by miskotes
$aid = intval( $_GET["user"]);
$arandom = intval($_GET["code"]);
if (!$aid || empty($aid) || $aid==0 || !$arandom || empty($arandom) || $arandom==0)
{
       standardheader('Access Denied');
       err_msg(ERROR,NOT_ADMIN_CP_ACCESS);
       stdfoot();
       exit;
}
//if ($arandom!=$ranid["random"] || $aid!=$ranid["id"])
//{
$mqry=mysql_query("select users.id from users inner join users_level on users_level.id=users.id_level where users.id=$aid and random=$arandom and admin_access='yes' and username=".sqlesc($CURUSER["username"])."");
if (mysql_num_rows($mqry)<1)
{
       standardheader('Access Denied');
       err_msg(ERROR,NOT_ADMIN_CP_ACCESS);
       stdfoot();
       exit;
}
// EOF

standardheader('Administrator Control Panel');

if (!$CURUSER || $CURUSER["admin_access"]!="yes")
   {
       err_msg(ERROR,NOT_ADMIN_CP_ACCESS);
       stdfoot();
       exit;
}
else
    {
    define("IN_ACP",true);
    //
    // Read a listing of uploaded category images for use in the edit menu link code...
    //
    $dir = @opendir('images/categories/');

    while($file = @readdir($dir))
    {
      if( !@is_dir('images/categories/' . $file) )
      {
        $img_size = @getimagesize('images/categories/' . $file);

        if( $img_size[0] && $img_size[1] )
        {
          $images[] = $file;
        }
      }
    }
    @closedir($dir);

    ?>

    <script language="javascript" type="text/javascript">
    <!--
    function update_cat(newimage)
    {
      if (newimage!="")
         document.cat_image.src = "images/categories/" + newimage;
      else
         document.cat_image.src = "";
    }
    //-->
    </script>

    <?php
    if (isset($_GET["do"])) $do=$_GET["do"];
      else $do = "";
    if (isset($_GET["action"]))
       $action=$_GET["action"];

    // begin the real admin page
     block_begin(ADMIN_CPANEL);
     print("\n<table class=\"lista\" width=\"100%\" align=\"center\"><tr>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=config&action=read\">".ACP_TRACKER_SETTINGS."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=banip&action=read\">".ACP_BAN_IP."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=read\">".ACP_CATEGORIES."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=read\">".ACP_USER_GROUP."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=language&action=read\">".ACP_LANGUAGES."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read\">".ACP_POLLS."</a></td>");
     print("</tr><tr>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=read\">".ACP_STYLES."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=read\">".ACP_FORUM."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=badwords&action=read\">".ACP_CENSURED."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=blocks&action=read\">".ACP_BLOCKS."</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=dbutil\">Mysql Database<br />Stats/Utils</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=masspm&action=write\">Mass PM</a></td>");
     print("</tr><tr>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=prunet\">Prune Torrents</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=pruneu\">Prune Users</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=logview\">View Sitelog</a></td>");
     print("\n<td class=\"header\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=searchdiff\">Search Diff.</a></td>");
     print("\n</tr></table>\n");

      if  ($do=="prunet")
         {
             include("include/prune_torrents.php");
         }
     elseif  ($do=="pruneu")
         {
             include("include/prune_users.php");
         }
     elseif  ($do=="masspm")
        {
            include("include/masspm.php");
        }
     elseif ($do=="logview")
        {
            include("include/sitelog.php");
        }
     elseif ($do=="searchdiff")
        {
            include("include/searchdiff.php");
        }
     elseif ($do=="config" && $action=="read")
        {
            block_begin(TRACKER_SETTINGS);
            ?>
            <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=config&action=write" name="config" method="post">
            <table class="lista" width="100%" align="center">
            <tr><td class="header" align="center" colspan=2><?php echo DB_SETTINGS; ?> (<span style="color:red"><?php echo DONT_NEED_CHANGE; ?></span>)</td></tr>
            <tr><td class="header">Host address (usually localhost):</td><td class="lista"><input type="text" name="dbhost" value="<?php echo $dbhost;?>" size="40" /></td></tr>
            <tr><td class="header">Database Name :</td><td class="lista"><input type="text" name="dbname" value="<?php echo $database;?>" size="40" /></td></tr>
            <tr><td class="header">Database User :</td><td class="lista"><input type="text" name="dbuser" value="<?php echo $dbuser;?>" size="40" /></td></tr>
            <tr><td class="header">Database Password :</td><td class="lista"><input type="text" name="dbpwd" value="<?php echo $dbpass;?>" size="40" /></td></tr>
            <tr><td class="header" align="center" colspan=2>Tracker's general settings</td></tr>
            <tr><td class="header">Tracker's Name:</td><td class="lista"><input type="text" name="trackername" value="<?php echo $SITENAME;?>" size="40" /></td></tr>
            <tr><td class="header">Base Tracker's URL (without last /):</td><td class="lista"><input type="text" name="trackerurl" value="<?php echo $BASEURL;?>" size="40" /></td></tr>
            <tr><td class="header">Tracker's Announce URLS (one url per row):</td><td class="lista"><textarea name="tracker_announceurl" rows="5" cols="40"><?php echo implode($TRACKER_ANNOUNCEURLS,"\n");?></textarea></td></tr>
            <tr><td class="header">Tracker's email:</td><td class="lista"><input type="text" name="trackeremail" value="<?php echo $SITEEMAIL;?>" size="40" /></td></tr>
            <tr><td class="header">Torrent's DIR:</td><td class="lista"><input type="text" name="torrentdir" value="<?php echo $TORRENTSDIR;?>" size="40" /></td></tr>
            <tr><td class="header">Allow External:</td><td class="lista"> true <input type="radio" name="exttorrents" value="true" <?php if ($EXTERNAL_TORRENTS==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="exttorrents" value="false" <?php if ($EXTERNAL_TORRENTS==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Enabled GZIP:</td><td class="lista"> true <input type="radio" name="gzip_enabled" value="true" <?php if ($GZIP_ENABLED==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="gzip_enabled" value="false" <?php if ($GZIP_ENABLED==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Show Debug infos on page's bottom:</td><td class="lista"> true <input type="radio" name="show_debug" value="true" <?php if ($PRINT_DEBUG==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="show_debug" value="false" <?php if ($PRINT_DEBUG==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Disable DHT (private flag in torrent)<br />will be set only on  new uploaded torrents:</td><td class="lista"> true <input type="radio" name="dht" value="true" <?php if ($DHT_PRIVATE==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="dht" value="false" <?php if ($DHT_PRIVATE==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Enable Live Stats (warning to high server load!):</td><td class="lista"> true <input type="radio" name="livestat" value="true" <?php if ($LIVESTATS==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="livestat" value="false" <?php if ($LIVESTATS==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Enable Site Log (log change on torrents/users):</td><td class="lista"> true <input type="radio" name="logactive" value="true" <?php if ($LOG_ACTIVE==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="logactive" value="false" <?php if ($LOG_ACTIVE==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Enable Basic History (torrents/users):</td><td class="lista"> true <input type="radio" name="loghistory" value="true" <?php if ($LOG_HISTORY==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="loghistory" value="false" <?php if ($LOG_HISTORY==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Private Announce:</td><td class="lista"> true <input type="radio" name="p_announce" value="true" <?php if ($PRIVATE_ANNOUNCE==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="p_announce" value="false" <?php if ($PRIVATE_ANNOUNCE==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Private Scrape:</td><td class="lista"> true <input type="radio" name="p_scrape" value="true" <?php if ($PRIVATE_SCRAPE==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="p_scrape" value="false" <?php if ($PRIVATE_SCRAPE==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Show Uploaders nick:</td><td class="lista"> true <input type="radio" name="show_uploader" value="true" <?php if ($SHOW_UPLOADER==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="show_uploader" value="false" <?php if ($SHOW_UPLOADER==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Use Popup for Torrents details/peers:</td><td class="lista"> true <input type="radio" name="usepopup" value="true" <?php if ($GLOBALS["usepopup"]) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="usepopup" value="false" <?php if (!$GLOBALS["usepopup"]) echo "checked" ?> /></td></tr>
            <tr><td class="header">Default Language:</td><td class=lista>
            <?php
            $lres=language_list();
            print("\n<select name=default_langue>");
            foreach($lres as $langue)
              {
                $option="<option ";
                if ($langue["id"]==$DEFAULT_LANGUAGE)
                   $option.="selected=selected ";
                $option.="value=".$langue["id"].">".$langue["language"]."</option>";
                print($option);
              }
            print("</select>\n");
            ?>
            </td></tr>
            <tr><td class="header">Character Encoding:</td><td class=lista>
            <select name=charset>
            <option <?php print($GLOBALS["charset"]=="ISO-8859-1"?"selected":""); ?>>ISO-8859-1
            <option <?php print($GLOBALS["charset"]=="ISO-8859-2"?"selected":""); ?>>ISO-8859-2
            <option <?php print($GLOBALS["charset"]=="ISO-8859-3"?"selected":""); ?>>ISO-8859-3
            <option <?php print($GLOBALS["charset"]=="ISO-8859-4"?"selected":""); ?>>ISO-8859-4
            <option <?php print($GLOBALS["charset"]=="ISO-8859-5"?"selected":""); ?>>ISO-8859-5
            <option <?php print($GLOBALS["charset"]=="ISO-8859-6"?"selected":""); ?>>ISO-8859-6
            <option <?php print($GLOBALS["charset"]=="ISO-8859-6-e"?"selected":""); ?>>ISO-8859-6-e
            <option <?php print($GLOBALS["charset"]=="ISO-8859-6-i"?"selected":""); ?>>ISO-8859-6-i
            <option <?php print($GLOBALS["charset"]=="ISO-8859-7"?"selected":""); ?>>ISO-8859-7
            <option <?php print($GLOBALS["charset"]=="ISO-8859-8"?"selected":""); ?>>ISO-8859-8
            <option <?php print($GLOBALS["charset"]=="ISO-8859-8-e"?"selected":""); ?>>ISO-8859-8-e
            <option <?php print($GLOBALS["charset"]=="ISO-8859-8-i"?"selected":""); ?>>ISO-8859-8-i
            <option <?php print($GLOBALS["charset"]=="ISO-8859-9"?"selected":""); ?>>ISO-8859-9
            <option <?php print($GLOBALS["charset"]=="ISO-8859-10"?"selected":""); ?>>ISO-8859-10
            <option <?php print($GLOBALS["charset"]=="ISO-8859-13"?"selected":""); ?>>ISO-8859-13
            <option <?php print($GLOBALS["charset"]=="ISO-8859-14"?"selected":""); ?>>ISO-8859-14
            <option <?php print($GLOBALS["charset"]=="ISO-8859-15"?"selected":""); ?>>ISO-8859-15
            <option <?php print($GLOBALS["charset"]=="UTF-8"?"selected":""); ?>>UTF-8
            <option <?php print($GLOBALS["charset"]=="ISO-2022-JP"?"selected":""); ?>>ISO-2022-JP
            <option <?php print($GLOBALS["charset"]=="EUC-JP"?"selected":""); ?>>EUC-JP
            <option <?php print($GLOBALS["charset"]=="Shift_JIS"?"selected":""); ?>>Shift_JIS
            <option <?php print($GLOBALS["charset"]=="GB2312"?"selected":""); ?>>GB2312
            <option <?php print($GLOBALS["charset"]=="Big5"?"selected":""); ?>>Big5
            <option <?php print($GLOBALS["charset"]=="EUC-KR"?"selected":""); ?>>EUC-KR
            <option <?php print($GLOBALS["charset"]=="windows-1250"?"selected":""); ?>>windows-1250
            <option <?php print($GLOBALS["charset"]=="windows-1251"?"selected":""); ?>>windows-1251
            <option <?php print($GLOBALS["charset"]=="windows-1252"?"selected":""); ?>>windows-1252
            <option <?php print($GLOBALS["charset"]=="windows-1253"?"selected":""); ?>>windows-1253
            <option <?php print($GLOBALS["charset"]=="windows-1254"?"selected":""); ?>>windows-1254
            <option <?php print($GLOBALS["charset"]=="windows-1255"?"selected":""); ?>>windows-1255
            <option <?php print($GLOBALS["charset"]=="windows-1256"?"selected":""); ?>>windows-1256
            <option <?php print($GLOBALS["charset"]=="windows-1257"?"selected":""); ?>>windows-1257
            <option <?php print($GLOBALS["charset"]=="windows-1258"?"selected":""); ?>>windows-1258
            <option <?php print($GLOBALS["charset"]=="KOI8-R"?"selected":""); ?>>KOI8-R
            <option <?php print($GLOBALS["charset"]=="KOI8-U"?"selected":""); ?>>KOI8-U
            <option <?php print($GLOBALS["charset"]=="cp866"?"selected":""); ?>>cp866
            <option <?php print($GLOBALS["charset"]=="cp874"?"selected":""); ?>>cp874
            <option <?php print($GLOBALS["charset"]=="TIS-620"?"selected":""); ?>>TIS-620
            <option <?php print($GLOBALS["charset"]=="VISCII"?"selected":""); ?>>VISCII
            <option <?php print($GLOBALS["charset"]=="VPS"?"selected":""); ?>>VPS
            <option <?php print($GLOBALS["charset"]=="TCVN-5712"?"selected":""); ?>>TCVN-5712
            </select>
            <tr><td class="header">Default Style:</td><td class=lista>
            <?php
            $sres=style_list();
            print("\n<select name=default_style>");
            foreach($sres as $style)
              {
                $option="<option ";
                if ($style["id"]==$DEFAULT_STYLE)
                   $option.="selected=selected ";
                $option.="value=".$style["id"].">".$style["style"]."</option>";
                print($option);
              }
            print("</select>\n");
            ?>
            </td></tr>
            <tr><td class="header">Max Users (numeric, 0 = no limits):</td><td class="lista"><input type="text" name="maxusers" value="<?php echo 0+$MAX_USERS;?>" size="40" /></td></tr>
            <tr><td class="header">Torrents per page:</td><td class="lista"><input type="text" name="ntorrents" value="<?php echo (0+$ntorrents==0?"15":$ntorrents);?>" size="40" /></td></tr>
            <tr><td class="header" align="center" colspan="2">Tracker's specific settings</td></tr>
            <tr><td class="header">Sanity interval (numeric seconds, 0 = disabled)<br />Good value, if enabled, is 1800 (30 minutes):</td><td class="lista"><input type="text" name="sinterval" value="<?php echo 0+$clean_interval;?>" size="40" /></td></tr>
            <tr><td class="header">Update External interval (numeric seconds, 0 = disabled)<br />Depending of how many external torrents:</td><td class="lista"><input type="text" name="uinterval" value="<?php echo 0+$update_interval;?>" size="40" /></td></tr>
            <tr><td class="header">Maximum reannounce interval (numeric seconds):</td><td class="lista"><input type="text" name="rinterval" value="<?php echo $GLOBALS["report_interval"];?>" size="40" /></td></tr>
            <tr><td class="header">Minimum reannounce interval (numeric seconds):</td><td class="lista"><input type="text" name="mininterval" value="<?php echo $GLOBALS["min_interval"];?>" size="40" /></td></tr>
            <tr><td class="header">Max N. of peers for request (numeric):</td><td class="lista"><input type="text" name="maxpeers" value="<?php echo $GLOBALS["maxpeers"];?>" size="40" /></td></tr>
            <tr><td class="header">Dynamic Torrents (not recommended):</td><td class="lista"> true <input type="radio" name="dynamic" value="true" <?php if ($GLOBALS["dynamic_torrents"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="dynamic" value="false" <?php if ($GLOBALS["dynamic_torrents"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">NAT checking :</td><td class="lista"> true <input type="radio" name="nat" value="true" <?php if ($GLOBALS["NAT"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="nat" value="false" <?php if ($GLOBALS["NAT"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Persistent connections (Database, not recommended):</td><td class="lista"> true <input type="radio" name="persist" value="true" <?php if ($GLOBALS["persist"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="persist" value="false" <?php if ($GLOBALS["persist"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Allow users to override ip :</td><td class="lista"> true <input type="radio" name="override" value="true" <?php if ($GLOBALS["ip_override"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="override" value="false" <?php if ($GLOBALS["ip_override"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Calculate Speed and Dow.ded bytes :</td><td class="lista"> true <input type="radio" name="countbyte" value="true" <?php if ($GLOBALS["countbytes"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="countbyte" value="false" <?php if ($GLOBALS["countbytes"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Table caches :</td><td class="lista"> true <input type="radio" name="caching" value="true" <?php if ($GLOBALS["peercaching"]==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="caching" value="false" <?php if ($GLOBALS["peercaching"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Max num. of seeds with same PID :</td><td class="lista"><input type="text" name="maxseeds" value="<?php echo $GLOBALS["maxseeds"];?>" size="40" /></td></tr>
            <tr><td class="header">Max num. of leechers with same PID :</td><td class="lista"><input type="text" name="maxleech" value="<?php echo $GLOBALS["maxleech"];?>" size="40" /></td></tr>
            <tr><td class="header">Validation Mode:</td><td class="lista">
            <select name="validation" size="1">
            <option value="none"<?php if($VALIDATION=="none") echo " selected"?>>none</option>
            <option value="user"<?php if($VALIDATION=="user") echo " selected"?>>user</option>
            <option value="admin"<?php if($VALIDATION=="admin") echo " selected"?>>admin</option>
            </select></td></tr>
            <tr><td class="header">Secure Registration (use ImageCode, GD+Freetype libraries needed):</td><td class="lista"> true <input type="radio" name="imagecode" value="true" <?php if ($USE_IMAGECODE==true) echo "checked" ?> />&nbsp;&nbsp; false <input type="radio" name="imagecode" value="false" <?php if ($USE_IMAGECODE==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Forum link (can be: forum link or internal/empty or none):</td><td class="lista"><input type="text" name="f_link" value="<?php echo $GLOBALS["FORUMLINK"];?>" size="40" /></td></tr>
            <tr><td class="header" align="center" colspan="2">Index/Blocks page settings</td></tr>
            <tr><td class="header">Clock type:</td><td class="lista">&nbsp;&nbsp;Analog&nbsp;<input type="radio" name="clocktype" value="true" <?php if ($GLOBALS["clocktype"]==true) echo "checked" ?> />&nbsp;&nbsp;Digital&nbsp;<input type="radio" name="clocktype" value="false" <?php if ($GLOBALS["clocktype"]==false) echo "checked" ?> /></td></tr>
            <tr><td class="header">Limit for Latest News block:</td><td class="lista"><input type="text" name="newslimit" value="<?php echo $GLOBALS["block_newslimit"];?>" size="3" maxlength="3" /></td></tr>
            <tr><td class="header">Limit for Forum block:</td><td class="lista"><input type="text" name="forumlimit" value="<?php echo $GLOBALS["block_forumlimit"];?>" size="3" maxlength="3" /></td></tr>
            <tr><td class="header">Limit for Latest Torrents block:</td><td class="lista"><input type="text" name="last10limit" value="<?php echo $GLOBALS["block_last10limit"];?>" size="3" maxlength="3" /></td></tr>
            <tr><td class="header">Limit for Most Popular Torrents block:</td><td class="lista"><input type="text" name="mostpoplimit" value="<?php echo $GLOBALS["block_mostpoplimit"];?>" size="3" maxlength="3" /></td></tr>
            <?php
            print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"invia\" value=\"".FRM_CANCEL."\" /></td></tr>");
            print("</table></form>");
            block_end();
            print("<br />");

        }
     elseif ($do=="config" && $action=="write")
            {
            if (isset($_POST["write"]) && $_POST["write"]==FRM_CONFIRM)
               {
                 @chmod("include/config.php",0777);
                 // if I get an error chmod, I'll try to put change into the file
                 $fd = fopen("include/config.php", "w") or die(CANT_WRITE_CONFIG);
                 $foutput ="<?php\n/* Tracker Configuration\n *\n *  This file provides configuration informatino for\n *  the tracker. The user-editable variables are at the top. It is\n *  recommended that you do not change the database settings\n *  unless you know what you are doing.\n */\n\n";
                 $foutput.= "//Maximum reannounce interval.\n";
                 $foutput.= "\$GLOBALS[\"report_interval\"] = " . $_POST["rinterval"] . ";\n";
                 $foutput.= "//Minimum reannounce interval. Optional.\n";
                 $foutput.= "\$GLOBALS[\"min_interval\"] = " . $_POST["mininterval"] . ";\n";
                 $foutput.= "//Number of peers to send in one request.\n";
                 $foutput.= "\$GLOBALS[\"maxpeers\"] = " . $_POST["maxpeers"] . ";\n";
                 $foutput.= "//If set to true, then the tracker will accept any and all\n";
                 $foutput.= "//torrents given to it. Not recommended, but available if you need it.\n";
                 $foutput.= "\$GLOBALS[\"dynamic_torrents\"] = " . $_POST["dynamic"] . ";\n";
                 $foutput.= "// If set to true, NAT checking will be performed.\n";
                 $foutput.= "// This may cause trouble with some providers, so it's\n";
                 $foutput.= "// off by default.\n";
                 $foutput.= "\$GLOBALS[\"NAT\"] = " . $_POST["nat"] . ";\n";
                 $foutput.= "// Persistent connections: true or false.\n";
                 $foutput.= "// Check with your webmaster to see if you're allowed to use these.\n";
                 $foutput.= "// not recommended, only if you get very higher loads, but use at you own risk.\n";
                 $foutput.= "\$GLOBALS[\"persist\"] = " . $_POST["persist"] . ";\n";
                 $foutput.= "// Allow users to override ip= ?\n";
                 $foutput.= "// Enable this if you know people have a legit reason to use\n";
                 $foutput.= "// this function. Leave disabled otherwise.\n";
                 $foutput.= "\$GLOBALS[\"ip_override\"] = " . $_POST["override"] . ";\n";
                 $foutput.= "// For heavily loaded trackers, set this to false. It will stop count the number\n";
                 $foutput.= "// of downloaded bytes and the speed of the torrent, but will significantly reduce\n";
                 $foutput.= "// the load.\n";
                 $foutput.= "\$GLOBALS[\"countbytes\"] = " . $_POST["countbyte"] . ";\n";
                 $foutput.= "// Table caches!\n";
                 $foutput.= "// Lowers the load on all systems, but takes up more disk space.\n";
                 $foutput.= "// You win some, you lose some. But since the load is the big problem,\n";
                 $foutput.= "// grab this.\n";
                 $foutput.= "//\n";
                 $foutput.= "// Warning! Enable this BEFORE making torrents, or else run makecache.php\n";
                 $foutput.= "// immediately, or else you'll be in deep trouble. The tables will lose\n";
                 $foutput.= "// sync and the database will be in a somewhat \"stale\" state.\n";
                 $foutput.= "\$GLOBALS[\"peercaching\"] = " . $_POST["caching"] . ";\n";
                 $foutput.= "//Max num. of seeders with same PID.\n";
                 $foutput.= "\$GLOBALS[\"maxseeds\"] = " . $_POST["maxseeds"] . ";\n";
                 $foutput.= "//Max num. of leechers with same PID.\n";
                 $foutput.= "\$GLOBALS[\"maxleech\"] = " . $_POST["maxleech"] . ";\n";
                 $foutput.= "\n/////////// End of User Configuration ///////////\n";
                 $foutput.= "\$dbhost = \"". $_POST["dbhost"] ."\";\n";
                 $foutput.= "\$dbuser = \"". $_POST["dbuser"] ."\";\n";
                 $foutput.= "\$dbpass = \"". $_POST["dbpwd"] ."\";\n";
                 $foutput.= "\$database = \"" .$_POST["dbname"] . "\";\n";
                 $foutput.= "//Tracker's name\n";
                 $foutput.= "\$SITENAME=\"".$_POST["trackername"]."\";\n";
                 $foutput.= "//Tracker's Base URL\n";
                 $foutput.= "\$BASEURL=\"".$_POST["trackerurl"]."\";\n";
                 $foutput.= "// tracker's announce urls, can be more than one\n";
                 $foutput.= "\$TRACKER_ANNOUNCEURLS=array();\n";
                 $tannounceurls=array();
                 $tannounceurls=explode("\n",$_POST["tracker_announceurl"]);
                 foreach($tannounceurls as $taurl)
                      {
                      $taurl=str_replace(array("\n","\r\n","\r"),"",$taurl);
                      if ($taurl!="")
                        $foutput.= "\$TRACKER_ANNOUNCEURLS[]=\"".trim($taurl)."\";\n";
                      }
                 $foutput.= "//Tracker's email (owner email)\n";
                 $foutput.= "\$SITEEMAIL=\"".$_POST["trackeremail"]."\";\n";
                 $foutput.= "//Torrent's DIR\n";
                 $foutput.= "\$TORRENTSDIR=\"".$_POST["torrentdir"]."\";\n";
                 $foutput.= "//validation type (must be none, user or admin\n";
                 $foutput.= "//none=validate immediatly, user=validate by email, admin=manually validate\n";
                 $foutput.= "\$VALIDATION=\"".$_POST["validation"]."\";\n";
                 $foutput.= "//Use or not the image code for new users' registration\n";
                 $foutput.= "\$USE_IMAGECODE=".$_POST["imagecode"].";\n";
                 $foutput.= "// interval for sanity check (good = 10 minutes)\n";
                 $foutput.= "\$clean_interval=\"".$_POST["sinterval"]."\";\n";
                 $foutput.= "// interval for updating external torrents (depending of how many external torrents)\n";
                 $foutput.= "\$update_interval=\"".$_POST["uinterval"]."\";\n";
                 $foutput.= "// forum link or internal (empty = internal) or none\n";
                 $foutput.= "\$FORUMLINK=\"".$_POST["f_link"]."\";\n";
                 $foutput.= "// If you want to allow users to upload external torrents values true/false\n";
                 $foutput.= "\$EXTERNAL_TORRENTS=".$_POST["exttorrents"].";\n";
                 $foutput.= "// Enable/disable GZIP compression, can save a lot of bandwidth\n";
                 $foutput.= "\$GZIP_ENABLED=".$_POST["gzip_enabled"].";\n";
                 $foutput.= "// Show/Hide bottom page information on script's generation time and gzip\n";
                 $foutput.= "\$PRINT_DEBUG=".$_POST["show_debug"].";\n";
                 $foutput.= "// Enable/disable DHT network, add private flag to \"info\" in torrent\n";
                 $foutput.= "\$DHT_PRIVATE=".$_POST["dht"].";\n";
                 $foutput.= "// Enable/disable Live Stats (up/down updated every announce) WARNING CAN DO HIGH SERVER LOAD!\n";
                 $foutput.= "\$LIVESTATS=".$_POST["livestat"].";\n";
                 $foutput.= "// Enable/disable Site log\n";
                 $foutput.= "\$LOG_ACTIVE=".$_POST["logactive"].";\n";
                 $foutput.= "//Enable Basic History (torrents/users)\n";
                 $foutput.= "\$LOG_HISTORY=".$_POST["loghistory"].";\n";
                 $foutput.= "// Default language (used for guest)\n";
                 $foutput.= "\$DEFAULT_LANGUAGE=".$_POST["default_langue"].";\n";
                 $foutput.= "// Default charset (used for guest)\n";
                 $foutput.= "\$GLOBALS[\"charset\"]=\"".$_POST["charset"]."\";\n";
                 $foutput.= "// Default style  (used for guest)\n";
                 $foutput.= "\$DEFAULT_STYLE=".$_POST["default_style"].";\n";
                 $foutput.= "// Maximum number of users (0 = no limits)\n";
                 $foutput.= "\$MAX_USERS=".$_POST["maxusers"].";\n";
                 $foutput.= "//torrents per page\n";
                 $foutput.= "\$ntorrents =\"".$_POST["ntorrents"]."\";\n";
                 $foutput.= "//private announce (true/false), if set to true don't allow non register user to download\n";
                 $foutput.= "\$PRIVATE_ANNOUNCE =".$_POST["p_announce"].";\n";
                 $foutput.= "//private scrape (true/false), if set to true don't allow non register user to scrape (for stats)\n";
                 $foutput.= "\$PRIVATE_SCRAPE =".$_POST["p_scrape"].";\n";
                 $foutput.= "//Show uploaders nick on torrent listing\n";
                 $foutput.= "\$SHOW_UPLOADER = ".$_POST["show_uploader"].";\n";
                 $foutput.= "\$GLOBALS[\"block_newslimit\"] = \"". $_POST["newslimit"]."\";\n";
                 $foutput.= "\$GLOBALS[\"block_forumlimit\"] = \"". $_POST["forumlimit"]."\";\n";
                 $foutput.= "\$GLOBALS[\"block_last10limit\"] = \"". $_POST["last10limit"]."\";\n";
                 $foutput.= "\$GLOBALS[\"block_mostpoplimit\"] = \"". $_POST["mostpoplimit"]."\";\n";
                 $foutput.= "\$GLOBALS[\"clocktype\"] = " . $_POST["clocktype"] . ";\n";
                 $foutput.= "\$GLOBALS[\"usepopup\"] = " . $_POST["usepopup"] . ";\n";
             $foutput.= "\n?>";
                 fwrite($fd,$foutput) or die(CANT_SAVE_CONFIG);
                 fclose($fd);
                 @chmod("include/config.php",0744);
                 mysql_query("UPDATE users SET language=$_POST[default_langue], style=$_POST[default_style] WHERE id_level=1");
                 print(CONFIG_SAVED);
                 redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]);
                 exit;
              }
            }
     elseif ($do=="category" && $action=="read")
            {
            $cat=genrelist();
            block_begin(CAT_SETTINGS);
            print("&nbsp;&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=add\"><img alt=".CAT_INSERT_NEW." border=0 src=\"images/new.gif\"></a>\n");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".CAT_SORT_INDEX."</td>\n");
            print("<td class=\"header\" align=\"center\">".NAME."</td>\n");
            print("<td class=\"header\" align=\"center\">".PICTURE."</td>\n");
            print("<td class=\"header\" align=\"center\">".EDIT."</td>\n");
            print("<td class=\"header\" align=\"center\">".DELETE."</td>\n");
            print("</tr>\n");
            foreach($cat as $category) {

            if($category["sub"] != 0)
            {
                $cate = sub_cat($category["sub"])." &raquo; ".$category["name"];
            } else {
                $cate = $category["name"];
            }
                         print("<tr>\n");
                         print("<td class=\"lista\">".$category["sort_index"]."</td>\n");
                         print("<td class=\"lista\">".StripSlashes($cate)."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".image_or_link(($category["image"]==""?"&nbsp;":"images/categories/".$category["image"]),"","")."</td>\n");
                         print("<td class=\"lista\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=edit&id=".$category["id"]."\">".image_or_link("$STYLEPATH/edit.png","",EDIT)."</a></td>\n");
                         print("<td class=\"lista\" align=\"center\"><a onclick=\"return confirm('".AddSlashes(DELETE_CONFIRM)."')\" href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=delete&id=".$category["id"]."\">".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td>\n");
                         print("</tr>\n");
                         }
            print("</table>");
            block_end();
            print("<br />");

            }
     elseif ($do=="category" && $action=="delete")
            {
                $id=intval($_GET["id"]);
                mysql_query("DELETE FROM categories WHERE id=$id") or die(mysql_error());
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=read");
            }
     elseif ($do=="category" && $action=="add")
            {
            block_begin(CAT_ADD_CAT);
            ?>
            <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=category&action=write_add" name="catadd" method="post" enctype="multipart/form-data">
            <table class="lista" width="100%" align="center">
            <tr>
            <td><?php echo NAME; ?></td><td><input type="text" name="name" size="40" maxlength="30" /></td>
            </tr>
            <tr>
            <td><?php echo SUB_CATEGORY; ?></td><td><?php sub_categories(); ?></td>
            </tr>
            <tr>
            <td><?php echo CAT_SORT_INDEX; ?></td><td><input type="text" name="sort" size="40" /></td>
            </tr>
            <tr>
            <td><?php echo CAT_IMAGE; ?>:</td>
            <td>
            <select name="image" onchange="update_cat(this.options[selectedIndex].value);">
            <option value=""><?php echo NONE; ?></option>
            <?php

            for( $i = 0; $i < count($images); $i++ )
            {
              print("<option value=\"" . $images[$i] . "\">" . $images[$i] . "</option>\n");
            }

            ?>
            </select> &nbsp;
            <?php
        if(!isset($image)) $image = "";
            print("<img name='cat_image' src='images/categories/" . $image . "'>");
            ?>
            </td>
            </tr>
            <tr>
            <td><?php echo UPLOAD_IMAGE; ?></td><td><input type="file" name="upimage" size="40" /></td></tr>
            <tr>
            <td><input type="submit" name="confirm" value=<?php echo FRM_CONFIRM ?> /></td>
            <td><input type="submit" name="confirm" value=<?php echo FRM_CANCEL ?> /></td>
            </tr>
            </table>
            </form>

            <?php
            block_end();
            print("<br />");
            }
     elseif ($do=="category" && $action=="write_add")
            {
                if ($_POST["confirm"]==FRM_CONFIRM)
                {
                   $name = mysql_escape_string($_POST["name"]);
                   $sub = mysql_escape_string($_POST["sub_category"]);
                   $sort=intval($_POST["sort"]);
                   if ($_FILES["upimage"]["name"]!=""&&str_replace(".php","",$_FILES["upimage"]["name"])==$_FILES["upimage"]["name"])
                   {
                        $img=$_FILES["upimage"]["name"];
                        move_uploaded_file($_FILES["upimage"]["tmp_name"],"images/categories/".$_FILES["upimage"]["name"]);
                   } else {
                       $img=$_POST["image"];
                   }
                   mysql_query("INSERT INTO categories SET name='$name', sub='$sub', sort_index='$sort', image='$img'") or die(mysql_error());
                }
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=read");
            }
     elseif ($do=="category" && $action=="edit")
            {
                $id=intval($_GET["id"]);
                $rcat=mysql_query("SELECT * FROM categories WHERE id=$id") or die(mysql_error());
                $rescat=mysql_fetch_array($rcat);
                if ($rescat)
                   {
                   block_begin(EDIT_CAT);
                   ?>
                   <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=category&action=write_edit&id=<?php echo $id;?>" name="catedit" method="post" enctype="multipart/form-data">
                   <table class="lista" width="100%" align="center">
                   <tr>
                   <td><?php echo NAME; ?></td><td><input type="text" name="name" value="<?php echo unesc($rescat["name"]); ?>" size="40" maxlength="30" /></td>
                   </tr>
                   <tr>
                   <td><?php echo SUB_CATEGORY; ?></td><td><?php sub_categories($rescat["sub"]); ?></td>
                   </tr>
                   <tr>
                   <td><?php echo CAT_SORT_INDEX; ?></td><td><input type="text" name="sort" value="<?php echo $rescat["sort_index"]; ?>" size="40" /></td>
                   </tr>
                   <tr>
                   <td><?php echo CAT_IMAGE; ?>:</td>
                   <td>
                   <select name="image" onchange="update_cat(this.options[selectedIndex].value);">
                   <option value=""><?php echo NONE; ?></option>
                   <?php

                   for( $i = 0; $i < count($images); $i++ )
                   {
                     if( $images[$i] == $rescat['image'] )
                     {
                       $selected = " selected=\"selected\"";
                       $image = $images[$i];
                     }
                     else
                     {
                       $selected = "";
                     }

                     print("<option value=\"" . $images[$i] . "\"" . $selected . ">" . $images[$i] . "</option>\n");
                   }

                   ?>
                   </select> &nbsp;&nbsp;
                   <?php
                   $image = "spacer.gif";
                   print("<img name='cat_image' src='images/categories/" . $image . "'>");
                   ?>
                   </td>
                   </tr>
                   <tr>
                   <td><?php echo UPLOAD_IMAGE; ?></td><td><input type="file" name="upimage" size="40" /></td>
                   </tr>
                   <tr>
                   <td><input type="submit" name="confirm" value=<?php echo FRM_CONFIRM ?> /></td>
                   <td><input type="submit" name="confirm" value=<?php echo FRM_CANCEL ?> /></td>
                   </tr>
                   </table>
                   </form>

                   <?php
                   block_end();
                   print("<br />");
                   }
                else
                  redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=read");

            }
     elseif ($do=="category" && $action=="write_edit")
            {
                if ($_POST["confirm"]==FRM_CONFIRM)
                   {
                   $id=intval($_GET["id"]);
                   $name=mysql_escape_string($_POST["name"]);
                   $sub = mysql_escape_string($_POST["sub_category"]);
                   $sort=intval($_POST["sort"]);
                   if ($_FILES["upimage"]["name"]!=""&&str_replace(".php","",$_FILES["upimage"]["name"])==$_FILES["upimage"]["name"])
                      {

                        $img=$_FILES["upimage"]["name"];
                        //die("aaa".$image);
                        move_uploaded_file($_FILES["upimage"]["tmp_name"],"images/categories/".$_FILES["upimage"]["name"]);
                      }
                   else
                       $img=$_POST["image"];

                   mysql_query("UPDATE categories SET name='$name', sub='$sub', sort_index='$sort', image='$img' WHERE id=$id") or die(mysql_error());
                }
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=category&action=read");
            }
     elseif ($do=="level" && $action=="read")
            {
            block_begin(USER_GROUPS);
            print("&nbsp;&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=add\"><img alt=".INSERT_USER_GROUP." border=0 src=\"images/new.gif\"></a>\n");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".GROUP."</td>\n");
            print("<td class=\"header\" align=\"center\">".MNU_TORRENT."<br />".VIEW_EDIT_DEL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MEMBERS."<br />".VIEW_EDIT_DEL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MNU_NEWS."<br />".VIEW_EDIT_DEL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MNU_FORUM."<br />".VIEW_EDIT_DEL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MNU_UPLOAD."</td>\n");
            print("<td class=\"header\" align=\"center\">".DOWNLOAD."</td>\n");
            print("<td class=\"header\" align=\"center\">".ADMIN_CPANEL."</td>\n");
            print("<td class=\"header\" align=\"center\">".WT."</td>\n");
            print("<td class=\"header\" align=\"center\">".DELETE."</td>\n");
            print("</tr>\n");
            $rlevel=mysql_query("SELECT * from users_level ORDER BY id_level");
            while ($level=mysql_fetch_array($rlevel)) {
                         print("<tr>\n");
                         print("<td class=\"lista\" align=\"center\"><a href=admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=edit&id=".$level["id"].">".unesc($level["prefixcolor"]).unesc($level["level"]).unesc($level["suffixcolor"])."<a></td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["view_torrents"]."/".$level["edit_torrents"]."/".$level["delete_torrents"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["view_users"]."/".$level["edit_users"]."/".$level["delete_users"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["view_news"]."/".$level["edit_news"]."/".$level["delete_news"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["view_forum"]."/".$level["edit_forum"]."/".$level["delete_forum"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["can_upload"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["can_download"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["admin_access"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$level["WT"]."</td>\n");
                         if ($level["can_be_deleted"]=="no")
                            print("<td class=\"lista\">&nbsp;</td>\n");
                         else
                             print("<td class=\"lista\" align=\"center\"><a onclick=\"return confirm('".AddSlashes(DELETE_CONFIRM)."')\" href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=delete&id=".$level["id"]."\">".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td>\n");
                         print("</tr>\n");
                         }
            print("</table>");
            block_end();
            print("<br />");
            }
     elseif ($do=="level" && $action=="edit")
        {
            $id=intval($_GET["id"]);
            $rlevel=mysql_query("SELECT * FROM users_level WHERE id=$id");
            if (!$rlevel)
               die(ERROR.CANT_FIND_GROUP);
            $level=mysql_fetch_array($rlevel);
            block_begin(USER_GROUPS);
            ?>
            <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=level&action=write&id=<?php echo $level["id"]; ?>" name="level" method="post">
            <table class="lista" width="100%" align="center">
            <tr><td class="header"><?php echo GROUP_NAME;?>:</td><td class="lista"><input type="text" name="gname" value="<?php echo unesc($level["level"]);?>" size="40" /></td></tr>
            <tr><td class="header"><?php echo GROUP_PCOLOR;?>&lt;span style='color:red'&gt;):</td><td class="lista"><input type="text" name="pcolor" value="<?php echo StripSlashes($level["prefixcolor"]);?>" size="40" maxlength="40" /></td></tr>
            <tr><td class="header"><?php echo GROUP_SCOLOR;?>&lt;/span&gt;):</td><td class="lista"><input type="text" name="scolor" value="<?php echo StripSlashes($level["suffixcolor"]);?>" size="40" maxlength="40" /></td></tr>
            <tr><td class="header"><?php echo GROUP_WT;?>&nbsp;(hours):</td><td class="lista"><input type="text" name="waiting" value="<?php echo $level["WT"];?>" size="40" maxlength="40" /></td></tr>
            <tr><td class="header"><?php echo GROUP_VIEW_TORR;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="vtorrent" value="yes" <?php if ($level["view_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="vtorrent" value="no" <?php if ($level["view_torrents"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_EDIT_TORR;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="etorrent" value="yes" <?php if ($level["edit_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="etorrent" value="no" <?php if ($level["edit_torrents"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_DELETE_TORR;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="dtorrent" value="yes" <?php if ($level["delete_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="dtorrent" value="no" <?php if ($level["delete_torrents"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_VIEW_USERS;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="vuser" value="yes" <?php if ($level["view_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="vuser" value="no" <?php if ($level["view_users"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_EDIT_USERS;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="euser" value="yes" <?php if ($level["edit_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="euser" value="no" <?php if ($level["edit_users"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_DELETE_USERS;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="duser" value="yes" <?php if ($level["delete_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="duser" value="no" <?php if ($level["delete_users"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_VIEW_NEWS;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="vnews" value="yes" <?php if ($level["view_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="vnews" value="no" <?php if ($level["view_news"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_EDIT_NEWS;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="enews" value="yes" <?php if ($level["edit_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="enews" value="no" <?php if ($level["edit_news"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_DELETE_NEWS;?>:</td><td class="lista"> <?php echo YES;?> <input type="radio" name="dnews" value="yes" <?php if ($level["delete_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="dnews" value="no" <?php if ($level["delete_news"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_VIEW_FORUM;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="vforum" value="yes" <?php if ($level["view_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="vforum" value="no" <?php if ($level["view_forum"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_EDIT_FORUM;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="eforum" value="yes" <?php if ($level["edit_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="eforum" value="no" <?php if ($level["edit_forum"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_DELETE_FORUM;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="dforum" value="yes" <?php if ($level["delete_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="dforum" value="no" <?php if ($level["delete_forum"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_UPLOAD;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="upload" value="yes" <?php if ($level["can_upload"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="upload" value="no" <?php if ($level["can_upload"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_DOWNLOAD;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="down" value="yes" <?php if ($level["can_download"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="down" value="no" <?php if ($level["can_download"]=="no") echo "checked" ?> /></td></tr>
            <tr><td class="header"><?php echo GROUP_GO_CP;?>:</td><td class="lista">  <?php echo YES;?> <input type="radio" name="admincp" value="yes" <?php if ($level["admin_access"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo NO;?> <input type="radio" name="admincp" value="no" <?php if ($level["admin_access"]=="no") echo "checked" ?> /></td></tr>
            <?php
            print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CANCEL."\" /></td></tr>");
            print("</table></form>");
            block_end();
            print("<br />");
        }
     elseif ($do=="level" && $action=="add")
        {
          block_begin(GROUP_ADD_NEW);
        ?>
          <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=level&action=writenew" name="level" method="post">
          <table class="lista" width="100%" align="center">
          <tr><td class="header"><?php echo GROUP_NAME;?>:</td><td class="lista"><input type="text" name="gname" value="" size="40" /></td></tr>
          <tr><td class="header"><?php echo GROUP_BASE_LEVEL;?></td><td class="lista"><select name="baselevel" size="1">
        <?php
          $rlevel=mysql_query("SELECT DISTINCT id_level,predef_level FROM users_level ORDER BY id_level");

          while($level=mysql_fetch_array($rlevel))
              {
              print("\n<option value=".$level["id_level"].">".$level["predef_level"]."</option>");
              }
          print("\n</select></td></tr>");
          print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CANCEL."\" /></td></tr>");
          print("</table></form>");
          block_end();
          print("<br />");
        }
     elseif ($do=="level" && $action=="writenew")
        {
            if ($_POST["write"]==FRM_CONFIRM)
               {
                 $id=intval($_POST["baselevel"]);
                 $rlevel=mysql_query("SELECT * FROM users_level WHERE id=$id") or die(mysql_error());
                 $level=mysql_fetch_array($rlevel);
                 if (!$level)
                    die(GROUP_ERR_BASE_SEL);
                 $update=array();
                 $update[]="level='".mysql_escape_string($_POST["gname"])."'";
                 $update[]="id_level='".$id."'";
                 $update[]="predef_level='".$level["predef_level"]."'";
                 $update[]="view_torrents='".$level["view_torrents"]."'";
                 $update[]="edit_torrents='".$level["edit_torrents"]."'";
                 $update[]="delete_torrents='".$level["delete_torrents"]."'";
                 $update[]="view_users='".$level["view_users"]."'";
                 $update[]="edit_users='".$level["edit_users"]."'";
                 $update[]="delete_users='".$level["delete_users"]."'";
                 $update[]="view_news='".$level["view_news"]."'";
                 $update[]="edit_news='".$level["edit_news"]."'";
                 $update[]="delete_news='".$level["delete_news"]."'";
                 $update[]="view_forum='".$level["view_forum"]."'";
                 $update[]="edit_forum='".$level["edit_forum"]."'";
                 $update[]="delete_forum='".$level["delete_forum"]."'";
                 $update[]="can_upload='".$level["can_upload"]."'";
                 $update[]="can_download='".$level["can_download"]."'";
                 $update[]="admin_access='".$level["admin_access"]."'";
                 $update[]="WT='".$level["WT"]."'";
                 $update[]="prefixcolor=".sqlesc($level["prefixcolor"]);
                 $update[]="suffixcolor=".sqlesc($level["suffixcolor"]);
                 $strupdate=implode(",",$update);
                 $id=intval($_GET["id"]);
                 mysql_query("INSERT INTO users_level SET $strupdate") or die(mysql_error());
            }
        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=read");
        }
     elseif ($do=="level" && $action=="delete")
        {
            $id=intval($_GET["id"]);
            // controle if this level can be cancelled
            $rcanc=mysql_query("SELECT can_be_deleted FROM users_level WHERE id=$id");
            if (!$rcanc)
               die(BAD_ID);
            $rcancanc=mysql_fetch_array($rcanc);
            if (!$rcancanc)
               die(BAD_ID);

            if ($rcancanc["can_be_deleted"]=="yes")
               {
               mysql_query("DELETE FROM users_level WHERE id=$id") or die(mysql_error());
               redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=read");
               }
            else
                err_msg(ERROR,CANT_DELETE_GROUP);
        }
     elseif ($do=="level" && $action=="write")
        {
            if ($_POST["write"]==FRM_CONFIRM)
               {
                 $update=array();
                 $update[]="level='".mysql_escape_string($_POST["gname"])."'";
                 $update[]="view_torrents='".$_POST["vtorrent"]."'";
                 $update[]="edit_torrents='".$_POST["etorrent"]."'";
                 $update[]="delete_torrents='".$_POST["dtorrent"]."'";
                 $update[]="view_users='".$_POST["vuser"]."'";
                 $update[]="edit_users='".$_POST["euser"]."'";
                 $update[]="delete_users='".$_POST["duser"]."'";
                 $update[]="view_news='".$_POST["vnews"]."'";
                 $update[]="edit_news='".$_POST["enews"]."'";
                 $update[]="delete_news='".$_POST["dnews"]."'";
                 $update[]="view_forum='".$_POST["vforum"]."'";
                 $update[]="edit_forum='".$_POST["eforum"]."'";
                 $update[]="delete_forum='".$_POST["dforum"]."'";
                 $update[]="can_upload='".$_POST["upload"]."'";
                 $update[]="can_download='".$_POST["down"]."'";
                 $update[]="admin_access='".$_POST["admincp"]."'";
                 $update[]="WT='".$_POST["waiting"]."'";
                 $update[]="prefixcolor=".sqlesc($_POST["pcolor"]);
                 $update[]="suffixcolor=".sqlesc($_POST["scolor"]);
                 $strupdate=implode(",",$update);
                 $id=intval($_GET["id"]);
                 mysql_query("UPDATE users_level SET $strupdate WHERE id=$id") or die(mysql_error());
            }
        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=level&action=read");
        }
     elseif ($do=="language" && $action=="read")
            {
            $cat=language_list();
            block_begin(LANGUAGE_SETTINGS);
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".USER_LANGUE."</td>\n");
            print("<td class=\"header\" align=\"center\">".URL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MEMBERS."</td>\n");

            print("</tr>\n");
            foreach($cat as $category) {
                         $res = mysql_query("SELECT * FROM users WHERE language = " . $category["id"]);
                         $total_users = 0+@mysql_num_rows($res);
                         print("<tr>\n");
                         print("<td class=\"lista\" align=\"center\">".unesc($category["language"])."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$category["language_url"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$total_users."</td>\n");
                         // print("<td class=\"lista\" align=\"center\">&nbsp;</td>\n");
                         print("</tr>\n");
                         }
            print("</table>");
            block_end();
            print("<br />");
            }


    elseif ($do=="polls" && $action=="read")
            {
            block_begin(POLLS_SETTINGS);
        print("&nbsp;&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=add\"><img alt=".INSERT_NEW_POLL." border=0 src=\"images/new.gif\"></a>\n");
            print("\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".POLLID."</td>\n");
            print("<td class=\"header\" align=\"center\">".QUESTION."</td>\n");
            print("<td class=\"header\" align=\"center\">".VOTES."</td>\n");
            print("<td class=\"header\" align=\"center\">".ACTIVATED."</td>\n");
            print("<td class=\"header\" align=\"center\">".EDIT."</td>\n");
            print("<td class=\"header\" align=\"center\">".DELETE."</td>\n");
            print("</tr>\n");
            $res = mysql_query("SELECT * FROM polls ORDER BY pid");
            ?>
            <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=polls&action=updatestatus" name="poll" method="post">
            <?php
            while($result=mysql_fetch_array($res)){
            print("<tr>\n");
            ?>
            <td class="lista" align="center"><?php echo $result["pid"];?></td>
            <td class="lista" align="center"><?php echo unesc($result["poll_question"]);?></td>
            <td class="lista" align="center"><?php echo $result["votes"];?></td>
            <td class="lista" align="center"><input type="radio" name="status" value="<?php echo $result["pid"]; ?>" <?php if ($result["status"]=="true") echo "checked"; ?>>

            <?php
            print("<td class=\"lista\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=edit&pid=".$result["pid"]."\">".image_or_link("$STYLEPATH/edit.png","",EDIT)."</a></td>\n");
            print("<td class=\"lista\" align=\"center\"><a onclick=\"return confirm('".AddSlashes(DELETE_CONFIRM)."')\" href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=delete&pid=".$result["pid"]."\">".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td>\n");
            print("</tr>\n");
            }
            ?>
            </table>
            <?php
            print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td></tr>");
            print("</form>");
            block_end();
            print("<br />");
            }


     elseif ($do=="polls" && $action=="updatestatus"){
        $activepoll=$_POST["status"];
        mysql_query("UPDATE polls SET status='true' WHERE pid='$activepoll'");
        mysql_query("UPDATE polls SET status='false' WHERE pid!='$activepoll'");
        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
     }

     elseif ($do=="polls" && $action=="add")
            {
                 block_begin(ADD_NEW_POLL);
                 ?>
                 <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=polls&action=writenew" name="poll" method="post">
                 <table class="lista" width="100%" align="center">
             <tr>
             <td class="header"><?php echo QUESTION;?></td>
             <td class="header"><input type="text" name="question" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer1" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer2" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer3" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer4" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer5" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer6" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer7" value="" size="40" /></td>
             </tr>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="answer8" value="" size="40" /></td>
             </tr>
                 <?php
                 print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CANCEL."\" /></td></tr>");
                 print("</table></form>");
                 block_end();
                 print("<br />");

            }

     elseif ($do=="polls" && $action=="edit")
            {
             $pid=intval($_GET["pid"]);
             $res =mysql_query("SELECT * FROM polls WHERE pid='$pid'") or die(mysql_error());
             if (!$res) die(ERROR.CANT_FIND_POLL);
                 $result=mysql_fetch_array($res);
             $question=unesc($result["poll_question"]);
                 block_begin("Poll: $question");
             $poll_answers = (unserialize($result["choices"]));
                 ?>
                 <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=polls&action=write&pid=<?php echo $result["pid"]; ?>" name="poll" method="post">
                 <table class="lista" width="100%" align="center">
             <tr>
             <td class="header"><?php echo QUESTION;?></td>
             <td class="header"><input type="text" name="question" value="<?php echo $question;?>" size="40" /></td>
             </tr>
             <?php
             $count=0;
               reset($poll_answers);
               foreach ($poll_answers as $entry){
                    $id     = $entry[0];
                    $choice = $entry[1];
                    $votes  = $entry[2];
                $clean=preg_replace('/\s+/', '', $choice);
             ?>
             <tr>
               <td class="lista"><?php echo OPTION;?></td>
             <td class="lista"><input type="text" name="<?php echo $clean;?>" value="<?php echo $choice;?>" size="40" /></td>
             </tr>
                 <?php
             $count++;
             }
             if($count<8){
             ?>
                <tr>
                <td class="lista"><?php echo OPTION;?></td>
                <td class="lista"><input type="text" name="newanswer" size="40" /></td>
                </tr>
             <?php
             }
                 print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" /></td><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"".FRM_CANCEL."\" /></td></tr>");
                 print("</table></form>");
                 block_end();
                 print("<br />");
            }

     elseif ($do=="polls" && $action=="delete")
            {
        $pid=intval($_GET["pid"]);
        mysql_query("DELETE FROM polls WHERE pid=$pid") or die(mysql_error());
        mysql_query("DELETE FROM poll_voters WHERE pid=$pid") or die(mysql_error());
        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
            }

     elseif ($do=="polls" && $action=="write")
            {
            if ($_POST["write"]==FRM_CANCEL) redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
            if ($_POST["write"]==FRM_CONFIRM){
                $pid=intval($_GET["pid"]);
                $total_votes = 0;
                $res =mysql_query("SELECT * FROM polls WHERE pid='$pid'") or die(mysql_error());
                if (!$res) die(ERROR.CANT_FIND_POLL);
                        $result=mysql_fetch_array($res);
                    $poll_answers = (unserialize($result["choices"]));
                    $question=unesc($result["poll_question"]);
                    reset($poll_answers);
                    $new_poll_array=array();
                        foreach ($poll_answers as $entry){
                                $id     = $entry[0];
                                $choice = $entry[1];
                                $votes  = $entry[2];
                            $clean=preg_replace('/\s+/', '', $choice);
                            if($_POST["$clean"]!=$choice){
                                 $choice=($_POST["$clean"]);
                                 $votes=0;
                            }
                            if($choice!=""){
                                $new_poll_array[] = array( $id, $choice, $votes);
                            }
                            $total_votes += $votes;
                        }
                        if(isset($_POST["newanswer"]) && $_POST["newanswer"]!=""){
                            $id++;
                            $votes=0;
                            $choice=$_POST["newanswer"];
                            $new_poll_array[] = array( $id, $choice, $votes);
                        }
                        $question=mysql_escape_string($_POST["question"]);
                        $votings= (serialize($new_poll_array));
                        mysql_query("UPDATE polls SET choices='$votings', votes='$total_votes', poll_question='$question' WHERE pid='$pid'");
                        redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
            }
            }


    elseif ($do=="polls" && $action=="writenew")
            {
            if ($_POST["write"]==FRM_CANCEL) redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
            if ($_POST["write"]==FRM_CONFIRM){
                    $new_poll_array=array();
                    $id=0;
                    $votes=0;
                    if($_POST["answer1"]!=""){
                        $choice= ($_POST["answer1"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer2"]!=""){
                        $choice= ($_POST["answer2"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer3"]!=""){
                        $choice= ($_POST["answer3"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer4"]!=""){
                        $choice= ($_POST["answer4"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer5"]!=""){
                        $choice= ($_POST["answer5"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer6"]!=""){
                        $choice= ($_POST["answer6"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer7"]!=""){
                        $choice= ($_POST["answer7"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    if($_POST["answer8"]!=""){
                        $choice= ($_POST["answer8"]);
                        $new_poll_array[] = array( $id, $choice, $votes);
                        $id++;
                    }
                    $question=mysql_escape_string($_POST["question"]);
                    $votings= AddSlashes(serialize($new_poll_array));
                    $startdate= time();
                    $starter=$CURUSER["uid"];
                    mysql_query("INSERT INTO polls SET startdate='$startdate', choices='$votings', starter_id='$starter', poll_question='$question'") or die(mysql_error());
                    redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=polls&action=read");
            }
            }


     elseif ($do=="blocks" && $action=="read")
            {
            block_begin(BLOCKS_SETTINGS);
            $position="";
            print("\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".BLOCK."</td>\n");
            print("<td class=\"header\" align=\"center\">".POSITION."</td>\n");
            print("<td class=\"header\" align=\"center\">".SORTID."</td>\n");
            print("<td class=\"header\" align=\"center\">".ACTIVE."</td>\n");
            print("</tr>\n");
            $res = mysql_query("SELECT * FROM blocks ORDER BY position, status, sortid");
            ?>
            <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=blocks&action=write" method="post" enctype="multipart/form-data">
            <?php
            while($result=mysql_fetch_array($res)){
            if($result["position"]=='l') $position=LEFT;
            if($result["position"]=='r') $position=RIGHT;
            if($result["position"]=='c') $position=CENTER;
            if($result["position"]=='t') $position=TOP;
            print("<tr>\n");
            ?>
            <td class="lista" align="center"><?php echo $result["content"];?>-block</td>
            <!--<td class="lista" align="right"><?php echo $position;?></td>-->
            <td class="lista" align="center"><select name="<?php echo $result["content"];?>position" size="1">
            <option value="l"<?php if($result["position"]=='l') echo " selected"?>>left</option>
                <option value="r"<?php if($result["position"]=='r') echo " selected"?>>right</option>
            <option value="c"<?php if($result["position"]=='c') echo " selected"?>>center</option>
            <option value="t"<?php if($result["position"]=='t') echo " selected"?>>top</option>
            </select></td>
                <td class="lista" align="center"><input type="text" name="<?php echo $result["content"];?>sortid" value="<?php echo $result["sortid"];?>" size="4" /></td>
            <td class="lista" align="center"><select name="<?php echo $result["content"];?>status" size="1">
            <option value="0"<?php if($result["status"]==0) echo " selected"?>>disabled</option>
                <option value="1"<?php if($result["status"]==1) echo " selected"?>>enabled</option>
            </select></td>
            <?php
            print("</tr>\n");
            }
            ?>
            </table>
            <tr>
            <td align="right"><input type="submit" name="write" value=<?php echo FRM_CONFIRM ?> /></td>
            </tr>
            </form>
            <?php

            block_end();
            print("<br />");
            }
     elseif ($do=="blocks" && $action=="write")
            {
                if ($_POST["write"]==FRM_CONFIRM)
                   {
                    $res = mysql_query("SELECT * FROM blocks");
                    while($result=mysql_fetch_array($res)){
                        $var1= $_POST["".$result["content"]."sortid"];
                        $var2= $_POST["".$result["content"]."status"];
                        $var3= $result["blockid"];
                        $var4= $_POST["".$result["content"]."position"];
                        mysql_query("UPDATE blocks SET sortid='$var1', status='$var2', position='$var4' WHERE blockid='$var3'") or die(mysql_error());
                    }
                   }
                   redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=blocks&action=read");
            }

     elseif ($do=="badwords" && $action=="read")
            {
              $f=@fopen("badwords.txt","r");
              $badwords=@fread($f,filesize("badwords.txt"));
              @fclose($f);
              block_begin(EDIT_CENSURED);
              ?>
              <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=badwords&action=write" method="post" enctype="multipart/form-data">
              <table class="lista" width="100%" align="center">
              <tr>
              <td align=center><?php echo CENS_ONE_PER_LINE;?></td>
              </tr>
              <tr>
              <td align=center><textarea name="badwords" rows="20" cols="60"><?php echo $badwords; ?></textarea></td>
              </tr>
              <tr>
              <td align=center><input type="submit" name="write" value=<?php echo FRM_CONFIRM ?> />&nbsp&nbsp<input type="submit" name="write" value=<?php echo FRM_CANCEL ?> /></td>
              </tr>
              </table>
              </form>
              <?php
              block_end();
              print("<br />");
            }
     elseif ($do=="badwords" && $action=="write")
            {
                if ($_POST["write"]==FRM_CONFIRM)
                   {
                   if (isset($_POST["badwords"]))
                      {
                      $f=fopen("badwords.txt","w+");
                      @fwrite($f,$_POST["badwords"]);
                      fclose($f);
                      }
                   }
                   redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]);
            }
     elseif ($do=="language" && $action=="delete")
            {
                $id=intval($_GET["id"]);
                if ($id!=$DEFAULT_LANGUAGE)
                   {
             $rlang=mysql_query("SELECT * FROM language WHERE id=$id") or die(mysql_error());
                     $reslang=mysql_fetch_array($rlang);
             $lang=$reslang["language_url"];
             if(unlink("$lang")){
                    mysql_query("UPDATE users SET language=$DEFAULT_LANGUAGE WHERE language=$id");
                    mysql_query("DELETE FROM language WHERE id=$id") or die(mysql_error());
                }
             else err_msg(ERROR,DELFAILED);
                   }
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=language&action=read");
            }
     elseif ($do=="style" && $action=="read")
            {
            $cat=style_list();
            block_begin(STYLE_SETTINGS);
            print("<br />&nbsp;&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=add\"><img alt=".INSERT_NEW_STYLE." border=0 src=\"images/new.gif\"></a>\n");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".STYLE_NAME."</td>\n");
            print("<td class=\"header\" align=\"center\">".STYLE_URL."</td>\n");
            print("<td class=\"header\" align=\"center\">".MEMBERS."</td>\n");
            print("<td class=\"header\" align=\"center\">".EDIT."</td>\n");
            print("<td class=\"header\" align=\"center\">".DELETE."</td>\n");
            print("</tr>\n");
            foreach($cat as $category) {
                         $res = mysql_query("SELECT * FROM users WHERE style = " . $category["id"]);
                         $total_users = 0+@mysql_num_rows($res);
                         print("<tr>\n");
                         print("<td class=\"lista\" align=\"center\">".unesc($category["style"])."</td>\n");
                         print("<td class=\"lista\">".$category["style_url"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$total_users."</td>\n");
                         print("<td class=\"lista\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=edit&id=".$category["id"]."\">".image_or_link("$STYLEPATH/edit.png","",EDIT)."</a></td>\n");
                         if ($category["id"]!=$DEFAULT_STYLE)
                            print("<td class=\"lista\" align=\"center\"><a onclick=\"return confirm('".AddSlashes(DELETE_CONFIRM)."')\" href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=delete&id=".$category["id"]."\">".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td>\n");
                         else
                             print("<td class=\"lista\" align=\"center\">&nbsp;</td>\n");
                         print("</tr>\n");
                         }
            print("</table>");
            block_end();
            print("<br />");
            }
     elseif ($do=="style" && $action=="edit")
            {
                $id=intval($_GET["id"]);
                $rstyle=mysql_query("SELECT * FROM style WHERE id=$id") or die(mysql_error());
                $resstyle=mysql_fetch_array($rstyle);
                if ($resstyle)
                   {
                   block_begin(EDIT_STYLE);
                   ?>
                   <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=style&action=write&id=<?php echo $id; ?> name="styleedit" method="post" enctype="multipart/form-data">
                   <table class="lista" width="100%" align="center">
                   <tr>
                   <td><?php echo STYLE_NAME;?></td><td><input type="text" name="style" value="<?php echo unesc($resstyle["style"]); ?>" size="40" maxlength="20" /></td>
                   </tr>
                   <tr>
                   <td><?php echo STYLE_URL;?></td><td><input type="text" name="style_url" value="<?php echo $resstyle["style_url"]; ?>" size="40" maxlength="100" /></td>
                   </tr>
                   <tr>
                   <td><input type="submit" name="write" value=<?php echo FRM_CONFIRM ?> /></td>
                   <td><input type="submit" name="write" value=<?php echo FRM_CANCEL ?> /></td>
                   </tr>
                   </table>
                   </form>
                   <?php
                   block_end();
                   print("<br />");
                   }
                else
                  redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=read");

            }
     elseif ($do=="style" && $action=="write")
            {
            if ($_POST["write"]==FRM_CONFIRM)
               {
                   if ($_GET["what"]=="new")
                      {
                       mysql_query("INSERT INTO style SET style='".mysql_escape_string($_POST["style"])."',style_url='".$_POST["style_url"]."'") or die(mysql_error());
                       print(STYLE_ADDED);
                      }
                   else
                       {
                       $id=intval($_GET["id"]);
                       mysql_query("UPDATE style SET style='".mysql_escape_string($_POST["style"])."',style_url='".$_POST["style_url"]."' WHERE id=$id") or die(mysql_error());
                       print(STYLE_MODIFIED);
                       }
                 redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=read");
                 exit;
              }
            else
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=read");
            }
     elseif ($do=="style" && $action=="add")
            {
              block_begin(STYLE_ADD);
              ?>
              <form action="admincp.php?user=<?php echo $CURUSER["uid"]; ?>&code=<?php echo $CURUSER["random"]; ?>&do=style&action=write&what=new" name="styleedit" method="post" enctype="multipart/form-data">
              <table class="lista" width="100%" align="center">
              <tr>
              <td><?php echo STYLE_NAME;?></td><td><input type="text" name="style" size="40" maxlength="20" /></td>
              </tr>
              <tr>
              <td><?php echo STYLE_URL;?></td><td><input type="text" name="style_url" size="40" maxlength="100" /></td>
              </tr>
              <tr>
              <td><input type="submit" name="write" value=<?php echo FRM_CONFIRM ?> /></td>
              <td><input type="submit" name="write" value=<?php echo FRM_CANCEL ?> /></td>
              </tr>
              </table>
              </form>
              <?php
              block_end();
              print("<br />");
            }
     elseif ($do=="style" && $action=="delete")
            {
                $id=intval($_GET["id"]);
                if ($id!=$DEFAULT_STYLE)
                   {
                   mysql_query("UPDATE users SET style=$DEFAULT_STYLE WHERE style=$id");
                   mysql_query("DELETE FROM style WHERE id=$id") or die(mysql_error());
                   }
                redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=style&action=read");
            }
     elseif ($do=="dbutil")
            {
            $ad_display="";
            include("include/dbutil.php");
            echo "<br />\n";

            }
     elseif ($do=="forum" && $action=="read")
            {
            $resforums=mysql_query("SELECT forums.*,uread.level as readlevel,uwrite.level as writelevel, ucreate.level as createlevel FROM forums inner join users_level  as uread on uread.id_level=minclassread inner join users_level as uwrite on uwrite.id_level=minclasswrite inner join users_level as ucreate on ucreate.id_level=minclasscreate WHERE ucreate.can_be_deleted='no' AND uread.can_be_deleted='no' AND uwrite.can_be_deleted='no' ORDER BY forums.id");
            block_begin(FORUM_SETTINGS);
            print("<br />&nbsp;&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=edit&what=new\"><img alt=".INSERT_NEW_FORUM." border=0 src=\"images/new.gif\"></a>\n");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_NAME."/Description</td>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_N_TOPICS."</td>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_N_POSTS."</td>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_READ."</td>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_WRITE."</td>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_CREATE."</td>\n");
            print("<td class=\"header\" align=\"center\">".EDIT."</td>\n");
            print("<td class=\"header\" align=\"center\">".DELETE."</td>\n");
            print("</tr>\n");
            while($result=mysql_fetch_array($resforums)) {
                         print("<tr>\n");
                         print("<td class=\"lista\"><b>".unesc($result["name"])."</b><br />".unesc($result["description"])."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$result["topiccount"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\">".$result["postcount"]."</td>\n");
                         print("<td class=\"lista\">".$result["readlevel"]."</td>\n");
                         print("<td class=\"lista\">".$result["writelevel"]."</td>\n");
                         print("<td class=\"lista\">".$result["createlevel"]."</td>\n");
                         print("<td class=\"lista\" align=\"center\"><a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=edit&id=".$result["id"]."\">".image_or_link("$STYLEPATH/edit.png","",EDIT)."</a></td>\n");
                         print("<td class=\"lista\" align=\"center\"><a onclick=\"return confirm('".AddSlashes(DELETE_CONFIRM)."')\" href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=delete&id=".$result["id"]."\">".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td>\n");
                         print("</tr>\n");
                         }
            print("</table>");
            block_end();
            print("<br />");
            }
     elseif ($do=="forum" && $action=="edit")
            {
            if (isset($_GET["what"])) $what=$_GET["what"];
        else $what="";
            if ($what!="new")
               {
           $id=intval($_GET["id"]);
               $resforums=mysql_query("SELECT * FROM forums WHERE id=".$id);
               }
            if (isset($resforums) && $resforums)
               $result=mysql_fetch_array($resforums);
            elseif ($what!="new")
                err_msg(ERROR,BAD_ID);

            block_begin(FORUM_SETTINGS);
            $rlevel=mysql_query("SELECT DISTINCT id_level, predef_level, level FROM users_level ORDER BY id_level");
            $alevel=array();
            while($reslevel=mysql_fetch_array($rlevel))
                $alevel[]=$reslevel;

        if (!isset($id)) $id = "";

            print("<form name=editforum action=admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=saveedit&id=$id&what=$what method=post>\n");
            print("<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\" align=\"center\">".NAME."</td>\n");
            print("<td class=\"lista\" align=\"center\"><input type=\"text\" name=\"name\" value=\"".($what == "new" ? "" : unesc($result["name"]))."\" size=\"40\" maxlength=\"60\" /></td>\n");
            print("</tr>\n<tr>\n");
            print("<td class=\"header\" align=\"center\">".DESCRIPTION."</td>\n");
            print("<td class=\"lista\" align=\"center\"><textarea name=\"description\" rows=\"3\" cols=\"40\" maxlength=\"200\">".($what == "new" ? "" : unesc($result["description"]))."</textarea></td>\n");
            print("</tr>\n<tr>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_READ."</td>\n");
            print("<td class=\"lista\" align=\"center\"><select name=readlevel>\n");
            foreach($alevel as $level)
                {
                print("<option value=".$level["id_level"].($result["minclassread"] == $level["id_level"] ? " selected>" : ">").$level["level"]."</option>\n");
                }
            print("</select>\n</td>\n");
            print("</tr>\n<tr>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_WRITE."</td>\n");
            print("<td class=\"lista\" align=\"center\"><select name=writelevel>\n");
            foreach($alevel as $level)
                {
                print("<option value=".$level["id_level"].($result["minclasswrite"] == $level["id_level"] ? " selected>" : ">").$level["level"]."</option>\n");
                }
            print("</select>\n</td>\n");
            print("</tr>\n<tr>\n");
            print("<td class=\"header\" align=\"center\">".FORUM_MIN_CREATE."</td>\n");
            print("<td class=\"lista\" align=\"center\"><select name=createlevel>\n");
            foreach($alevel as $level)
                {
                print("<option value=".$level["id_level"].($result["minclasscreate"] == $level["id_level"] ? " selected>" : ">").$level["level"]."</option>\n");
                }
            print("</select>\n</td>\n");
            print("</tr>\n<tr>\n");
            print("<td class=\"lista\" align=\"center\">&nbsp;</td>\n<td class=\"lista\" align=\"center\"><input type=\"submit\" name=\"confirm\" value=\"".FRM_CONFIRM."\" />\n");
            print("&nbsp;&nbsp;<input type=\"submit\" name=\"confirm\" value=\"".FRM_CANCEL."\" /></td>\n");
            print("</tr>\n");
            print("</table>\n");
            print("</form>\n");
            block_end();
            print("<br />");
            }
     elseif ($do=="forum" && $action=="saveedit")
            {
            $what=$_GET["what"];
            $minclassread=intval($_POST["readlevel"]);
            $minclasswrite=intval($_POST["writelevel"]);
            $minclasscreate=intval($_POST["createlevel"]);
            $description=sqlesc($_POST["description"]);
            $name=sqlesc($_POST["name"]);
            if ($what!="new")
               {
               $id=intval($_GET["id"]);
               mysql_query("UPDATE forums SET name=$name,description=$description,minclassread=$minclassread,minclasswrite=$minclasswrite,minclasscreate=$minclasscreate WHERE id=$id") or die(mysql_error());
               }
            else
                {
               mysql_query("INSERT INTO forums SET name=$name,description=$description,minclassread=$minclassread,minclasswrite=$minclasswrite,minclasscreate=$minclasscreate") or die(mysql_error());
                }
            redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=read");
            }
     elseif ($do=="forum" && $action=="delete")
            {
            $id=intval($_GET["id"]);
            // control if there are posts/topics
            $resforum=mysql_query("SELECT * FROM forums WHERE id=$id");
            if ($_GET["confirm"]==1)
               {
               mysql_query("DELETE FROM topics WHERE forumid=$id") or die(mysql_error());
               mysql_query("DELETE FROM forums WHERE id=$id") or die(mysql_error());
               redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=read");
               exit();
               }
            if ($resforum)
               {
                   $result=mysql_fetch_array($resforum);
                   if ($result["topiccount"]>0 || $result["postcount"]>0)
                   $msg=FORUM_PRUNE_1;
                   $msg.=FORUM_PRUNE_2." <a href=admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=forum&action=delete&id=$id&confirm=1>".CLICK_HERE."</a>";
                   $msg.=",<br />".FORUM_PRUNE_3;
                   err_msg($msg,WARNING);
               }
            }
     elseif ($do=="banip" && $action=="read")
            {
            block_begin(ACP_BAN_IP);
            $getbanned = mysql_query("SELECT * FROM bannedip ORDER BY added DESC") or die(mysql_error());
            $rowsbanned = @mysql_num_rows($getbanned);
            print("<form action=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=banip&action=write\" name=\"ban\" method=\"post\">");
            print("<center>".BAN_NOTE."</center>");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr><td class=header>".ADDED."</td><td class=header align=left>".FIRST_IP."</td>".
            "<td class=header align=left>".LAST_IP."</td><td class=header align=left>".BY."</td>".
            "<td class=header align=left>".COMMENTS."</td><td class=header>".REMOVE."</td></tr>\n");
            if ($rowsbanned>0)
            {
               while ($arr=mysql_fetch_assoc($getbanned))
               {
               $r2 = mysql_query("SELECT username FROM users WHERE id=$arr[addedby]") or die(mysql_error());
               $a2 = mysql_fetch_assoc($r2);
               $arr["first"] = long2ip($arr["first"]);
               $arr["last"] = long2ip($arr["last"]);
               print("<tr><td class=lista>".get_date_time($arr['added'])."</td><td  class=lista align=left>$arr[first]</td>".
                 "<td align=left class=lista>$arr[last]</td><td align=left class=lista><a href=userdetails.php?id=$arr[addedby]>$a2[username]".
                 "</a></td><td align=left class=lista>$arr[comment]</td><td class=lista><a href=admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=banip&action=delete&ip=$arr[id]>".image_or_link("$STYLEPATH/delete.png","",DELETE)."</a></td></tr>\n");
               }
               print("</table>\n");
            }
            else
                print("<tr><td colspan=6 align=center>".NO_BANNED_IPS."</td></tr></table>");
            print("<br /><br />\n<table class=\"lista\" width=\"100%\" align=\"center\">\n");
            print("<tr>\n");
            print("<td class=\"header\">".FIRST_IP." :</td><td class=\"lista\"><input type=\"text\" name=\"firstip\" size=\"15\" /></td>");
            print("<td class=\"header\">".LAST_IP." :</td><td class=\"lista\"><input type=\"text\" name=\"lastip\" size=\"15\" /></td>");
            print("</tr>\n<tr>\n");
            print("<td class=\"header\">".COMMENTS." :</td><td class=\"lista\" colspan=3><input type=\"text\" name=\"comment\" size=\"60\" /></td>");
            print("</tr>\n");
            print("<tr><td align=\"center\" class=\"header\" colspan=4>");
            print("<input type=\"submit\" name=\"write\" value=\"".FRM_CONFIRM."\" />");
            print("&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"write\" value=\"".FRM_CANCEL."\" />");
            print("</td></tr>\n");
            print("</table>\n</form>\n");
            block_end();
            }
     elseif ($do=="banip" && $action=="write")
            {
            if ($_POST['firstip']=="" || $_POST['lastip']=="")
                err_msg(ERROR,NO_IP_WRITE);
              else
                    {
                      //ban the ip for real
                      $firstip = $_POST["firstip"];
                      $lastip = $_POST["lastip"];
                      $comment = $_POST["comment"];
                      $firstip = sprintf("%u", ip2long($firstip));
                      $lastip = sprintf("%u", ip2long($lastip));
                      if ($firstip == -1 || $lastip == -1)
                           err_msg(ERROR,IP_ERROR);
                      else{
                               $comment = sqlesc($comment);
                               $added = sqlesc(time());
                               echo "INSERT INTO bannedip (added, addedby, first, last, comment) VALUES($added, $CURUSER[uid], $firstip, $lastip, $comment)";
                               mysql_query("INSERT INTO bannedip (added, addedby, first, last, comment) VALUES($added, $CURUSER[uid], $firstip, $lastip, $comment)") or die(mysql_error());
                              redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=banip&action=read");
                      }
                }
            }
     elseif ($do=="banip" && $action=="delete")
            {

            if ($_GET['ip']=="")
                err_msg(ERROR,INVALID_ID);
            //delete the ip from db
            $id = intval($_GET['ip']);
            mysql_query("DELETE FROM bannedip WHERE id=".$id) or die(mysql_error());
            redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=banip&action=read");
            }
     elseif ($do=="sanity" && $action=="now")
          {
            require_once(dirname(__FILE__)."/include/sanity.php");

            $now = time();

            $res = mysql_query("SELECT last_time FROM tasks WHERE task='sanity'");
            $row = mysql_fetch_row($res);
            if (!$row)
                mysql_query("INSERT INTO tasks (task, last_time) VALUES ('sanity',$now)");
            else
            {
              $ts = $row[0];
              mysql_query("UPDATE tasks SET last_time=$now WHERE task='sanity' AND last_time = $ts");
            }
            do_sanity();
            redirect("admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]);
          }
     else {
          block_begin(WELCOME_ADMINCP);
          $res=mysql_query("SELECT * FROM tasks");
          print("<div  style=\"padding-left: 20px\"><center><br /><br />".ADMINCP_NOTES."</center><br /><br />\nSome statistic/system info:<br />");
          if ($res)
             {
              while ($result=mysql_fetch_array($res))
                    {
                    if ($result["task"]=="sanity")
                       print(LAST_SANITY.get_date_time($result["last_time"])." (".NEXT.": ".get_date_time($result["last_time"]+intval($GLOBALS["clean_interval"])).")&nbsp;<a href=\"admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"]."&do=sanity&action=now\">Do it now!</a><br />");
                    elseif ($result["task"]=="update")
                       print(LAST_EXTERNAL.get_date_time($result["last_time"])." (".NEXT.": ".get_date_time($result["last_time"]+intval($GLOBALS["update_interval"])).")<br />");
                 }
             }
          // check torrents' folder
          if (file_exists($TORRENTSDIR))
            {
            if (is_writable($TORRENTSDIR))
                  print("<br />\nTorrent's folder $TORRENTSDIR <span style=\"color:#00FF00; font-weight: bold;\">is writable</span><br />\n");
            else
                  print("<br />\nTorrent's folder $TORRENTSDIR is <span style=\"color:#FF0000; font-weight: bold;\">NOT writable</span><br />\n");
            }
          else
            print("<br />\nTorrent's folder $TORRENTSDIR <span style=\"color:#FF0000; font-weight: bold;\">NOT FOUND!</span><br />\n");

          // check config.php
          if (file_exists("include/config.php"))
            {
            if (is_writable("include/config.php"))
                      print("config.php <span style=\"color:#00FF00; font-weight: bold;\">is writable</span><br />\n");
            else
                  print("config.php is <span style=\"color:#FF0000; font-weight: bold;\">NOT writable</span> (cannot writing tracker's configuration change)<br />\n");
            }
          else // never go here, if not exist got error before...
            print("<br />\nconfig.php file <span style=\"color:#FF0000; font-weight: bold;\">NOT FOUND!</span><br />\n");

/* language upload turned off...
          // check language folder
          if (file_exists("language"))
            {
              if (is_writable("language"))
                    print("Language's folder <span style=\"color:#00FF00; font-weight: bold;\">is writable</span><br />\n");
              else
                    print("Language's folder is <span style=\"color:#FF0000; font-weight: bold;\">NOT writable</span> (cannot writing tracker's configuration change)<br />\n");
            }
          else
            print("<br />\nLanguage's folder <span style=\"color:#FF0000; font-weight: bold;\">NOT FOUND!</span><br />\n");
*/
          // check users online storage file
          if (file_exists("addons/guest.dat"))
            {
              if (is_writable("addons/guest.dat"))
                    print("Users Online file (addons/guest.dat) <span style=\"color:#00FF00; font-weight: bold;\">is writable</span><br />\n");
              else
                    print("Users Online file (addons/guest.dat) is <span style=\"color:#FF0000; font-weight: bold;\">NOT writable</span> (cannot writing tracker's configuration change)<br />\n");
             }
          else
            print("<br />\nUsers Online file (addons/guest.dat) <span style=\"color:#FF0000; font-weight: bold;\">NOT FOUND!</span><br />\n");

          // check censored worlds file
          if (file_exists("badwords.txt"))
            {
            if (is_writable("badwords.txt"))
                  print("Censored worls file (badwords.txt) <span style=\"color:#00FF00; font-weight: bold;\">is writable</span><br />\n");
            else
                  print("Censored worls file (badwords.txt) is <span style=\"color:#FF0000; font-weight: bold;\">NOT writable</span> (cannot writing tracker's configuration change)<br />\n");
             }
          else
            print("<br />\nCensored worls file (badwords.txt) <span style=\"color:#FF0000; font-weight: bold;\">NOT FOUND!</span><br />\n");

          print("<br />\n<table border=\"0\">\n");
          print("<tr><td>Server's OS:</td><td>".php_uname()."</td></tr>");
          print("<tr><td>PHP version:</td><td>".phpversion()."</td></tr>");
          $sqlver=mysql_fetch_row(mysql_query("SELECT VERSION()"));
          print("\n<tr><td>MYSQL version:</td><td>$sqlver[0]</td></tr>");
          $sqlver=mysql_stat();
          $sqlver=explode('  ',$sqlver);
          print("\n<tr><td valign=\"top\" rowspan=\"".(count($sqlver)+1)."\">MYSQL stats  : </td>\n");
          for ($i=0;$i<count($sqlver);$i++)
                print(($i==0?"":"<tr>")."<td>$sqlver[$i]</td></tr>\n");
          print("\n</table><br />\n</div>");
          block_end();
          print("<br />");
          include("blocks/serverload_block.php");
         }
     block_end(); //admincp
     }

stdfoot();
//exit();
?>
