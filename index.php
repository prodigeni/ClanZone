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

// important data include
include("_mysql.php");
include("_settings.php");
include("_functions.php");

$_language->read_module('index');
$index_language = $_language->module;
// end important data include
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="description" content="Teeworlds ClanZone - The battle starts here!"/>
<meta name="author" content="Alucard, Limit"/>
<meta name="keywords" content="teeworlds, clans, clanzone, vanilla, ctf, tdm, dm"/>
<meta name="copyright" content="Copyright &copy; 2012 by Alucard"/>
<meta name="generator" content="webSPELL"/>
<title><?php echo PAGETITLE; ?></title>
<link href="_stylesheet.css" rel="stylesheet" type="text/css"/>
<?php if ($_GET['site'] == 'forum' || $_GET['site'] == 'forum_topic') echo "<link href=\"_forum.css\" rel=\"stylesheet\" type=\"text/css\"/>\n"; ?>
<script src="js/bbcode.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/jquery-1.8.3.min.js"><\/script>')</script>
<script src="js/tinyslider.js" type="text/javascript"></script>
<script src="js/index.js" type="text/javascript"></script>
</head>
<body>
<div id="wrap">
	<div id="header">
		<div id="banner"><a href="index.php"><img src="images/banner.png" width="400" height="100"/></a></div>
		<?php include "login.php"; ?>
	</div>
	<div id="page">
		<div id="left_column">
			<div id="navigation">
				<div id="nav_arrows">
					<div id="nav_arrow_up" class="nav_arrow" onclick="showSubnavi('prev')"></div>
					<div id="nav_arrow_down" class="nav_arrow" onclick="showSubnavi('next')"></div>
				</div>
				<ul>
					<li id="nav_item1"><a id="cat_id1" href="javascript:showSubnavi(1)">Info area</a></li>
					<li id="nav_item2"><a id="cat_id2" href="javascript:showSubnavi(2)">Community area</a></li>
					<li id="nav_item3"><a id="cat_id3" href="javascript:showSubnavi(3)">Clan area</a></li>
					<li id="nav_item4"><a id="cat_id4" href="javascript:showSubnavi(4)">Media area</a></li>
				</ul>
			</div>
			<?php
			if ($_GET['site'] != 'forum' && $_GET['site'] != 'forum_topic')
			{
			?>
			<div class="block">
				<h2 id="under_nav">Last news</h2>
				<div class="content">
					<?php include "sc_headlines.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Last topics</h2>
				<div class="content">
					<?php include "latesttopics.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Shoutbox</h2>
				<div class="content">
					<?php include "shoutbox.php"; ?>
				</div>
			</div>
			<?php
			}
			?>
		</div>
		<div id="subnavi">
			<?php include "navigation.php"; ?>
		</div>
		<div id="announcements">
			<div id="highlighted">
				<h2>Highlighted</h2>
				<?php include "highlighted.php"; ?>
			</div>
			<div id="upcoming_match">
				<h2>Upcoming Match</h2>
				<?php include "upcoming_match.php"; ?>
				<!--<div class="content"><img src="http://evil-twc.ucoz.com/banner/page_banner.png" width="330" height="170"/></div>-->
			</div>
		</div>
		<div id="middle_column">
			<div class="block">
				<div class="article_container">
					<?php
					if (!isset($site))
						$site="news";
					$invalide = array('\\','/','//',':','.');
					$site = str_replace($invalide,' ',$site);
					if (!file_exists($site.".php"))
						$site = "news";
					
					include $site.".php";
					?>
				</div>
			</div>
		</div>
		<?php
		if ($_GET['site'] != 'forum' && $_GET['site'] != 'forum_topic')
		{
		?>
		<div id="right_column">
			<div class="block" style="padding-top:5px;">
				<h2>Latest wars</h2>
				<div class="content">
					<?php include "sc_results.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Quick search</h2>
				<div class="content">
					<?php include "quicksearch.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Sponsors</h2>
				<div class="content center">
					<?php include "sc_sponsors.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Partner</h2>
				<div class="content center">
					<?php include "partners.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Pic of the moment</h2>
				<div class="content center">
					<?php include "sc_potm.php"; ?>
				</div>
			</div>
			<div class="block">
				<h2>Page statistics</h2>
				<div class="content">
					<?php include "counter.php"; ?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<div class="clear_both"></div>
	</div>
	<div id="footer">Copyright &copy; <b><?php echo $myclanname ?></b> team&nbsp; | &nbsp;This site is powered by <a href="http://www.webspell.org" target="_blank"><b>webSPELL.org</b></a></div>
</div>
</body>
</html>