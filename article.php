<?php
	require_once "header.php";
	require_once "config.php";
	require_once "newspost.php";

	if (isset($_GET['aid'])) {
		$aArray = NewsPost::getNewsPost(null, $_GET['aid']);
		$article = $aArray[0];
	}
	
	// Store this article ID in a cookie that will live for an hour
    setcookie( 'YANS_Article', $_GET['aid'], time() + 3600 );
    
    $authToken = YANS::updateAuthToken( $userInfo['id'] );
?><title>
<?php echo $article->title;?></title>
</head>

<body>

<div id="content">
    <div id="stream">
        <h1>Y<span>et</span> A<span>nother</span> N<span>ews</span> S<span>ite</span> <a href="index.php">home page</a></h1>
        <div id="newsPosts">
        <?php
        
            echo "<h2>" . $article->title . "</h2>";
            
            if( $_SESSION['admin'] == "on" ){
                echo '<p class="admin"><a href="delete.php?article=' . $article->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
            }
            
            elseif( $article->author == $userInfo['username'] ){
                echo '<p class="admin"><a href="post.php?edit=' . $article->id . '">[EDIT]</a>';
                echo '<a href="delete.php?article=' . $article->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
            }
            
            echo '<div class="post">';
            echo '<p class="meta"><strong>' . $article->author . "</strong> <span>at</span> ";
            echo date( 'g:i a l, F jS', $article->datePosted );
            
            if( strlen( $article->url ) ){
                echo '<p class="url"><a href="'. $article->url .'">'. $article->url .' &raquo;</a></p>';
            }
            
            echo "<p>" . nl2br( $article->content ) . "</p>";
            echo "</div>";

        ?>
        </div>
        <div id="comments">
        <?php
        if( isset( $_GET['post'] ) ){
            echo '<h2 id="addcomment">Add a Comment</h2>';
            echo '<form action="savecomment.php" method="post" />' . "\n";
            echo '<input type="hidden" name="articleID" value="'. $_GET['aid'] .'" />';
            echo '<textarea rows="8" cols="50" class="big" name="comment" id="comment"></textarea><br />';
            echo '<input type="submit" value="Add Comment" />';
            echo '</form>';
        }
        else{
            if( count( $article->comments ) > 0 ){
                foreach ($article->comments as $comment)
                {
                    echo '<div class="comment">'; 
                    
                    if( $_SESSION['admin'] == "on" ){
                        echo '<p class="admin"><a href="delete.php?article=' . $article->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
                    }
                    
                    elseif( $comment->author == $userInfo['username'] ){
                        echo '<p class="admin"><a href="editcomment.php?id=' . $comment->id . '">[EDIT]</a>';
                        echo '<a href="delete.php?comment=' . $comment->id .'&amp;authToken='. $authToken .'">[DELETE]</a></p>';
                    }
                                       
                    echo "<h2>" . $comment->author . " | <span>" . date( 'g:i a \o\n n/j/y', $comment->datePosted ) . "</span></h2>";
                    echo "<p>" . nl2br( $comment->content ) . "</p>";
                    echo "</div>";
                }
            }
            else{
                echo '<h2>No comments here!</h2><p><a href="article.php?aid='. $_GET['aid'] .'&amp;post#addcomment">Be the first</a>!</p>';
            }
        }
        ?>
        </div>
    </div>
    <div id="sidebar">
    <?php
        // Output message if there is one
        $msg=$_GET['msg'];
        if( strlen( $msg ) ){
            echo '<p class="notice">';
            switch($msg) {
                case 1:
                    echo "Your comment has been posted.";
                    break;
                case 2:
                    echo "Sorry, but your comment couldn't be posted.";
                    break;
                case 3:
                    echo "We noticed you were reading this recently, so we brought you back here.";
                    break;
                case 4:
                    echo "Your comment has been updated.";
                    break;
            }
        }
    ?>
    <div class="login">
		<?php
		if( $userInfo['id'] == 0 ) {
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
		    
		    echo '<a href="article.php?aid='. $_GET['aid'] .'&amp;post#addcomment">Add a Comment</a>';
		}
		?>
		</div>
    </div>
</div>
</body>

</html>

