<?php
	$page_name = "Игроки";
	include "1_header.php";
	require_once "mysql.class.php";
	$main = new Mysql();
	$main->sql_connect();
	if(isset($_GET['id'])) {
		if($main->check_num($_GET['id'])) {
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
			$surname = $row['surname'];
			$name = $row['name'];
			$patronymic = $row['patronymic'];
			$birthday = $row['birthday'];
			$reg = $row['reg'];
			$note = $row['note'];
			$photo = $row['photo'];
			echo <<<ATATA
<h2>Редактирование игрока "$surname $name $patronymic"</h2>
</form>
<form action="delete_player.php" method="post">
<input type="submit" value="Удалить игрока" style="font-size: 10px;color: red;">
</form>
<form action="edit_player.php" method="post" style="margin-top:10px;">
<input name="id" type="hidden" value="$id">
<table>
	<tr>
		<td>Фамилия:</td><td><input name="surname" type="text" value="$surname" style="width:200px;"></td>
	</tr>
	<tr>
		<td>Имя:</td><td><input name="name" type="text" value="$name" style="width:200px;"></td>
	</tr>
	<tr>
		<td>Отчество:</td><td><input name="patronymic" type="text" value="$patronymic" style="width:200px;"></td>
	</tr>
	<tr>
		<td>Пол:</td><td><input name="gender" type="radio" value="male"$check1>Мужской<input name="gender" type="radio" value="female"$check2>Женский</td>
	</tr>
	<tr>
		<td>Дата рождения:</td><td><input class="datepicker" name="birthday" type="text" value="$birthday" style="width:70px;"></td>
	</tr>
	<tr>
		<td>Дата регистрации:</td><td><input class="datepicker" name="reg" type="text" value="$reg" style="width:70px;"></td>
	</tr>
	<tr>
		<td>Заметки:</td><td><textarea name="note" style="width:200px;">$note</textarea></td>
	</tr>
	<tr>
		<td>Фото:</td><td><input name="photo" type="text" value="$photo" style="width:200px;"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center"><input type="submit" value="Отредактировать" style="color: blue;"></td>
	</tr>
</table>
ATATA;
		}
	} else {
		// отображение списка игроков
		$page = 1;
		$search = "-";
		if(isset($_GET['search']))
			if($main->check_str($_GET['search']))
				$search = $_GET['search'];
		if(isset($_GET['players_page']))
			if($main->check_num($_GET['players_page']))
				$page = $_GET['players_page'];

		// делаем выборку 15-ти игроков с учетом поиска и номера страницы
		$main->sql_query[1] = "SELECT SQL_CALC_FOUND_ROWS * FROM players";
		if($search!="-") {
			$main->sql_query[1].=" WHERE (surname LIKE '%".$search."%' OR name LIKE '%".$search."%' OR patronymic LIKE '%".$search."%')";
		}
		$main->sql_query[1].=" LIMIT ".(($page-1)*15).",15";
		$main->sql_execute(1);
		// узнаем количество игроков
		$main->sql_query[2] = "SELECT FOUND_ROWS()";
		$main->sql_execute(2);
		$n = mysql_result($main->sql_res[2],0);
		// вычисляем необходимое количество страниц
		$pages = ceil($n/15);
		// далее генерируем таблицу и выводим список 15-ти игроков
		echo <<<ATATA
<h2>Игроки</h2>
<table style="min-width: 400px;margin-top: 10px;">
	<tr>
		<td colspan="2">
			<form method="get">
			Поиск: <input type="text" name="search" value="" style="width:85%;">
			</form>
		</td>
	</tr>
	<tr>
		<td style="width:25px;"><b>#</b></td>
		<td><b>Игрок</b></td>
	</tr>
ATATA;
		for($i=0;$i<15;$i++) {
			if($row = mysql_fetch_array($main->sql_res[1])) {
				$id = $row['id'];
				$player = $row['surname']." ".$row['name']." ".$row['patronymic'];
				echo <<<ATATA
				
	<tr>
		<td style="width:25px;">$id</td>
		<td><a href="players.php?id=$id">$player</a></td>
	</tr>
ATATA;
			} else {
				echo <<<ATATA
	<tr>
		<td colspan="2">&nbsp</td>
	</tr>
ATATA;
			}
		}
		echo <<<ATATA
\n	<tr>
		<td colspan="2" style="padding:0px;">
ATATA;
		// пагинатор
		echo <<<ATATA
\n		<table style="width:100%;border:0px;">
			<tr>\n
ATATA;
		for($i=0;$i<=$pages+1;$i++) {
			echo "				<td style=\"width:25px;text-align:center;border:0px;\">";
			if($i==0) {
				if(($i+1)==$page) {
					echo "&lt;=";
				} else {
					if($search=="-") {
						echo "<a href=\"?players_page=".($page-1)."\">&lt;=</a>";
					} else {
						echo "<a href=\"?search=".$search."&players_page=".($page-1)."\">&lt;=</a>";
					}
				}
			} else
			if($i==$pages+1) {
				if(($i-1)==$page) {
					echo "=&gt;";
				} else {
					if($search=="-") {
						echo "<a href=\"?players_page=".($page+1)."\">=&gt;</a>";
					} else {
						echo "<a href=\"?search=".$search."&players_page=".($page+1)."\">=&gt;</a>";
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
			echo "</td>\n";
		}
		echo <<<ATATA
				<td style="width:150px;text-align:center;border:0px;"><a href="add_player.php">Добавить игрока</a></td>
			</tr>
		</table>
		</td>
	</tr>
</table>\n
ATATA;
	}
	$main->sql_close();
	include "2_footer.php";
?>