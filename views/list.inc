<?php

if ( !isset ( $adventure )) { die ( 'Cannot access list.inc directly.' ); }
error_reporting(E_ALL);


?>

<html>
<head>
<style  TYPE="text/css"> 
<!--
td.files {
  padding: 10px;
}
.message {
 color: red;
  font-weight: bold;
}
-->
</style>

<script type="text/javascript">
<!--
function dogame(a,f)
{
  action.value=a;
  gamename.value=f;
}
//-->
</script>

</head>

<table width="100%">
<tr>
<td>Welcome, <?php echo "$username"; ?>.  You are logged in as an editor
for the sabsl adventure games.</td>
<td align="right"><a href="index.php">Play</a></td>
</tr>
</table>

<p/>

<div style="padding: 10px; background-color: #88ff88; width: 400;">
<b>Create a new game</b><br/>
<div class="message">
<?php if ( array_key_exists ( 'create', $list_messages )) { echo $list_messages['create']; } ?>
</div>
<form action="editor.php" method="POST">
<input type="hidden" name="action" value="create" />
<table cell-padding="4" width="400">
  <tr style="background-color: white;">
    <td class="files" width="*%">
    <input type="text" name="gamename" value="" size="30" />
    </td>
    <td class="files" width="56">
    <input type="image"
           src="images/add.png"
           title="Create" />
    </td>
  </tr>
</table>
</form>
</div>

<p/>

<?php if ( sizeof ( $games ) > 0 ) { ?>
<div style="padding: 10px; background-color: #8888ff; width: 400;">
<b>Work with your existing games</b><br/>
<form action="editor.php" method="POST">
<input id="action" type="hidden" name="action" value="__notset__" />
<input id="gamename" type="hidden" name="gamename" value="__notset__" />
<table cell-padding="4" width="400">

<?php foreach ( $games as $game ) { ?>

  <tr style="background-color: white;">
    <td class="files" width="*%"> <?php echo $game; ?> </td>
    <td class="files" width="56">
    <input type="image" 
	   src="images/page_edit.png" 
	   title="Edit <?php echo $game; ?>"
	   onclick="dogame('edit', '<?php echo $game; ?>');"
           value="Edit" />
    <input type="image" src="images/delete.png" 
	   title="Delete <?php echo $game; ?>"
           onclick="if (confirm('Delete game <?php echo $game; ?>?')) { dogame('delete', '<?php echo $game; ?>'); submit(); }"
	   value="Delete" />
    </td>
  </tr>

<?php } ?>

</table>
</form>
</div>
<?php } ?>


</html>