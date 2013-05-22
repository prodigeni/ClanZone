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
#   Copyright 2005-2006 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org    						 						 #
#									 									 #
#       ++++++++++++++++++++++++++++++++++++++++++++++++++               #
#  		+						 						 +				 #
#   	+	   Guestbook Addon by b4z!c		 			 +		 		 #
#       +   						 					 +		 		 #
#		+						 					 	 +		 		 #
#   	+    visit www.bazic.net and www.die-rache.net	 +		 		 #
#		+						 						 +		 		 #
#       ++++++++++++++++++++++++++++++++++++++++++++++++++  		 	 #
 ########################################################################
*/
$_language->read_module('guestbook');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['guestbook'].' &raquo; '.$_language->module['guestbook_check'].'</h1>';

  	$abfrage = "SELECT gbID, date, name, ip, comment FROM ".PREFIX."guestbook WHERE active='0'";
  	$ergebnis = mysql_query($abfrage);
  	$anzahl = mysql_num_rows($ergebnis);

	if ($anzahl>0)
	{
	while($row = mysql_fetch_array($ergebnis))
	{
	$date = date("d.m.Y - H:i", $row['date']);
	echo '<center>
		<form method="post" name="form" action="admincenter.php?site=guestbook">
			<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
  				<tr>
    		  	  <td width="35%" bgcolor="#FFFFFF" align="left">
    		  	  <b>'.$_language->module['name'].':</b> '.$row['name'].'<br />
			  	  <b>'.$_language->module['date'].':</b> '.$date.'<br />
			  	  <b>'.$_language->module['ip'].':</b> '.$row['ip'].'
    			  </td>
    			  <td bgcolor="#FFFFFF" align="left">'.$row['comment'].'</td>
    			  <td width="5%" bgcolor="#FFFFFF"><input type="checkbox" name="id[]" value="'.$row['gbID'].'" /></td>
  				</tr>
			</table><br />';
			}
			echo 	'<table width="90%" border="0" cellspacing="0" cellpadding="0">
  						<tr>
                          <td align="right">
						  	<input name="activate" type="submit" value="'.$_language->module['check'].'" /> 
					      	<input name="delete" type="submit" value="'.$_language->module['del'].'" />
						  </td>
  						</tr>
					</table>';
					
			echo	'<br /><br />';
			
			echo	'<table width="90%" border="0" cellspacing="0" cellpadding="0">
  						<tr>
    					  <td align="right">
						  	<input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'
    						<select name="quickactiontype">
      						<option value="check">'.$_language->module['check_all'].'</option>
      						<option value="delete">'.$_language->module['del_all'].'</option>
    						</select>
    						<input type="submit" name="quickaction" value="'.$_language->module['done'].'" />
						  </td>
  						</tr>
					</table></center></form>';
			}
  			else
  			{
   		echo $_language->module['no_entries'];
	}
	
if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if (isset($_POST['activate'])) {
	if(isset($_POST['id'])){
		foreach($_POST['id'] as $id) {
  	$abfrage = "UPDATE ".PREFIX."guestbook SET active='1' WHERE gbID='$id'";
  	$ergebnis = mysql_query($abfrage);	
		}
	}

  	echo $_language->module['check_suc'];
	echo '<meta http-equiv="refresh" content="1; url=admincenter.php?site=guestbook" />';
	}

elseif (isset($_POST['delete'])) {
	if(isset($_POST['id'])){
		foreach($_POST['id'] as $id) {
			$abfrage = "DELETE FROM ".PREFIX."guestbook WHERE gbID='$id'";
			$ergebnis = mysql_query($abfrage);
		}
	}

   	echo $_language->module['del_suc'];
	echo '<meta http-equiv="refresh" content="1; url=admincenter.php?site=guestbook" />';
	}
	
elseif(isset($_POST['quickaction'])) 
	{
	$quickactiontype = $_POST['quickactiontype'];
	$id = $_POST['id'];
		if($quickactiontype=="check") {
				$abfrage = "UPDATE ".PREFIX."guestbook SET active='1' WHERE active='0'";
				$ergebnis = mysql_query($abfrage);
				
				echo $_language->module['check_suc_all'];
				echo '<meta http-equiv="refresh" content="1; url=admincenter.php?site=guestbook" />';
		}
		elseif($quickactiontype=="delete") {
				$abfrage = "DELETE FROM ".PREFIX."guestbook WHERE active='0'";
				$ergebnis = mysql_query($abfrage);
				
				echo $_language->module['del_suc_all'];
				echo '<meta http-equiv="refresh" content="1; url=admincenter.php?site=guestbook" />';
		}
}
?>