<?php
	$page_name = "Добавить турнир";
	
	include "1_header.php";
	// если были уже введены какие-либо данные
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		
	} else {
		require_once "mysql.class.php";
		$main = new Mysql();
		$main->sql_connect();
		echo <<<ATATA
<h2>Добавить турнир</h2>
<form action="add_tournament.php" method="post">
<table>
	<tr>
		<td>Название турнира*:</td>
		<td><input name="name" type="text" value="" style="width: 250px;"></td>
	</tr>
	<tr>
		<td>Дата*:</td>
		<td><input name="date" type="text" value="" style="width: 250px;"></td>
	</tr>
	<tr>
		<td>Количество партий:</td>
		<td>
			<select name="rounds">
				<option value="3">3</option>
				<option value="5">5</option>
				<option value="7">7</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Тип протокола:</td>
		<td>
			<select name="protocol">
				<option value="krug">По круговой системе</option>
				<option value="vib8">На выбывание (8 человек)</option>
				<option value="vib16">На выбывание (16 человек)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Примечание:</td>
		<td>
			<textarea name="note" style="width: 250px;"></textarea>
		</td>
	</tr>
	<tr>
		<td>Игроки:</td>
		<td>
			<select class="player-select" data-placeholder="Выбор игроков" multiple="multiple">
				<option value=""></option>
ATATA;
	$main->sql_query[1] = "SELECT * FROM players ORDER BY surname ASC";
	$main->sql_execute(1);
	while($row = mysql_fetch_array($main->sql_res[1])) {
		$id = $row['id'];
		$surname = $row['surname'];
		$name = $row['name'];
		$patronymic = $row['patronymic'];
		echo <<<ATATA
				<option value="$id">
					$surname $name $patronymic
				</option>
ATATA;
	}
echo <<< ATATA
			</select>
		</td>
	</tr>
</table>
</form>
ATATA;
	$main->sql_close();
	}
	include "2_footer.php";
?>