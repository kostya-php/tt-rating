<?php
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
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/bootstrap-datepicker.standalone.min.css">
	<link rel="stylesheet" href="css/chosen.min.css">
	<script type="text/JavaScript" src="js/jquery.js"></script>
	<script type="text/JavaScript" src="js/script.js"></script>
	<script type="text/JavaScript" src="js/bootstrap-datepicker.min.js"></script>
	<script type="text/JavaScript" src="js/bootstrap-datepicker.ru.min.js"></script>
	<script type="text/JavaScript" src="js/chosen.jquery.min.js"></script>

</head>

<body>

<ul class="menu">
	<li class="menu-item"><a href="index.php">Главная</a></li>
	<li class="menu-item"><a href="players.php">Игроки</a></li>
	<li class="menu-item"><a href="tournaments.php">Турниры</a></li>
</ul>