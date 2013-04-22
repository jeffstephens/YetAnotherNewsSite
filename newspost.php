<?php

require_once('config.php');
require_once('comment.php');

class NewsPost
{

public $id;
public $title;
public $content;
public $author;
public $datePosted;
public $url;
public $comments;

function __construct($inId=null, $inTitle=null, $inContent=null, $inAuthor=null, $inDatePosted=null, $inUrl=null, $inComments=null)
{
	if (!empty($inId))
	{
		$this->id = $inId;
	}
	if (!empty($inTitle))
	{
		$this->title = $inTitle;
	}
	if (!empty($inContent))
	{
		$this->content = $inContent;
	}

	if (!empty($inDatePosted))
	{
		$this->datePosted = date(strtotime($inDatePosted));
	}

	if (!empty($inUrl))
	{
		$this->url = $inUrl;
	}

	if (!empty($inAuthor))
	{
		$this->author = $inAuthor;
	}

	if (!empty($inComments))
	{
		$this->comments = $inComments;
	}
}

function getNewsPost($inAuthorId=null, $inArticleId=null)
{    
	if ($mysqli = YANS::dbconnect()) {
		if (!empty($inAuthorId))
		{
		    $inAuthorId = YANS::sanitize( $inAuthorId );
		    $sql = "SELECT 
				users.username AS username, 
				articles.author_id AS author_id, 
				articles.id AS id, 
				articles.title AS title, 
				articles.content AS content, 
				articles.date_posted AS date_posted, 
				articles.url AS url FROM articles JOIN users ON (articles.author_id = users.id) WHERE articles.author_id = '" . $inAuthorId . "' ORDER BY articles.date_posted DESC;";
            YANS::log_info( "Getting one author. query:\n" . $sql );
			if( $query = $mysqli->query($sql) ){
			    ;
			}
			else{
			    YANS::log_error( "Failed to execute query for one author.\n" . $mysqli->error );			    
			}
		}
		else if (!empty($inArticleId)) 
		{
		    $inArticleId = YANS::sanitize( $inArticleId );
		    $sql = "SELECT "
		         . "users.username AS username, "
				 . "articles.author_id AS author_id, "
				 . "articles.id AS id, "
				 . "articles.title AS title, "
				 . "articles.content AS content, "
				 . "articles.date_posted AS date_posted, "
				 . "articles.url AS url FROM articles JOIN users ON (articles.author_id = users.id) WHERE articles.id = '" . $inArticleId . "' ORDER BY articles.date_posted DESC;";
            YANS::log_info( "Getting one article. query:\n" . $sql );
			if( $query = $mysqli->query($sql) ){
			    ;
			}
			else{
			    YANS::log_error( "Failed to execute query for one article.\n" . $mysqli->error );
			}
		}
		else
		{
		    $sql = "SELECT 
				users.username AS username, 
				articles.author_id AS author_id, 
				articles.id AS id, 
				articles.title AS title, 
				articles.content AS content, 
				articles.date_posted AS date_posted, 
				articles.url AS url FROM articles JOIN users ON (articles.author_id = users.id) ORDER BY articles.date_posted DESC;";
			YANS::log_info( "Getting all articles. query:\n" . $sql );
			if( $query = $mysqli->query($sql) ){
			    ;
			}
			else{
			    YANS::log_error( "Failed to execute query for all articles.\n" . $mysqli->error );
			}
		}
		
		$postArray = array();
		while ($row = $query->fetch_assoc())
		{
		    $sql = "SELECT comments.id AS id, 
				comments.content AS content, 
				comments.date_posted AS date_posted, 
				users.username AS author FROM comments JOIN users on (comments.author_id = users.id) WHERE comments.article_id = '" . $row['id'] . "' ORDER BY date_posted DESC;";
			
			if( $cQuery = $mysqli->query($sql) ){
                $comments = array();
    
                while ($cRow = $cQuery->fetch_assoc())
                {
                    $curComment = new Comment($cRow['id'], $cRow['content'], $cRow['author'], $cRow['date_posted']);
                    array_push($comments, $curComment);
                }
                $curPost = new NewsPost($row['id'], $row['title'], $row['content'], $row["username"], $row['date_posted'], $row['url'], $comments);
                array_push($postArray, $curPost);
            }
            else{
                YANS::log_error( "Failed to execute query for article comments.\n" . $mysqli->error );
                YANS::log_info( "Getting comments for an article. query:\n" . $sql );
            }
		}
		return $postArray;
	}
}
}
?>
