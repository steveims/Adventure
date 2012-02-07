<?php

  //error_reporting(E_ALL);
session_start();

// Required marker for views.
$adventure = true;

$method = $_SERVER['REQUEST_METHOD'];

if ( $method == "GET" ) {
  $pathinfo = $_SERVER['PATH_INFO'];
  $game_file = substr ( $pathinfo, 1 );

  $game_definition = file_get_contents ( $game_file );
  $locations = json_decode ( $game_definition, true );

  if (!$locations) die("Failed to load game file:  $game_file.  Check for syntax errors in the game file.\n");

  $location     = $locations["start"];
  $location_map = array ();
  $inventory    = array ();

  saveStateToSession();

  $output = getWelcome();
  $output .= getDescription();

  require 'views/play.inc';


} elseif ( $method == "POST" ) {
  if ( !array_key_exists ( 'command', $_POST )) {
    die ( 'Missing required POST parameter:  command.' );
  }
  $command = trim ( $_POST['command'] );

  if ( !array_key_exists ( 'output', $_POST )) {
    die ( 'Missing required POST parameter:  output.' );
  }
  $output = stripslashes ( $_POST['output'] );

  $output .= "<br/>> $command<br/><br/>";


  loadStateFromSession();

  if ($command == "inventory") {
    listInventory();

  } elseif ($command == "quit") {
    quit ();

  } elseif ($command == "help") {
    help ();

  } else {
    // command has form:  verb object
    $offset = strpos ( $command, ' ' );
    if ( !$offset ) {
      $output .= "Unknown command:  $command<br/>";

    } else {
      $verb = substr ( $command, 0, $offset );
      $object = ltrim ( substr ( $command, $offset ));

      if ( $verb == "go" ) {
	go ( $object );

      } else if ( $verb == "look" ) {
	look ( $object );

      } else if ( $verb == "take" ) {
	take ( $object );

      } else if ( $verb == "use" ) {
	useItem ( $object );

      } else {
	$output .= "Unknown command:  $command<br/>";

      }
    }
  }

  saveStateToSession();

  require 'views/play.inc';
}


function quit () {
  unset ( $_SESSION['locations'] );
  unset ( $_SESSION['location'] );
  unset ( $_SESSION['location_map'] );
  unset ( $_SESSION['inventory'] );

  header("Location: http://sabsl.org/Adventure/"); /* Redirect browser */
}


function help () {
  global $output;

  $output .= <<<HELP
Commands include:<br/>
  go   &lt;n|s|e|w&gt;<br/>
  look &lt;object&gt;<br/>
  take &lt;item&gt;<br/>
  use  &lt;item&gt;<br/>
  inventory<br/>
  quit<br/>
<br/>
HELP;
}


function saveStateToSession() {
  global $location, $locations, $location_map, $inventory;

  // Don't need to save locations...
  $_SESSION['location'] = $location;
  $_SESSION['locations'] = $locations;
  $_SESSION['location_map'] = $location_map;
  $_SESSION['inventory'] = $inventory;
}


function loadStateFromSession() {
  global $location, $locations, $location_map, $inventory;

  $locations = $_SESSION['locations'];
  $location = $_SESSION['location'];
  $location_map = $_SESSION['location_map'];
  $inventory = $_SESSION['inventory'];
}


function go ( $direction ) {
  global $location, $output;

  $locationArray = getCurrentLocationDefinition( );
  $next_location = $locationArray["go"][$direction];

  if ( $next_location ) {
    // next location is stored with an @ prefix ("@L1"); strip the prefix.
    $location = substr ( $next_location, 1 );
    $output .= getDescription($location);

  } else {
    $output .= "Cannot go $direction.<br/>";
  }
}


function look ( $object ) {
  global $inventory, $output;

  $locationArray = getCurrentLocationDefinition ( );
  $ref = $locationArray["look"][$object];

  if ( ! $ref ) {
    $output .= "$object is not found here.<br/>";

  } elseif ( strpos ( $ref, "#" ) === 0 ) {
    // Refers to a removeable item (e.g. #key)

    $name = substr ( $ref, 1 );

    if ( in_array ( $name, $inventory )) {
      $output .= "You already found the $name here.<br/>";

    } else {
      $output .= "You see a $name.<br/>";
    }

  } else {
    // Refers to a non-removeable item
    $output .= "$ref<br/>";
  }
}


function take ( $item ) {
  global $inventory, $output;

  $locationArray = getCurrentLocationDefinition( );
  $objects = $locationArray["look"];

  if ( $objects && array_search ( "#".$item, $objects )) {
    // Item is defined at this location; check if player already has the item.
    if ( in_array ( $item, $inventory )) {
      $output .= "You already have the $item.<br/>";

    } else {
      $inventory[] = $item;
      $output .= "You picked up the $item.<br/>";
    }

  } else {
    $output .= "$item is not found here.<br/>";
  }
}


function useItem ( $item ) {
  global $inventory, $location, $location_map, $output;

  $locationArray = getCurrentLocationDefinition( );

  if ( array_key_exists ( $item, $locationArray['use'] )) {
    $fixed_item_reactions = $locationArray["use"][$item];
    processUseReactions ( $fixed_item_reactions );

  } elseif ( ! in_array ( $item, $inventory )) {
      $output .= "You don't have a $item.<br/>";

  } elseif ( array_key_exists ( "#".$item, $locationArray['use'] )) {
    $portable_item_reactions = $locationArray["use"]["#".$item];
    processUseReactions ( $portable_item_reactions );

  } else {
    if ( ! $carried_reaction ) {
      $output .= "$item has no effect here.<br/>";
    }
  }
}


function processUseReactions ( $reactions ) {
  if ( is_array ( $reactions )) {
    foreach ( $reactions as $reaction ) {
      processUseReaction ( $reaction );
    }

  } else {
    processUseReaction ( $reactions );
  }
}


function processUseReaction ( $reaction ) {
  global $location, $location_map, $output;

  if ( strpos ( $reaction, "@" ) === 0 ) {
    $location = substr ( $reaction, 1 );
    $output .= getDescription ( );

  } elseif ( strpos ( $reaction, "^" ) === 0 ) {
    $location_map[$location] = substr ( $reaction, 1 );
    $output .= getDescription ( );

  } else {
    $output .= "$reaction<br/>";
  }
}


function getDescription ( )
{
  $locationArray = getCurrentLocationDefinition ( );

  return $locationArray["description"] . "<br/>";
}


function getWelcome () {
  global $locations;

  $welcome = <<<WELCOME
Welcome to the sabsl adventure game player.<br/>
<br/>
About this game:<br/>
{$locations['description']}<br/>
<br/>
Enter help for a list of commands.<br/>
<br/>
<br/>
WELCOME;

  return $welcome;
}


function listInventory ( )
{
  global $inventory, $output;

  foreach ( $inventory as $item ) {
    $output .= "  $item<br/>";
  }
}


function getCurrentLocationDefinition ( ) {
  global $location, $locations, $location_map;

  // location_map stores the variant definitions; 
  // use the variant, if set.
  if ( array_key_exists ( $location, $location_map )) {
    $alt_location = $location_map[$location];
    return $locations["location_definitions"][$alt_location];

  } else {
    return $locations["location_definitions"][$location];
  }
}

?>
