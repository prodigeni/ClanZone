<?php
/*
 ########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2006 by webspell.org / webspell.info                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENCE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org / webspell.info   #
#                                                                        #
#   visit webspell.org / webspell.info                                   #
#                                                                        #
#                             					         				 #
#                                                                        #
 ########################################################################
*/
$_language->read_module('badword');

$action = "";
if(isset($_GET['action'])) $action = $_GET['action'];
$delstring = "";
if(isset($_GET['delstring'])) $delstring = $_GET['delstring'];

if($action=="save") {
	safe_query("INSERT INTO ".PREFIX."badword (badword) values( '".$_POST['badword']."') ");
	
	$n=0;
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."comments WHERE comment LIKE '%".$_POST['badword']."%'");
	while($ds=mysql_fetch_array($ergebnis)) {
		$n++;
		if($n == 1) echo '<div style="background:red; padding:10px; color:white;">'.$_language->module['already_spam'].' <i>'.$_POST['badword'].'</i></div>';
		echo '<p style="border:1px solid #000; padding:10px; margin:0 0 10px 0;">'.$ds['comment'].'<br/>';
		if($ds['userID']==0) echo 'by Guest ('.$ds['nickname'].')';
		else echo 'by '.getnickname($ds['userID']);
		echo '</p>';
	}
	if($n > 0)echo '<a href="admincenter.php?site=badword&delstring='.$_POST['badword'].'&action=delete">'.$_language->module['delete_all'].' ('.$n.')!</a>';

}
elseif($action == "delete" && $delstring != "") {
	safe_query("DELETE FROM ".PREFIX."comments WHERE comment LIKE '%".$delstring."%'");
	echo '<div style="padding:10px 0;"><img src="../images/icons/adminicons/delete.gif" alt="" /> '.$_language->module['comments_containing'].' "<i>'.$delstring.'</i>" '.$_language->module['deleted'].'!</div><hr />';
	
}
elseif($action=="saveedit") {
	safe_query("UPDATE ".PREFIX."badword SET badword='".$_POST['badword']."' WHERE badwordID='".$_POST['badwordID']."'");
	echo '<div style="padding:10px 0;"><img src="../images/icons/adminicons/edit.gif" alt="" /> '.$_language->module['changed_to'].' <i>'.$_POST['badword'].'</i><br />
  <a href="admincenter.php?site=badword&delstring='.$_POST['badword'].'">'.$_language->module['search_for'].' '.$_POST['badword'].'?</a></div><hr />';

}
elseif($action=="delete" && $delstring == "") {
  $badwordID = $_GET['badwordID'];
	safe_query("DELETE FROM ".PREFIX."badword WHERE badwordID='$badwordID'");
	echo '<div style="padding:10px 0;"><img src="../images/icons/adminicons/delete.gif" alt="" /> '.$_language->module['badword'].' '.$_language->module['deleted'].'!</div><hr />';
}

if($action=="add") {
	echo'<h1>&curren; <a href="admincenter.php?site=badword" class="white">'.$_language->module['badwords'].'</a> &raquo; '.$_language->module['addbadword'].'</h1>';
    echo'<form method="post" action="admincenter.php?site=badword&action=save" enctype="multipart/form-data">
	     <table cellpadding="5" cellspacing="0">
		 <tr>
		   <td>'.$_language->module['badword'].':</td>
		   <td><input type="text" name="badword" size="27" class="form_off" onFocus="this.className=\'form_on\'" onBlur="this.className=\'form_off\'"></td>
		 </tr>
		 <tr>
		   <td>&nbsp;</td>
		   <td><input type="submit" name="save" value="'.$_language->module['addbadword'].'"></td>
		 </tr>
		 </table>
		 </form>';
}
elseif($action=="edit") {
  echo'<h1>&curren; <a href="admincenter.php?site=badword" class="white">'.$_language->module['badwords'].'</a> &raquo; '.$_language->module['editbadword'].'</h1>';
  
  $badwordID = $_GET['badwordID'];
  $ergebnis=safe_query("SELECT * FROM ".PREFIX."badword WHERE badwordID='$badwordID'");
	$ds=mysql_fetch_array($ergebnis);
	
	echo'<form method="post" action="admincenter.php?site=badword&action=saveedit" enctype="multipart/form-data">
	     <table cellpadding="5" cellspacing="0">
		 <tr>
		   <td>'.$_language->module['badword'].':</td>
		   <td><input type="text" name="badword" size="32" value="'.$ds['badword'].'" class="form_off" onFocus="this.className=\'form_on\'" onBlur="this.className=\'form_off\'"></td>
		 </tr>
		 <tr>
		   <td><input type="hidden" name="badwordID" value="'.$ds['badwordID'].'"></td>
		   <td><input type="submit" name="saveedit" value="'.$_language->module['editbadword'].'"></td>
		 </tr>
		 </table>
		 </form>';
}

