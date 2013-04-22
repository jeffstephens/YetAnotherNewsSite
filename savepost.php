<?php
/* This file saves all new posts to the database and then redirects back accordingly. */
require_once( "config.php" );
$userInfo = YANS::validateLogin();

if( !isset( $_POST['id'] ) ){
    if( YANS::validateAuthToken( $_POST['authToken'], $userInfo['id'] ) ){
        // Create new post
        $author_id = $userInfo['id'];
        $title = YANS::sanitize( $_POST['title'] );
        $content = YANS::sanitize( $_POST['body'] );
        $url = YANS::sanitize( $_POST['url'] );
        
        // Clear out dummy text
        if( $title == 'Snarky and/or witty title here...' ){
            $title = '';
        }
        if( $url == 'Paste a URL here or make a text-only post.' ){
            $url = '';
        }
        if( $content == 'Write some interesting content!' ){
            $content = '';
        }
        
        // Text post or link?
        if( strlen( $url ) ){
            $mysqli = YANS::dbconnect();
            $query = 'INSERT INTO `articles` (`author_id`, `title`, `content`, `url`)
                      VALUES ("'. $author_id .'", "'. $title .'", "'. $content .'", "'. $url .'");';
            
            YANS::log_info( 'Query: ' . $query );
            
            if( $mysqli->query( $query ) ){
                // Success
                Header( "Location: index.php?msg=12" );
            }
            else{
                // Failure
                YANS::log_error( "An error occurred while making a link post.\n" . $mysqli->error );
                Header( "Location: index.php?msg=11" );
            }
            
            $mysqli->close();
        }
        else{
            $mysqli = YANS::dbconnect();
            $query = 'INSERT INTO `articles` (`author_id`, `title`, `content`, `url`)
                      VALUES ("'. $author_id .'", "'. $title .'", "'. $content .'", "");';
            
            YANS::log_info( 'Query: ' . $query );
            
            if( $mysqli->query( $query ) ){
                // Success
                Header( "Location: index.php?msg=12" );
            }
            else{
                // Failure
                YANS::log_error( "An error occurred while making a text post.\n" . $mysqli->error );
                Header( "Location: index.php?msg=11" );
            }
            
            $mysqli->close();
        }
    }
    else{
        Header( "Location: index.php?msg=19" );
    }
}
else{
    if( YANS::validateAuthToken( $_POST['authToken'], $userInfo['id'] ) ){
        // Edit old post
        $id = YANS::sanitize( $_POST['id'] );
        $title = YANS::sanitize( $_POST['title'] );
        
        $content = YANS::sanitize( $_POST['body'] );
        $url = YANS::sanitize( $_POST['url'] );
        
        // Clear out dummy text
        if( $title == 'Snarky and/or witty title here...' ){
            $title = '';
        }
        if( $url == 'Paste a URL here or make a text-only post.' ){
            $url = '';
        }
        if( $content == 'Write some interesting content!' ){
            $content = '';
        }
        
        $mysqli = YANS::dbconnect();
        $query = 'UPDATE `articles` SET `title` = "'. $title .'",
                 `content` = "'. $content .'", `url` = "'. $url .'"
                 WHERE `id` = '. $id .' AND `author_id` = '. YANS::sanitize( $userInfo['id'] ) .';';
        
        YANS::log_info( 'Query: ' . $query );
        
        if( $mysqli->query( $query ) ){
            // Success
            Header( "Location: index.php?msg=16" );
        }
        else{
            // Failure
            YANS::log_error( "An error occurred while updating a post.\n" . $mysqli->error );
            Header( "Location: index.php?msg=11" );
        }
        
        $mysqli->close();
    }
    else{
        Header( "Location: index.php?msg=19" );
    }
}