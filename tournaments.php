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
				//echo "<h2>Турнир \"".$row['name']."\"</h2>";
				$name = $row['name'];
				
				echo <<<ATATA
<h2>Турнир "$name"</h2>
<form action="delete_tournament.php" method="post">
	<input name="id" type="hidden" value="$tournament">
	<input name="name" type="hidden" value="$name">
	<input type="submit" value="Удалить турнир" style="font-size: 10px;color: red;">
</form>
ATATA;
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
						echo chr(13)."<table style=\"margin-top:30px;\">".chr(13);						
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
						$p1 = Array(); // массив с player1 (индекс - номер игры)
						$p2 = Array(); // массив с player2 (индекс - номер игры)
						$px = Array(); // массив с x (индекс - номер игры)
						$py = Array(); // массив с y (индекс - номер игры)
						$match_id = Array(); // массив с id (индекс - номер игры)
						$place = Array(); // массив с place (индекс - номер игры)
						$rounds = Array(); // массив с rounds (индекс - номер игры)
						
						$link1 = Array();
						$link2 = Array();
						
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
							if(!is_null($row['rounds'])) $rounds[$i] = "(".$row['rounds'].")";
								else $rounds[$i] = "-";
						}
						for($i=1;$i<=14;$i++) {
							if($main->check_route($tournament,$i)) {
								$link1[$i] = "<a href=\"matches.php?id=".$match_id[$i]."\">";
								$link2[$i] = "</a>";
							} else {
								$link1[$i] = "";
								$link2[$i] = "";
							}
						}
						for($i=1;$i<=8;$i++) {
							$place[$i] = "?";
						}
						
						$j = 8;
						for($i=11;$i<=14;$i++) {
							if((is_numeric($px[$i]))and(is_numeric($py[$i]))) {
								if($px[$i]>$py[$i]) {
									$place[$j-1] = $p1[$i];
									$place[$j] = $p2[$i];
								}
								if($px[$i]<$py[$i]) {
									$place[$j-1] = $p2[$i];
									$place[$j] = $p1[$i];
								}
							}
							$j=$j-2;
						}
						
						echo <<<ATATA
