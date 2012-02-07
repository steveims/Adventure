<html>
<head>
<style  TYPE="text/css"> 
<!--
.files {
  padding: 10px;
}
.message {
 color: red;
  font-weight: bold;
}
-->
</style>
</head>

<body>
<table width="100%">
<tr>
<td>Welcome to the sabsl adventure games.</td>
<td align="right"><a href="editor.php">Edit</a></td>
</tr>
</table>
<hr>

<?php
  $userdirs = glob ("games/*");
  if ( !$userdirs ) {
?>

<p>No games are available.  Ask a game editor to create one.</p>

<?php } else { ?>

<p>Click on a game to get started.</p>


<div style="padding: 10px; background-color: #ff8888; width: 600;">
<table cell-padding="4" width="100%">

<tr style="background-color: white;">
  <th class="files">Game</th>
  <th class="files">Author</th>
  <th class="files">Description</th>
</tr>

<?php
  foreach ( $userdirs as $userdir ) {
  $username = basename ( $userdir );
    foreach ( glob ("$userdir/*") as $file ) {
      $gamename = basename ( $file, '.json' );
      $game_definition = file_get_contents ( $file );
      $locations = json_decode($game_definition, true);

      if ( is_array ( $locations ) 
	   &&array_key_exists ( 'description', $locations )) {
	$game_desc = $locations['description'];
      } else {
	$game_desc = "&lt;No description&gt;";
      }

 ?>

<tr style="background-color: white;">
  <td class="files" width="*%"><?php echo "<a href=\"play.php/$file\">$gamename</a>"; ?></td>
  <td class="files" width="*%"><?php echo "$username"; ?></td>
  <td class="files" width="*%"><?php echo "$game_desc"; ?></td>
</tr>

<?php
    } // foreach
   } // else
  }
 ?>

</table>
</div>

</body>
</html>