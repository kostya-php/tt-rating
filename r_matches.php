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
	$pg1 = "";
	$pg2 = "";
	$n1 = "";
	$n2 = "";
	if($protocol == "vib8") {
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
		//echo "true";
	}
	$main->sql_close();
	Header("Location: tournaments.php?id=".$tournament);
}
?>