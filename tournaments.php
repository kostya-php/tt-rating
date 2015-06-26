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
						$place = Array(); // массив с местами игроков
						while ($row = mysql_fetch_array($main->sql_res[1])) {
							$number = $row['number'];
							$player[$number] = $row['player'];
							$player_name[$number] = $row['surname']." ".$row['name']." ".$row['patronymic'];
							//var_dump($row['place']);
							if(is_null($row['place'])) {
								$place[$number] = "?";
							} else {
								$place[$number] = $main->dec2roman($row['place']);
							}
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
																if (($j == $n + 4) and ($i > 0)) {
																	echo "		<td style=\"text-align:center;\">".$place[$i]."</td>".chr(13);
																} else
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
																							if($j<$i) $res = "2<hr style=\"margin:0px;\">$x:$y";
																						} else
																							if($y>$x) {
																								if($j>$i) $res = "1<hr style=\"margin:0px;\">$x:$y";
																								if($j<$i) $res = "1<hr style=\"margin:0px;\">$x:$y";
																							}
																						break;
																				}
																				echo "		<td style=\"text-align:center;\"><a title=\"$player_name[$i] - $player_name[$j]\" href=\"matches.php?id=$match_id\">$res</a></td>".chr(13);
																			} else
																				if($i==$j) echo "		<td style=\"text-align:center;\">-</td>".chr(13);
							}
							echo "	</tr>".chr(13);
						}
						$a = $n+5;
						echo <<<ATATA
<tr>
	<td colspan="$a" style="border:0px;text-align:right;">
		<a href="place.php?id=$tournament">Выставить места</a>
	</td>
</tr>
ATATA;
						echo "</table>".chr(13);
						break;
					case "vib8":
						$p1 = Array();
						$p2 = Array();
						$px = Array();
						$py = Array();
						$match_id = Array();
						$place = Array();
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
						WHERE tournament='$tournament' ORDER BY number ASC";
						$main->sql_execute(1);
						$i = 0;
						while($row = mysql_fetch_array($main->sql_res[1])) {
							$i++;
							$match_id[$i] = $row['id'];
							if(!is_null($row['player1'])) $p1[$i] = $row['surname1']." ".$row['name1']." ".$row['patronymic1'];
								else $p1[$i] = "---";
							if(!is_null($row['player2'])) $p2[$i] = $row['surname2']." ".$row['name2']." ".$row['patronymic2'];
								else $p2[$i] = "---";
							if(!is_null($row['x'])) $px[$i] = $row['x'];
								else $px[$i] = "?";
							if(!is_null($row['y'])) $py[$i] = $row['y'];
								else $py[$i] = "?";
							/*
							if($px[$i]>$py[$i]) {
								$p1[$i] = "<b>".$p1[$i]."</b>";
								$px[$i] = "<b>".$px[$i]."</b>";
							}
							if($px[$i]<$py[$i]) {
								$p2[$i] = "<b>".$p2[$i]."</b>";
								$py[$i] = "<b>".$py[$i]."</b>";
							}
							*/
						}
						for($i=1;$i<=8;$i++) {
							$place[$i] = "?";
						}
						if((is_numeric($px[11]))and(is_numeric($py[11]))) {
							if($px[11]>$py[11]) {
								$place[7] = $p1[11];
								$place[8] = $p2[11];
							}
							if($px[11]<$py[11]) {
								$place[7] = $p2[11];
								$place[8] = $p1[11];								
							}
						}
						if((is_numeric($px[12]))and(is_numeric($py[12]))) {
							if($px[12]>$py[12]) {
								$place[5] = $p1[12];
								$place[6] = $p2[12];
							}
							if($px[12]<$py[12]) {
								$place[5] = $p2[12];
								$place[6] = $p1[12];								
							}
						}
						if((is_numeric($px[13]))and(is_numeric($py[13]))) {
							if($px[13]>$py[13]) {
								$place[3] = $p1[13];
								$place[4] = $p2[13];
							}
							if($px[13]<$py[13]) {
								$place[3] = $p2[13];
								$place[4] = $p1[13];								
							}
						}
						if((is_numeric($px[14]))and(is_numeric($py[14]))) {
							if($px[14]>$py[14]) {
								$place[1] = $p1[14];
								$place[2] = $p2[14];
							}
							if($px[14]<$py[14]) {
								$place[1] = $p2[14];
								$place[2] = $p1[14];								
							}
						}
						echo <<<ATATA
