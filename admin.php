<?php
require_once( 'config.php' );

if( YANS::iAmAdmin() ){
    if( $_SESSION['admin'] == "on" ){
        $_SESSION['admin'] = "off";
    }
    else{
        $_SESSION['admin'] = "on";
    }
    Header( "Location: " . $_SERVER['HTTP_REFERER'] );
}
else{
    log_error( "Someone's getting curious and poking around admin.php." );
    Header( "Location: index.php" );
}