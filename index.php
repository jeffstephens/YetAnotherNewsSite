<?php
require_once( 'header.php' );
require_once( 'newspost.php' );

$authToken = YANS::updateAuthToken( $userInfo['id'] );
?><title><?php
			$i = rand(1,7);
			switch($i) {
				case 1:
                    echo "The Magical YANS";
                    break;
				case 2:
                    echo "The Mysterious YANS";
                    break;
				case 3:
                    echo "The Sometimes Scary YANS";
                    break;
				case 4:
                    echo "The Slightly Impaired YANS";
                    break;
				case 5:
                    echo "The Great and Powerful YANS";
                    break;
				case 6:
                    echo "The Whimsical YANS";
                    break;
				case 7:
                    echo "The Fanciful and Quaint YANS";
                    break;
			}
		?></title>
<script type="text/javascript">
usernameText = 'Username';
passwordText = 'Password';
emailText = 'Email Address';

function focusUsername(){
    var elt = document.getElementById( 'username' );
    if( elt.value == usernameText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurUsername(){
    var elt = document.getElementById( 'username' );
    if( elt.value == usernameText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = usernameText;
    }
}

function focusPassword(){
    var elt = document.getElementById( 'pw' );
    if( elt.value == passwordText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurPassword(){
    var elt = document.getElementById( 'pw' );
    if( elt.value == passwordText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = passwordText;
    }
}

function focusEmail(){
    var elt = document.getElementById( 'email' );
    if( elt.value == emailText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurEmail(){
    var elt = document.getElementById( 'email' );
    if( elt.value == emailText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = emailText;
    }
}

function focusCPassword(){
    var elt = document.getElementById( 'cpw' );
    if( elt.value == passwordText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurCPassword(){
    var elt = document.getElementById( 'cpw' );
    if( elt.value == passwordText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = passwordText;
    }
}
</script>
</head>

<body onload="blurUsername();<?php if( !isset($_GET['forgotpw']) ){ echo 'blurPassword();'; } if( isset( $_GET['reg'] ) ){ echo 'blurEmail();blurCPassword();'; } if( isset($_GET['forgotpw']) ){ echo 'blurEmail();'; } ?>">
<div id="content">
    <div id="stream">
        <h1>Y<span>et</span> A<span>nother</span> N<span>ews</span> S<span>ite</span></h1>
        <div id="newsPosts">
        <?php
        
        $newsPosts = NewsPost::getNewsPost();
        
        foreach ($newsPosts as $post)
        {
            echo "<h2><a href=\"article.php?aid=" . $post->id ."\">" . $post->title . " &raquo;</a></h2>";
            
            if( $_SESSION['admin'] == "on" ){
                echo '<p class="admin"><a href="delete.php?article=' . $post->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
            }
            
            elseif( $post->author == $userInfo['username'] ){
                echo '<p class="admin"><a href="post.php?edit=' . $post->id . '">[EDIT]</a>';
                echo '<a href="delete.php?article=' . $post->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
            }
            
            echo '<div class="post">';
            echo '<p class="meta"><strong>' . $post->author . "</strong> <span>at</span> ";
            echo date( 'g:i a l, F jS', $post->datePosted ) . ' <span>with</span> <a href="article.php?aid=' . $post->id .'">';
            
            $comments = sizeof( $post->comments );
            
            if( $comments == 0 ){
                echo "no comments";
            }
            else if( $comments == 1 ){
                echo "one comment";
            }
            else{
                echo $comments . " comments";
            }
            
            // Trim content to 500 ±20 characters
            $content = $post->content;
            if( strlen( $content ) > 520 ){
                $content_words = explode( ' ', $content );
                $content_trimmed = '';
                $i = 0;
                while( strlen( $content_trimmed ) < 480 ){
                    if( strlen( $content_trimmed ) > 520 ){
                        $content_trimmed = substr( $content, 0, 500 );
                        break;
                    }
                    $content_trimmed .= " " . $content_words[$i];
                    $i++;
                }
                $content_trimmed .= '... <a href="article.php?aid=' . $post->id .'" class="readmore">read more &raquo;</a>';
            }
            else{
                $content_trimmed = $content;
            }
            
            echo " &raquo;</a></p>";
            
            if( strlen( $post->url ) ){
                echo '<p class="url"><a href="'. $post->url .'">'. $post->url .' &raquo;</a></p>';
            }
            
            echo "<p>" . trim( nl2br( $content_trimmed ) ) . '</p>';
            echo "</div>";
        }
        
        if( count( $post ) == 0 ){
            echo "<h2>There's nothing here!</h2><p>Why not post something and get the ball rolling?</p>";
        }
        ?>
        </div>
    </div><!-- /div id="stream" -->
    <div id="sidebar">
        <?php
        // Output message if there is one
        $msg=$_GET['msg'];
        if( strlen( $msg ) ){
            echo '<p class="notice">';
            switch($msg) {
                case 1:
                    echo "Please enter your username and password.";
                    break;
                case 2:
                    echo "Your username and password do not match, please try again.";
                    break;
                case 3:
                    echo "Please enter your username and password.";
                    break;
                case 4:
                    echo "Please enter your password.";
                    break;
                case 5:
                    echo "Your confirmation password has been mistyped or is empty, please try again.";
                    break;
                case 6:
                    echo "The username you have choosen is already taken. Please choose a new one.";
                    break;
                case 7:
                    echo "Please fill in <b>all</b> the fields";
                    break;
                case 8:
                    echo "Your username is either spelled incorrect or does not exist, please try again.";
                    break;
                case 9:
                    $em=$_GET['email'];
                    echo "Your password has been sent to <b>$em</b>" ;
                    break;
                case 10:
                    echo "Sorry, but we failed to send an email.";
                    break;
                case 11:
                    echo "The database is currently unreachable. Please try again later.";
                    break;
                case 12:
                    echo "Your post has been created!";
                    break;
                case 13:
                    echo "Please provide an email address and confirm your password to make your account.";
                    break;
                case 14:
                    echo "Logout successful. Come back soon!";
                    break;
                case 15:
                    echo "You're signed up and logged in. Welcome!";
                    break;
                case 16:
                    echo "Your post has been updated.";
                    break;
                case 17:
                    echo "The entry and its comments were deleted.";
                    break;
                case 18:
                    echo "The comment was deleted.";
                    break;
                case 19:
                    echo "You're not authorized to do that.";
                    break;
            }
            echo '</p>';
        }
		?>
		<div class="login">
		<?php
		if( isset( $_GET['forgotpw'] ) ){
		    echo '<form name="form1" method="post" action="password.php">';
		    echo '<input name="username" type="text" class="big" id="username" size="25" onfocus="focusUsername();" onblur="blurUsername();" />';
		    echo "\n<br />\n";
		    echo '<input name="email" type="text" class="big" id="email" size="25" onfocus="focusEmail();" onblur="blurEmail();" /><br />' . "\n";
		    echo '<input type="submit" value="Reset Password" />';
		    echo '</form>';
		}
		else if( $userInfo['id'] == 0 ) {
            echo '<form name="form1" method="post" action="';
            if( isset( $_GET['reg'] ) ){ 
                echo 'register.php';
            }
            else{
                echo 'login.php';
            }
            echo '">
            <input name="username" type="text" class="big" id="username" size="25" onfocus="focusUsername();" onblur="blurUsername();"';
            if( isset( $_GET['username'] ) ){
                echo ' value="' . $_GET['username'] .'"';
            }
            echo ' /><br />';
            if( isset( $_GET['reg'] ) ){
                echo '<input name="email" type="text" class="big" id="email" size="25" onfocus="focusEmail();" onblur="blurEmail();" /><br />' . "\n";
            }            
            echo '<input name="pw" type="password" class="big" id="pw" size="25" onfocus="focusPassword();" onblur="blurPassword();" /><br />';
            if( isset( $_GET['reg'] ) ){
                echo '<input name="cpw" type="password" class="big" id="cpw" size="25" onfocus="focusCPassword();" onblur="blurCPassword();" /><br />' . "\n";
            }            
            echo '<input type="submit" name="submit" value="Login/Sign Up" /> <a href="index.php?forgotpw">Forgot Password?</a><br />
		</form>';
		}
		
		else{
		    echo 'Welcome, ' . $userInfo['username'] . '. <a href="logout.php">Logout</a><br /><br />';
		    
		    if( YANS::iAmAdmin() ){
		        if( $_SESSION['admin'] == "on" ){
		            echo '<strong>Careful! You\'re admin.</strong> <a href="admin.php">Back to Normal</a><br /><br />';
		        }
		        else{
		            echo '<a href="admin.php">Elevate to Admin</a><br /><br />';
		        }
		    }
		    
		    echo '<a href="post.php">Add Article</a>';
		}
		?>
		</div>
    </div><!-- /div id="sidebar" -->
</div>

</body>

</html>
