<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

if(isset($site)) $_language->read_module('cash_box');

if(isset($_POST['save']) and $_POST['save']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('cash_box');
	if(!iscashadmin($userID)) die($_language->module['no_access']);

	$date=time();
	$paydate=mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

	safe_query("INSERT INTO ".PREFIX."cash_box ( date, paydate, usedfor, info, totalcosts, usercosts, squad, konto ) VALUES ('$date', '$paydate', '".$_POST['usedfor']."', '".$_POST['info']."', '".$_POST['euro']."', '".$_POST['usereuro']."', '".$_POST['squad']."', '".$_POST['konto']."' ) ");
	$id=mysql_insert_id();

	header("Location: index.php?site=cash_box&id=$id");
}
elseif(isset($_POST['saveedit']) and $_POST['saveedit']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('cash_box');
	if(!iscashadmin($userID)) die($_language->module['no_access']);

	$date=time();
	$paydate=mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

	$id = $_POST['id'];

	safe_query("UPDATE ".PREFIX."cash_box SET date='".$date."',
											  paydate='".$paydate."',
											  usedfor='".$_POST['usedfor']."',
											  info='".$_POST['info']."',
											  totalcosts='".$_POST['euro']."',
											  squad='".$_POST['squad']."',
											  konto='".$_POST['konto']."',
											  usercosts='".$_POST['usereuro']."'  WHERE cashID='$id'");

	header("Location: index.php?site=cash_box&id=$id");
}
elseif(isset($_GET['delete']) and $_GET['delete']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('cash_box');
	if(!iscashadmin($userID)) die($_language->module['no_access']);
	$id = $_GET['id'];
	safe_query("DELETE FROM ".PREFIX."cash_box WHERE cashID='$id'");
	safe_query("DELETE FROM ".PREFIX."cash_box_payed WHERE cashID='$id'");

	header("Location: index.php?site=cash_box");
}
elseif(isset($_POST['pay']) and $_POST['pay']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('cash_box');
	if(!iscashadmin($userID)) die($_language->module['no_access']);

	$payid = $_POST['payid'];
	$costs = isset($_POST['costs']);
	$id = $_POST['id'];

	$date=time();
	foreach ( $payid as $usID => $costs ) {
		if($costs!="") {
			if(mysql_num_rows(safe_query("SELECT payedID FROM ".PREFIX."cash_box_payed WHERE userID='$usID' AND cashID='$id'"))) {
				safe_query("UPDATE ".PREFIX."cash_box_payed SET costs='$costs' WHERE userID='$usID' AND cashID='$id'");
			}
			else {
				safe_query("INSERT INTO ".PREFIX."cash_box_payed (cashID, userID, costs, date, payed) VALUES ('$id', '$usID', '$costs', '$date', '1')");
			}
		}
	}

	header("Location: index.php?site=cash_box&id=$id");
}

if(!isclanmember($userID) and !iscashadmin($userID)) echo $_language->module['clanmembers_only'];
else {

	if(isset($_GET['action']) and $_GET['action']=="new") {

		if(!iscashadmin($userID)) die($_language->module['no_access']);
	    echo'<h1>'.$_language->module['cash_box'].'</h1>';

	    $anz=0;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."user ORDER BY nickname");
		while($du=mysql_fetch_array($ergebnis)) {
			if(isclanmember($du['userID'])) $anz++;
		}
	
		if(isset($_GET['euro'])=="") $euro="0.00";
		if(isset($_GET['usereuro'])=="") $usereuro="0.00";
		$squads = '<option value="0">'.$_language->module['each_squad'].'</option>'.getsquads();
	
		eval ("\$cash_box_new = \"".gettemplate("cash_box_new")."\";");
		echo $cash_box_new;
	
	} elseif(isset($_GET['action']) and $_GET['action']=="edit") {

		if(!iscashadmin($userID)) die($_language->module['no_access']);
		
    	echo'<h1>'.$_language->module['cash_box'].'</h1>';

		$id = $_GET['id'];
		$ergebnis=safe_query("SELECT * FROM ".PREFIX."cash_box WHERE cashID='$id'");
		$ds=mysql_fetch_array($ergebnis);

		$day=date("d", $ds['paydate']);
		$month=date("m", $ds['paydate']);
		$year=date("Y", $ds['paydate']);
		
		$info=getinput($ds['info']);
		$usage=getinput($ds['usedfor']);
		$bank_account=getinput($ds['konto']);

		$anz=0;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."user ORDER BY nickname");
		
    	while($du=mysql_fetch_array($ergebnis)) {
			if(iscashadmin($du['userID'])) $anz++;
		}
		$squads = '<option value="0">'.$_language->module['each_squad'].'</option>'.getsquads();
		$squads=str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);

		eval ("\$cash_box_edit = \"".gettemplate("cash_box_edit")."\";");
		echo $cash_box_edit;

	} else {

		echo'<h1>'.$_language->module['cash_box'].'</h1>';

		function print_cashbox($squadID,$id) {
			global $_language;
			$_language->read_module('cash_box');

			$bg1=BG_1;
			$bg2=BG_2;
			$pagebg=PAGEBG;
			$border=BORDER;
			$bghead=BGHEAD;
			$bgcat=BGCAT;

			global $wincolor;
			global $loosecolor;
			global $drawcolor;
			global $userID;

			if($id) {
				$squadergebnis=safe_query("SELECT squad FROM ".PREFIX."cash_box WHERE cashID='".$id."'");
				$dv=mysql_fetch_array($squadergebnis);
				$squadID = $dv['squad'];
			}

			$costs_squad = '';
			if($squadID == 0) $usersquad = $_language->module['clan'];
			else {
				$ergebnis_squad = safe_query("SELECT * FROM ".PREFIX."cash_box_payed, ".PREFIX."cash_box WHERE ".PREFIX."cash_box_payed.payed='1' AND ".PREFIX."cash_box_payed.cashID=".PREFIX."cash_box.cashID AND ".PREFIX."cash_box.squad = '".$squadID."'");
				$anz_squad = mysql_num_rows($ergebnis_squad);
				$costs_squad=0.00;
				if($anz_squad) {
					while($dss=mysql_fetch_array($ergebnis_squad)) {
						$costs_squad+=$dss['costs'];
					}
				}
				$ergebnis_squad = safe_query("SELECT * FROM ".PREFIX."cash_box WHERE squad='$squadID'");
				$anz_squad = mysql_num_rows($ergebnis_squad);
				if($anz_squad) {
					while($dss=mysql_fetch_array($ergebnis_squad)) {
						$costs_squad-=$dss['totalcosts'];
					}
				}

				$costs_squad = ' ('.$costs_squad.' euro)';
				$usersquad = $_language->module['squad'].": ".getsquadname($squadID);
			}
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."cash_box WHERE squad='".$squadID."' ORDER BY paydate DESC LIMIT 0,1");

			echo'<br /><br /><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top" width="180"><b>'.$usersquad.$costs_squad.'</b></td>
        </tr>
        <tr>
          <td height="1" bgcolor="'.BG_1.'" width="100%" colspan="4"></td>
        </tr>
        <tr><td height="15"></td></tr>';

			echo '<tr>
							<td valign="top" width="180">';

			if(mysql_num_rows($ergebnis)) {
				$ds=mysql_fetch_array($ergebnis);
				if(!$id) $id = $ds['cashID'];

				$ergebnis=safe_query("SELECT * FROM ".PREFIX."cash_box WHERE cashID='$id'");
				$ds=mysql_fetch_array($ergebnis);
				$date=date("d.m.Y", $ds['date']);
				$paydate=date("d.m.Y", $ds['paydate']);

				$bezahlen = safe_query("SELECT * FROM ".PREFIX."cash_box_payed WHERE cashID='$id' AND payed='1' ");
				$payed = mysql_num_rows($bezahlen);
				$konto = cleartext($ds['konto']);

				$usage=$ds['usedfor'];


				if(iscashadmin($userID)) $adminaction='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=cash_box&amp;action=edit&amp;id='.$id.'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'cash_box.php?delete=true&amp;id='.$id.'\')" value="'.$_language->module['delete'].'" />';

				eval ("\$cash_box_usage = \"".gettemplate("cash_box_usage")."\";");
				echo $cash_box_usage;

				$all=safe_query("SELECT * FROM ".PREFIX."cash_box WHERE squad='".$squadID."' ORDER BY paydate DESC");
				echo'<br /><br />';
				while($ds=mysql_fetch_array($all)) {
					echo'&#8226; <a href="index.php?site=cash_box&amp;id='.$ds['cashID'].'&amp;squad='.$squadID.'"><b>'.$ds['usedfor'].'</b></a><br />';
				}

				echo'</td><td width="10">&nbsp;</td>
					<td valign="top">';

				$members = array();
				$ergebnis = safe_query("SELECT * FROM ".PREFIX."user ORDER BY nickname");
				while($du=mysql_fetch_array($ergebnis)) {
					if($squadID == 0) {
						if(isclanmember($du['userID'],$squadID)) $members[]=$du['userID'];
					}
					else if(issquadmember($du['userID'],$squadID)) $members[]=$du['userID'];
				}
				
		        eval ("\$cash_box_head = \"".gettemplate("cash_box_head")."\";");
				echo $cash_box_head;
        
				if(count($members)) {
					foreach($members as $usID) {
						$ergebnis = safe_query("SELECT * FROM ".PREFIX."cash_box_payed WHERE userID='$usID' AND cashID='$id'");
						$du=mysql_fetch_array($ergebnis);
						$user='<a href="index.php?site=profile&amp;id='.$usID.'"><b>'.getnickname($usID).'</b></a>';
						if($du['payed']) {
							$paydate=date("d.m.Y", $du['date']);
							$payed='<font color="'.$wincolor.'">'.$_language->module['paid'].': '.$paydate.'</font>';
						}
						else {
							$payed='<font color="'.$loosecolor.'">'.$_language->module['not_paid'].'</font>';
						}

						if(iscashadmin($userID)) {
							if($du['costs']) {
								$bg=BG_1;
								$costs=$du['costs'];
							}
							else {
								$costs="";
								$bg=BG_2;
							}
							$payment='<input type="text" size="7" name="payid['.$usID.']" value="'.$costs.'" dir="rtl" /> &#8364;';
						}
						else {
							if($du['costs']) {
								$costs='<font color="'.$wincolor.'"><b>'.$du['costs'].' &#8364;</b></font>';
								$bg=BG_1;
							}
							else {
								$costs='<font color="'.$loosecolor.'">0.00 &#8364;</font>';
								$bg=BG_2;
							}
							$payment=$costs;
						}

						eval ("\$cash_box_content = \"".gettemplate("cash_box_content")."\";");
						echo $cash_box_content;
					}
				}

				if(iscashadmin($userID)) $admin='<input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="pay" value="'.$_language->module['update'].'" />';
				eval ("\$cash_box_foot = \"".gettemplate("cash_box_foot")."\";");
				echo $cash_box_foot;

			}
			else echo $_language->module['no_entries'];
			
      echo'</td></tr></table>';
		}

		$ergebnis = safe_query("SELECT * FROM ".PREFIX."cash_box_payed WHERE payed='1'");
		$anz = mysql_num_rows($ergebnis);
		$costs=0.00;
		if($anz) {
			while($ds=mysql_fetch_array($ergebnis)) {
				$costs+=$ds['costs'];
			}
		}

		$ergebnis = safe_query("SELECT * FROM ".PREFIX."cash_box ");
		$anz = mysql_num_rows($ergebnis);
		if($anz) {
			while($ds=mysql_fetch_array($ergebnis)) {
				$costs-=$ds['totalcosts'];
			}
		}

		if($costs<0) $fontcolor=$loosecolor;
		else $fontcolor=$wincolor;

		$bg1=BG_1;
		$bg2=BG_2;

		if(iscashadmin($userID)) $cashadmin='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=cash_box&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['add_payment'].'" />';


		eval ("\$cash_box_top = \"".gettemplate("cash_box_top")."\";");
		echo $cash_box_top;

		if(!isset($_GET['id'])) {
			print_cashbox(0,0);
			if(iscashadmin($userID)) {
				$squadergebnis=safe_query("SELECT squadID FROM ".PREFIX."squads");
			}
			else {
				$squadergebnis=safe_query("SELECT squadID FROM ".PREFIX."squads_members WHERE userID='".$userID."'");
			}
			while($da=mysql_fetch_array($squadergebnis)) {
				print_cashbox($da['squadID'],0);
			}
		}
		else {
			$id = $_GET['id'];
			if(isset($_GET['squad'])) $get_squad = $_GET['squad'];
			else $get_squad = 0;
			if($get_squad == 0) {
				print_cashbox(0, $id);
			}
			else {
				print_cashbox(0, 0);
			}
			if(iscashadmin($userID)) {
				$squadergebnis=safe_query("SELECT squadID FROM ".PREFIX."squads");
			}
			else {
				$squadergebnis=safe_query("SELECT squadID FROM ".PREFIX."squads_members WHERE userID='".$userID."'");
			}
			while($da=mysql_fetch_array($squadergebnis)) {
				if($get_squad == $da['squadID']) {
					print_cashbox($da['squadID'], $id);
				}
				else {
					print_cashbox($da['squadID'], 0);
				}
			}
		}
	}
}

?>