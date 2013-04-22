<?php
require_once( 'config.php' );
require_once( 'header.php' );

if( isset( $_GET['id'] ) ){
    $id = YANS::sanitize( $_GET['id'] );
    if( $mysqli = YANS::dbconnect() ){
        $sql = 'SELECT `author_id`, `article_id`, `content` FROM `comments`
                WHERE `id` = '. $id .';';
        if( $result = $mysqli->query( $sql ) ){
            $data = $result->fetch_assoc();
            
            // Security check
            if( $data['author_id'] != $userInfo['id'] ){
                YANS::log_error( "Someone's poking around and trying to edit a comment they don't own." );
                Header( "Location: index.php" );
                exit();
            }
            
            $articleID = $data['article_id'];
            $content = $data['content'];
        }
        else{
            YANS::log_error( "Database error when looking up comment to edit.\n" . $mysqli->error );
        }
    }
    else{
        YANS::log_error( "Failed to connect to database to edit a comment.\n" . $mysqli->error );
        Header( "Location: index.php?msg=11" );
    }
}
elseif( isset( $_POST['commentID'] ) ){
    if( YANS::validateAuthToken( $_POST['authToken'], $userInfo['id'] ) ){
        $id = YANS::sanitize( $_POST['commentID'] );
        $articleID = YANS::sanitize( $_POST['articleID'] );
        $content = YANS::sanitize( $_POST['comment'] );
        
        $sql = 'UPDATE `comments` SET `content` = "'. $content .'"
                WHERE `id` = "'. $id .'" AND `author_id` = "'. YANS::sanitize( $userInfo['id'] ) .'" AND `article_id` = '. $articleID .';';
        
        if( $mysqli = YANS::dbconnect() ){
            if( $result = $mysqli->query( $sql ) ){
                Header( "Location: article.php?aid=" . $articleID ."&msg=4#comments" );
            }
            else{
                YANS::log_error( "Database error when updating a comment.\n" . $sql . "\n\n" . $mysqli->error );
                Header( "Location: index.php?msg=11" );
            }
        }
        else{
            YANS::log_error( "Failed to connect to database to update a comment.\n". $mysqli->error );
            Header( "Location: index.php?msg=11" );
        }
    }
    else{
        Header( "Location: index.php?msg=19" );
        exit();
    }
}
else{
    Header( "Location: index.php" );
    // Weird but IDGAF.
}
?><title>YANS: Edit Comment</title>
</head>
<body>
<h1>Y<span>et</span> A<span>nother</span> N<span>ews</span> S<span>ite</span>: <span>Edit Comment <a href="index.php">home page</a></h1>

<div id="content">
    <div id="box">
        <form action="editcomment.php" method="post" />
        <input type="hidden" name="authToken" value="<?php echo YANS::updateAuthToken( $userInfo['id'] ); ?>" />
        <input type="hidden" name="commentID" value="<?php echo $id; ?>" />
        <input type="hidden" name="articleID" value="<?php echo $articleID; ?>" />
        <textarea rows="8" cols="50" class="big" name="comment" id="comment"><?php echo $content; ?></textarea><br />
        <input type="submit" value="Update Comment" />
        </form>
    </div>
</div>

</body>
</html>
