<?php
	$page_name = "Турниры";
	include "1_header.php";
	require_once "mysql.class.php";
	$main = new Mysql();
	$main->sql_connect();
	if(isset($_GET['id'])) {
		// ================================
		// если выбран турнир, вывести о нем информацию
		// ================================
		if(is_numeric($_GET['id'])) {
			// =============
			// если id корректный
			// =============
			$tournament = $_GET['id'];
			$main->sql_query[1] = "SELECT * FROM tournaments WHERE id='$tournament'";
			$main->sql_execute(1);
			if(mysql_num_rows($main->sql_res[1])>0) {
				$row = mysql_fetch_array($main->sql_res[1]);
				echo "<h2>Турнир \"".$row['name']."\"</h2>";
				$main->sql_query[1] = "SELECT * FROM in_tournament LEFT JOIN players ON players.id=in_tournament.player WHERE tournament='$tournament'";
				$main->sql_execute(1);
				echo $main->sql_err[1];
				echo "<p><b>Игроки на турнире:</b></p>";
				echo "<ol>";
				while ($row = mysql_fetch_array($main->sql_res[1])) {
					echo "<li>".$row['surname']." ".$row['name']." ".$row['patronymic']."</li>";
				}
				echo "</ol>";
				echo "<p><b>Игры:</b></p>";
				echo <<<ATATA
<table>
	<tr>
		<!--<td>id</td>-->
		<td><b>#</b></td>
		<td><b>Игрок 1</b></td>
		<td><b>Результат</b></td>
		<td><b>Игрок 2</b></td>
		<td><b>Статус</b></td>
		<td><b>Действия</b></td>
	</tr>
ATATA;
				$main->sql_query[1] = "SELECT matches.*,
					t1.surname as surname1,
					t1.name as name1,
					t1.patronymic as patronymic1,
					t2.surname as surname2,
					t2.name as name2,
					t2.patronymic as patronymic2
				FROM matches
				LEFT JOIN players AS t1 ON t1.id=matches.player1
				LEFT JOIN players AS t2 ON t2.id=matches.player2
				WHERE tournament='$tournament'";
				$main->sql_execute(1);
				while ($row = mysql_fetch_array($main->sql_res[1])) {
					$id = $row['id'];
					$number = $row['number'];
					$player1 = $row['surname1']." ".$row['name1'];//." ".$row['patronymic1'];
					$res = $row['x'].":".$row['y']."<br />(".$row['rounds'].")";
					$player2 = $row['surname2']." ".$row['name2'];//." ".$row['patronymic2'];
					$status = $row['status'];
					echo <<<ATATA
	<tr>
		<!--<td>$id</td>-->
		<td style="text-align:center;padding:3px;">$number</td>
		<td style="text-align:center;padding:3px;">$player1</td>
		<td style="text-align:center;padding:3px;">$res</td>
		<td style="text-align:center;padding:3px;">$player2</td>
		<td style="text-align:center;padding:3px;">$status</td>
		<td style="text-align:center;padding:3px;"><a href="matches.php?id=$id">[Р]</a></td>
	</tr>
ATATA;
				}
				echo <<<ATATA
</table>
ATATA;
			}
		} else {
			// ==============
			// если некорректный id
			// ==============
			Header ("Location: tournaments.php");
		}
	} else {
		// ===============================
		// если не выбран турнир, отобразить все турниры
		// ===============================
		$main->sql_query[1] = "SELECT * FROM tournaments ORDER BY date ASC";
		$main->sql_execute(1);
		echo <<<ATATA
<h2>Турниры</h2>
<button onclick="location.href='add_tournament.php'" style="color:blue;font-size:10px;">Добавить турнир</button>
<table style="min-width: 400px;margin-top: 10px;">
<tr>
	<td><b>Дата</b></td>
	<td><b>Название турнира</b></td>
</tr>
ATATA;
		$i = 0;
		while ($row = mysql_fetch_array($main->sql_res[1])) {
			$i++;
			$id = $row['id'];
			$name = $row['name'];
			$date = $row['date'];
			echo <<<ATATA
<tr>
		<td>$date</td>
		<td><a href="tournaments.php?id=$id">$name</a></td>
</tr>
ATATA;
		}
		echo "</table>";
	}
	$main->sql_close();
	include "2_footer.php";
?>