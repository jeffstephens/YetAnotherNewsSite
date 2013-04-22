<?php
	require_once('config.php');
	if ($mysqli = YANS::dbconnect()) {
		if (true) {
			if ( !strlen( $_POST['username']) || !strlen( $_POST['email'] ) ) {
				header( "Location:index.php?msg=7&forgotpw" );
				exit();
			}
			$name  = YANS::sanitize( $_POST['username'] );
			$email = YANS::sanitize( $_POST['email'] );

			$query = "SELECT `id` FROM users "
			       . "WHERE `username` = '". $name ."' AND `email` = '". $email ."';";
			
			if( $result = $mysqli->query( $query ) ){
                YANS::log_info( 'Changing password; queried ' . $query );
    
                $num = $result->num_rows;
                if ($num == 1) {
                    $pass = YANS::randString(rand(5,12));
                    $msg  = "Someone (hopefully you) requested your password be reset on YANS.";
                    $msg .= "\nYour new password: ". $pass;
    
                    $subject = "YANS Password Reset";
                    
                    $data = $result->fetch_assoc();
                    
                    if ( YANS::changepw( $name, $pass ) ){
                        try{
                            YANS::sendMail( $data['id'], $subject, $msg );
                        }
                        catch( Exception $e ){
                            YANS::log_error( "Caught an exception. Looks like our mail function failed.\n" . $e->getMessage() );
                            Header( "Location: index.php?msg=10" );
                            exit();
                        }
                        header ( "Location:index.php?msg=9&email=" . $email );
                        exit();
                    }
                    else{
                        header( "Location:index.php?msg=10" );
                        exit();
                    }
                }
                else{
                    header( "Location:index.php?msg=8&forgotpw" );
                    exit();
                }
            }
            else{
                YANS::log_error( "Database error when resetting password.\n" . $mysqli->error );
                YANS::log_info( "Query: \n" . $query );
            }
		}
	}
	else{
	    YANS::log_error( "Could not connect to database to reset password.\n" . $mysqli->error );
	    Header("Location:index.php?msg=11");
	}
/*

<!DOCTYPE HTML>
<html>
<head>
	<title>Recover Password</title>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
	<link href="style.css" rel=="stylesheet" type="text/css">
</head>
<body>
	<table width="100%" border="0" cellspacing="7" cellpadding="0">
		<tr class="temptitle">
			<td>Password Recovery</td>
		</tr>
		<tr>
			<td>
				<form name="form1" method="post" action="password.php">Please Fill in the following:<br>
					<table width="445" border="0">
						<tr>
							<td width="187"><div align="left">Username</div></td>
							<td width="242"><input name="name" type="text" size="40"></td>
						</tr>
						<tr>
							<td><div align="left">Email</div></td>
							<td><input name="mail" type="text" size="40"></td>
						</tr>
						<tr>
							<td><input name="Submit" type="submit"></td>
							<td></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</body>
</html>
*/