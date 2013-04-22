<?php

class Comment
{

public $id;
public $content;
public $author;
public $datePosted;

function __construct($inId=null, $inContent=null, $inAuthor=null, $inDatePosted=null)
{
	if (!empty($inId))
	{
		$this->id = $inId;
	}
	if (!empty($inContent))
	{
		$this->content = $inContent;
	}
	if (!empty($inDatePosted))
	{
		$this->datePosted = date(strtotime($inDatePosted));
	}
	if (!empty($inAuthor))
	{
		$this->author = $inAuthor;
	}
}
}
?>