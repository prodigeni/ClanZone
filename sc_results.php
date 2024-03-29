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

$ergebnis=safe_query("SELECT c.cwID, c.date, c.homescore, c.oppscore, s1.name AS homename, s2.name AS oppname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s1 ON c.squad = s1.squadID LEFT JOIN ".PREFIX."squads s2 ON c.opponent = s2.squadID ORDER BY date DESC LIMIT 0, ".$maxresults);
if(mysql_num_rows($ergebnis)){
	echo'<ul class="menu">';
	$n=1;
	while($ds=mysql_fetch_assoc($ergebnis)) {
		//var_dump($ds);
		$date=date("d.m.Y", $ds['date']);
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
		
		if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$result='<font color="'.$homecolor.'">'.$homescr.'</font>:<font color="'.$oppcolor.'">'.$oppscr.'</font>';

		$resultID=$ds['cwID'];
		$gameicon="images/games/";
		if(file_exists($gameicon.$ds['game'].".gif")) $gameicon = $gameicon.$ds['game'].".gif";

		eval ("\$results = \"".gettemplate("results")."\";");
		echo $results;
		$n++;
	}
	echo'</ul>';
}
?>