<table>
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{1}-->
			$link1[1]
			<table class="child">
				<tr>
					<td class="player">$p1[1]</td>
					<td class="point">$px[1]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[1]</i>&nbsp&nbsp&nbsp<span style="color:black;">1</span></td>
				</tr>
				<tr>
					<td class="player">$p2[1]</td>
					<td class="point">$py[1]</td>
				</tr>
			</table>
			$link2[1]
		</td>
		<td class="parent" rowspan="2">
			<!--{5}-->
			$link1[5]
			<table class="child">
				<tr>
					<td class="player">$p1[5]</td>
					<td class="point">$px[5]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[5]</i>&nbsp&nbsp&nbsp<span style="color:black;">5</span></td>
				</tr>
				<tr>
					<td class="player">$p2[5]</td>
					<td class="point">$py[5]</td>
				</tr>
			</table>
			$link2[5]
		</td>
		<td class="parent" rowspan="4">
			<!--{14}-->
			$link1[14]
			<table class="child">
				<tr>
					<td class="player">$p1[14]</td>
					<td class="point">$px[14]</td>
				</tr>
				<tr style="height:250px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[14]</i>&nbsp&nbsp&nbsp<span style="color:black;">14</span></td>
				</tr>
				<tr>
					<td class="player">$p2[14]</td>
					<td class="point">$py[14]</td>
				</tr>
			</table>
			$link2[14]
		</td>
		<td class="parent" rowspan="4">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>1-е</b> - $place[1]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>2-е</b> - $place[2]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{2}-->
			$link1[2]
			<table class="child">
				<tr>
					<td class="player">$p1[2]</td>
					<td class="point">$px[2]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[2]</i>&nbsp&nbsp&nbsp<span style="color:black;">2</span></td>
				</tr>
				<tr>
					<td class="player">$p2[2]</td>
					<td class="point">$py[2]</td>
				</tr>
			</table>
			$link2[2]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{3}-->
			$link1[3]
			<table class="child">
				<tr>
					<td class="player">$p1[3]</td>
					<td class="point">$px[3]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[3]</i>&nbsp&nbsp&nbsp<span style="color:black;">3</span></td>
				</tr>
				<tr>
					<td class="player">$p2[3]</td>
					<td class="point">$py[3]</td>
				</tr>
			</table>
			$link2[3]
		</td>
		<td class="parent" rowspan="2">
			<!--{6}-->
			$link1[6]
			<table class="child">
				<tr>
					<td class="player">$p1[6]</td>
					<td class="point">$px[6]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[6]</i>&nbsp&nbsp&nbsp<span style="color:black;">6</span></td>
				</tr>
				<tr>
					<td class="player">$p2[6]</td>
					<td class="point">$py[6]</td>
				</tr>
			</table>
			$link2[6]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{4}-->
			$link1[4]
			<table class="child">
				<tr>
					<td class="player">$p1[4]</td>
					<td class="point">$px[4]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[4]</i>&nbsp&nbsp&nbsp<span style="color:black;">4</span></td>
				</tr>
				<tr>
					<td class="player">$p2[4]</td>
					<td class="point">$py[4]</td>
				</tr>
			</table>
			$link2[4]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="height:89px;"></td>
		<td class="parent" style="height:89px;" rowspan="2">
			<!--{9}-->
			$link1[9]
			<table class="child">
				<tr>
					<td class="los">-5</td>
					<td style="width:250px;">$p1[9]</td>
					<td style="width:25px;">$px[9]</td>
				</tr>
				
				<tr style="height:70px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[9]</i>&nbsp&nbsp&nbsp<span style="color:black;">9</span></td>
				</tr>
				
				<tr>
					<td class="player" colspan="2">$p2[9]</td>
					<td class="point">$py[9]</td>
				</tr>
			</table>
			$link2[9]
		</td>
		<td class="parent" rowspan="4">
			<!--{13}-->
			$link1[13]
			<table class="child">
				<tr>
					<td class="player">$p1[13]</td>
					<td class="point">$px[13]</td>
				</tr>
				<tr style="height:210px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[13]</i>&nbsp&nbsp&nbsp<span style="color:black;">13</span></td>
				</tr>
				<tr>
					<td class="player">$p2[13]</td>
					<td class="point">$py[13]</td>
				</tr>
			</table>
			$link2[13]
		</td>
		<td class="parent" rowspan="4">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>3-е</b> - $place[3]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>4-е</b> - $place[4]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{7}-->
			$link1[7]
			<table class="child">
				<tr>
					<td class="los">-1</td>
					<td style="width:250px;">$p1[7]</td>
					<td style="width:25px;">$px[7]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[7]</i>&nbsp&nbsp&nbsp<span style="color:black;">7</span></td>
				</tr>
				<tr>
					<td class="los">-2</td>
					<td style="width:250px;">$p2[7]</td>
					<td style="width:25px;">$py[7]</td>
				</tr>
			</table>
			$link2[7]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{8}-->
			$link1[8]
			<table class="child">
				<tr>
					<td class="los">-3</td>
					<td style="width:250px;">$p1[8]</td>
					<td style="width:25px;">$px[8]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[8]</i>&nbsp&nbsp&nbsp<span style="color:black;">8</span></td>
				</tr>
				<tr>
					<td class="los">-4</td>
					<td style="width:250px;">$p2[8]</td>
					<td style="width:25px;">$py[8]</td>
				</tr>
			</table>
			$link2[8]
		</td>
		<td class="parent" rowspan="2">
			<!--{10}-->
			$link1[10]
			<table class="child">
				<tr>
					<td class="player" colspan="2">$p1[10]</td>
					<td class="point">$px[10]</td>
				</tr>
				<tr style="height:70px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[10]</i>&nbsp&nbsp&nbsp<span style="color:black;">10</span></td>
				</tr>
				<tr>
					<td class="los">-6</td>
					<td style="width:250px;">$p2[10]</td>
					<td style="width:25px;">$py[10]</td>
				</tr>
			</table>
			$link2[10]
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
			$link1[12]
			<table class="child">
				<tr>
					<td class="los">-9</td>
					<td style="width:250px;">$p1[12]</td>
					<td style="width:25px;">$px[12]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[12]</i>&nbsp&nbsp&nbsp<span style="color:black;">12</span></td>
				</tr>
				<tr>
					<td class="los">-10</td>
					<td style="width:250px;">$p2[12]</td>
					<td style="width:25px;">$py[12]</td>
				</tr>
			</table>
			$link2[12]
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
			$link1[11]
			<table class="child">
				<tr>
					<td class="los">-7</td>
					<td style="width:250px;">$p1[11]</td>
					<td style="width:25px;">$px[11]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[11]</i>&nbsp&nbsp&nbsp<span style="color:black;">11</span></td>
				</tr>
				<tr>
					<td class="los">-8</td>
					<td style="width:250px;">$p2[11]</td>
					<td style="width:25px;">$py[11]</td>
				</tr>
			</table>
			$link2[11]
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>7-е</b> - $place[7]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>8-е</b> - $place[8]</p>
		</td>	
	</tr>
