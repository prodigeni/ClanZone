<?php
if (isanyadmin($userID))
{
	if ($_GET['action'] == 'add')
	{
	?>
		<h1>Add a new entry</h1>
		
		<form action="index.php?site=highlighted_edit&amp;action=add" method="post">
			<table bgcolor="#fff" width="100%">
				<tr><td>Title:</td><td><input type="text" name="title" value="" maxlength="40"/></td></tr>
				<tr><td>Link:</td><td><input type="text" name="link" value="" maxlength="100"/></td></tr>
				<tr><td></td><td><input type="submit" name="submit" value="Submit"/></td></tr>
			</table>
		</form>
	<?php
		if (isset($_POST['submit']))
		{
			/*********************************\
			|                                 |
			|     TODO: make this safer!      |
			|                                 |
			\*********************************/
			
			$title = $_POST['title'];
			$link = $_POST['link'];
			
			safe_query("INSERT INTO ".PREFIX."highlighted (highID, title, link, screens) VALUES (NULL, '".$title."', '".$link."', '')");
			
			$highID = mysql_insert_id();
			?>
			<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit&action=image&id=<?php echo $highID; ?>"</script>
			<?php
			//header("Location: index.php?site=highlighted_edit&action=image&id=".$highID);
		}
	}
	else if ($_GET['action'] == 'image' && is_numeric($_GET['id']))
	{
	?>
		<h1>Image</h1>
	<?php
		$query = safe_query("SELECT * FROM ".PREFIX."highlighted WHERE highID = ".$_GET['id']);
		$ds = mysql_fetch_assoc($query);
		$upload_dir = "images/highlighted-pics/";
		if (!empty($ds['screens']) && file_exists($upload_dir.$ds['screens']))
		{
		?>
			<p>This entry already has an image:</p>
			<img src="<?php echo $upload_dir.$ds['screens']; ?>" width="330" height="170"/>
			<p>If you want to upload a new one, you have to delete the old image first.</p>
			<p>Do you want to do it?</p>
			
			<form action="index.php?site=highlighted_edit&amp;action=image&amp;id=<?php echo $_GET['id']; ?>" method="post">
				<input type="submit" name="yes" value="Yes" style="width: 100px"/>
				<input type="submit" name="no" value="No" style="width: 100px"/>
			</form>
			<?php
			if ($_POST['yes'] == 'Yes')
			{
				safe_query("UPDATE ".PREFIX."highlighted SET screens='' WHERE highID=".$ds['highID']);
				unlink($upload_dir.$ds['screens']);
				?>
				<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit&action=image&id=<?php echo $_GET['id']; ?>"</script>
				<?php
				//header("Location: index.php?site=highlighted_edit&action=image&id=".$_GET['id']);
			}
			else if ($_POST['no'] == 'No')
			{
				?>
				<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit"</script>
				<?php
				//header("Location: index.php?site=highlighted_edit");
			}
		}
		else
		{
		?>
			<form action="index.php?site=highlighted_edit&amp;action=image&amp;id=<?php echo $_GET['id']; ?>" method="post" enctype="multipart/form-data">
				<label for="file">Upload image (330x170):</label> <input type="file" name="file" id="file"/><br/>
				<input type="submit" name="submit" value="Submit"/>
			</form>
			<a href="index.php?site=highlighted_edit">Go back!</a>
			<?php
			//var_dump($_POST);
			//var_dump($ds);
			if (isset($_POST['submit']))
			{
				$file = $_FILES['file'];
				$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
				
				if ($file['type'] == 'image/gif' || $file['type'] == 'image/png' || $file['type'] == 'image/jpeg' || $file['type'] == 'image/pjpeg')
				{
					if ($file['error'] > 0)
						echo "Error: ".$file['error'];
					else
					{
						move_uploaded_file($file['tmp_name'], $upload_dir.$file['name']);
						rename($upload_dir.$file['name'], $upload_dir.$ds['highID'].".".$ext);
						
						echo "<br/>\nName: ".$file['name']."<br/>\n";
						echo "Type: ".$file['type']."<br/>\n";
						echo "Size: ".ceil($file['size'] / 1024)." kB";
						
						safe_query("UPDATE ".PREFIX."highlighted SET screens='".$ds['highID'].".".$ext."' WHERE highID=".$ds['highID']);
						?>
						<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit"</script>
						<?php
						//header("Location: index.php?site=highlighted_edit");
					}
				}
				else
				{
					echo "The only allowed filetypes are .gif, .png and .jpg!";
				}
			}
		}
	}
	else if ($_GET['action'] == 'edit' && is_numeric($_GET['id']))
	{
		$query = safe_query("SELECT * FROM ".PREFIX."highlighted WHERE highID = ".$_GET['id']." ORDER BY highID");
		?>
		<h1>Highlighted ID <?php echo $_GET['id']; ?></h1>
		<?php
		$ds = mysql_fetch_assoc($query);
		//var_dump($ds);
		//var_dump($_POST);
		?>
		<form action="index.php?site=highlighted_edit&amp;action=edit&amp;id=<?php echo $_GET['id']; ?>" method="post">
			<table bgcolor="#fff" width="100%">
				<tr><td>ID:</td><td><input type="text" name="highID" value="<?php echo $ds['highID']; ?>" readonly="readonly"/></td></tr>
				<tr><td>Title:</td><td><input type="text" name="title" value="<?php echo $ds['title']; ?>" maxlength="40"/></td></tr>
				<tr><td>Link:</td><td><input type="text" name="link" value="<?php echo $ds['link']; ?>" maxlength="100"/></td></tr>
				<tr><td></td><td><input type="submit" name="submit" value="Submit"/></td></tr>
			</table>
		</form>
		<?php
		if (isset($_POST['submit']))
		{
			/*********************************\
			|                                 |
			|     TODO: make this safer!      |
			|                                 |
			\*********************************/
			
			$highID = $_POST['highID'];
			$title = $_POST['title'];
			$link = $_POST['link'];
			
			safe_query("UPDATE ".PREFIX."highlighted SET title='".$title."', link='".$link."' WHERE highID=".$highID);
			?>
			<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit"</script>
			<?php
			//header("Location: index.php?site=highlighted_edit");
		}
	}
	else if ($_GET['action'] == 'delete' && is_numeric($_GET['id']))
	{
		$query = safe_query("SELECT * FROM ".PREFIX."highlighted WHERE highID = ".$_GET['id']." ORDER BY highID");
		?>
		<h1>Are you sure?</h1>
		<?php
		$ds = mysql_fetch_assoc($query);
		//var_dump($ds);
		if (!$ds)
		{
		?>
			<p>A highlighted entry with this ID hasn't been found or something went wrong with the database query.</p>
			<p><a href="index.php?site=highlighted_edit">Click here to go back.</a></p>
		<?php
		}
		else
		{
		?>
			<p>Are you sure you want to delete this highlighted entry?</p>
			<ul>
				<li>Title: <b><?php echo $ds['title']; ?></b></li>
			</ul>
			
			<form action="index.php?site=highlighted_edit&amp;action=delete&amp;id=<?php echo $_GET['id']; ?>" method="post">
				<input type="submit" name="yes" value="Yes" style="width: 100px"/>
				<input type="submit" name="no" value="No" style="width: 100px"/>
			</form>
			<?php
			if ($_POST['yes'] == 'Yes')
			{
				safe_query("DELETE FROM ".PREFIX."highlighted WHERE highID = ".$_GET['id']);
				?>
				<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit"</script>
				<?php
				//header("Location: index.php?site=highlighted_edit");
			}
			else if ($_POST['no'] == 'No')
			{
				?>
				<script language="javascript" type="text/javascript">window.location.href="index.php?site=highlighted_edit"</script>
				<?php
				//header("Location: index.php?site=highlighted_edit");
			}
		}
	}
	else
	{
		$query = safe_query("SELECT * FROM ".PREFIX."highlighted ORDER BY highID");
		?>		
		<h1>Highlighted</h1>
		
		<table bgcolor="#fff" width="100%">
		<thead><tr><td>ID</td><td>Title</td><td>Link</td><td></td><td></td><td></td></tr></thead>
		<tbody>
		<?php
		while ($ds = mysql_fetch_assoc($query))
		{
			//var_dump($ds);
			?>
			<tr>
				<td><?php echo $ds['highID']; ?></td>
				<td><?php echo $ds['title']; ?></td>
				<td><?php echo $ds['link']; ?></td>
				<td><input type="button" onclick="MM_goToURL('parent','index.php?site=highlighted_edit&amp;action=image&amp;id=<?php echo $ds['highID']; ?>');return document.MM_returnValue" value="Image"/></td>
				<td><input type="button" onclick="MM_goToURL('parent','index.php?site=highlighted_edit&amp;action=edit&amp;id=<?php echo $ds['highID']; ?>');return document.MM_returnValue" value="Edit"/></td>
				<td><input type="button" onclick="MM_goToURL('parent','index.php?site=highlighted_edit&amp;action=delete&amp;id=<?php echo $ds['highID']; ?>');return document.MM_returnValue" value="Delete"/></td>
			</tr>
			<?php
		}
		?>
			<tr>
				<td></td>
				<td><a href="index.php?site=highlighted_edit&amp;action=add">Add new</a></td>
			</tr>
		</tbody>
		</table>
		<?php
	}
}
else
{
	?>
	<script language="javascript" type="text/javascript">window.location.href="index.php"</script>
	<?php
	die("You don't have access to this page!");
}
?>