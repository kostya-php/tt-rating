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
	if($protocol == "vib8") {
		switch($number) {
			case 1:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				
				if((!is_null($x))and(!is_null($y))) {
					if($x > $y) {
						$p1 = $row['player1'];
						$p2 = $row['player2'];
					}
					if($x < $y) {
						$p1 = $row['player2'];
						$p2 = $row['player1'];
					}
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='5' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='7' AND tournament='$tournament'";
					$main->sql_execute(1);
				} else {
					if($row['status'] == "1") {
						$main->sql_query[1] = "UPDATE matches SET player1=null WHERE number='5' AND tournament='$tournament'";
						$main->sql_execute(1);
						$main->sql_query[1] = "UPDATE matches SET player1=null WHERE number='7' AND tournament='$tournament'";
						$main->sql_execute(1);
					}
				}
				/*
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='5' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='7' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='5' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='7' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				*/
				break;
			case 2:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='5' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='7' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='5' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='7' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 3:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='6' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='8' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='6' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='8' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 4:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='6' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='8' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='6' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='8' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 5:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='14' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='9' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='14' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='9' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 6:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='14' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='10' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='14' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='10' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 7:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='9' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='11' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='9' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='11' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 8:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='10' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='11' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='10' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='11' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 9:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='13' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='12' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player1='$p2' WHERE number='13' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player1='$p1' WHERE number='12' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
			case 10:
				$main->sql_query[1] = "SELECT * FROM matches WHERE number='$number' AND tournament='$tournament'";
				$main->sql_execute(1);
				$row = mysql_fetch_array($main->sql_res[1]);
				$x = $row['x'];
				$y = $row['y'];
				$p1 = $row['player1'];
				$p2 = $row['player2'];
				if($x > $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='13' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='12' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				if($x < $y) {
					$main->sql_query[1] = "UPDATE matches SET player2='$p2' WHERE number='13' AND tournament='$tournament'";
					$main->sql_execute(1);
					$main->sql_query[1] = "UPDATE matches SET player2='$p1' WHERE number='12' AND tournament='$tournament'";
					$main->sql_execute(1);
				}
				break;
		}
		$main->sql_query[1] = "SELECT * FROM matches WHERE tournament='$tournament' AND number>4";
		$main->sql_execute(1);
		while($row = mysql_fetch_array($main->sql_res[1])) {
			$match_id = $row['id'];
			$p1 = $row['player1'];
			$p2 = $row['player2'];
			if((!is_null($row['player1']))and(!is_null($row['player2']))) {
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
}
?>