</table>
ATATA;
						break;
					case "vib16":
						$p1 = Array(); // массив с player1 (индекс - номер игры)
						$p2 = Array(); // массив с player2 (индекс - номер игры)
						$px = Array(); // массив с x (индекс - номер игры)
						$py = Array(); // массив с y (индекс - номер игры)
						$match_id = Array(); // массив с id (индекс - номер игры)
						$place = Array(); // массив с place (индекс - номер игры)
						$rounds = Array(); // массив с rounds (индекс - номер игры)
						
						$link1 = Array();
						$link2 = Array();
						
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
							if(!is_null($row['rounds'])) $rounds[$i] = "(".$row['rounds'].")";
								else $rounds[$i] = "-";
						}
						
						for($i=1;$i<=32;$i++) {
							if($main->check_route($tournament,$i)) {
								$link1[$i] = "<a href=\"matches.php?id=".$match_id[$i]."\">";
								$link2[$i] = "</a>";
							} else {
								$link1[$i] = "";
								$link2[$i] = "";
							}
						}
						
						for($i=1;$i<=16;$i++) {
							$place[$i] = "?";
						}
						$j = 16;
						for($i=25;$i<=32;$i++) {
							if((is_numeric($px[$i]))and(is_numeric($py[$i]))) {
								if($px[$i]>$py[$i]) {
									$place[$j-1] = $p1[$i];
									$place[$j] = $p2[$i];
								}
								if($px[$i]<$py[$i]) {
									$place[$j-1] = $p2[$i];
									$place[$j] = $p1[$i];
								}
							}
							$j=$j-2;
						}
						echo <<<ATATA
