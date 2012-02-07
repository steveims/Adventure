<?php

  //error_reporting(E_ALL);
session_start();

$adventure = true;
$user_registry = array ( "steve" => "John316",
			 "brianna" => "John112" );


// User must login.
if ( !authenticated ( )) {
  require ( 'views/login.inc' );
  return;
}

$method = $_SERVER['REQUEST_METHOD'];
$username = $_SESSION['username'];

$list_messages = array();


if ( $method == "GET" ) {
  doList();
  return;

} elseif ( $method == "POST" ) {
  if ( !array_key_exists ( 'action', $_POST )) {
    die ( 'Missing required POST parameter:  action.' );
  }

  $action = $_POST['action'];
  if ( $action == "login" ) {
    doList();

  } elseif ( $action == "create" ) {
    doCreate();

  } elseif ( $action == "edit" ) {
    doEdit();

  } elseif ( $action == "delete" ) {
    doDelete();

  } elseif ( $action == "update" ) {
    doUpdate();

  } elseif ( $action == "cancel" ) {
    doList();

  } else {
    die ( 'Unknown action:  $action' );
  }
}


function doList() {
  global $adventure, $username, $list_messages;

  $games = array ();
  $userdir = glob ("games/$username");
  if ( $userdir ) {
    foreach ( glob ("games/$username/*.json") as $file ) {
      $games[] = basename ( $file, '.json' );
    }
  }

  require ( 'views/list.inc' );
}


function doCreate() {
  global $username, $list_messages;

  if ( !array_key_exists ( 'gamename', $_POST )) {
    die ( 'Missing required POST parameter:  gamename.' );
  }
  
  // User cannot specify directory component; use only basename
  $gamename = basename ( $_POST['gamename'] );

  // User files go into a user directory
  $userdir = "games/$username";
  if ( !is_dir ( $userdir )) {
    mkdir ( $userdir, 0777, true ) or die ( "Cannot create directory $userdir" );
  }
  $f = $userdir . '/' . $gamename . '.json';

  if ( file_exists ( $f )) {
    $list_messages['create'] = "Game '$gamename' already exists.";
    doList();

  } else {
    $fh = fopen($f, "w");
    fclose ( $fh );
    //    $template = 'template.json';
    //    copy ( $template, $f ) or die("Failed to copy $template to $f.");

    doEdit();
  }
}

function doEdit() {
  global $username, $adventure;

  if ( !array_key_exists ( 'gamename', $_POST )) {
    die ( 'Missing required POST parameter:  gamename.' );
  }
  
  // User cannot specify directory component; use only basename
  $gamename = basename ( $_POST['gamename'] );

  $f = "games/$username/$gamename.json";

  (is_file ( $f )) or die ( "Cannot find file $f" );

  $game_definition = file_get_contents ( $f );

  require ( 'views/edit.inc' );
}

function doDelete() {
  global $username;

  if ( !array_key_exists ( 'gamename', $_POST )) {
    die ( 'Missing required POST parameter:  gamename.' );
  }
  
  // User cannot specify directory component; use only basename
  $gamename = basename ( $_POST['gamename'] );

  $f = "games/$username/$gamename.json";
  unlink ( $f ) or die ( 'Failed to delete $f' );

  doList();
}

function doUpdate() {
  global $username;

  if ( !array_key_exists ( 'gamename', $_POST )) {
    die ( 'Missing required POST parameter:  gamename.' );
  }
  
  // User cannot specify directory component; use only basename
  $gamename = basename ( $_POST['gamename'] );

  $f = "games/$username/$gamename.json";


  array_key_exists ( 'content', $_POST ) or 
    die ( 'Missing required POST parameter:  gamename.' );

  $fh = fopen($f, "w");

  $content = $_POST['content'];
  fwrite ( $fh, stripslashes ( $content ));
  fclose ( $fh );

  doList();
}


function authenticated ( ) {
  global $user_registry;

  if ( array_key_exists ( 'username', $_SESSION)) {
    return true;
  }

  if ( !array_key_exists ( 'username', $_POST )
       || !array_key_exists ( 'password', $_POST )) {
    return false;
  }

  $login_username = $_POST['username'];
  $login_password = $_POST['password'];

  if ( !array_key_exists ( $login_username, $user_registry )) {
    return false;
  }

  $isauthn = ( $user_registry[$login_username] == $login_password );
  if ( $isauthn ) {
    $_SESSION['username'] = $login_username;
  }

  return $isauthn;
}

?>