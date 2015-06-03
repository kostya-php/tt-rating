<?php
	$page_name = "Игры";
	include "1_header.php";
	require_once "mysql.class.php";
	$main = new Mysql();
	$main->sql_connect();
	if(isset($_GET['id'])) {
		if(is_numeric($_GET['id'])) {
			$id = $_GET['id'];
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
				WHERE matches.id='$id'";
			$main->sql_execute(1);
			echo $main->sql_err[1];
			if(mysql_num_rows($main->sql_res[1])>0) {
				$row = mysql_fetch_array($main->sql_res[1]);
				$player1 = $row['surname1']." ".$row['name1'];//." ".$row['patronymic1'];
				$player2 = $row['surname2']." ".$row['name2'];//." ".$row['patronymic2'];
				$res = $row['x'].":".$row['y'];
				echo "<h2>$player1 $res $player2</h2>";
			}
		} else {
			Header ("Location: index.php");
		}
	} else {
		Header ("Location: index.php");
	}
	$main->sql_close();
	include "2_footer.php";
?>