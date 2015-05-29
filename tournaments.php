<?php
	$page_name = "Турниры";
	include "1_header.php";
	require_once "mysql.class.php";
	$main = new Mysql();
	$main->sql_connect();
	if(isset($_GET['id'])) {
		
	} else {
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
	
	include "2_footer.php";
?>