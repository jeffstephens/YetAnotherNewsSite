<?php
require_once( 'header.php' );

// Maybe we're editing a post!
if( isset( $_GET['edit'] ) ){
    $id = YANS::sanitize( $_GET['edit'] );
    
    if( $mysqli = YANS::dbconnect() ){
        $sql = 'SELECT `author_id`, `title`, `content`, `url` FROM `articles` WHERE `id` = "'. $id .'";';
        if( $result = $mysqli->query( $sql ) ){
            $data = $result->fetch_assoc();
            $title = $data['title'];
            $content = $data['content'];
            $url = $data['url'];
        }
        else{
            YANS::log_error( "Query failed while fetching data to edit a post. \n". $sql ."\n\n" . $mysqli->error );
            Header( "Location: index.php?msg=11" );
        }
    }
    else{
        YANS::log_error( "Database connection failed trying to edit a post.\n" . $sql . "\n\n" . $mysqli->error );
        Header( "Location: index.php?msg=11" );
    }
}
?>
<title>YANS: <?php echo ( isset( $title ) ) ? 'Edit Post' : 'Create Post'; ?></title>
<script type="text/javascript">
titleText = 'Snarky and/or witty title here...';
urlText   = 'Paste a URL here or make a text-only post.'; 
bodyText  = 'Write some interesting content!';

function focusTitle(){
    var elt = document.getElementById( 'title' );
    if( elt.value == titleText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurTitle(){
    var elt = document.getElementById( 'title' );
    if( elt.value == titleText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = titleText;
    }
}

function focusUrl(){
    var elt = document.getElementById( 'url' );
    if( elt.value == urlText || elt.value == '' ){
        elt.style.color = '#000';
        elt.value = '';
    }
}

function blurUrl(){
    var elt = document.getElementById( 'url' );
    if( elt.value == urlText || elt.value == '' ){
        elt.style.color = '#666';
        elt.value = urlText;
    }
}

function focusBody(){
    var elt = document.getElementById( 'body' );
    if( elt.innerHTML == bodyText || elt.innerHTML == '' ){
        elt.style.color = '#000';
        elt.innerHTML = '';
    }
}

function blurBody(){
    var elt = document.getElementById( 'body' );
    if( elt.innerHTML == bodyText || elt.innerHTML == '' ){
        elt.style.color = '#666';
        elt.innerHTML = bodyText;
    }
}
</script>
</head>
<body onload="blurTitle(); blurUrl(); blurBody();">
<h1>Y<span>et</span> A<span>nother</span> N<span>ews</span> S<span>ite</span>: <span><?php echo ( isset( $title ) ) ? 'Edit Post' : 'Create Post'; ?></span> <a href="index.php">home page</a></h1>

<div id="content">
    <div id="box">
        <form action="savepost.php" method="post">
        <input type="hidden" name="authToken" value="<?php echo YANS::updateAuthToken( $userInfo['id'] ); ?>" /><?php
        if( isset( $title ) ){
            echo "\n" . '<input type="hidden" name="id" value="'. $id .'" />';
        }
        ?>
        <input type="text" name="title" id="title" size="60" class="big" onfocus="focusTitle();" onblur="blurTitle();"<?php if( isset( $title ) ){ echo ' value="'. $title .'"'; } ?> />
        <br />
        <input type="text" name="url" id="url" size="60" class="big" onfocus="focusUrl();" onblur="blurUrl();"<?php if( isset( $url ) ){ echo ' value="'. $url .'"'; } ?> />
        <br />
        <textarea name="body" id="body" rows="8" cols="65" class="big" onfocus="focusBody();" onblur="blurBody();"><?php if( isset( $content ) ){ echo $content; } ?></textarea>
        <br />
        <input type="submit" value="Post" />
        </form>
    </div>
</div>

</body>
</html>
