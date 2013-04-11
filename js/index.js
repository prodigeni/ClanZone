var activeCategory;

function getUrlParameters() // http://jquery-howto.blogspot.sk/2009/09/get-url-parameters-values-with-jquery.html
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}

function checkActiveCategory()
{
	var site = getUrlParameters()['site'];
	if (site == undefined)
		return 1;
	else
	{
		var search = [1, 2, 3, 4];
		search[0] = ['news', 'about', 'articles', 'history', 'contact', 'imprint']; // pages in the first category
		search[1] = ['forum', 'forum_topic', 'guestbook', 'registered_users', 'whoisonline', 'polls', 'server', 'calendar']; // pages in the second category
		search[2] = ['squads', 'members', 'clanwars', 'clanwars_details', 'awards']; // pages in the third category
		search[3] = ['files', 'demos', 'links', 'gallery', 'linkus']; // pages in the fourth category
		
		var result = 1;
		
		for (var i = 1; i <= search.length; i++)
		{
			if ($.inArray(site, search[i - 1]) != -1)
			{
				result = i;
				break;
			}
			else
			{
				continue;
			}
		}
		return result;
	}
}

function showSubnavi(id)
{
	var categoryCount = $('#navigation ul li').length;
	
	// reset
	$('#navigation ul li a').removeClass();
	$('#subnavi ul').hide();
	
	if (id == 'next')
	{
		if (activeCategory == categoryCount)
			showSubnavi(1);
		else
			showSubnavi(activeCategory + 1);
	}
	else if (id == 'prev')
	{
		if (activeCategory == 1)
			showSubnavi(categoryCount);
		else
			showSubnavi(activeCategory - 1);
	}
	else
	{
		$('#cat_id'+id).addClass('active_cat');
		$('#subnavi_'+id).show();
		activeCategory = id;
	}
}

function slideElement(id, duration)
{
	$(id).slideToggle(duration);
}

$(document).ready(function(){
	showSubnavi(checkActiveCategory());
	$('.highlighted_title').css('opacity', '0');
	$('#highlighted .content').hover(
		function()
		{
			$('.highlighted_title').animate({opacity: '0.8'}, {duration: 400, queue: false});
		},
		function()
		{
			$('.highlighted_title').animate({opacity: '0'}, {duration: 400, queue: false});
		}
	);
});