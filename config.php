<?php
session_start();

// Swiftmailer library for sending mail
require_once( 'lib/swiftmailer/lib/swift_required.php' );

class YANS{
    // Database connection constants
    const DB_HOST    = "localhost";
    const DB_USER    = "user";
    const DB_PASS    = "password";
    const DB_NAME    = "news";
    const USER_TABLE = "users";
    
    // Other constants
    const SYSTEM_FILE_PATH = "../"; // a place hidden from the public eye
    const GMAIL_USER       = "yetanothernewssite@gmail.com";
    const GMAIL_PASS       = "password";
 
    /* Connect to the database and return an instance of the mysqli function. */
    function dbconnect(){
        $connection = new mysqli( self::DB_HOST, self::DB_USER, self::DB_PASS );
        $connection->select_db( self::DB_NAME );
        
        // Did it work?
        if( mysqli_connect_errno() ){
            self::log_error( 'Database connection failed. ' . mysqli_connect_error() );
            return false;
        }
        
        return $connection;
    }
    
    /* Sanitize input - remove all HTML and escape characters so it's MySQL-safe.
     *
     * @param $input    String The string to sanitize
     * @return          String The sanitized and optionally formatted input.
     */
    function sanitize( $input ){
        $mysqli = self::dbconnect();
        $input = $mysqli->real_escape_string( $input );
        $input = trim($input);
        $input = htmlentities($input);
        $mysqli->close();
        return $input;
    }
    
    /* Log an error message.
     *
     * @param $msg String The error message to log.
     * @return void
     */
    function log_error( $msg ){
        self::system_log( $msg, 'ERROR' );
    }
    
    /* Log an information message.
     *
     * @param $msg String The info message to log.
     */
    function log_info( $msg ){
        self::system_log( $msg, 'INFO' );
    }
    
    /* Write a log message with severity.
     * All log entries will automatically include date/time and a bunch of the user's
     * information.
     *
     * @param $msg The message to log
     * @param $level The severity level of the message. Defaults to info.
     * @return void
     */
    function system_log( $msg, $level = 'INFO' ){
        $ip       = $_SERVER['REMOTE_ADDR'];
        $datetime = date('g:i a n/j/y');
        $script   = $_SERVER['PHP_SELF'];
        $query    = $_SERVER['QUERY_STRING'];
        
        $log_msg  = "\n\n=========================================\n"
                  . $level . " at " . $datetime . "\n"
                  . "Script: " . $script . "\n"
                  . "Query: " . $query . "\n"
                  . "Message: " . $msg;
        
        file_put_contents( self::SYSTEM_FILE_PATH . "/error.log", $log_msg, FILE_APPEND );
        
        // Notify Jeff by email
        if( $level == 'ERROR' ){
            self::sendMail( 1, 'YANS Error Report', $log_msg );
        }
    }
    
    /* Mutate an input, transforming markdown into HTML.
     *
     * @param $input The text to mutate from markdown to HTML
     * @return void
     */
    function markdownify( &$input ){
        return $input;
    }
    
    /* Encrypt a password. If not given a salt, generate one and return a two-element
     * array. Otherwise, just use the salt and return just the hashed password.
     *
     * @param $password String The password you'd like to hash.
     * @param $salt     String The optional salt to hash it with.
     * @return Just a password if a salt is provided, else Array [hashed password,salt]
     */
    function encrypt( $password, $salt = "" ){
        if( !strlen( $salt ) ){
            // First generate a salt
            $salt = self::getSalt();
            $madeASalt = true;
        }
        else{
            $madeASalt = false;
        }
        
        // Blowfish-hash the password
        $hashed = crypt( $password, $salt );
  
        // Return different data depending on whether a salt was provided
        if( $madeASalt ){
            return Array( $hashed, $salt );
        }
        else{
            return $hashed;
        }
    }

    function changepw($name, $newpw){
        $parray = self::encrypt($newpw);
        $query = "UPDATE users SET pw = '". $parray[0] ."', salt = '". $parray[1] ."' WHERE username = '". $name ."';";

        $mysqli = self::dbconnect();

        if( $mysqli->query($query) ){
            $mysqli->close();
            return true;
        }
        else{
            self::log_error( "Password change failed.\n" . $mysqli->error );
            $mysqli->close();
            return false;
        }
    }

