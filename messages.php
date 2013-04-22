<!DOCTYPE HTML>
<html>
<head>
	<title>Messages</title>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
	<link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
	<table width="100%"  border="0" cellspacing="7" cellpadding="0">
	  <tr class="temptitle">
	    <td>Message</td>
	  </tr>
	  <tr>
	    <td>
	    	<?php 
				$msg=$_GET['msg'];
				switch($msg) {
					case 1:
                        echo "Please enter your username and password.";
                        break;
					case 2:
                        echo "Your username and password do not match, please try again.";
                        break;
					case 3:
                        echo "Please enter your username and password";
                        break;
					case 4:
                        echo "Please enter your password";
                        break;
					case 5:
                        echo "Your confirmation password has been mistyped or is empty, please try again";
                        break;
					case 6:
                        echo "The username you have choosen is already taken, Please choose a new one";
                        break;
					case 7:
                        echo "Please fill in <b>all</b> the fields";
                        break;
					case 8:
                        echo "Your username is either spelled incorrect or does not exist, please try again";
                        break;
					case 9:
                        $em=$_GET['email'];
                        echo "Your password has been sent to <b>$em</b>" ;
                        break;
					case 10:
                        echo "There was a error while trying to send the message, please check your mail settings.";
                        break;
					case 11:
                        echo "The database is currently unreachable. Please try again later.";
                        break;
                    case 12:
                        echo "Your content has been posted!";
                        break;
				}
			?>
		</td>
	  </tr>
	</table>
</body>
</html>
