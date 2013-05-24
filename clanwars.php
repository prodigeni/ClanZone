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

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";
if($action=="new") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if(isset($_GET['upID'])) $upID = $_GET['upID'];

	if(isclanwaradmin($userID)) {
		$squads=getgamesquads();
		$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);

		$games="";
    $hometeam="";
    
    $gamesa=safe_query("SELECT * FROM ".PREFIX."games ORDER BY name");
		while($ds=mysql_fetch_array($gamesa)) {
			$games.='<option value="'.$ds['tag'].'">'.$ds['name'].'</option>';
		}

		$gamesquads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
		while($ds=mysql_fetch_array($gamesquads)) {
			$hometeam.='<option value="0">'.$ds['name'].'</option>';
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='$ds[squadID]' ORDER BY sort");
			while($dm=mysql_fetch_array($squadmembers)) {
				$hometeam.='<option value="'.$dm['userID'].'">&nbsp; - '.getnickname($dm['userID']).'</option>';
			}
			$hometeam.='<option value="0" disabled="disabled">-----</option>';
		}

		$day='';
		$month='';
		$year='';
    
    for($i=1; $i<32; $i++) {
			if($i==date("d", time())) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", time())) $month.='<option value="'.$i.'" selected="selected">'.date("M", time()).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2025; $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}

		$leaguehp="http://";
		$opphp="http://";
		$linkpage="http://";
		$server = "";
		$league = "";
		$opponent = "";
		$opptag = "";
		if(isset($upID)) {
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."upcoming WHERE upID='$upID'");
			$ds=mysql_fetch_array($ergebnis);
			$league=$ds['league'];
			if($ds['leaguehp'] != $leaguehp) $leaguehp=$ds['leaguehp'];
			$opponent=$ds['opponent'];
			$opptag=$ds['opptag'];
			if($ds['opphp'] != $opphp) $opphp=$ds['opphp'];
			$countries=str_replace(" selected=\"selected\"", "", $countries);
			$countries=str_replace('value="'.$ds['oppcountry'].'"', 'value="'.$ds['oppcountry'].'" selected="selected"', $countries);

			$squads=str_replace(" selected=\"selected\"", "", $squads);
			$squads=str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);
			$server = $ds['server'];
			$day=str_replace(" selected=\"selected\"", "", $day);
			$day=str_replace('<option>'.date("j", $ds['date']).'</option>', '<option selected="selected">'.date("j", $ds['date']).'</option>', $day);
			$month=str_replace(" selected=\"selected\"", "", $month);
			$month=str_replace('value="'.date("n", $ds['date']).'"', 'value="'.date("n", $ds['date']).'" selected="selected"', $month);
			$year=str_replace(" selected=\"selected\"", "", $year);
			$year=str_replace('value="'.date("Y", $ds['date']).'"', 'value="'.date("Y", $ds['date']).'" selected="selected"', $year);
		}

		$bg1=BG_1;
		eval ("\$clanwar_new = \"".gettemplate("clanwar_new")."\";");
		echo $clanwar_new;
	}
	else redirect('index.php?site=clanwars', 'no access!');
}
elseif($action=="save") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	if(isset($_POST['hometeam'])) $hometeam = $_POST['hometeam'];
	if(isset($_POST['squad'])) $squad = $_POST['squad'];
	else $squad = '';
	$game = $_POST['game'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$oppcountry = $_POST['oppcountry'];
	$opphp = $_POST['opphp'];
	$oppteam = $_POST['oppteam'];
	$server = $_POST['server'];
	$hltv = $_POST['hltv'];
	$report = $_POST['message'];
	$comments = $_POST['comments'];
	$linkpage = $_POST['linkpage'];
	if(isset($_POST['news'])) $news=$_POST['news'];
  

	// v1.0 -- EXTENDED CLANWAR RESULTS
	if(isset($_POST['map_name'])) $maplist = $_POST['map_name'];
	if(isset($_POST['map_result_home'])) $homescr = $_POST['map_result_home'];
	if(isset($_POST['map_result_opp'])) $oppscr = $_POST['map_result_opp'];
	if(isset($_POST['map_format'])) $format = $_POST['map_format'];

	$maps = array();
	if(!empty($maplist)) {
		if(is_array($maplist)) {
			foreach($maplist as $map) {
				$maps[]=stripslashes($map);
			}
		}
	}
	$backup_theMaps = serialize($maps);
	if(function_exists("mysql_real_escape_string")) {
		$theMaps = mysql_real_escape_string($backup_theMaps);
	}
	else{
		$theMaps = addslashes($backup_theMaps);
	}
	$scores = array();
	if(!empty($homescr)) {
		if(is_array($homescr)) {
			foreach($homescr as $result) {
				$scores[]=$result;
			}
		}
	}
	$theHomeScore = serialize($scores);
	
	$results = array();
	if(!empty($oppscr)) {
		if(is_array($oppscr)) {
			foreach($oppscr as $result) {
				$results[]=$result;
			}
		}
	}
	$theOppScore = serialize($results);
	
	$formats = array();
	if (!empty($format)) {
		if (is_array($format)) {
			foreach ($format as $result) {
				$formats[] = $result;
			}
		}
	}
	$format = serialize($formats);
	
	$team=array();
	if(is_array($hometeam)) {
		foreach($hometeam as $player) {
			if(!in_array($player, $team)) $team[]=$player;
		}
	}
	$home_string = serialize($team);
	
	$opp_team=array();
	if(is_array($oppteam)) {
		foreach($oppteam as $player) {
			if(!in_array($player, $opp_team)) $opp_team[]=$player;
		}
	}
	$opp_string = serialize($opp_team);

	$date=mktime(0,0,0,$month,$day,$year);

	safe_query("INSERT INTO ".PREFIX."clanwars ( date, squad, game, league, leaguehp, opponent, opptag, oppcountry, opphp, maps, format, hometeam, oppteam, server, hltv, homescore, oppscore, report, comments, linkpage)
                 VALUES( '$date', '$squad', '$game', '".$league."', '$leaguehp', '".$opponent."', '".$opptag."', '$oppcountry', '$opphp', '".$theMaps."', '".$format."', '$home_string', '$opp_string', '$server', '$hltv', '$theHomeScore', '$theOppScore', '".$report."', '$comments', '$linkpage' ) ");

	$cwID=mysql_insert_id();
	$date=date("d.m.Y", $date);

	// INSERT CW-NEWS
	if(isset($news)) {
	 	$_language->read_module('news',true);
	 	$_language->read_module('bbcode', true);
	 	
		safe_query("INSERT INTO ".PREFIX."news (date, poster, saved, cwID) VALUES ('".time()."', '$userID', '0', '$cwID')");
		$newsID=mysql_insert_id();
		
		$rubrics = '';
		$newsrubrics=safe_query("SELECT rubricID, rubric FROM ".PREFIX."news_rubrics ORDER BY rubric");
		while($dr=mysql_fetch_array($newsrubrics)) {
			$rubrics.='<option value="'.$dr['rubricID'].'">'.$dr['rubric'].'</option>';
		}

		$count_langs = 0;
		$lang=safe_query("SELECT lang, language FROM ".PREFIX."news_languages ORDER BY language");
		$langs='';
		while($dl=mysql_fetch_array($lang)) {
			$langs.="news_languages[".$count_langs."] = new Array();\nnews_languages[".$count_langs."][0] = '".$dl['lang']."';\nnews_languages[".$count_langs."][1] = '".$dl['language']."';\n";
			$count_langs++;
		}

		$squad=getsquadname($squad);
		$opponent=getsquadname($opponent);
		//$link1=$opptag;
		//$url1=$opphp;
		$link2=$league;
		$url2=$leaguehp;
		$url3="http://";
		$url4="http://";
		$link3="";
		$link4="";
		$window1_new = 'checked="checked"';
		$window1_self = '';
		$window2_new = 'checked="checked"';
		$window2_self = '';
		$window3_new = 'checked="checked"';
		$window3_self = '';
		$window4_new = 'checked="checked"';
		$window4_self = '';

		// v1.0 -- PREPARE CW-NEWS OUTPUT
		$maps = unserialize($backup_theMaps);
		$scoreHome = unserialize($theHomeScore);
		$scoreOpp = unserialize($theOppScore);
		$homescr=0;
		$oppscr=0;
		$homescrArray=unserialize($ds['homescore']);
		$oppscrArray=unserialize($ds['oppscore']);
		for ($i = 0; $i < count($homescrArray); ++$i) {
			if ($homescrArray[$i] > $oppscrArray[$i]) {
				++$homescr;
			}
			else {
				++$oppscr;
			}
		}

		/*if($homescr>$oppscr) {
			$results='[color='.$wincolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='won';
		}
		elseif($homescr<$oppscr) {
			$results='[color='.$loosecolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='lost';
		}
		else {
			$results='[color='.$drawcolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='draw';
		}*/
		
		$results='[b]'.$homescr.':'.$oppscr.'[/b]';
		
		$headline1=stripslashes($squad).' vs. '.stripslashes($opponent);
		//if($url1!='http://' AND !(empty($url1))) $opponent='[url='.$opphp.'][b]'.$opptag.' / '.$opponent.'[/b][/url]';
		//else $opponent='[b]'.$opptag.' / '.$opponent.'[/b]';
		if($url2!='http://' AND !(empty($url2))) $league='[url='.$leaguehp.']'.$league.'[/url]';
		// v1.0 -- CREATE CW-NEWS EXTENDED RESULTS
		if(is_array($maps)) {
			$d=0;
			$results_ext='[TOGGLE=Results (extended)]';
			foreach($maps as $maptmp) {
				$map=stripslashes($maptmp);
			 	$score = "";
				if($scoreHome[$d] > $scoreOpp[$d]) $score.='<td>[color='.$wincolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$loosecolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				elseif($scoreHome[$d] < $scoreOpp[$d]) $score.='<td>[color='.$loosecolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$wincolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				else $score.='<td>[color='.$drawcolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$drawcolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				$d++;
				eval ("\$news_cw_results = \"".gettemplate("news_cw_results")."\";");
				$results_ext.=$news_cw_results;
				unset($score);
			}
			$results_ext.='[/TOGGLE]';
		}

		if(!empty($report)) {
			$more1='[TOGGLE=Report]'.getforminput($report).'[/TOGGLE]';
		}
		$home = "";
		if(is_array($team)) {
			$n=1;
			foreach($team as $id) {
				if(!empty($id)) {
					if($n>1) $home.=', <a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					else $home='<a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					$n++;
				}
			}
		}
		$away = "";
		if(is_array($opp_team)) {
			$n=1;
			foreach($opp_team as $id) {
				if(!empty($id)) {
					if($n>1) $away.=', <a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					else $away='<a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					$n++;
				}
			}
		}
    
  	$_languagepagedefault = new Language;
    $_languagepagedefault->set_language($rss_default_language);
    $_languagepagedefault->read_module('clanwars');
		$message='[b]'.stripslashes($squad).'[/b] vs [b]'.stripslashes($opponent).'[/b] '.$_language->module['on'].' '.$date.'
		
'.$_language->module['league'].': '.stripslashes($league).'
'.$_language->module['result'].': '.$results.'
'.$results_ext.'
'.stripslashes($squad).' '.$_language->module['team'].': '.stripslashes($home).'
'.stripslashes($opponent).' '.$_language->module['team'].': '.stripslashes($away).'

'.$more1.'
<a href="index.php?site=clanwars_details&#38;&#97;&#109;&#112;&#59;cwID='.$cwID.'">'.$_languagepagedefault->module['clanwar_details'].'</a>';
		$i = 0;
		$message_vars = "message[".$i."] = '".js_replace($message)."';\n";
		$headline_vars = "headline[".$i."] = '".js_replace(htmlspecialchars($headline1))."';\n";
		$langs_vars = "langs[".$i."] = '$default_language';\n";
		$langcount = 1;
		$selects = "";
		for($i = 1; $i <= $count_langs; $i++) {
			if($i == $langcount) $selects .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			else $selects .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$intern = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
		$topnews = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
		
		$rubrics='';
		$newsrubrics=safe_query("SELECT rubricID, rubric FROM ".PREFIX."news_rubrics ORDER BY rubric");
		while($dr=mysql_fetch_array($newsrubrics)) {
			$rubrics.='<option value="'.$dr['rubricID'].'">'.$dr['rubric'].'</option>';
		}
		$bg1=BG_1;
		
		$comments='<option value="0">'.$_language->module['no_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2" selected="selected">'.$_language->module['visitor_comments'].'</option>';
		
		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
		eval ("\$addflags = \"".gettemplate("flags")."\";");
		$_language->read_module('news');
		eval ("\$news_post = \"".gettemplate("news_post")."\";");
		echo $news_post;

	}
	else echo'<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
  <link href="_stylesheet.css" rel="stylesheet" type="text/css">
  <center><br /><br /><br /><br />
  <b>'.$_language->module['clanwar_saved'].'.</b><br /><br />
  <input type="button" onclick="MM_openBrWindow(\'upload.php?cwID='.$cwID.'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['upload_screenshot'].'" class="button1"/>
  <input type="button" onclick="javascript:self.close()" value="'.$_language->module['close_window'].'" /></center>';
}
elseif($action=="edit") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');
	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$cwID = $_GET['cwID'];

	if(isclanwaradmin($userID)) {
		$squads=getgamesquads();
		$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);

		//$games="";
    $maps="";
    $hometeam="";
	$oppteam="";
    $day='';
		$month='';
		$year='';
    
    $ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."clanwars WHERE cwID='$cwID'"));

		/*$gamesa=safe_query("SELECT tag, name FROM ".PREFIX."games ORDER BY name");
		while($dv=mysql_fetch_array($gamesa)) {
			$games.='<option value="'.$dv['tag'].'">'.$dv['name'].'</option>';
		}*/

		
    
    for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['date'])) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['date'])) $month.='<option value="'.$i.'" selected="selected">'.date("M", $ds['date']).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2025; $i++) {
			if($i==date("Y", $ds['date'])) $year.='<option selected="selected">'.$i.'</option>';
			else $year.='<option>'.$i.'</option>';
		}
		//$games=str_replace('value="'.$ds['game'].'"', 'value="'.$ds['game'].'" selected="selected"', $games);
		$squads=getgamesquads();
		$squads=str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);
		$opp_squads = getgamesquads();
		$opp_squads = str_replace('value="'.$ds['opponent'].'"', 'value="'.$ds['opponent'].'" selected="selected"', $opp_squads);
		$league=htmlspecialchars($ds['league']);
		$leaguehp=htmlspecialchars($ds['leaguehp']);
		//$opponent=htmlspecialchars($ds['opponent']);
		$opptag=htmlspecialchars($ds['opptag']);
		$countries=str_replace('value="at" selected="selected"', 'value="at"', $countries);
		$countries=str_replace('value="'.$ds['oppcountry'].'"', 'value="'.$ds['oppcountry'].'" selected="selected"', $countries);
		$opphp=htmlspecialchars($ds['opphp']);
		//$oppteam=htmlspecialchars($ds['oppteam']);
		$server=htmlspecialchars($ds['server']);
		$hltv=htmlspecialchars($ds['hltv']);
		$linkpage=htmlspecialchars($ds['linkpage']);
		$report=htmlspecialchars($ds['report']);
		$linkpage=htmlspecialchars($ds['linkpage']);

		// map-output, v1.0
		$map = unserialize($ds['maps']);
		$theHomeScore = unserialize($ds['homescore']);
		$theOppScore = unserialize($ds['oppscore']);
		$format = unserialize($ds['format']);
		$i=0;
		for($i=0; $i<count($map); $i++) {
			
      $maps.='
      <tr>
        <td width="15%"><input type="hidden" name="map_id[]" value="'.$i.'" />match #'.($i+1).'</td>
				<td width="35%"><input type="text" name="map_name[]" value="'.getinput($map[$i]).'" size="35" /></td>
				<td width="15%"><input type="text" name="map_result_home[]" value="'.$theHomeScore[$i].'" size="3" /></td>
				<td width="15%"><input type="text" name="map_result_opp[]" value="'.$theOppScore[$i].'" size="3" /></td>
				<td width="10%"><input type="text" name="map_format[]" value="'.$format[$i].'" size="3"</td>
				<td width="10%"><input type="checkbox" name="delete['.$i.']" value="1" /> '.$_language->module['delete'].'</td>
			</tr>';
		}

		$gamesquads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
		while($dq=mysql_fetch_array($gamesquads)) {
			$hometeam.='<option value="0">'.$dq['name'].'</option>';
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='$dq[squadID]' ORDER BY sort");
			while($dm=mysql_fetch_array($squadmembers)) {
				$hometeam.='<option value="'.$dm['userID'].'">&nbsp; - '.getnickname($dm['userID']).'</option>';
			}
			$hometeam.='<option value="0">&nbsp;</option>';
		}
		
		if(!empty($ds['hometeam'])) {
			$array = unserialize($ds['hometeam']);
			foreach($array as $id) {
				if(!empty($id)) $hometeam=str_replace('value="'.$id.'"', 'value="'.$id.'" selected="selected"', $hometeam);
			}
		}
		
		$gamesquads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
		while($dq=mysql_fetch_array($gamesquads)) {
			$oppteam.='<option value="0">'.$dq['name'].'</option>';
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='$dq[squadID]' ORDER BY sort");
			while($dm=mysql_fetch_array($squadmembers)) {
				$oppteam.='<option value="'.$dm['userID'].'">&nbsp; - '.getnickname($dm['userID']).'</option>';
			}
			$oppteam.='<option value="0">&nbsp;</option>';
		}
		
		if(!empty($ds['oppteam'])) {
			$array = unserialize($ds['oppteam']);
			foreach($array as $id) {
				if(!empty($id)) $oppteam=str_replace('value="'.$id.'"', 'value="'.$id.'" selected="selected"', $oppteam);
			}
		}

		$comments='<option value="0">'.$_language->module['disable_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2">'.$_language->module['visitor_comments'].'</option>';
		$comments=str_replace('value="'.$ds['comments'].'"', 'value="'.$ds['comments'].'" selected="selected"', $comments);

		$bg1=BG_1;
		eval ("\$clanwar_edit = \"".gettemplate("clanwar_edit")."\";");
		echo $clanwar_edit;
	}
	else redirect('index.php?site=clanwars', $_language->module['no_access']);
}
elseif($action=="saveedit") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$cwID = $_POST['cwID'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	if(isset($_POST['hometeam'])) $hometeam = $_POST['hometeam'];
	else $hometeam = array();
	$squad = $_POST['squad'];
	$game = $_POST['game'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$oppcountry = $_POST['oppcountry'];
	$opphp = $_POST['opphp'];
	$oppteam = $_POST['oppteam'];
	$server = $_POST['server'];
	$hltv = $_POST['hltv'];
	$report = $_POST['message'];
	$comments = $_POST['comments'];
	$linkpage = $_POST['linkpage'];
	$maplist = $_POST['map_name'];
	$homescr = $_POST['map_result_home'];
	$oppscr = $_POST['map_result_opp'];
	$format = $_POST['map_format'];
	if(isset($_POST['delete'])) $delete = $_POST['delete'];
	else $delete = array();
	
	// v1.0 -- MAP-REMOVAL
	$theMaps = array();
	$theHomeScore = array();
	$theOppScore = array();
	$theFormat = array();
	
	if(is_array($maplist)){
		foreach($maplist as $key=>$map) {
			if(!isset($delete[$key])) {
				$theMaps[]=stripslashes($map);
				$theHomeScore[]=$homescr[$key];
				$theOppScore[]=$oppscr[$key];
				$theFormat[]=$format[$key];
			}
		}
	}
	if(function_exists("mysql_real_escape_string")) {
		$theMaps = mysql_real_escape_string(serialize($theMaps));
	}
	else{
		$theMaps = addslashes(serialize($theMaps));
	}
	$theHomeScore = serialize($theHomeScore);
	$theOppScore = serialize($theOppScore);
	$theFormat = serialize($theFormat);

	echo'<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
  <link href="_stylesheet.css" rel="stylesheet" type="text/css">';

	$date=mktime(0,0,0,$month,$day,$year);
	$team=array();
	if(is_array($hometeam)) {
		foreach($hometeam as $player) {
			if(!in_array($player, $team)) $team[]=$player;
		}
	}
	$home_string = serialize($team);
	
	$team=array();
	if(is_array($oppteam)) {
		foreach($oppteam as $player) {
			if(!in_array($player, $team)) $team[]=$player;
		}
	}
	$opp_string = serialize($team);

	safe_query("UPDATE ".PREFIX."clanwars SET date='$date',
                 squad='$squad',
								 game='$game',
								 league='".$league."',
								 leaguehp='$leaguehp',
								 opponent='".$opponent."',
								 opptag='".$opptag."',
								 oppcountry='$oppcountry',
								 opphp='$opphp',
								 maps='".$theMaps."',
								 format='".$theFormat."',
								 hometeam='".$home_string."',
								 oppteam='".$opp_string."',
								 server='$server',
								 hltv='$hltv',
								 homescore='$theHomeScore',
								 oppscore='$theOppScore',
								 report='".$report."',
								 comments='$comments',
                 linkpage='$linkpage' WHERE cwID='$cwID'");

	echo'<center><br /><br /><br /><br />
  <b>'.$_language->module['clanwar_updated'].'</b><br /><br />
  <input type="button" onclick="MM_openBrWindow(\'upload.php?cwID='.$cwID.'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['upload_screenshot'].'" />
  <input type="button" onclick="javascript:self.close()" value="'.$_language->module['close_window'].'" /></center>';
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);
	if(isset($_POST['cwID'])) $cwID = $_POST['cwID'];
	if(!isset($cwID)) $cwID = $_GET['cwID'];
	$ergebnis=safe_query("SELECT screens FROM ".PREFIX."clanwars WHERE cwID='$cwID'");
	$ds=mysql_fetch_array($ergebnis);
	$screens=explode("|", $ds['screens']);
	$filepath = "./images/clanwar-screens/";
	if(is_array($screens)) {
		foreach($screens as $screen) {
			if(!empty($screen)) {
				if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
			}
		}
	}
	safe_query("DELETE FROM ".PREFIX."clanwars WHERE cwID='$cwID'");
	header("Location: index.php?site=clanwars");
}
elseif(isset($_POST['quickactiontype'])=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die('no access!');
	if(isset($_POST['cwID'])){
		$cwID = $_POST['cwID'];
		foreach($cwID as $id) {
			$ergebnis=safe_query("SELECT screens FROM ".PREFIX."clanwars WHERE cwID='$id'");
			$ds=mysql_fetch_array($ergebnis);
			$screens=explode("|", $ds['screens']);
			$filepath = "./images/clanwar-screens/";
			if(is_array($screens)) {
				foreach($screens as $screen) {
					if(!empty($screen)) {
						if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
					}
				}
			}
	
			safe_query("DELETE FROM ".PREFIX."clanwars WHERE cwID='$id'");
			safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='$id' AND type='cw'");
		}
	}
	header("Location: index.php?site=clanwars");
}
elseif($action=="stats") {
	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;

	echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars\');return document.MM_returnValue" value="'.$_language->module['show_clanwars'].'" class="button1"/><br/>';

	$bg1=BG_1;
	$bg2=BG_2;
	
	$query = safe_query("SELECT squadID FROM ".PREFIX."squads WHERE dead = 0");
	while ($ds = mysql_fetch_assoc($query))
	{
		$squadname = getsquadname($ds['squadID']);
		
		$wonpoints = 0;
		$lostpoints = 0;
		$woncws = 0;
		$lostcws = 0;
		$drawcws = 0;
		$totalcws = 0;
		
		$pointsquery = safe_query("SELECT homescore, oppscore FROM ".PREFIX."clanwars WHERE squad = ".$ds['squadID']);
		while ($dp = mysql_fetch_assoc($pointsquery))
		{
			$squadscore = 0;
			$opponentscore = 0;
			$squadscoreArray = unserialize($dp['homescore']);
			$opponentscoreArray = unserialize($dp['oppscore']);
			for ($i = 0; $i < count($squadscoreArray); ++$i) {
				if ($squadscoreArray[$i] > $opponentscoreArray[$i]) {
					++$squadscore;
				}
				else {
					++$opponentscore;
				}
			}
			
			$wonpoints += $squadscore;
			$lostpoints += $opponentscore;
			
			if ($squadscore > $opponentscore) $woncws++;
			else if ($squadscore < $opponentscore) $lostcws++;
			else $drawcws++;
		}
		$pointsquery = safe_query("SELECT homescore, oppscore FROM ".PREFIX."clanwars WHERE opponent = ".$ds['squadID']);
		while ($dp = mysql_fetch_assoc($pointsquery))
		{
			$squadscore = 0;
			$opponentscore = 0;
			$squadscoreArray = unserialize($dp['oppscore']);
			$opponentscoreArray = unserialize($dp['homescore']);
			for ($i = 0; $i < count($squadscoreArray); ++$i) {
				if ($squadscoreArray[$i] > $opponentscoreArray[$i]) {
					++$squadscore;
				}
				else {
					++$opponentscore;
				}
			}
			
			$wonpoints += $squadscore;
			$lostpoints += $opponentscore;
			
			if ($squadscore > $opponentscore) $woncws++;
			else if ($squadscore < $opponentscore) $lostcws++;
			else $drawcws++;
		}
		
		$totalcws = $woncws + $lostcws + $drawcws;
		$totalpoints = $wonpoints + $lostpoints;
		
		$wonpointspercent = percent($wonpoints, $totalpoints, 2);
		$lostpointspercent = percent($lostpoints, $totalpoints, 2);
		$woncwspercent = percent($woncws, $totalcws, 2);
		$lostcwspercent = percent($lostcws, $totalcws, 2);
		$drawcwspercent = percent($drawcws, $totalcws, 2);
		
		//if($totalwonperc) $totalwon=$totalwonperc.'%<br /><img src="images/icons/won.gif" width="30" height="'.round($totalwonperc, 0).'" border="1" alt="'.$_language->module['won'].'" />';
		
		eval ("\$clanwars_stats = \"".gettemplate("clanwars_stats")."\";");
		echo $clanwars_stats;
	}
}
elseif($action=="showonly") {
	if(isset($_GET['cwID'])) $cwID = (int)$_GET['cwID'];
	if(isset($_GET['id'])){
		if(is_numeric($_GET['id']) || (is_gametag($_GET['id']))) $id = $_GET['id'];
	}
	$only2 = 'opponent';
	$only = 'squad';
	if(isset($_GET['only'])){
		if(($_GET['only']=="squad") || ($_GET['only']=="game")) $only = $_GET['only'];
	}
	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='game') || ($_GET['sort']=='squad') || ($_GET['sort']=='opponent') || ($_GET['sort']=='league')) $sort=$_GET['sort'];
	}
	
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	
	$squads=getgamesquads();
	
  $jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
	$jumpmenu='<select name="selectgame" onchange="MM_jumpMenu(\'parent\',this,0)">			   <option value="index.php?site=clanwars">- '.$_language->module['show_all_squads'].' -</option>'.$jumpsquads.'</select> <input type="button" name="Button1" value="'.$_language->module['go'].'" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" class="button1"/>';		

	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;
  
	$gesamt = mysql_num_rows(safe_query("SELECT cwID FROM ".PREFIX."clanwars WHERE $only='$id' OR $only2='$id'"));
	$pages=1;
	
	$max=$maxclanwars;
	$pages = ceil($gesamt/$max);

  if($pages>1) $page_link = makepagelink("index.php?site=clanwars&amp;action=showonly&amp;id=$id&amp;sort=$sort&amp;type=$type&amp;only=$only", $page, $pages);
  else $page_link = "";

	if ($page == "1") {
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent WHERE $only=$id OR $only2=$id ORDER BY $sort $type LIMIT 0,$max");
		//echo "SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent WHERE $only=$id OR $only2=$id ORDER BY $sort $type LIMIT 0,$max";
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent WHERE $only=$id OR $only2=$id ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}


	/*if($page == "1") {
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent ORDER BY c.$sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}*/



	if($type=="ASC")
	$seiten='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC&amp;only='.$only.'">'.$_language->module['sort'].':</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';
	else
	$seiten='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC&amp;only='.$only.'">'.$_language->module['sort'].':</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';

	if(isclanwaradmin($userID)) $admin='<input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=new\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['new_clanwar'].'" class="button1"/>';
  else $admin='';
	$Statistics='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars&amp;action=stats\');return document.MM_returnValue" value="'.$_language->module['stat'].'" class="button1"/>';

	echo'<form name="jump" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td>'.$admin.' '.$Statistics.'</td>
      <td align="right">'.$jumpmenu.'</td>
    </tr>
    <tr>
      <td>'.$seiten.'</td>
      <td></td>
    </tr>
  </table>
  </form>';

	if($gesamt) {
		$headdate='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'">'.$_language->module['date'].':</a>';
		$headgame='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'">'.$_language->module['game'].':</a>';
		$headsquad='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=squad&amp;type='.$type.'">'.$_language->module['squad'].':</a>';
		$headopponent='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=opponent&amp;type='.$type.'">'.$_language->module['opponent'].':</a>';
		$headcountry='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=oppcountry&amp;type='.$type.'">'.$_language->module['country'].':</a>';
		$headleague='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=league&amp;type='.$type.'">'.$_language->module['league'].':</a>';

		eval ("\$clanwars_head = \"".gettemplate("clanwars_head")."\";");
		echo $clanwars_head;
		$n=1;
	
		while($ds=mysql_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$date=date("d.m.y", $ds['date']);
			$league='<a href="'.$ds['leaguehp'].'" target="_blank">'.$ds['league'].'</a>';
			$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
			$country=flags($oppcountry);
			$maps=$ds['maps'];
			$hometeam=$ds['hometeam'];
			$oppteam=$ds['oppteam'];
			$server=$ds['server'];

			$squad='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['squad'].'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'&amp;only=squad"><b>'.$ds['squadname'].'</b></a>';
			$opponent='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['opponent'].'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'&amp;only=squad"><b>'.$ds['oppname'].'</b></a>';
			//$opponent='<a href="'.$ds['opphp'].'" target="_blank"><b>'.$ds['opptag'].'</b></a>';
			//if(file_exists('images/games/'.$ds['game'].'.gif')) $pic = $ds['game'].'.gif';
			//$game='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['game'].'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=game"><img src="images/games/'.$pic.'" width="13" height="13" border="0" alt="" /></a>';

			$homescr=0;
			$oppscr=0;
			$homescrArray=unserialize($ds['homescore']);
			$oppscrArray=unserialize($ds['oppscore']);
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
			
			$results='<font color="'.$homecolor.'">'.$homescr.'</font>:<font color="'.$oppcolor.'">'.$oppscr.'</font>';

			if(getanzcwcomments($ds['cwID'])) $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/newhotfolder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.getanzcwcomments($ds['cwID']).')</a>';
			else $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/folder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.getanzcwcomments($ds['cwID']).')</a>';

			$multiple='';
			$admdel='';
			if(isclanwaradmin($userID)) $multiple='<input class="input" type="checkbox" name="cwID[]" value="'.$ds['cwID'].'" />';

			eval ("\$clanwars_content = \"".gettemplate("clanwars_content")."\";");
			echo $clanwars_content;
			unset($result);
			$n++;
		}
    if(isclanwaradmin($userID)) $admdel='<table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td><input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'</td>
        <td align="right"><select name="quickactiontype">
        <option value="delete">'.$_language->module['delete_selected'].'</option>
        </select>
        <input type="submit" name="quickaction" value="'.$_language->module['go'].'" class="button1"/></td>
      </tr>
    </table>';

		eval ("\$clanwars_foot = \"".gettemplate("clanwars_foot")."\";");
		echo $clanwars_foot;
	}
	else echo $_language->module['no_entries'];
}
elseif(empty($_GET['action'])) {
	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='game') || ($_GET['sort']=='squad') || ($_GET['sort']=='opponent') || ($_GET['sort']=='league')) $sort=$_GET['sort'];
	}
	
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	$squads=getgamesquads();
	$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
	$jumpmenu='<select name="selectgame" onchange="MM_jumpMenu(\'parent\',this,0)"><option value="index.php?site=clanwars">- '.$_language->module['show_all_squads'].' -</option>'.$jumpsquads.'</select> <input type="button" name="Button1" value="'.$_language->module['go'].'" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" class="button1"/>';		

	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;

	$gesamt = mysql_num_rows(safe_query("SELECT cwID FROM ".PREFIX."clanwars"));
	$pages=1;
	if(!isset($page)) $page = 1;
	if(!isset($sort)) $sort = "date";
	if(!isset($type)) $type = "DESC";

	$max=$maxclanwars;
	$pages = ceil($gesamt/$max);

	if($pages>1) $page_link = makepagelink("index.php?site=clanwars&amp;sort=$sort&amp;type=$type", $page, $pages);
  else $page_link = "";

	if($page == "1") {
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent ORDER BY c.$sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad LEFT JOIN ".PREFIX."squads s2 ON s2.squadID = c.opponent ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}

  if($type=="ASC") $seiten='<a href="index.php?site=clanwars&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC">'.$_language->module['sort'].':</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';
	else $seiten='<a href="index.php?site=clanwars&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC">'.$_language->module['sort'].':</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';

  if(isclanwaradmin($userID)) $admin='<input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=new\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['new_clanwar'].'" class="button1"/>';
  else $admin='';
	$statistics='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars&amp;action=stats\');return document.MM_returnValue" value="'.$_language->module['stat'].'" class="button1"/>';

	echo'<form name="jump" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td>'.$admin.' '.$statistics.'</td>
      <td align="right">'.$jumpmenu.'</td>
    </tr>
    <tr>
      <td>'.$seiten.'</td>
      <td></td>
    </tr>
  </table>
  </form>';

	if($gesamt) {
		$headdate='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'">'.$_language->module['date'].':</a>';
		$headgame='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=games&amp;type='.$type.'">'.$_language->module['game'].':</a>';
		$headsquad='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=squad&amp;type='.$type.'">'.$_language->module['squad'].':</a>';
		$headopponent='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=opponent&amp;type='.$type.'">'.$_language->module['opponent'].':</a>';
		//$headcountry='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=oppcountry&amp;type='.$type.'">'.$_language->module['country'].':</a>';
		$headleague='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=league&amp;type='.$type.'">'.$_language->module['league'].':</a>';

		eval ("\$clanwars_head = \"".gettemplate("clanwars_head")."\";");
		echo $clanwars_head;

		$n=1;
		while($ds=mysql_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$date=date("d.m.y", $ds['date']);
			$squad='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['squad'].'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'&amp;only=squad"><b>'.$ds['squadname'].'</b></a>';
			$league='<a href="'.getinput($ds['leaguehp']).'" target="_blank">'.$ds['league'].'</a>';
			$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
			$country=flags($oppcountry);
			$opponent='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['opponent'].'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'&amp;only=squad"><b>'.$ds['oppname'].'</b></a>';
			$hometeam=$ds['hometeam'];
			$oppteam=$ds['oppteam'];
			$server=$ds['server'];
			if(file_exists('images/games/'.$ds['game'].'.gif')) $pic = $ds['game'].'.gif';
			//$game='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['game'].'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=game"><img src="images/games/'.$pic.'" width="13" height="13" border="0" alt="" /></a>';

			$homescr=0;
			$oppscr=0;
			$homescrArray=unserialize($ds['homescore']);
			$oppscrArray=unserialize($ds['oppscore']);
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
			
			$results='<font color="'.$homecolor.'">'.$homescr.'</font>:<font color="'.$oppcolor.'">'.$oppscr.'</font>';

			if($anzcomments = getanzcwcomments($ds['cwID'])) $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/newhotfolder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.$anzcomments.')</a>';
			else $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/folder.gif" alt="'.$_language->module['details'].'" border="0" /> (0)</a>';

			$multiple='';
			$admdel='';
			if(isclanwaradmin($userID)) $multiple='<input class="input" type="checkbox" name="cwID[]" value="'.$ds['cwID'].'" />';

			eval ("\$clanwars_content = \"".gettemplate("clanwars_content")."\";");
			echo $clanwars_content;
			unset($result,$anzcomments);
			$n++;
		}
    if(isclanwaradmin($userID)) $admdel='<table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td><input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'</td>
        <td align="right"><select name="quickactiontype">
        <option value="delete">'.$_language->module['delete_selected'].'</option>
        </select>
        <input type="submit" name="quickaction" value="'.$_language->module['go'].'" class="button1"/></td>
      </tr>
    </table>';

		eval ("\$clanwars_foot = \"".gettemplate("clanwars_foot")."\";");
		echo $clanwars_foot;
	}
	else echo $_language->module['no_entries'];
}
?>