<table>
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{1}-->
			$link1[1]
			<table class="child">
				<tr>
					<td class="player">$p1[1]</td>
					<td class="point">$px[1]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[1]</i>&nbsp&nbsp&nbsp<span style="color:black;">1</span></td>
				</tr>
				<tr>
					<td class="player">$p2[1]</td>
					<td class="point">$py[1]</td>
				</tr>
			</table>
			$link2[1]
		</td>
		<td class="parent" rowspan="2">
			<!--{9}-->
			$link1[9]
			<table class="child">
				<tr>
					<td class="player">$p1[9]</td>
					<td class="point">$px[9]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[9]</i>&nbsp&nbsp&nbsp<span style="color:black;">9</span></td>
				</tr>
				<tr>
					<td class="player">$p2[9]</td>
					<td class="point">$py[9]</td>
				</tr>
			</table>
			$link2[9]
		</td>
		<td class="parent" rowspan="4">
			<!--{17}-->
			$link1[17]
			<table class="child">
				<tr>
					<td class="player">$p1[17]</td>
					<td class="point">$px[17]</td>
				</tr>
				<tr style="height:250px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[17]</i>&nbsp&nbsp&nbsp<span style="color:black;">17</span></td>
				</tr>
				<tr>
					<td class="player">$p2[17]</td>
					<td class="point">$py[17]</td>
				</tr>
			</table>
			$link2[17]
		</td>
		<td class="parent" rowspan="8">
			<!--{32}-->
			$link1[32]
			<table class="child">
				<tr>
					<td class="player">$p1[32]</td>
					<td class="point">$px[32]</td>
				</tr>
				<tr style="height:525px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[32]</i>&nbsp&nbsp&nbsp<span style="color:black;">32</span></td>
				</tr>
				<tr>
					<td class="player">$p2[32]</td>
					<td class="point">$py[32]</td>
				</tr>
			</table>
			$link2[32]
		</td>
		<td class="parent" rowspan="8">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>1-е</b> - $place[1]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>2-е</b> - $place[2]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{2}-->
			$link1[2]
			<table class="child">
				<tr>
					<td class="player">$p1[2]</td>
					<td class="point">$px[2]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[2]</i>&nbsp&nbsp&nbsp<span style="color:black;">2</span></td>
				</tr>
				<tr>
					<td class="player">$p2[2]</td>
					<td class="point">$py[2]</td>
				</tr>
			</table>
			$link2[2]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{3}-->
			$link1[3]
			<table class="child">
				<tr>
					<td class="player">$p1[3]</td>
					<td class="point">$px[3]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[3]</i>&nbsp&nbsp&nbsp<span style="color:black;">3</span></td>
				</tr>
				<tr>
					<td class="player">$p2[3]</td>
					<td class="point">$py[3]</td>
				</tr>
			</table>
			$link2[3]
		</td>
		<td class="parent" rowspan="2">
			<!--{10}-->
			$link1[10]
			<table class="child">
				<tr>
					<td class="player">$p1[10]</td>
					<td class="point">$px[10]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[10]</i>&nbsp&nbsp&nbsp<span style="color:black;">10</span></td>
				</tr>
				<tr>
					<td class="player">$p2[10]</td>
					<td class="point">$py[10]</td>
				</tr>
			</table>
			$link2[10]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{4}-->
			$link1[4]
			<table class="child">
				<tr>
					<td class="player">$p1[4]</td>
					<td class="point">$px[4]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[4]</i>&nbsp&nbsp&nbsp<span style="color:black;">4</span></td>
				</tr>
				<tr>
					<td class="player">$p2[4]</td>
					<td class="point">$py[4]</td>
				</tr>
			</table>
			$link2[4]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{5}-->
			$link1[5]
			<table class="child">
				<tr>
					<td class="player">$p1[5]</td>
					<td class="point">$px[5]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[5]</i>&nbsp&nbsp&nbsp<span style="color:black;">5</span></td>
				</tr>
				<tr>
					<td class="player">$p2[5]</td>
					<td class="point">$py[5]</td>
				</tr>
			</table>
			$link2[5]
		</td>
		<td class="parent" rowspan="2">
			<!--{11}-->
			$link1[11]
			<table class="child">
				<tr>
					<td class="player">$p1[11]</td>
					<td class="point">$px[11]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[11]</i>&nbsp&nbsp&nbsp<span style="color:black;">11</span></td>
				</tr>
				<tr>
					<td class="player">$p2[11]</td>
					<td class="point">$py[11]</td>
				</tr>
			</table>
			$link2[11]
		</td>
		<td class="parent" rowspan="4">
			<!--{18}-->
			$link1[18]
			<table class="child">
				<tr>
					<td class="player">$p1[18]</td>
					<td class="point">$px[18]</td>
				</tr>
				<tr style="height:250px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[18]</i>&nbsp&nbsp&nbsp<span style="color:black;">18</span></td>
				</tr>
				<tr>
					<td class="player">$p2[18]</td>
					<td class="point">$py[18]</td>
				</tr>
			</table>
			$link2[18]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{6}-->
			$link1[16]
			<table class="child">
				<tr>
					<td class="player">$p1[6]</td>
					<td class="point">$px[6]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[6]</i>&nbsp&nbsp&nbsp<span style="color:black;">6</span></td>
				</tr>
				<tr>
					<td class="player">$p2[6]</td>
					<td class="point">$py[6]</td>
				</tr>
			</table>
			$link2[16]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{7}-->
			$link1[7]
			<table class="child">
				<tr>
					<td class="player">$p1[7]</td>
					<td class="point">$px[7]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[7]</i>&nbsp&nbsp&nbsp<span style="color:black;">7</span></td>
				</tr>
				<tr>
					<td class="player">$p2[7]</td>
					<td class="point">$py[7]</td>
				</tr>
			</table>
			$link2[7]
		</td>
		<td class="parent" rowspan="2">
			<!--{12}-->
			$link1[12]
			<table class="child">
				<tr>
					<td class="player">$p1[12]</td>
					<td class="point">$px[12]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[12]</i>&nbsp&nbsp&nbsp<span style="color:black;">12</span></td>
				</tr>
				<tr>
					<td class="player">$p2[12]</td>
					<td class="point">$py[12]</td>
				</tr>
			</table>
			$link2[12]
		</td>
	</tr>
	
	<tr>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{8}-->
			$link1[8]
			<table class="child">
				<tr>
					<td class="player">$p1[8]</td>
					<td class="point">$px[8]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[8]</i>&nbsp&nbsp&nbsp<span style="color:black;">8</span></td>
				</tr>
				<tr>
					<td class="player">$p2[8]</td>
					<td class="point">$py[8]</td>
				</tr>
			</table>
			$link2[8]
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{31}-->
			$link1[31]
			<table class="child">
				<tr>
					<td class="los">-17</td>
					<td style="width:250px;">$p1[31]</td>
					<td style="width:25px;">$px[31]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[31]</i>&nbsp&nbsp&nbsp<span style="color:black;">31</span></td>
				</tr>
				<tr>
					<td class="los">-18</td>
					<td style="width:250px;">$p2[31]</td>
					<td style="width:25px;">$py[31]</td>
				</tr>
			</table>
			$link2[31]
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>3-е</b> - $place[3]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>4-е</b> - $place[4]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{19}-->
			$link1[19]
			<table class="child">
				<tr>
					<td class="los">-9</td>
					<td style="width:250px;">$p1[19]</td>
					<td style="width:25px;">$px[19]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[19]</i>&nbsp&nbsp&nbsp<span style="color:black;">19</span></td>
				</tr>
				<tr>
					<td class="los">-10</td>
					<td style="width:250px;">$p2[19]</td>
					<td style="width:25px;">$py[19]</td>
				</tr>
			</table>
			$link2[19]
		</td>
		<td class="parent" rowspan="2" style="padding-top:20px;padding-bottom:20px;">
			<!--{30}-->
			$link1[30]
			<table class="child">
				<tr>
					<td class="player">$p1[30]</td>
					<td class="point">$px[30]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[30]</i>&nbsp&nbsp&nbsp<span style="color:black;">30</span></td>
				</tr>
				<tr>
					<td class="player">$p2[30]</td>
					<td class="point">$py[30]</td>
				</tr>
			</table>
			$link2[30]
		</td>
		<td class="parent" rowspan="2">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>5-е</b> - $place[5]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>6-е</b> - $place[6]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{20}-->
			$link1[20]
			<table class="child">
				<tr>
					<td class="los">-11</td>
					<td style="width:250px;">$p1[20]</td>
					<td style="width:25px;">$px[20]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[20]</i>&nbsp&nbsp&nbsp<span style="color:black;">20</span></td>
				</tr>
				<tr>
					<td class="los">-12</td>
					<td style="width:250px;">$p2[20]</td>
					<td style="width:25px;">$py[20]</td>
				</tr>
			</table>
			$link2[20]
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{29}-->
			$link1[29]
			<table class="child">
				<tr>
					<td class="los">-19</td>
					<td style="width:250px;">$p1[29]</td>
					<td style="width:25px;">$px[29]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[29]</i>&nbsp&nbsp&nbsp<span style="color:black;">29</span></td>
				</tr>
				<tr>
					<td class="los">-20</td>
					<td style="width:250px;">$p2[29]</td>
					<td style="width:25px;">$py[29]</td>
				</tr>
			</table>
			$link2[29]
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>7-е</b> - $place[7]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>8-е</b> - $place[8]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{13}-->
			$link1[13]
			<table class="child">
				<tr>
					<td class="los">-1</td>
					<td style="width:250px;">$p1[13]</td>
					<td style="width:25px;">$px[13]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[13]</i>&nbsp&nbsp&nbsp<span style="color:black;">13</span></td>
				</tr>
				<tr>
					<td class="los">-2</td>
					<td style="width:250px;">$p2[13]</td>
					<td style="width:25px;">$py[13]</td>
				</tr>
			</table>
			$link2[13]
		</td>
		<td class="parent" rowspan="2">
			<!--{21}-->
			$link1[21]
			<table class="child">
				<tr>
					<td class="player">$p1[21]</td>
					<td class="point">$px[21]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[21]</i>&nbsp&nbsp&nbsp<span style="color:black;">21</span></td>
				</tr>
				<tr>
					<td class="player">$p2[21]</td>
					<td class="point">$py[21]</td>
				</tr>
			</table>
			$link2[21]
		</td>
		<td class="parent" rowspan="4">
			<!--{28}-->
			$link1[28]
			<table class="child">
				<tr>
					<td class="player">$p1[28]</td>
					<td class="point">$px[28]</td>
				</tr>
				<tr style="height:250px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[28]</i>&nbsp&nbsp&nbsp<span style="color:black;">28</span></td>
				</tr>
				<tr>
					<td class="player">$p2[28]</td>
					<td class="point">$py[28]</td>
				</tr>
			</table>
			$link2[28]
		</td>
		<td class="parent" rowspan="4">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>9-е</b> - $place[9]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>10-е</b> - $place[10]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{14}-->
			$link1[14]
			<table class="child">
				<tr>
					<td class="los">-3</td>
					<td style="width:250px;">$p1[14]</td>
					<td style="width:25px;">$px[14]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[14]</i>&nbsp&nbsp&nbsp<span style="color:black;">14</span></td>
				</tr>
				<tr>
					<td class="los">-4</td>
					<td style="width:250px;">$p2[14]</td>
					<td style="width:25px;">$py[14]</td>
				</tr>
			</table>
			$link2[14]
		</td>
		<td class="parent"></td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{15}-->
			$link1[15]
			<table class="child">
				<tr>
					<td class="los">-5</td>
					<td style="width:250px;">$p1[15]</td>
					<td style="width:25px;">$px[15]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[15]</i>&nbsp&nbsp&nbsp<span style="color:black;">15</span></td>
				</tr>
				<tr>
					<td class="los">-6</td>
					<td style="width:250px;">$p2[15]</td>
					<td style="width:25px;">$py[15]</td>
				</tr>
			</table>
			$link2[15]
		</td>
		<td class="parent" rowspan="2">
			<!--{22}-->
			$link1[22]
			<table class="child">
				<tr>
					<td class="player">$p1[22]</td>
					<td class="point">$px[22]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[22]</i>&nbsp&nbsp&nbsp<span style="color:black;">22</span></td>
				</tr>
				<tr>
					<td class="player">$p2[22]</td>
					<td class="point">$py[22]</td>
				</tr>
			</table>
			$link2[22]
		</td>
		<td class="parent"></td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{16}-->
			$link1[16]
			<table class="child">
				<tr>
					<td class="los">-7</td>
					<td style="width:250px;">$p1[16]</td>
					<td style="width:25px;">$px[16]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[16]</i>&nbsp&nbsp&nbsp<span style="color:black;">16</span></td>
				</tr>
				<tr>
					<td class="los">-8</td>
					<td style="width:250px;">$p2[16]</td>
					<td style="width:25px;">$py[16]</td>
				</tr>
			</table>
			$link2[16]
		</td>
		<td class="parent"></td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{27}-->
			$link1[27]
			<table class="child">
				<tr>
					<td class="los">-21</td>
					<td style="width:250px;">$p1[27]</td>
					<td style="width:25px;">$px[27]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[27]</i>&nbsp&nbsp&nbsp<span style="color:black;">27</span></td>
				</tr>
				<tr>
					<td class="los">-22</td>
					<td style="width:250px;">$p2[27]</td>
					<td style="width:25px;">$py[27]</td>
				</tr>
			</table>
			$link2[27]
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>11-е</b> - $place[11]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>12-е</b> - $place[12]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{23}-->
			$link1[23]
			<table class="child">
				<tr>
					<td class="los">-13</td>
					<td style="width:250px;">$p1[23]</td>
					<td style="width:25px;">$px[23]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[23]</i>&nbsp&nbsp&nbsp<span style="color:black;">23</span></td>
				</tr>
				<tr>
					<td class="los">-14</td>
					<td style="width:250px;">$p2[23]</td>
					<td style="width:25px;">$py[23]</td>
				</tr>
			</table>
			$link2[23]
		</td>
		<td class="parent" rowspan="2">
			<!--{26}-->
			$link1[26]
			<table class="child">
				<tr>
					<td class="player">$p1[26]</td>
					<td class="point">$px[26]</td>
				</tr>
				<tr style="height:105px;">
					<td colspan="2" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[26]</i>&nbsp&nbsp&nbsp<span style="color:black;">26</span></td>
				</tr>
				<tr>
					<td class="player">$p2[26]</td>
					<td class="point">$py[26]</td>
				</tr>
			</table>
			$link2[26]
		</td>
		<td class="parent" rowspan="2">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>13-е</b> - $place[13]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>14-е</b> - $place[14]</p>
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{24}-->
			$link1[24]
			<table class="child">
				<tr>
					<td class="los">-15</td>
					<td style="width:250px;">$p1[24]</td>
					<td style="width:25px;">$px[24]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[24]</i>&nbsp&nbsp&nbsp<span style="color:black;">24</span></td>
				</tr>
				<tr>
					<td class="los">-16</td>
					<td style="width:250px;">$p2[24]</td>
					<td style="width:25px;">$py[24]</td>
				</tr>
			</table>
			$link2[24]
		</td>
	</tr>
	
	<tr>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent"></td>
		<td class="parent" style="padding-top:20px;padding-bottom:20px;">
			<!--{25}-->
			$link1[25]
			<table class="child">
				<tr>
					<td class="los">-23</td>
					<td style="width:250px;">$p1[25]</td>
					<td style="width:25px;">$px[25]</td>
				</tr>
				<tr style="height:15px;">
					<td colspan="3" style="border:0px;border-right:1px solid black;color:blue;"><i>$rounds[25]</i>&nbsp&nbsp&nbsp<span style="color:black;">25</span></td>
				</tr>
				<tr>
					<td class="los">-24</td>
					<td style="width:250px;">$p2[25]</td>
					<td style="width:25px;">$py[25]</td>
				</tr>
			</table>
			$link2[25]
		</td>
		<td class="parent">
			<p style="border-bottom:1px solid black;width:250px;margin-top:75px;"><b>15-е</b> - $place[15]</p>
			<p style="border-bottom:1px solid black;width:250px;margin-top:50px;"><b>16-е</b> - $place[16]</p>
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
