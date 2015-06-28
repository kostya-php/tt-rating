<?php
	if(((isset($_GET['t']))and(is_numeric($_GET['t'])))and((isset($_GET['n']))and(is_numeric($_GET['n'])))) {
		require_once "main.class.php";
		$main = new Main();
		$main->sql_connect();
		$tournament = $_GET['t'];
		$number = $_GET['n'];
		$checked = 1;
		
		$main->sql_query[1] = "SELECT * FROM tournaments WHERE id='$tournament'";
		$main->sql_execute(1);
		$row = mysql_fetch_array($main->sql_res[1]);
		$protocol = $row['protocol'];
		
		switch($protocol) {
			case "krug":
				$checked = 1;
				break;
			case "vib8":
				// Генерируем массив
				$games = Array(
					"1" => Array(5,7,9,11,12,13,14),
					"2" => Array(5,7,9,11,12,13,14),
					"3" => Array(6,8,10,11,12,13,14),
					"4" => Array(6,8,10,11,12,13,14),
					"5" => Array(9,13,14),
					"6" => Array(10,13,14),
					"7" => Array(9,11,12,13),
					"8" => Array(10,11,12,13),
					"9" => Array(12,13),
					"10" => Array(12,13)
				);
				// Генерируем запрос
				$query = "SELECT * FROM matches WHERE tournament='$tournament' AND (";
				for($i=0;$i<count($games[$number]);$i++) {
					$game = $games[$number][$i];
					$query.="number='$game'";
					if($i!=(count($games[$number])-1))
						$query.=" OR ";
				}
				$query.=")";
				
				$main->sql_query[1] = $query;
				$main->sql_execute(1);
				while($row = mysql_fetch_array($main->sql_res[1])) {
					$status = $row['status'];
					if($status > 1) $checked = 0;
				}
				break;
		}
		$main->sql_close();
		echo $checked;
	} else {
		echo 0;
	}
?>