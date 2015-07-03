<?php
require_once "main.class.php";
$main = new Main();

if(((isset($_GET['t']))and(is_numeric($_GET['t'])))and((isset($_GET['n']))and(is_numeric($_GET['n'])))) {
	$tournament = $_GET['t'];
	$number = $_GET['n'];
	$main->sql_connect();
	$main->sql_query[1] = "SELECT * FROM tournaments WHERE id='$tournament'";
	$main->sql_execute(1);
	$row = mysql_fetch_array($main->sql_res[1]);
	$protocol = $row['protocol'];
	$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
	$main->sql_execute(1);
	$row = mysql_fetch_array($main->sql_res[1]);
	$x = $row['x'];
	$y = $row['y'];
	$player1 = "";
	$player2 = "";
	$n1 = "";
	$n2 = "";
	if($protocol == "vib8") {
		$pp = Array(
			"1"	=> Array(1,1),
			"2"	=> Array(2,2),
			"3"	=> Array(1,1),
			"4"	=> Array(2,2),
			"5"	=> Array(1,1),
			"6"	=> Array(2,2),
			"7"	=> Array(2,1),
			"8"	=> Array(1,2),
			"9"	=> Array(1,1),
			"10"	=> Array(2,2)
		);
		$nn = Array(
			"1"	=> Array(5,7),
			"2"	=> Array(5,7),
			"3"	=> Array(6,8),
			"4"	=> Array(6,8),
			"5"	=> Array(14,9),
			"6"	=> Array(14,10),
			"7"	=> Array(9,11),
			"8"	=> Array(10,11),
			"9"	=> Array(13,12),
			"10"	=> Array(13,12)
		);
		$player1 = $pp[$number][0];
		$player2 = $pp[$number][1];
		$n1 = $nn[$number][0];
		$n2 = $nn[$number][1];
		if((!is_null($x))and(!is_null($y))) {
			if($x > $y) {
				$p1 = "'".$row['player1']."'";
				$p2 = "'".$row['player2']."'";
			}
			if($x < $y) {
				$p1 = "'".$row['player2']."'";
				$p2 = "'".$row['player1']."'";
			}
		} else {
			if($row['status'] == "1") {
				$p1 = "null";
				$p2 = "null";
			}
		}
		$main->sql_query[1] = "UPDATE matches SET player".$player1."=$p1 WHERE number='$n1' AND tournament='$tournament'";
		$main->sql_execute(1);
		$main->sql_query[1] = "UPDATE matches SET player".$player2."=$p2 WHERE number='$n2' AND tournament='$tournament'";
		$main->sql_execute(1);

		$main->sql_query[1] = "SELECT * FROM matches WHERE tournament='$tournament' AND number>4";
		$main->sql_execute(1);
		
		while($row = mysql_fetch_array($main->sql_res[1])) {
			$match_id = $row['id'];
			if((!is_null($row['player1']))and(!is_null($row['player2']))and($row['status']=="0")) {
				$main->sql_query[2] = "UPDATE matches SET status='1' WHERE id='$match_id'";
				$main->sql_execute(2);
			} else
				if((is_null($row['player1']))or(is_null($row['player2']))) {
					$main->sql_query[2] = "UPDATE matches SET status='0' WHERE id='$match_id'";
					$main->sql_execute(2);
				}
		}
		/*
		switch($number) {
			case 1:
				$pg1 = "player1";
				$pg2 = "player1";
				$n1 = "'5'";
				$n2 = "'7'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 2:
				$pg1 = "player2";
				$pg2 = "player2";
				$n1 = "'5'";
				$n2 = "'7'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 3:
				$pg1 = "player1";
				$pg2 = "player1";
				$n1 = "'6'";
				$n2 = "'8'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 4:
				$pg1 = "player2";
				$pg2 = "player2";
				$n1 = "'6'";
				$n2 = "'8'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 5:
				$pg1 = "player1";
				$pg2 = "player1";
				$n1 = "'14'";
				$n2 = "'9'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 6:
				$pg1 = "player2";
				$pg2 = "player2";
				$n1 = "'14'";
				$n2 = "'10'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 7:
				$pg1 = "player2";
				$pg2 = "player1";
				$n1 = "'9'";
				$n2 = "'11'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 8:
				$pg1 = "player1";
				$pg2 = "player2";
				$n1 = "'10'";
				$n2 = "'11'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 9:
				$pg1 = "player1";
				$pg2 = "player1";
				$n1 = "'13'";
				$n2 = "'12'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
			case 10:
				$pg1 = "player2";
				$pg2 = "player2";
				$n1 = "'13'";
				$n2 = "'12'";
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = "'".$row['player1']."'";
						$p2 = "'".$row['player2']."'";
					}
					if($x < $y) {
						$p1 = "'".$row['player2']."'";
						$p2 = "'".$row['player1']."'";
					}
				} else {
					if($row['status'] == "1") {
						$p1 = "null";
						$p2 = "null";
					}
				}
				break;
		}
		
		$main->sql_query[1] = "UPDATE matches SET $pg1=$p1 WHERE number=$n1 AND tournament='$tournament'";
		$main->sql_execute(1);
		$main->sql_query[1] = "UPDATE matches SET $pg1=$p2 WHERE number=$n2 AND tournament='$tournament'";
		$main->sql_execute(1);
					
		$main->sql_query[1] = "SELECT * FROM matches WHERE tournament='$tournament' AND number>4";
		$main->sql_execute(1);
		while($row = mysql_fetch_array($main->sql_res[1])) {
			$match_id = $row['id'];
			$p1 = $row['player1'];
			$p2 = $row['player2'];
			if((!is_null($row['player1']))and(!is_null($row['player2']))and($row['status']=="0")) {
				$main->sql_query[2] = "UPDATE matches SET status='1' WHERE id='$match_id'";
				$main->sql_execute(2);
			} else
				if((is_null($row['player1']))or(is_null($row['player2']))) {
					$main->sql_query[2] = "UPDATE matches SET status='0' WHERE id='$match_id'";
					$main->sql_execute(2);
				}
		}
		*/
	} else
		if($protocol == "vib16") {
			switch($number) {
				case 1:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'9'";
					$n2 = "'13'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 2:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'9'";
					$n2 = "'13'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 3:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'10'";
					$n2 = "'14'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 4:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'10'";
					$n2 = "'14'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 5:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'11'";
					$n2 = "'15'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 6:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'11'";
					$n2 = "'15'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 7:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'12'";
					$n2 = "'16'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 8:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'12'";
					$n2 = "'16'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 9:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'17'";
					$n2 = "'19'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 10:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'17'";
					$n2 = "'19'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 11:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'18'";
					$n2 = "'20'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 12:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'18'";
					$n2 = "'20'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 13:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'21'";
					$n2 = "'23'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 14:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'21'";
					$n2 = "'23'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 15:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'22'";
					$n2 = "'24'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 16:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'22'";
					$n2 = "'24'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 17:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'32'";
					$n2 = "'31'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 18:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'32'";
					$n2 = "'31'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 19:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'30'";
					$n2 = "'29'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 20:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'30'";
					$n2 = "'29'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 21:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'28'";
					$n2 = "'27'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 22:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'28'";
					$n2 = "'27'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 23:
					$pg1 = "player1";
					$pg2 = "player1";
					$n1 = "'26'";
					$n2 = "'25'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
				case 24:
					$pg1 = "player2";
					$pg2 = "player2";
					$n1 = "'26'";
					$n2 = "'25'";
					if((!is_null($x))and(!is_null($y))) {
						if($x > $y) {
							$p1 = "'".$row['player1']."'";
							$p2 = "'".$row['player2']."'";
						}
						if($x < $y) {
							$p1 = "'".$row['player2']."'";
							$p2 = "'".$row['player1']."'";
						}					
					} else {
						if($row['status'] == "1") {
							$p1 = "null";
							$p2 = "null";
						}
					}
					break;
			}
			
			$main->sql_query[1] = "UPDATE matches SET $pg1=$p1 WHERE number=$n1 AND tournament='$tournament'";
			$main->sql_execute(1);
			$main->sql_query[1] = "UPDATE matches SET $pg1=$p2 WHERE number=$n2 AND tournament='$tournament'";
			$main->sql_execute(1);
						
			$main->sql_query[1] = "SELECT * FROM matches WHERE tournament='$tournament' AND number>4";
			$main->sql_execute(1);
			while($row = mysql_fetch_array($main->sql_res[1])) {
				$match_id = $row['id'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if((!is_null($row['player1']))and(!is_null($row['player2']))and($row['status']=="0")) {
					$main->sql_query[2] = "UPDATE matches SET status='1' WHERE id='$match_id'";
					$main->sql_execute(2);
				} else
					if((is_null($row['player1']))or(is_null($row['player2']))) {
						$main->sql_query[2] = "UPDATE matches SET status='0' WHERE id='$match_id'";
						$main->sql_execute(2);
					}
			}
		}
		
	$main->sql_close();
	Header("Location: tournaments.php?id=".$tournament);
}
?>