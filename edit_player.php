<?php


	// проверка строки на наличие русских символов
	function check_str($s) {
		if (preg_match("/^[а-яёa-z]+$/iu",$s)) {
			return true;
		} else {
			return false;
		}
	}
	
	// проверка на положительное число
	function check_num($a) {
		if(($a>0)and(is_numeric($a))) {
			return true;
		} else {
			return false;
		}
	}
	
	if(isset($_POST['id']))
		if(check_num($_POST['id'])) {
			require_once "mysql.class.php";
			$main = new Mysql();
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
			//echo($_POST['gender']);
			//break;
			if(!check_num($id)) {
				Header ("Location: players.php");	
				break;
			}
			if(!check_str($surname)) {
				Header ("Location: players.php?id=$id");	
				break;
			}
			if(!check_str($name)) {
				Header ("Location: players.php?id=$id");	
				break;
			}
			if(!check_str($patronymic)) {
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
				if(!check_str($note)) {
					Header ("Location: players.php?id=$id");	
					break;
				}
			
			$main->sql_query[1] = "UPDATE players SET surname='$surname', name='$name', patronymic='$patronymic', gender='$gender', birthday='$birthday', reg='$reg', note='$note', photo='$photo' WHERE id='$id'";
			$main->sql_execute(1);
			$main->sql_close();
			Header("Location: players.php?id=$id");			
		}
?>