    function randString($length, $strength=8) {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
        if ($strength >= 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength >= 2) {
            $vowels .= "AEUY";
        }
        if ($strength >= 4) {
            $consonants .= '23456789';
        }
        if ($strength >= 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }
    
    /* Generate a shiny new salt for something say, hashing a password.
     * Pay no attention to the code inside this function.
     */
    function getSalt(){
        $inside = time() . "az51" . time();        
        $salt = '$2a$07$' . $inside . '$';
        return $salt;
    }

    /* Send email to a person using the Swiftmailer library
     *
     * @param $to      int    The user ID of the recipient
     * @param $subject String The subject of the email
     * @param $body    String The content of the email
     */
    function sendMail( $to, $subject, $body ){
        // Look up user information
        $mysqli = self::dbconnect();
        $query = 'SELECT `username`, `email` FROM `users` WHERE `id` = "'. self::sanitize( $to ) .'";';
        $result = $mysqli->query( $query );
        $userData = $result->fetch_assoc();
                
        // Send mail using Swiftmailer with Gmail
        $transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername( self::GMAIL_USER )
            ->setPassword( self::GMAIL_PASS );

        $mailer = Swift_Mailer::newInstance( $transporter );
        
        $message = Swift_Message::newInstance( $transporter )
            ->setSubject( $subject )
            ->setFrom( Array( 'yetanothernewssite@gmail.com' => 'YANS Team' ) )
            ->setTo( Array( $userData['email'] => $userData['username'] ) )
            ->setBody( $body )
        ;
        
        $result = $mailer->send( $message );
        
        if( !$result ){
            self::log_error( 'Mail failed to send!' );
            return false;
        }
        
        return true;
    }
    
    /* Check whether the user is currently logged in. If they're on a page that requires
     * login and they're not logged in, make them login so they can login while they login.
     */
    function validateLogin(){
        $userInfo = Array();
        
        $login_required = Array( "post.php",
                                 "comment.php",
                                 "settings.php",
                                 "savepost.php",
                                 "savecomment.php" );
        
        // Check for login, and if this is a restricted page, redirect to form.
        if( isset( $_SESSION['userid'] ) ){
            $mysqli = self::dbconnect();
            
            $query = "SELECT `username`, `level` FROM `users` "
                   . "WHERE `id` = '" . self::sanitize( $_SESSION['userid'] ) ."';";
            
            $result = $mysqli->query( $query );
            $userdata = $result->fetch_assoc();
            
            $userInfo['id']       = $_SESSION['userid'];
            $userInfo['username'] = $userdata['username'];
            $userInfo['level']    = $userdata['level'];
            
            $mysqli->close();
        }
        else{
            if( in_array( $_SERVER['PHP_SELF'], $login_required ) ){
                Header( "login.php?r=" . $_SERVER['PHP_SELF'] );
            }
            
            $userInfo['id']       = 0;
            $userInfo['username'] = "Guest";
            $userInfo['level']    = 0;
        }
        
        return $userInfo;
    }
    
    /* Return true if the current user is an admin, false otherwise. */
    function iAmAdmin(){
        $adminIDs = Array( 1, 2 );
        
        return in_array( $_SESSION['userid'], $adminIDs );
    }
    
    /* Update a user's auth token in the database and return this new auth token. */
    function updateAuthToken( $userID ){
        $newToken = self::sanitize( self::genAuthToken() );
        $userID = self::sanitize( $userID );
        $sql = 'UPDATE `users` SET `auth` = "'. $newToken .'" WHERE `id` = '. $userID .';';
        
        if( $mysqli = self::dbconnect() ){
            if( $result = $mysqli->query( $sql ) ){
                return $newToken;
            }
            else{
                self::log_error( "Failed to generate a new auth token.\n" . $sql . "\n\n". $mysqli->error );
            }
        }
        else{
            self::log_error( "Failed to generate a new auth token; couldn't connect to DB." . $mysqli->error );
        }
    }
    
    /* Check a given Auth Token against the user's auth token. This also consumes their auth token. */
    function validateAuthToken( $authToken, $userID ){
        $authToken = self::sanitize( $authToken );
        $userID    = self::sanitize( $userID );
        $sql = 'SELECT `auth` FROM `users` WHERE `id` = '. $userID .';';
        
        if( $mysqli = self::dbconnect() ){
            if( $result = $mysqli->query( $sql ) ){
                $data = $result->fetch_assoc();
                if( $data['auth'] == $authToken ){
                    self::updateAuthToken( $userID );
                    return true;
                }
                else{
                    self::updateAuthToken( $userID );
                    self::log_info( "Auth token invalid. " . $authToken ." vs. " . $data['auth'] );
                    return false;
                }
            }
            else{
                self::log_error( "Failed to validate auth token.\n" . $sql . "\n\n". $mysqli->error );
            }
        }
        else{
            self::log_error( "Failed to validate auth token; couldn't connect to DB." . $mysqli->error );
        }
    }
    
    /* Generate a psuedorandom string to be used as an auth token. */
    function genAuthToken(){
        $timestamp = time();
        $username = $_SESSION['userid'];
        $random = rand(0, 50);
        
        return md5( "YANS" . $timestamp . $username . $random );
    }
}
?>
