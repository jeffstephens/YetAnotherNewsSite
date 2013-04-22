<?php
// This header file will be included at the top of all pages behind the login wall.
// It does things like check login status (including bans)
// It also includes required scripts and stylesheets

// No closing PHP tag to prevent extraneous whitespace

session_start();
require_once( 'config.php' );

$userInfo = YANS::validateLogin();
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link type="text/css" rel="stylesheet" href="style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
