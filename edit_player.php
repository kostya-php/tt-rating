<?php
	require_once "mysql.class.php";
	$main = new Mysql();
	if(isset($_POST['id']))
		if($main->check_num($_POST['id'])) {
			$main->sql_connect();
			$id = $_POST['id'];
			$surname = $_POST['surname'];
			$name = $_POST['name'];
			$patronymic = $_POST['patronymic'];
			$gender = $_POST['gender'];
			$birthday = $_POST['birthday'];
			$reg = $_POST['reg'];
			$note = $_POST['note'];
			$photo = $_POST['photo'];
			if(!$main->check_num($id)) {
				Header ("Location: players.php");	
				break;
			}
			if(!$main->check_str($surname)) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if(!$main->check_str($name)) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if(!$main->check_str($patronymic)) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if(($gender!="male")and($gender!="female")) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if(!$main->validateDate($birthday, "Y-m-d")) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if(!$main->validateDate($reg, "Y-m-d")) {
				Header ("Location: players.php?id=$id");
				break;
			}
			if($note!="")
				if (!preg_match("/^[0-9а-яёa-z-\s\/.,()]+$/iu",$note)) {
					Header ("Location: players.php?id=$id");
					break;
				}
			if($note!="")
				if (!preg_match("/^[a-z-\/.:]+$/iu",$photo)) {
					Header ("Location: players.php?id=$id");
					break;
				}
			$main->sql_query[1] = "UPDATE players SET surname='$surname', name='$name', patronymic='$patronymic', gender='$gender', birthday='$birthday', reg='$reg', note='$note', photo='$photo' WHERE id='$id'";
			$main->sql_execute(1);
			$main->sql_close();
			Header("Location: players.php?id=$id");
		}
?>