<?php

	$page_name = "Добавить игрока";
	
	include "1_header.php";
	
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		require_once "mysql.class.php";
		$main = new Mysql();
		
		if(isset($_POST['surname']))$surname = $_POST['surname']; else $surname = "";
		if(isset($_POST['name']))$name = $_POST['name']; else $name = "";
		if(isset($_POST['patronymic']))$patronymic = $_POST['patronymic']; else $patronymic = "";
		if(isset($_POST['gender']))$gender = $_POST['gender']; else $gender = "";
		if(isset($_POST['birthday']))$birthday = $_POST['birthday']; else $birthday = "";
		if(isset($_POST['reg']))$reg = $_POST['reg']; else $reg = "";
		if(isset($_POST['note']))$note = $_POST['note']; else $note = "";
		if(isset($_POST['photo']))$photo = $_POST['photo']; else $photo = "";
		
		$error = false;

		if(!$main->check_str($surname)) {
			$error = true;
			echo "<p style=\"color:red;\">Неверно заполнено поле \"Фамилия\"</p>";
		}
		if(!$main->check_str($name)) {
			$error = true;
			echo "<p style=\"color:red;\">Неверно заполнено поле  \"Имя\"</p>";
		}
		if(!$main->check_str($patronymic)) {
			$error = true;
			echo "<p style=\"color:red;\">Неверно заполнено поле \"Отчество\"</p>";
		}
		$check1 = "";
		$check2 = "";
		if(($gender!="male")and($gender!="female")) {
			$error = true;
			echo "<p style=\"color:red;\">Не выбран пол</p>";
		} else {
			switch($gender) {
				case "male"; $check1 = " CHECKED";break;
				case "female"; $check2 = " CHECKED";break;
			}
		}
		$translit_name = $main->rus2translit($surname." ".$name." ".$patronymic);
		if(!$main->validateDate($birthday, "Y-m-d")) {
			$error = true;
			echo "<p style=\"color:red;\">Неверно заполнено поле  \"Дата рождения\"</p>";
		}
		if(!$main->validateDate($reg, "Y-m-d")) {
			$error = true;
			echo "<p style=\"color:red;\">Неверно заполнено поле \"Дата регистрации\"</p>";
		}
		if($note!="")
			if (!preg_match("/^[0-9а-яёa-z-\s\/.,()]+$/iu",$note)) {
				$error = true;
				echo "<p style=\"color:red;\">Неверно заполнено поле \"Примечание\"</p>";
			}
		if($photo!="")
			if (!preg_match("/^[0-9a-z-\/.:]+$/iu",$photo)) {
				$error = true;
				echo "<p style=\"color:red;\">Неверно заполнено поле \"Фото\"</p>";
			}
			
		if($error) {
			echo "<h2>Добавить игрока</h2>";
			echo "<form action=\"add_player.php\" method=\"post\" style=\"margin-top:10px;\">";
			echo "<table>";
				echo "<tr>";
					echo "<td>Фамилия*:</td><td><input name=\"surname\" type=\"text\" value=\"$surname\" style=\"width:200px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Имя*:</td><td><input name=\"name\" type=\"text\" value=\"$name\" style=\"width:200px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Отчество*:</td><td><input name=\"patronymic\" type=\"text\" value=\"$patronymic\" style=\"width:200px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Пол*:</td><td><input name=\"gender\" type=\"radio\" value=\"male\"$check1>Мужской<input name=\"gender\" type=\"radio\" value=\"female\"$check2>Женский</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Дата рождения*:</td><td><input class=\"datepicker\" name=\"birthday\" type=\"text\" value=\"$birthday\" style=\"width:70px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Дата регистрации*:</td><td><input class=\"datepicker\" name=\"reg\" type=\"text\" value=\"$reg\" style=\"width:70px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Заметки:</td><td><textarea name=\"note\" style=\"width:200px;\">$note</textarea></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Фото:</td><td><input name=\"photo\" type=\"text\" value=\"$photo\" style=\"width:200px;\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td colspan=\"2\" style=\"text-align:center\"><input type=\"submit\" value=\"Добавить\"></td>";
				echo "</tr>";
			echo "</table>";
			echo "</form>";
		} else {
			$main->sql_connect();
			$query = mysql_query("SHOW TABLE STATUS FROM `".$main->sql_database."` LIKE 'players'");
			$nextid = mysql_result($query, 0, "Auto_increment");
			echo $nextid;
			$main->sql_query[1] = "INSERT INTO players VALUES(null,'$surname','$name','$patronymic','$gender','$translit_name','$birthday','$reg','$note','$photo')";
			$main->sql_execute(1);
			$main->sql_close();
			Header("Location: players.php?id=$nextid");
		}
	} else {
		echo "<h2>Добавить игрока</h2>";
		echo "<form action=\"add_player.php\" method=\"post\" style=\"margin-top:10px;\">";
		echo "<table>";
			echo "<tr>";
				echo "<td>Фамилия*:</td><td><input name=\"surname\" type=\"text\" value=\"\" style=\"width:200px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Имя*:</td><td><input name=\"name\" type=\"text\" value=\"\" style=\"width:200px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Отчество*:</td><td><input name=\"patronymic\" type=\"text\" value=\"\" style=\"width:200px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Пол*:</td><td><input name=\"gender\" type=\"radio\" value=\"male\">Мужской<input name=\"gender\" type=\"radio\" value=\"female\">Женский</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Дата рождения*:</td><td><input class=\"datepicker\" name=\"birthday\" type=\"text\" value=\"\" style=\"width:70px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Дата регистрации*:</td><td><input class=\"datepicker\" name=\"reg\" type=\"text\" value=\"\" style=\"width:70px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Заметки:</td><td><textarea name=\"note\" style=\"width:200px;\"></textarea></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Фото:</td><td><input name=\"photo\" type=\"text\" value=\"\" style=\"width:200px;\"></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan=\"2\" style=\"text-align:center\"><input type=\"submit\" value=\"Добавить\"></td>";
			echo "</tr>";
		echo "</table>";
		echo "</form>";
	}
	
	include "2_footer.php";
?>