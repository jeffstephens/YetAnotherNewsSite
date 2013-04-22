<?php
// Delete an entry or a comment with no warning!
require_once( 'config.php' );
$userInfo = YANS::validateLogin();

if( YANS::validateAuthToken( $_GET['authToken'], $userInfo['id'] ) ){
    if( $mysqli = YANS::dbconnect() ){
        if( isset( $_GET['article'] ) ){
            $id = YANS::sanitize( $_GET['article'] );
            $sql = 'SELECT `author_id` FROM `articles` WHERE `id` = '. $id .';';
            if( $result = $mysqli->query( $sql ) ){
                $data = $result->fetch_assoc();
                if( $data['author_id'] == $userInfo['id'] || $_SESSION['admin'] == "on" ){
                    $commentquery = 'DELETE FROM `comments` WHERE `article_id` = '. $id .';';
                    if( $mysqli->query( $commentquery ) ){
                        $query = 'DELETE FROM `articles` WHERE `id` = '. $id .' LIMIT 1;';
                        if( $mysqli->query( $query ) ){
                            Header( "Location: index.php?msg=17" );
                        }
                        else{
                            YANS::log_error( "Failed to delete an article's comments.\n" . $query ."\n\n". $mysqli->error);
                            Header( "Location: index.php?msg=11" );
                        }
                    }
                    else{
                        YANS::log_error( "Failed to delete article.\n" . $query . "\n\n" . $mysqli->error );
                        Header( "Location: index.php?msg=11" );
                    }
                }
            }
            else{
                YANS::log_error( "Database error while looking up author id from article to delete.\n". $sql ."\n\n" . $mysqli->error );
                Header( "Location: index.php?msg=11" );
            }
        }
        
        elseif( isset( $_GET['comment'] ) ){
            $id = YANS::sanitize( $_GET['comment'] );
            $sql = 'SELECT `author_id` FROM `comments` WHERE `id` = '. $id .';';
            if( $result = $mysqli->query( $sql ) ){
                $data = $result->fetch_assoc();
                if( $data['author_id'] == $userInfo['id'] || $_SESSION['admin'] == "on" ){
                    $query = 'DELETE FROM `comments` WHERE `id` = '. $id .' LIMIT 1;';
                    if( $mysqli->query( $query ) ){
                        Header( "Location: index.php?msg=18" );
                    }
                    else{
                        YANS::log_error( "Failed to delete comment.\n" . $query . "\n\n" . $mysqli->error );
                        Header( "Location: index.php?msg=11" );
                    }
                }
            }
            else{
                YANS::log_error( "Database error while looking up author id from article to delete.\n". $sql ."\n\n" . $mysqli->error );
                Header( "Location: index.php?msg=11" );
            }
        }
        else{
            Header( "Location: index.php" );
        }
    }
    else{
        YANS::log_error( "Database connection failed while trying to delete.\n" . $mysqli->error );
        Header( "Location: index.php?msg=11" );
    }
}
else{
    Header( "Location: index.php?msg=19" );
}
?>