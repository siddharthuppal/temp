<?php
global $CURUSER;

  if (isset($CURUSER) && $CURUSER && $CURUSER["uid"]>1)
  {
?>
<table class="lista" cellpadding="2" cellspacing="0" width="100%">
<tr>
<?php
$style=style_list();
$langue=language_list();
$resuser=mysql_query("SELECT * FROM users WHERE id=".$CURUSER["uid"]);
$rowuser=mysql_fetch_array($resuser);
//print("<td class=lista align=center>".WELCOME_BACK." ".$CURUSER['username']." (<a href=logout.php>".LOGOUT."</a>)</td>\n");
print("<td class=lista align=center>".USER_LEVEL.": ".$CURUSER["level"]."</td>\n");
print("<td class=green align=center>&#8593&nbsp;".makesize($rowuser['uploaded']));
print("</td><td class=red align=center>&#8595&nbsp;".makesize($rowuser['downloaded']));
print("</td><td class=lista align=center>(SR ".($rowuser['downloaded']>0?number_format($rowuser['uploaded']/$rowuser['downloaded'],2):"---").")</td>\n");
if ($CURUSER["admin_access"]=="yes")
   print("\n<td align=center class=lista><a href=admincp.php?user=".$CURUSER["uid"]."&code=".$CURUSER["random"].">".MNU_ADMINCP."</a></td>\n");

print("<td class=lista align=center><a href=usercp.php?uid=".$CURUSER["uid"].">".USER_CP."</a></td>\n");

$resmail=mysql_query("SELECT COUNT(*) FROM messages WHERE readed='no' AND receiver=$CURUSER[uid]");
if ($resmail && mysql_num_rows($resmail)>0)
   {
    $mail=mysql_fetch_row($resmail);
    if ($mail[0]>0)
       print("<td class=lista align=center><a href=usercp.php?uid=".$CURUSER["uid"]."&do=pm&action=list>".MAILBOX."</a> (<font color=\"#FF0000\"><b>$mail[0]</b></font>)</td>\n");
    else
        print("<td class=lista align=center><a href=usercp.php?uid=".$CURUSER["uid"]."&do=pm&action=list>".MAILBOX."</a></td>\n");
   }
else
    print("<td class=lista align=center><a href=usercp.php?uid=".$CURUSER["uid"]."&do=pm&action=list>".MAILBOX."</a></td>\n");

print("\n<form name=jump1><td class=lista><select name=\"style\" size=\"1\" onChange=\"location=document.jump1.style.options[document.jump1.style.selectedIndex].value\" style=\"font-size:10px\">");
foreach($style as $a)
               {
               print("<option ");
               if ($a["id"]==$CURUSER["style"])
                  print("selected=selected");
               print(" value=account_change.php?style=".$a["id"]."&returnto=".urlencode($_SERVER['REQUEST_URI']).">".$a["style"]."</option>");
               }
print("</select></td>");

print("\n<td class=lista><select name=\"langue\" size=\"1\" onChange=\"location=document.jump1.langue.options[document.jump1.langue.selectedIndex].value\" style=\"font-size:10px\">>");
foreach($langue as $a)
               {
               print("<option ");
               if ($a["id"]==$CURUSER["language"])
                  print("selected=selected");
               print(" value=account_change.php?langue=".$a["id"]."&returnto=".urlencode($_SERVER['REQUEST_URI']).">".$a["language"]."</option>");
               }
print("</select></td></form>");
//print("<td class=lista align=center>".USER_LASTACCESS.": ".date("d/m/Y H:i:s",$CURUSER["lastconnect"])."</td>\n");
?>
</tr>
</table>
<?php
}
else
    {
    if (!isset($user)) $user = '';
    ?>
    <form action="login.php" name="login" method="post">
    <table class="lista" border="0" width="100%" cellpadding="2" cellspacing="0">
    <tr>
    <td class="lista" align="left">
      <table border="0" cellpadding="2" cellspacing="0">
      <tr>
      <td align="right" class="lista"><?php echo USER_NAME?>:</td>
      <td class="lista"><input type="text" size="15" name="uid" value="<?php $user ?>" maxlength="40" style="font-size:10px" /></td>
      <td align="right" class="lista"><?php echo USER_PWD?>:</td>
      <td class="lista"><input type="password" size="15" name="pwd" maxlength="40" style="font-size:10px" /></td>
      <td class="lista" align="center"><input type="submit" value="<?php echo FRM_LOGIN?>" style="font-size:10px" /></td>
      </tr>
      </table>
    </td>
    <td class="lista" align="center"><a href="account.php"><?php echo ACCOUNT_CREATE?></a></td>
    <td class="lista" align="center"><a href="recover.php"><?php echo RECOVER_PWD?></a></td>
    </tr>
    </table>
    </form>
    <?php
}
?>