<table>
	<tr>
		<td class="parent" style="width:250px;padding-top:20px;padding-bottom:20px;">
			<!--{1}-->
			<a href="matches.php?id=$match_id[1]">
			<table class="child">
				<tr>
					<td class="player">$p1[1]</td>
					<td class="point">$px[1]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">1</td>
				</tr>
				<tr>
					<td class="player">$p2[1]</td>
					<td class="point">$py[1]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="2">
			<!--{5}-->
			<a href="matches.php?id=$match_id[5]">
			<table class="child">
				<tr>
					<td class="player">$p1[5]</td>
					<td class="point">$px[5]</td>
				</tr>
				<tr style="height:85px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">5</td>
				</tr>
				<tr>
					<td class="player">$p2[5]</td>
					<td class="point">$py[5]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="4">
			<!--{14}-->
			<a href="matches.php?id=$match_id[14]">
			<table class="child">
				<tr>
					<td class="player">$p1[14]</td>
					<td class="point">$px[14]</td>
				</tr>
				<tr style="height:210px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">14</td>
				</tr>
				<tr>
					<td class="player">$p2[14]</td>
					<td class="point">$py[14]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="4">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>1-е</b> - $place[1]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>2-е</b> - $place[2]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="width:250px;padding-top:20px;padding-bottom:20px;">
			<!--{2}-->
			<a href="matches.php?id=$match_id[2]">
			<table class="child">
				<tr>
					<td class="player">$p1[2]</td>
					<td class="point">$px[2]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">2</td>
				</tr>
				<tr>
					<td class="player">$p2[2]</td>
					<td class="point">$py[2]</td>
				</tr>
			</table>
			</a>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="width:250px;padding-top:20px;padding-bottom:20px;">
			<!--{3}-->
			<a href="matches.php?id=$match_id[3]">
			<table class="child">
				<tr>
					<td class="player">$p1[3]</td>
					<td class="point">$px[3]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">3</td>
				</tr>
				<tr>
					<td class="player">$p2[3]</td>
					<td class="point">$py[3]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="2">
			<!--{6}-->
			<a href="matches.php?id=$match_id[6]">
			<table class="child">
				<tr>
					<td class="player">$p1[6]</td>
					<td class="point">$px[6]</td>
				</tr>
				<tr style="height:85px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">6</td>
				</tr>
				<tr>
					<td class="player">$p2[6]</td>
					<td class="point">$py[6]</td>
				</tr>
			</table>
			</a>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="width:250px;padding-top:20px;padding-bottom:20px;">
			<!--{4}-->
			<a href="matches.php?id=$match_id[4]">
			<table class="child">
				<tr>
					<td class="player">$p1[4]</td>
					<td class="point">$px[4]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">4</td>
				</tr>
				<tr>
					<td class="player">$p2[4]</td>
					<td class="point">$py[4]</td>
				</tr>
			</table>
			</a>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="height:89px;"></td>
		<td class="parent" style="height:89px;" rowspan="2">
			<!--{9}-->
			<a href="matches.php?id=$match_id[9]">
			<table class="child">
				<tr>
					<td class="los">-5</td>
					<td style="width:250px;">$p1[9]</td>
					<td style="width:25px;">$px[9]</td>
				</tr>
				
				<tr style="height:70px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">9</td>
				</tr>
				
				<tr>
					<td class="player" colspan="2">$p2[9]</td>
					<td class="point">$py[9]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="4">
			<!--{13}-->
			<a href="matches.php?id=$match_id[13]">
			<table class="child">
				<tr>
					<td class="player">$p1[13]</td>
					<td class="point">$px[13]</td>
				</tr>
				<tr style="height:190px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">13</td>
				</tr>
				<tr>
					<td class="player">$p2[13]</td>
					<td class="point">$py[13]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="4">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>3-е</b> - $place[3]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>4-е</b> - $place[4]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{7}-->
			<a href="matches.php?id=$match_id[7]">
			<table class="child">
				<tr>
					<td class="los">-1</td>
					<td style="width:250px;">$p1[7]</td>
					<td style="width:25px;">$px[7]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">7</td>
				</tr>
				<tr>
					<td class="los">-2</td>
					<td style="width:250px;">$p2[7]</td>
					<td style="width:25px;">$py[7]</td>
				</tr>
			</table>
			</a>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{8}-->
			<a href="matches.php?id=$match_id[8]">
			<table class="child">
				<tr>
					<td class="los">-3</td>
					<td style="width:250px;">$p1[8]</td>
					<td style="width:25px;">$px[8]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">8</td>
				</tr>
				<tr>
					<td class="los">-4</td>
					<td style="width:250px;">$p2[8]</td>
					<td style="width:25px;">$py[8]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent" rowspan="2">
			<!--{10}-->
			<a href="matches.php?id=$match_id[10]">
			<table class="child">
				<tr>
					<td class="player" colspan="2">$p1[10]</td>
					<td class="point">$px[10]</td>
				</tr>
				<tr style="height:70px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">10</td>
				</tr>
				<tr>
					<td class="los">-6</td>
					<td style="width:250px;">$p2[10]</td>
					<td style="width:25px;">$py[10]</td>
				</tr>
			</table>
			</a>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="height:89px;"></td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent">
			<!--{12}-->
			<a href="matches.php?id=$match_id[12]">
			<table class="child">
				<tr>
					<td class="los">-9</td>
					<td style="width:250px;">$p1[12]</td>
					<td style="width:25px;">$px[12]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">12</td>
				</tr>
				<tr>
					<td class="los">-10</td>
					<td style="width:250px;">$p2[12]</td>
					<td style="width:25px;">$py[12]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>5-е</b> - $place[5]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>6-е</b> - $place[6]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent">
			<!--{11}-->
			<a href="matches.php?id=$match_id[11]">
			<table class="child">
				<tr>
					<td class="los">-7</td>
					<td style="width:250px;">$p1[11]</td>
					<td style="width:25px;">$px[11]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;padding:0px;font-size:10px;color:blue;">11</td>
				</tr>
				<tr>
					<td class="los">-8</td>
					<td style="width:250px;">$p2[11]</td>
					<td style="width:25px;">$py[11]</td>
				</tr>
			</table>
			</a>
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>7-е</b> - $place[7]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>8-е</b> - $place[8]</p>
		</td>	
	</tr>
</table>
ATATA;
						break;
				}
				
				echo <<<ATATA
<table style="margin-top:20px;">
	<tr>
		<td>id</td>
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
		<td>$id</td>
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
		$main->sql_query[1] = "SELECT * FROM tournaments ORDER BY date DESC";
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
