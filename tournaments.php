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
				
				switch($row['protocol']) {
					case "krug":
						$main->sql_query[1] = "SELECT * FROM in_tournament LEFT JOIN players ON players.id=in_tournament.player WHERE tournament='$tournament'";
						$main->sql_execute(1);
						
						$player = Array(); // массив игроков (их id)
						$player_name = Array(); // массив с именами игроков
						$points = Array(); // массив с очками игроков
						$sets = Array(); // массив с сетами игроков
						$games = Array(); // массив с играми игроков
						while ($row = mysql_fetch_array($main->sql_res[1])) {
							$number = $row['number'];
							$player[$number] = $row['player'];
							$player_name[$number] = $row['surname']." ".$row['name']." ".$row['patronymic'];
							$points[$number] = 0;
							$sets[$number] = "";
							$games[$number] = "";
							$main->sql_query[2] = "SELECT * FROM matches WHERE tournament='$tournament' AND (player1='".$row['player']."' OR player2='".$row['player']."')";
							$main->sql_execute(2);
							$wins_sets = 0;
							$loses_sets = 0;
							$wins = 0;
							$loses = 0;
							while($row2 = mysql_fetch_array($main->sql_res[2])) {
								$x = 0;
								$y = 0;
								if($row2['player1'] == $row['player']) {
									$x = $row2['x'];
									$y = $row2['y'];
								} else 
									if($row2['player2'] == $row['player']) {
										$x = $row2['y'];
										$y = $row2['x'];
									}
								$wins_sets+=$x;
								$loses_sets+=$y;
								switch($row2['status']) {
									case 2:
										if($x>$y) {
											$points[$number]+=2;
											$wins++;
										}
										if($y>$x) {
											$points[$number]+=1;
											$loses++;
										}
										break;
									case 3:
										if($x>$y) {
											$points[$number]+=2;
											$wins++;
										}
										if($y>$x) {
											$loses++;
										}
										break;
									case 5:
										if($x>$y) {
											$points[$number]+=2;
										}
										if($y>$x) {
											$points[$number]+=1;
										}
										break;
								}
								$sets[$number] = $wins_sets.":".$loses_sets;
								$games[$number] = ($wins+$loses).",$wins:$loses";
							}
						}
						$n = count($player);
						echo chr(13)."<table>".chr(13);
						for ($i = 0; $i <= $n; $i++) {
							echo "	<tr>".chr(13);
							for ($j = 0; $j <= $n + 4; $j++) {
								// Заголовок "Игроки"
								if (($i == 0) and ($j == 0))
									echo "		<td style=\"text-align:center;font-weight:bold;\">Игроки</td>".chr(13); else
									// Заголовок "Игры"
									if (($j == $n + 1) and ($i == 0))
										echo "		<td style=\"text-align:center;font-weight:bold;width:50px;\">Игры</td>".chr(13); else
										// Заголовок "Сеты"
										if (($j == $n + 2) and ($i == 0))
											echo "		<td style=\"text-align:center;font-weight:bold;width:50px;\">Сеты</td>".chr(13); else
											// Заголовок "Очки"
											if (($j == $n + 3) and ($i == 0))
												echo "		<td style=\"text-align:center;font-weight:bold;width:50px;\">Очки</td>".chr(13); else
												// Заголовок "Место"
												if (($j == $n + 4) and ($i == 0))
													echo "		<td style=\"text-align:center;font-weight:bold;width:50px;\">Место</td>".chr(13); else
													// Игры игрока
													if (($j == $n + 1) and ($i > 0))
														echo "		<td style=\"text-align:center;\">$games[$i]</td>".chr(13); else
														// Сеты игрока
														if (($j == $n + 2) and ($i > 0)) {
															echo "		<td style=\"text-align:center;\">$sets[$i]</td>".chr(13); 
														} else
															// Очки игрока
															if (($j == $n + 3) and ($i > 0)) {
																echo "		<td style=\"text-align:center;\">$points[$i]</td>".chr(13);
															} else
																// Место, которое занял игрок
																if (($j == $n + 4) and ($i > 0))
																	echo "		<td style=\"text-align:center;\">место</td>".chr(13); else
																	// Заголовок с номером игрока
																	if (($i == 0) and ($j > 0))
																		echo "		<td style=\"text-align:center;font-weight:bold;width:50px;\">$j</td>".chr(13); else
																		// Имя игрока
																		if (($i > 0) and ($j == 0))
																			echo "		<td style=\"height:50px;\"><b>$i.</b> $player_name[$i]</td>".chr(13); else
																			// Результат партии
																			if (($j > $i)or($i > $j)) {
																				$main->sql_query[1] = "SELECT * FROM matches WHERE tournament='$tournament' AND (player1='$player[$j]' AND player2='$player[$i]')OR(player1='$player[$i]' AND player2='$player[$j]')";
																				$main->sql_execute(1);
																				$x = "";
																				$y = "";
																				$status = "";
																				$res = "";
																				$match_id = "";
																				while($row = mysql_fetch_array($main->sql_res[1])) {
																					if(($player[$i]==$row['player1'])and($player[$j]==$row['player2'])) {
																						$x = $row['x'];
																						$y = $row['y'];
																					} else
																						if(($player[$i]==$row['player2'])and($player[$j]==$row['player1'])) {
																							$x = $row['y'];
																							$y = $row['x'];
																						}
																					$status = $row['status'];
																					$match_id = $row['id'];
																				}
																				switch($status) {
																					case 1:
																						$res = "еще не<hr style=\"margin:0px;\">играли";
																						break;
																					case 2:
																						if($x>$y) {
																							if($j>$i) $res = "2<hr style=\"margin:0px;\">$x:$y";
																							if($j<$i) $res = "2<hr style=\"margin:0px;\">$x:$y";
																						} else
																							if($y>$x) {
																								if($j>$i) $res = "1<hr style=\"margin:0px;\">$x:$y";
																								if($j<$i) $res = "1<hr style=\"margin:0px;\">$x:$y";
																							}
																						break;
																					case 3:
																						if($x>$y) {
																							if($j>$i) $res = "W<hr style=\"margin:0px;\">$x:$y";
																							if($i>$j) $res = "W<hr style=\"margin:0px;\">$x:$y";
																						} else
																							if($y>$x) {
																								if($j>$i) $res = "L<hr style=\"margin:0px;\">$x:$y";
																								if($j<$i) $res = "L<hr style=\"margin:0px;\">$x:$y";
																							}
																						break;
																					case 4:
																						$res = "-<hr style=\"margin:0px;\">-:-";
																						break;
																					case 5:
																						$res = "-<hr style=\"margin:0px;\">-:-";
																						if($x>$y) {
																							if($j>$i) $res = "2<hr style=\"margin:0px;\">$x:$y";
																							if($j<$i) $res = "1<hr style=\"margin:0px;\">$y:$x";
																						} else
																							if($y>$x) {
																								if($j>$i) $res = "1<hr style=\"margin:0px;\">$x:$y";
																								if($j<$i) $res = "2<hr style=\"margin:0px;\">$y:$x";
																							}
																						break;
																				}
																				echo "		<td style=\"text-align:center;\"><a title=\"$player_name[$i] - $player_name[$j]\" href=\"matches.php?id=$match_id\">$res</a></td>".chr(13);
																			} else
																				if($i==$j) echo "		<td style=\"text-align:center;\">-</td>".chr(13);
							}
							echo "	</tr>".chr(13);
						}
						echo "</table>".chr(13);
						break;
				}
				echo <<<ATATA
<table style="margin-top:20px;">
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
