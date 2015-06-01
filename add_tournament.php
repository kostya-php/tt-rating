<?php
	$page_name = "Добавить турнир";
	
	include "1_header.php";
	require_once "mysql.class.php";
	$main = new Mysql();
	// если были уже введены какие-либо данные
	if($_SERVER['REQUEST_METHOD'] == "POST") {
			
		$roundsArray = Array ("3","5","7");
		$protocolArray = Array ("krug","vib8","vib16");
		
		if(isset($_POST['name'])) {
			$name = $_POST['name'];
			$name_translit = $main->rus2translit($name);
		} else {
			$name = NULL;
			$name_translit = NULL;
		}
		if(isset($_POST['date']))$date = $_POST['date']; else $date = NULL;
		if(isset($_POST['rounds']))$rounds = $_POST['rounds']; else $rounds = NULL;
		if(isset($_POST['protocol']))$protocol = $_POST['protocol']; else $protocol = NULL;
		if(isset($_POST['note']))$note = $_POST['note']; else $note = NULL;
		if(isset($_POST['players']))$players = $_POST['players']; else $players = NULL;
		
		$error = NULL;
		
		if(!preg_match("/^[0-9а-яёa-z-\s.,]+$/iu",$name)) {
			$error.="<p style=\"color:red;\">Неверно заполнено поле \"Название турнира\"</p>";
		}
		
		if(!$main->validateDate($date, "Y-m-d")) {
			$error.="<p style=\"color:red;\">Неверно заполнено поле \"Дата\"</p>";
		}
		
		if(!in_array($rounds,$roundsArray)) {
			$error .="<p style=\"color:red;\">Неверно выбрано количество партий</p>";				
		}
			
		if(!in_array($protocol,$protocolArray)) {
			$error.="<p style=\"color:red;\">Неверно выбран тип протокола</p>";				
		}
		
		if($note!="") {
			if (!preg_match("/^[0-9а-яёa-z-\s\/.,()]+$/iu",$note)) {
				$error.="<p style=\"color:red;\">Неверно заполнено поле \"Примечание\"</p>";
			}
		}
		if($players==NULL) {
			$error.="<p style=\"color:red;\">Не выбраны игроки для турнира</p>";
		}
		if($error!=NULL) {
		// если есть ошибки
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
		<td><input class="datepicker" name="date" type="text" value="" style="width: 75px;"></td>
	</tr>
	<tr>
		<td>Количество партий*:</td>
		<td>
			<select name="rounds">
				<option value=""></option>
				<option value="3">3 (до 2-х побед)</option>
				<option value="5">5 (до 3-х побед)</option>
				<option value="7">7 (до 4-х побед)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Тип протокола:*</td>
		<td>
			<select name="protocol">
				<option value=""></option>
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
		<td>Игроки*:</td>
		<td>
			<select name="players[]" class="player-select" data-placeholder="Выбор игроков" multiple="multiple">
				<option value=""></option>
ATATA;
			$main->sql_connect();
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
	<tr>
		<td colspan="2" style="text-align:center;"><input type="submit" value="Добавить" style="color:blue;"></td>
	</tr>
</table>
</form>
$error
ATATA;
			$main->sql_close();
		} else {
			// если ошибки отсутствуют
			$main->sql_connect();
			
			$query = mysql_query("SHOW TABLE STATUS FROM `".$main->sql_database."` LIKE 'tournaments'");
			$tournament = mysql_result($query, 0, "Auto_increment");
			
			$main->sql_query[1] = "INSERT INTO tournaments VALUES (null,'$name','$name_translit','$date','0','$rounds','$protocol','$note')";
			$main->sql_execute(1);
			
			$i = 0;
			foreach($players as $player) {
				$i++;
				$main->sql_query[2] = "INSERT INTO in_tournament VALUES (null,'$tournament','$i','$player')";
				$main->sql_execute(2);
			}
			
			$main->sql_close();
			
			Header ("Location: tournaments.php?id=$tournament");
		}
	} else {
		// если никаких данных не было отправлено
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
		<td><input class="datepicker" name="date" type="text" value="" style="width: 75px;"></td>
	</tr>
	<tr>
		<td>Количество партий*:</td>
		<td>
			<select name="rounds">
				<option value=""></option>
				<option value="3">3 (до 2-х побед)</option>
				<option value="5">5 (до 3-х побед)</option>
				<option value="7">7 (до 4-х побед)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Тип протокола:*</td>
		<td>
			<select name="protocol">
				<option value=""></option>
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
		<td>Игроки*:</td>
		<td>
			<select name="players[]" class="player-select" data-placeholder="Выбор игроков" multiple="multiple">
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
	<tr>
		<td colspan="2" style="text-align:center;"><input type="submit" value="Добавить" style="color:blue;"></td>
	</tr>
</table>
</form>
ATATA;
	$main->sql_close();
	}
	include "2_footer.php";
?>