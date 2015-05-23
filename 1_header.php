﻿<?php
	// для отладки
	ini_set("error_reporting", E_ALL);
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
?>
<!DOCTYPE html>
<html>

<head>

	<title><?php echo $page_name ?></title>
	<meta charset="utf-8">
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="style.css">
	<script type="text/JavaScript" src="script.js"></script>
</head>

<body>

<ul class="menu">
	<li class="menu-item"><a href="index.php">Главная</a></li>
	<li class="menu-item"><a href="players.php">Игроки</a></li>
	<li class="menu-item"><a href="tournaments.php">Турниры</a></li>
</ul>