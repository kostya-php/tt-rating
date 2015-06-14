<?php
	$page_name = "Турниры";
	include "1_header.php";
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
				$player = Array();
				$player_name = Array();
				while ($row = mysql_fetch_array($main->sql_res[1])) {
					echo "<li>".$row['surname']." ".$row['name']." ".$row['patronymic']."</li>";
					$player[$row['number']] = $row['player'];
					$player_name[$row['number']] = $row['surname']." ".$row['name']." ".$row['patronymic'];
				}
				$n = count($player);
				echo "</ol>";
				
				echo "<table>";
				for ($i = 0; $i <= $n; $i++) {
					echo "<tr>";
					for ($j = 0; $j <= $n + 4; $j++) {
						echo "<td style=\"text-align:center;\">";
						// Заголовок "Игроки"
						if (($i == 0) and ($j == 0))
							echo "Игроки"; else
							// Заголовок "Игры"
							if (($j == $n + 1) and ($i == 0))
								echo "Игры"; else
								// Заголовок "Сеты"
								if (($j == $n + 2) and ($i == 0))
									echo "Сеты"; else
									// Заголовок "Очки"
									if (($j == $n + 3) and ($i == 0))
										echo "Очки"; else
										// Заголовок "Место"
										if (($j == $n + 4) and ($i == 0))
											echo "Место"; else
											// Игры игрока
											if (($j == $n + 1) and ($i > 0))
												echo "игры"; else
												// Сеты игрока
												if (($j == $n + 2) and ($i > 0))
													echo "сеты"; else
													// Очки игрока
													if (($j == $n + 3) and ($i > 0))
														echo "очки"; else
														// Место, которое занял игрок
														if (($j == $n + 4) and ($i > 0))
															echo "место"; else
															// Заголовок с номером игрока
															if (($i == 0) and ($j > 0))
																echo "$j"; else
																// Имя игрока
																if (($i > 0) and ($j == 0))
																	echo $player_name[$i]; else
																	// Результат партии
																	if ($j > $i) {
																		echo "$i $j";
																	} else
																		if ($i > $j) {
																			echo "$i $j";
																		} else
																			if($i==$j)
																				echo "-"; else
																				echo "$i $j";
						echo "</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
				echo "<p><b>Игры:</b></p>";
				echo <<<ATATA
<table>
	<tr>
		<!--<td>id</td>-->
		<td style="text-align:center;padding:3px;"><b>#</b></td>
		<td style="text-align:center;padding:3px;"><b>Игрок 1</b></td>
		<td style="text-align:center;padding:3px;"><b>Результат</b></td>
		<td style="text-align:center;padding:3px;"><b>Игрок 2</b></td>
		<td style="text-align:center;padding:3px;"><b>Статус</b></td>
		<td style="text-align:center;padding:3px;"><b>Действия</b></td>
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
					$res = $row['x'].":".$row['y'];
					$player2 = $row['surname2']." ".$row['name2'];//." ".$row['patronymic2'];
					$status = "";
					switch($row['status']) {
						case 0:
							$res = "-";
							$status = "соперники<br />не определены";
							break;
						case 1:
							$res = "-";
							$status = "не играли";
							break;
						case 2:
							$res.="<br />(".$row['rounds'].")";
							$status = "сыграли";
							break;
						case 3:
							$status = "техническое поражение";
							break;
						case 4:
							$res = "-";
							$status = "неявка игроков";
							break;
						case 5:
							$status = "по результатам<br />предыдущей встречи";
							break;
					}
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
