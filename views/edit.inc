<?php

if ( !isset ( $adventure )) { die ( 'Cannot access edit.inc directly.' ); }
error_reporting(E_ALL);


?>

<html>
<head>
<script type="text/javascript">
<!--
function setaction(a)
{
  action.value=a;
}
//-->
</script>

</head>

<p>Welcome, <?php echo "$username"; ?>.  You are logged in as an editor
for the sabsl adventure games.</p>

<p/>

<div style="padding: 10px; background-color: #ffff88; width: 580;">

<p><b>Editing game:  <?php echo $gamename; ?></b></p>

<form action="editor.php" method="POST">
  <input id="action" type="hidden" name="action" value="__notset__" />
  <input type="hidden" name="gamename" value="<?php echo $gamename; ?>" />

  <table width="100%">
  <tr><td>
  <input type="submit"
         onclick="setaction('update');"
         value="Save" />
  <input type="reset" 
         value="Reset" />
  <input type="submit"
         onclick="setaction('cancel');"
         value="Cancel" />
  </td>
  <td align="right">
  <a href="help.html" target="_blank">Example and syntax help</a>
  </td>
  </tr>
  </table>
  <br/>

  <textarea name="content" cols="80" rows="40"><?php echo $game_definition; ?></textarea>

</form>
</div>

</html>