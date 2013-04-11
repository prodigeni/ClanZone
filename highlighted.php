<?php
$query = safe_query("SELECT * FROM ".PREFIX."highlighted ORDER BY highID"); ?>
<div class="content">
	<div id="highlighted_slider">
		<ul>
<?php
$num = 0;

while ($ds = mysql_fetch_assoc($query))
{
	//var_dump($ds);
	$default_path = "images/highlighted-pics/";
	$default_img = "default.png";
	
	echo "<li><img src=\"".$default_path;
	if (!empty($ds['screens']))
	{
		if (file_exists($default_path.$ds['screens']))
			echo $ds['screens'];
	}
	else
		echo $default_img;
	
	echo "\" width=\"330\" height=\"170\" alt=\"Image number ".$ds['highID']."\"/><div id=\"highlighted_title".$num."\" class=\"highlighted_title\"><a href=\"".$ds['link']."\">".$ds['title']."</a></div></li>\n";
	$num++;
}
//echo $num;
?>
		</ul>
	</div>
<?php
if (isanyadmin($userID))
{ ?>
	<a href="index.php?site=highlighted_edit" id="highlighted_slider_admin">Edit</a>
<?php
} 
if ($num > 1) { ?>
	<ul id="highlighted_slider_nav" class="highlighted_slider_nav">
<?php
for ($i = 0; $i < $num; $i++)
{
	echo "<li onclick=\"highlighted_slideshow.pos(".$i.")\"></li>\n";
}
?>
	</ul>
<div class="highlighted_sliderbutton" id="highlighted_slideleft" onclick="highlighted_slideshow.move(-1)"></div>
<div class="highlighted_sliderbutton" id="highlighted_slideright" onclick="highlighted_slideshow.move(1)"></div>
<?php } ?>
</div>
<script type="text/javascript">
var highlighted_slideshow = new TINY.slider.slide('highlighted_slideshow',{
	id: 'highlighted_slider',
	auto: 4,
	resume: false,
	vertical: false,
	navid: 'highlighted_slider_nav',
	activeclass: 'current',
	position: 0,
	rewind: false,
	//elastic: true,
	left: 'highlighted_slideleft',
	right: 'highlighted_slideright'
});
</script>