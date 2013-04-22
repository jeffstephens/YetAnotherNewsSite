<?php
require_once( 'config.php' );

if( $mysqli = YANS::dbconnect() ){
    if( isset( $_POST['username'] ) && isset( $_POST['pw'] ) ){
        $username = YANS::sanitize( $_POST['username'] );
        $password = YANS::sanitize( $_POST['pw'] );
        
        $userquery = 'SELECT `id`, `pw`, `salt` FROM `users` WHERE `username` = "'. $username .'";';
        if( $userresult = $mysqli->query( $userquery ) ){
            if( $userresult->num_rows == 0 ){
                // User doesn't exist; prompt them to register.
                Header( "Location: index.php?msg=13&reg&username=" . $username );
            }
            else{
                $userarray = $userresult->fetch_assoc();
                if( $userarray['pw'] == YANS::encrypt( $password, $userarray['salt'] ) ){
                    $_SESSION['time'] = time();
                    $_SESSION['userid'] = $userarray['id'];
                    
                    if( strlen( $_COOKIE['YANS_Article'] ) ){
                        Header( "Location: article.php?msg=3&aid=" . $_COOKIE['YANS_Article'] );
                    }
                    else{
                        Header( "Location: index.php" );
                    }
                }
                else{
                    YANS::log_info( 'Password mismatch: ' . $userarray['pw'] .' vs. ' . YANS::encrypt( $password, $userarray['salt'] ) );
                    Header( "Location: index.php?msg=2" );
                }
            }
        }
        else{
            YANS::log_error( "Database error when querying for user information during login.\n" . $mysql->error );
        }
    }
    else{
        Header( "Location: index.php?msg=3" );
    }
}

else{
    YANS::log_error( "Failed to establish database connection for login.\n" . $mysqli->error );
    Header("Location: index.php?msg=11");
}