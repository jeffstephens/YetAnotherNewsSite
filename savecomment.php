<?php
require_once( 'config.php' );
$userInfo = YANS::validateLogin();

if( $mysqli = YANS::dbconnect() ){
    $post = YANS::sanitize( $_POST['comment'] );
    $articleID = YANS::sanitize( $_POST['articleID'] );
    
    if( strlen( $post ) && strlen( $articleID ) ){
        $query = 'INSERT INTO `comments` (`author_id`, `article_id`, `content`) '
               . 'VALUES ("'. $userInfo['id'] .'", "'. $articleID .'", "'. $post .'");';
        
        if( $result = $mysqli->query( $query ) ){
            Header( "Location: article.php?aid=" . $articleID ."&msg=1#comments" );
        }
        else{
            YANS::log_error( "Database error while adding comment.\n" . $mysqli->error );
        }
    }
    else{
        YANS::log_error( "Empty comment; not posting." );
        Header( "Location: article.php?msg=2&aid=" . $_POST['articleID'] );
    }
}
else{
    YANS::log_error( "Couldn't connect to database to post a comment.\n" . $mysqli->error );
    Header( "Location: article.php?msg=2&aid=" . $_POST['articleID'] );
}
?>