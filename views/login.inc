<?php

if ( !isset ( $adventure )) { die ( 'Cannot access list.inc directly.' ); }

?>

<html>
<form action="editor.php" method="POST">
  <input type="hidden" name="action" value="login" />
  <b>Login for game editors</b><br/>

  <table>
  <tr><td>Username:</td><td><input type="text" name="username" /></td></tr>
  <tr><td>Password:</td><td><input type="password" name="password" /></td></tr>
  <tr><td colspan="2"><input type="submit" value="Login" /></td></tr>
  </table>
</form>
</html>