elseif($action=="" && $delstring != "") {
	echo'<h1>&curren; <a href="admincenter.php?site=badword" class="white">'.$_language->module['badwords'].'</a> &raquo; '.$_language->module['just_del_spam'].'</h1>';
	$n=0;
	$ergebnis = safe_query("SELECT comment,commentID FROM ".PREFIX."comments WHERE comment LIKE '%".$_GET['delstring']."%'");
		while($ds=mysql_fetch_array($ergebnis)) {
		echo '<p style="float:left;">'.$ds['comment'].'</p><div style="padding:0 0 10px 0; float:right;"><img src="../images/icons/adminicons/delete.gif" alt="" /> <a href="admincenter.php?site=badword&delstring='.$ds['commentID'].'&action=delbyid">'.$_language->module['delete'].'!</a></div><hr style="clear:both;" />';
		$n++;
	}
	if($n > 0)echo '<a href="admincenter.php?site=badword&delstring='.$delstring.'&action=delete">'.$_language->module['delete_all'].' ('.$n.')!</a>';
	else echo '<div style="padding:10px 0;"> '.$delstring.'</div><hr />'.$_language->module['search_for'].' <input name="delstr" type="text" value="" id="delstring"> <button onClick="newsearch()">Search</button>';
}

elseif($action=="delbyid" && $delstring != "") {
	echo'<h1>&curren; <a href="admincenter.php?site=badword" class="white">'.$_language->module['badwords'].'</a> &raquo; '.$_language->module['deleted'].'</h1>';
	safe_query("DELETE FROM ".PREFIX."comments WHERE commentID='$delstring'");
	echo '<div style="padding:10px 0;"><img src="../images/icons/adminicons/delete.gif" alt="" /> Spam '.$_language->module['deleted'].'!</div><hr />';
}

else {
	echo'<h1>&curren; '.$_language->module['badwords'].'</h1>';
	echo'('.$_language->module['desc_badword'].')<br />
<input type="button" class="button" onClick="MM_goToURL(\'parent\',\'admincenter.php?site=badword&action=add\');return document.MM_returnValue" value="'.$_language->module['addbadword'].'"><br><br>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."badword ORDER BY badword");
	echo'<table width="60%" cellpadding="5" cellspacing="0" border="1">
   		<tr bgcolor="#CCCCCC">
   		<td width="80%" class="title" align="center">'.$_language->module['badwords'].':</td>
   		<td width="20%" class="title" align="center">'.$_language->module['actions'].':</td>
   		</tr>
		<tr bgcolor="#FFFFFF"><td colspan="4"></td></tr>';
		
	while($ds=mysql_fetch_array($ergebnis)) {
    	echo'<tr bgcolor="#FFFFFF" valign="top">
	       		<td>'.$ds['badword'].'</td>
		   	<td align="center"><img src="../images/icons/adminicons/edit.gif" alt="'.$_language->module['edit'].'" width="16" height="16" onClick="MM_goToURL(\'parent\',\'admincenter.php?site=badword&action=edit&badwordID='.$ds['badwordID'].'\');return document.MM_returnValue"> <img src="../images/icons/adminicons/delete.gif" alt="'.$_language->module['delete'].'" width="16" height="16" onClick="MM_confirm(\''.$_language->module['deletebadword'].'\', \'admincenter.php?site=badword&action=delete&badwordID='.$ds['badwordID'].'\')"></td>
		 	</tr>';
	}
	echo'</table>';
		echo'<br><table border="0" cellpadding="0" cellspacing="0">
<tr height="17">
 <td width="20px" align="center"><img src="../images/icons/adminicons/edit.gif" border="0" alt="'.$_language->module['edit'].'"></td>
 <td>'.$_language->module['edit'].'</td>
 <td width="25">
 <td width="20px" align="center"><img src="../images/icons/adminicons/delete.gif" border="0" alt="'.$_language->module['delete'].'"></td>
 <td>'.$_language->module['delete'].'</td>
</tr>
</table>';

echo'<h1 style="margin-top:10px;">&curren; '.$_language->module['just_del_spam'].'</h1>
('.$_language->module['desc_just_del'].')
<p>'.$_language->module['search_for'].' <input name="delstr" type="text" value="" id="delstring"> <button onClick="newsearch()">Search</button></p>';
}	
?>
<script language="javascript" type="text/javascript">
function newsearch() {
	var delstring = document.getElementById('delstring').value;
	window.location = "admincenter.php?site=badword&delstring="+delstring;
}
</script>