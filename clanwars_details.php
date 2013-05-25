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
if (isset($site)) $_language->read_module('clanwars');

eval ("\$title_clanwars_details = \"".gettemplate("title_clanwars_details")."\";");
echo $title_clanwars_details;

echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars\');return document.MM_returnValue" value="'.$_language->module['show_clanwars'].'" class="button1"/>
<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars&amp;action=stats\');return document.MM_returnValue" value="'.$_language->module['stat'].'" class="button1"/><br /><br />';

if(!isset($_GET['action'])) {
	$cwID = (int)$_GET['cwID'];
	$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."clanwars WHERE cwID='$cwID'"));
	$date=date("d.m.Y", $ds['date']);
	$league='<a href="'.getinput($ds['leaguehp']).'" target="_blank">'.getinput($ds['league']).'</a>';
	
	$hometeam="";
	$oppteam="";
	$screens="";
	$score="";
	$extendedresults="";
	$screenshots="";
	$nbr="";

	// v1.0
	$homescr = 0;
	$oppscr = 0;
	$homescrArray = unserialize($ds['homescore']);
	$oppscrArray = unserialize($ds['oppscore']);
	
	for ($i = 0; $i < count($homescrArray); ++$i) {
		if ($homescrArray[$i] > $oppscrArray[$i]) {
			++$homescr;
		}
		else {
			++$oppscr;
		}
	}
	
	if ($homescr > $oppscr) {
		$homecolor = $wincolor;
		$oppcolor = $loosecolor;
	}
	else if ($homescr < $oppscr) {
		$homecolor = $loosecolor;
		$oppcolor = $wincolor;
	}
	else {
		$homecolor = $oppcolor = $drawcolor;
	}
	
	$results_1='<font color="'.$homecolor.'">'.$homescr.'</font>';
	$results_2='<font color="'.$oppcolor.'">'.$oppscr.'</font>';
	
	$theMaps = unserialize($ds['maps']);
	$theFormats = unserialize($ds['format']);

	if(isclanwaradmin($userID))
	$adminaction='<input type="button" onclick="MM_openBrWindow(\'upload.php?cwID='.$cwID.'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['upload_screenshot'].'" class="button1"/>
  <input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=edit&amp;cwID='.$ds['cwID'].'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['edit'].'" class="button1"/>
  <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_clanwar'].'?\',\'clanwars.php?action=delete&amp;cwID='.$ds['cwID'].'\')" value="'.$_language->module['delete'].'" class="button1"/>';
  else $adminaction='';

	$report=cleartext($ds['report']);
	$report = toggle($report, $ds['cwID']);
	if($report=="") $report="n/a";
	
	$squadname = getsquadname($ds['squad']);
	$oppname = getsquadname($ds['opponent']);
	
	$squad='<a href="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id='.$ds['squad'].'"><b>'.$squadname.'</b></a>';
	$opponent='<a href="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id='.$ds['opponent'].'"><b>'.$oppname.'</b></a>';

  if(!empty($ds['hometeam'])) {
		$array=unserialize($ds['hometeam']);
		$n=1;
		foreach($array as $id) {
			if(!empty($id)) {
				if($n>1) $hometeam.=', <a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
				else $hometeam.='<a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
				$n++;
			}
		}
	}
  if(!empty($ds['oppteam'])) {
		$array=unserialize($ds['oppteam']);
		$n=1;
		foreach($array as $id) {
			if(!empty($id)) {
				if($n>1) $oppteam.=', <a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
				else $oppteam.='<a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
				$n++;
			}
		}
	}
	$screenshots = '';
  if(!empty($ds['screens'])) $screens=explode("|", $ds['screens']);
	if(is_array($screens)) {
		$n=1;
		foreach($screens as $screen) {
			if(!empty($screen)) {
				$screenshots.='<a href="images/clanwar-screens/'.$screen.'" target="_blank"><img src="images/clanwar-screens/'.$screen.'" width="150" height="100" border="0" style="padding-top:3px; padding-right:3px;" alt="" /></a>';
				$n++;
			}
		}
	}

  if(!(mb_strlen(trim($screenshots)))) $screenshots=$_language->module['no_screenshots'];

	$bg1=BG_1;
	$bg2=BG_2;
	$bg3=BG_3;
	$bg4=BG_4;

	$linkpage=cleartext($ds['linkpage']);
	$linkpage=str_replace('http://','',$ds['linkpage']);
	if($linkpage=="") $linkpage="#";

	// -- v1.0, extended results -- //

	//$scoreHome=unserialize($ds['homescore']);
	//$scoreOpp=unserialize($ds['oppscore']);
	//$homescr=array_sum($scoreHome);
	//$oppscr=array_sum($scoreOpp);

	/*if($homescr>$oppscr) {
		$result_map='[color='.$wincolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
		$result_map2='won';
	}
	elseif($homescr<$oppscr) {
		$result_map='[color='.$loosecolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
		$result_map2='lost';
	}
	else {
		$result_map='[color='.$drawcolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
		$result_map2='draw';
	}*/

  if(is_array($theMaps)) {
		$d=0;
		$matchID=1;
		foreach($theMaps as $map) {
			$score='';
      		if(($d+1)%2) { $bgone=BG_1; $bgtwo=BG_2; } else { $bgone=BG_3; $bgtwo=BG_4; }
			
			if ($homescrArray[$d] > $oppscrArray[$d]) {
				$homecolor = $wincolor;
				$oppcolor = $loosecolor;
			}
			else if ($homescrArray[$d] < $oppscrArray[$d]) {
				$homecolor = $loosecolor;
				$oppcolor = $wincolor;
			}
			else {
				$homecolor = $oppcolor = $drawcolor;
			}
			
			$score_1='<font color="'.$homecolor.'">'.$homescrArray[$d].'</font>';
			$score_2='<font color="'.$oppcolor.'">'.$oppscrArray[$d].'</font>';
			$format = $theFormats[$d];
			
      		eval ("\$clanwars_details_results = \"".gettemplate("clanwars_details_results")."\";");
      		$extendedresults.=$clanwars_details_results;
			unset($score);
			$d++;
			$matchID++;
		}
	} else $extendedresults='';

	// -- clanwar output -- //

	eval ("\$clanwars_details = \"".gettemplate("clanwars_details")."\";");
	echo $clanwars_details;

	$comments_allowed = $ds['comments'];
	$parentID = $cwID;
	$type = "cw";
	$referer = "index.php?site=clanwars_details&amp;cwID=$cwID";

	include("comments.php");
}

?>