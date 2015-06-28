<?php
	require_once "main.class.php";
	$main = new Main();
	if((isset($_POST['id']))and(is_numeric($_POST['id']))and(isset($_POST['option']))) {
		$id = $_POST['id'];
		$main->sql_connect();
		$main->sql_query[1] = "SELECT matches.*,
					t1.surname as surname1,
					t1.name as name1,
					t1.patronymic as patronymic1,
					t2.surname as surname2,
					t2.name as name2,
					t2.patronymic as patronymic2,
					t3.id as t_id,
					t3.name as name,
					t3.rounds as r,
					t3.protocol as p
				FROM matches
				LEFT JOIN players AS t1 ON t1.id=matches.player1
				LEFT JOIN players AS t2 ON t2.id=matches.player2
				LEFT JOIN tournaments AS t3 ON t3.id=matches.tournament
				WHERE matches.id='$id'";
		$main->sql_execute(1);
		$row = mysql_fetch_array($main->sql_res[1]);
		$tournament = $row['t_id']; // id турнира
		$protocol = $row['p']; // протокол турнира
		$number = $row['number'];
		$r = $row['r']; // количество партий в текущем турнире
		$min_win = ceil($r/2); // минимальное количество партий, необходимое для выйгрыша
		$option = $_POST['option'];
		switch($option) {
			case "none":
				// не выбрано никаких опций, проверяем введенные данные
				if((isset($_POST['x']))and(isset($_POST['y']))) {
					$error = false;
					$x = $_POST['x'];
					$y = $_POST['y'];
					$rounds = ""; // для запроса
					$xx = "";
					$yy = "";
					// проверяем соответсвие количества выйгранных партий
					if(((($x>$y)and($y>=0))or(($y>$x)and($x>=0)))and(($x==$min_win)or($y==$min_win))) {
						for($i=1;$i<=($x+$y);$i++) {
							if((isset($_POST['xx_'.$i]))and(isset($_POST['yy_'.$i]))) {
								$xx = $_POST['xx_'.$i];
								$yy = $_POST['yy_'.$i];
								// проверяем корректность ввода счета
								if((($xx>$yy)or($yy>$xx))and(($xx>=11)or($yy>=11))) {
									// проверяем корректность баланса
									if(($xx>11)or($yy>11)) {
										if($xx>$yy) {
											if(($xx-$yy)==2) {
												$rounds.=$xx.":".$yy;
												if($i!=($x+$y)) $rounds.=",";
											} else $error = true;
										} else {
											if($yy>$xx) {
												if(($yy-$xx)==2) {
													$rounds.=$xx.":".$yy;
													if($i!=($x+$y)) $rounds.=",";
												} else $error = true;
											}
										}
									} else {
										$rounds.=$xx.":".$yy;
										if($i!=($x+$y)) $rounds.=",";
									} 
								} else $error = true;
							} else $error = true;
						}
					} else $error = true;
					// если ошибок не обнаружено, выполняем запрос
					if(!$error) {
						$main->sql_query[1] = "UPDATE matches SET x='$x', y='$y', rounds='$rounds', status='2' WHERE id='$id'";
						$main->sql_execute(1);
					}
				}
				break;
			case "neyav":
				// выбрана опция - Неявка игроков
				if(($protocol!="vib8")and($protocol!="vib16")) {
					$main->sql_query[1] = "UPDATE matches SET x=null, y=null, rounds=null, status='4' WHERE id='$id'";
					$main->sql_execute(1);
					//Header("Location: tournaments.php?id=".$tournament);
				}
				break;
			case "tech_x":
				// выбрана опция - Техническое поражение (первый игрок)
				$main->sql_query[1] = "UPDATE matches SET x='0', y='$min_win', rounds=null, status='3' WHERE id='$id'";
				$main->sql_execute(1);
				break;
			case "tech_y":
				// выбрана опция - Техническое поражение (второй игрок)
				$main->sql_query[1] = "UPDATE matches SET x='$min_win', y='0', rounds=null, status='3' WHERE id='$id'";
				$main->sql_execute(1);
				break;
			case "pred":
				// выбрана опция - По результатам предыдущей встречи
				if(((isset($_POST['x']))and(is_numeric($_POST['x'])))and((isset($_POST['y']))and(is_numeric($_POST['y'])))) {
					$x = $_POST['x'];
					$y = $_POST['y'];
					if(((($x>$y)and($y>=0))or(($y>$x)and($x>=0)))and(($x==$min_win)or($y==$min_win))) {
						$main->sql_query[1] = "UPDATE matches SET x='$x', y='$y', rounds=null, status='5' WHERE id='$id'";
						$main->sql_execute(1);
						//Header("Location: tournaments.php?id=".$tournament);
					}
				}
				break;
			case "reset":
				// выбрана опция - Сброс результатов
				$main->sql_query[1] = "UPDATE matches SET x=null, y=null, rounds=null, status='1' WHERE id='$id'";
				$main->sql_execute(1);
				break;
		}
		/*
		if(isset($_POST['tech_x'])) {
			// выбрана опция - Техническое поражение (первый игрок)
			$main->sql_query[1] = "UPDATE matches SET x='0', y='$min_win', rounds=null, status='3' WHERE id='$id'";
			$main->sql_execute(1);
			//Header("Location: tournaments.php?id=".$tournament);
		} else
			if(isset($_POST['tech_y'])) {
				// выбрана опция - Техническое поражение (второй игрок)
				$main->sql_query[1] = "UPDATE matches SET x='$min_win', y='0', rounds=null, status='3' WHERE id='$id'";
				$main->sql_execute(1);
				//Header("Location: tournaments.php?id=".$tournament);
			} else
				if(isset($_POST['neyav'])) {
					// выбрана опция - Неявка игроков
					//echo "atata";
					if(($protocol!="vib8")and($protocol!="vib16")) {
						$main->sql_query[1] = "UPDATE matches SET x=null, y=null, rounds=null, status='4' WHERE id='$id'";
						$main->sql_execute(1);
						//Header("Location: tournaments.php?id=".$tournament);
					} else {
						$main->sql_query[1] = "UPDATE matches SET x=null, y=null, rounds=null, status='1' WHERE id='$id'";
						$main->sql_execute(1);
					}
				} else
					if(isset($_POST['pred'])) {
						// выбрана опция - По результатам предыдущей встречи
						if(((isset($_POST['x']))and(is_numeric($_POST['x'])))and((isset($_POST['y']))and(is_numeric($_POST['y'])))) {
							$x = $_POST['x'];
							$y = $_POST['y'];
							if(((($x>$y)and($y>=0))or(($y>$x)and($x>=0)))and(($x==$min_win)or($y==$min_win))) {
								$main->sql_query[1] = "UPDATE matches SET x='$x', y='$y', rounds=null, status='5' WHERE id='$id'";
								$main->sql_execute(1);
								//Header("Location: tournaments.php?id=".$tournament);
							}
						}
					} else {
						// не выбрано никаких опций, проверяем введенные данные
						if((isset($_POST['x']))and(isset($_POST['y']))) {
							$error = false;
							$x = $_POST['x'];
							$y = $_POST['y'];
							$rounds = ""; // для запроса
							$xx = "";
							$yy = "";
							// проверяем соответсвие количества выйгранных партий
							if(((($x>$y)and($y>=0))or(($y>$x)and($x>=0)))and(($x==$min_win)or($y==$min_win))) {
								for($i=1;$i<=($x+$y);$i++) {
									if((isset($_POST['xx_'.$i]))and(isset($_POST['yy_'.$i]))) {
										$xx = $_POST['xx_'.$i];
										$yy = $_POST['yy_'.$i];
										// проверяем корректность ввода счета
										if((($xx>$yy)or($yy>$xx))and(($xx>=11)or($yy>=11))) {
											// проверяем корректность баланса
											if(($xx>11)or($yy>11)) {
												if($xx>$yy) {
													if(($xx-$yy)==2) {
														$rounds.=$xx.":".$yy;
														if($i!=($x+$y)) $rounds.=",";
													} else $error = true;
												} else {
													if($yy>$xx) {
														if(($yy-$xx)==2) {
															$rounds.=$xx.":".$yy;
															if($i!=($x+$y)) $rounds.=",";
														} else $error = true;
													}
												}
											} else {
												$rounds.=$xx.":".$yy;
												if($i!=($x+$y)) $rounds.=",";
											} 
										} else $error = true;
									} else $error = true;
								}
							} else $error = true;
							// если ошибок не обнаружено, выполняем запрос
							if(!$error) {
								$main->sql_query[1] = "UPDATE matches SET x='$x', y='$y', rounds='$rounds', status='2' WHERE id='$id'";
								$main->sql_execute(1);
							}
						} else {
							//Header("Location: tournaments.php?id=".$tournament);
						}
					}
		*/
		
		/*
		if($protocol = "vib8") {
			$home_url = $_SERVER['HTTP_HOST'];
			$send = curl_init("http://$home_url/r_matches.php?t=$tournament&n=$number");
			curl_exec($send);
			curl_close($send);
		}
		*/
		
		Header("Location: tournaments.php?id=".$tournament);
		$main->sql_close();
	}
?>
