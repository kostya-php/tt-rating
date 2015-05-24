<?php
	
	$page_name = "Игроки";
	
	include "1_header.php";
	
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
	
	require_once "mysql.class.php";
	$main = new Mysql();
	$main->sql_connect();
	
	if(isset($_GET['id'])) {
		if(check_num($_GET['id'])) {
			// форма редактирования игрока
			$id = $_GET['id'];
			$main->sql_query[1] = "SELECT * FROM players WHERE id='$id'";
			$main->sql_execute(1);
			$row = mysql_fetch_array($main->sql_res[1]);
			$check1 = "";
			$check2 = "";
			switch ($row['gender']) {
				case 'male': $check1 = " CHECKED"; break;
				case 'female': $check2 = " CHECKED"; break;
			}
			echo "<form action=\"edit_player.php\" method=\"post\" style=\"margin-top:10px;\">";
			echo "<input name=\"id\" type=\"hidden\" value=\"".$row['id']."\">";
			echo "<table>";
				echo "<tr>";
					echo "<td>Фамилия:</td><td><input name=\"surname\" type=\"text\" value=\"".$row['surname']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Имя:</td><td><input name=\"name\" type=\"text\" value=\"".$row['name']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Отчество:</td><td><input name=\"patronymic\" type=\"text\" value=\"".$row['patronymic']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Пол:</td><td><input name=\"gender\" type=\"radio\" value=\"male\"$check1>Мужской<input name=\"gender\" type=\"radio\" value=\"female\"$check2>Женский</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Дата рождения:</td><td><input name=\"birthday\" type=\"text\" value=\"".$row['birthday']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Дата регистрации:</td><td><input name=\"reg\" type=\"text\" value=\"".$row['reg']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Заметки:</td><td><textarea name=\"note\">".$row['note']."</textarea></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Фото:</td><td><input name=\"photo\" type=\"text\" value=\"".$row['photo']."\"></td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td colspan=\"2\" style=\"text-align:center\"><input type=\"submit\" value=\"Отредактировать\"></td>";
				echo "</tr>";
			echo "</table>";
			echo "</form>";
		}
	} else {
		// отображение игроков
		$page = 1;
		$search = "-";
		if(isset($_GET['search']))
			if(check_str($_GET['search']))
				$search = $_GET['search'];
		if(isset($_GET['players_page']))
			if(check_num($_GET['players_page']))
				$page = $_GET['players_page'];

		// делаем выборку 15-ти игроков
		$main->sql_query[1] = "SELECT SQL_CALC_FOUND_ROWS * FROM players";
		if($search!="-") {
			$main->sql_query[1].=" WHERE (surname LIKE '%".$search."%' OR name LIKE '%".$search."%' OR patronymic LIKE '%".$search."%')";
		}
		$main->sql_query[1].=" LIMIT ".(($page-1)*15).",15";
		$main->sql_execute(1);
		
		$main->sql_query[2] = "SELECT FOUND_ROWS()";
		$main->sql_execute(2);
		
		$n = mysql_result($main->sql_res[2],0);
		$pages = ceil($n/15);
		echo "<p>Игроков: $n</p>";
		echo "<p>Страниц: $pages</p>";
		
		echo "<table style=\"min-width: 400px;\">";
		echo "<tr>";
		echo "<td colspan=\"2\">";
		echo "<form method=\"get\">";
		echo "Поиск: <input type=\"text\" name=\"search\" value=\"\" style=\"width:85%;\">";
		echo "</form>";
		echo"</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td style=\"width:25px;\"><b>#</b></td>";
		echo "<td><b>Игрок</b></td>";
		echo "</tr>";
		
		for($i=0;$i<15;$i++) {
			if($row = mysql_fetch_array($main->sql_res[1])) {
				echo "<tr>";
				echo "<td style=\"width:25px;\">".$row['id']."</td>";
				echo "<td><a href=\"players.php?id=".$row['id']."\">".$row['surname']." ".$row['name']." ".$row['patronymic']."</a></td>";
				echo "</tr>";
			} else {
				echo "<tr>";
				echo "<td colspan=\"2\">&nbsp</td>";
				echo "</tr>";
			}
		}
		
		echo "<tr>";
		echo "<td colspan=\"2\" style=\"padding:0px;\">";
		
		// пагинатор
		echo "<table style=\"width:100%;border:0px;\">";
		echo "<tr>";	
		for($i=0;$i<=$pages+1;$i++) {
			echo "<td style=\"width:25px;text-align:center;border:0px;\">";
			
			if($i==0) {
				if(($i+1)==$page) {
					echo "<=";
				} else {
					if($search=="-") {
						echo "<a href=\"?players_page=".($page-1)."\"><=</a>";
					} else {
						echo "<a href=\"?search=".$search."&players_page=".($page-1)."\"><=</a>";
					}
				}
			} else
			if($i==$pages+1) {
				if(($i-1)==$page) {
					echo "=>";
				} else {
					if($search=="-") {
						echo "<a href=\"?players_page=".($page+1)."\">=></a>";
					} else {
						echo "<a href=\"?search=".$search."&players_page=".($page+1)."\">=></a>";
					}
				}
			} else {
				if($i==$page) {
					echo "<b>[$i]</b>";
				} else {
					if($search=="-") {
						echo "<a href=\"?players_page=$i\">[$i]</a>";
					} else {
						echo "<a href=\"?search=".$search."&players_page=$i\">[$i]</a>";
					}
				}
			}
			echo "</td>";
		}
		
		echo "</tr>";
		echo "</table>";

		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}
	$main->sql_close();
	
	include "2_footer.php";
?>