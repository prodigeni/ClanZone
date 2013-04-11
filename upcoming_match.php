<?php
$query = safe_query("SELECT u.*, s1.icon AS homeicon, s2.icon AS oppicon FROM ".PREFIX."upcoming u LEFT JOIN ".PREFIX."squads s1 ON u.squad = s1.squadID LEFT JOIN ".PREFIX."squads s2 ON u.opponent = s2.squadID WHERE type='c' AND date > ".time()." ORDER BY date ASC");
$ds = mysql_fetch_assoc($query);
//var_dump($ds);
if ($ds)
{
$hometeam = getsquadname($ds['squad']);
$oppteam = getsquadname($ds['opponent']);
$icons_dir = "images/squadicons/";
$default_img = "default.png";
?>
<div class="content">
	<div class="clan_icons">
		<img alt="<?php echo $hometeam; ?>" src="<?php if(file_exists($icons_dir.$ds['homeicon']) && $ds['homeicon'] != '') echo $icons_dir.$ds['homeicon']; else echo $icons_dir.$default_img; ?>" class="left" width="80" height="80"/>
		<img alt="<?php echo $oppteam; ?>" src="<?php if(file_exists($icons_dir.$ds['oppicon']) && $ds['oppicon'] != '') echo $icons_dir.$ds['oppicon']; else echo $icons_dir.$default_img; ?>" class="right" width="80" height="80"/>
	</div>
	<div class="clan_names">
		<span class="left"><a href="index.php?site=squads&action=show&squadID=<?php echo $ds['squad']; ?>"><?php echo $hometeam; ?></a></span>
		<span class="right"><a href="index.php?site=squads&action=show&squadID=<?php echo $ds['opponent']; ?>"><?php echo $oppteam; ?></a></span>
	</div>
	<div class="footer"><b>Date:</b> <?php echo date('j. n. Y - G:i', $ds['date']); ?> <a href="index.php?site=calendar<?php echo "&amp;tag=".date("d", $ds['date'])."&amp;month=".date("n", $ds['date'])."&amp;year=".date("Y", $ds['date'])."#event" ?>" class="more">Read more!</a></div>
</div>
<?php
}
else
{
?>
<div class="no_match">There are no upcoming matches at the moment.</div>
<?php
}
?>