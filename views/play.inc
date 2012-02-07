<?php

if ( !isset ( $adventure )) { die ( 'Cannot access play.inc directly.' ); }
error_reporting(E_ALL);


?>

<html>
<head>
<script type="text/javascript">
<!--
function setoutput()
{
  output.value=document.getElementById("showoutput").innerHTML;
}
//-->
</script>

</head>

<body onload="document.f.command.focus()">

<div id="showoutput" style="font-family: courier;" width="100%">
<?php echo $output; ?>
</div>

<form name="f" method="POST">
  <input id="output" type="hidden" name="output" value="__notset__" />
  <input type="text" name="command" autocomplete="off" />
  <input type="submit" value="Do" onclick="setoutput();" />
</form>

</body>

</html>