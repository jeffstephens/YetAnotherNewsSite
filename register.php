<?php
require_once( 'config.php' );
if( $mysqli = YANS::dbconnect() ){
	if( isset( $_POST['username'] ) ){
		if( empty( $_POST['username'] ) || empty( $_POST['email'] ) ){
			header( "Location:index.php?msg=3" );
			exit();
		}
		if( empty( $_POST['pw'] ) || empty( $_POST['cpw'] ) ){
			header( "Location:index.php?msg=4" );
			exit();
		}

		$name = YANS::sanitize( $_POST['username'] );
		$email = YANS::sanitize( $_POST['email'] );

		$pw1 = YANS::sanitize( $_POST['pw'] );
		$pw2 = YANS::sanitize( $_POST['cpw'] );

		if( $pw1 != $pw2 ){
			header( "Location:index.php?msg=5" );
			exit();
		}

		$query = "SELECT id FROM `users` WHERE `username` = '". $name ."';";
		if( $result = $mysqli->query( $query ) ){
            $num = $result->num_rows;
        }

		if( $num > 0 ){
			header ( "Location:index.php?msg=6" );
			exit();
		}
		else{
            $ip = $_SERVER['REMOTE_ADDR'];
            $parray = YANS::encrypt($pw1);
			$query = "INSERT INTO `users` ( `username`, `pw`, `salt`, `email`, `ip`) "
			       . "VALUES ( '". $name ."', '". $parray[0] ."', '". $parray[1] ."', '". $email ."', '". $ip ."');";
			
			if( $mysqli->query( $query ) ){
			    // Registered. Login!
			    $_SESSION['time'] = time();
			    
			    // Look up new user information
			    $newusersql = 'SELECT `id` FROM `users` WHERE `username` = "' . $name . '";';
			    if( $newuserquery = $mysqli->query( $newusersql ) ){
                    $newuserarray = $newuserquery->fetch_assoc();
                    $welcomemsg = 'Welcome to "Yet Another News Site"! You\'ll soon see why our title is a bit misleading ;)';
                    
                    $_SESSION['userid'] = $newuserarray['id'];
                    
                    if( strlen( $_COOKIE['YANS_Article'] ) ){
                        Header( "Location: article.php?msg=3&aid=" . $_COOKIE['YANS_Article'] );
                    }
                    else{
                        header( "Location:index.php?msg=15" );
                    }
                }
                else{
                    YANS::log_error( "Query to lookup just-created user failed.\n" . $mysqli->error );
                    Header( "Location: index.php" );
                }
				exit();
			}
			else{
			    YANS::log_error( 'Database connection failed. ' . $mysqli->error );
			    Header( "Location:index.php?msg=11" );
			}
		}
		$mysqli->close();
	}
	else{
	    Header( "Location: index.php" );
	    exit();
	}
}
else{
    Header( "Location:index.php?msg=11" );
    exit();
}
/*
<!DOCTYPE HTML>
<html>
<head>
	<title>Registration</title>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
	<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<table width="100%" border="0" cellspacing="7" cellpadding="0">
		<tr class="temptitle">
			<td>New User Registration</td>
		</tr>
		<tr>
			<td>
				<form name="form1" action="register.php" method="post">
					<table width="657" border="0">
						<tr>
							<td width="122"><div align="left">Name</div></td>
							<td width="525"><input name="name" type="text" size="40"></td>
						</tr>
						<tr>
							<td><div align="left">Email</div></td>
							<td><input name="email" type="text" size="40"></td>
						</tr>
						<tr>
							<td><div align="left">Password</div></td>
							<td><input name="pw1" type="password" size="40"></td>
						</tr>
						<tr>
							<td><div align="left">Confirm Password</div></td>
							<td><input name="pw2" type="password" size="40"></td>
						</tr>
						<tr>
							<td></td>
							<td><input name="Submit" type="submit"></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>
</body>
</html>
*/