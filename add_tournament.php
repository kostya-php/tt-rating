<?php
	$page_name = "Добавить турнир";
	
	include "1_header.php";
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		// =============================
		// если были уже введены какие-либо данные
		// =============================
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
		if(isset($_POST['players']))$playerArray = $_POST['players']; else $playerArray = NULL;
		
		$error = NULL;
		
		if(!preg_match("/^[0-9а-яёa-z-\s.,!]+$/iu",$name)) {
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
		if($playerArray==NULL) {
			$error.="<p style=\"color:red;\">Не выбраны игроки для турнира</p>";
		}
		if($error!=NULL) {
			// ============
			// если есть ошибки
			// ============
			echo <<<ATATA
$error
<button onclick="location.href='add_tournament.php'" style="color:blue;font-size:10px;">Назад</button>
ATATA;
		} else {
			// =================
			// если ошибки отсутствуют
			// =================
			$main->sql_connect();
			
			$query = mysql_query("SHOW TABLE STATUS FROM `".$main->sql_database."` LIKE 'tournaments'");
			$tournament = mysql_result($query, 0, "Auto_increment");
			$membership = count($playerArray);
			
			$i = 0; // счетчик для нумерации запросов в БД
			$main->sql_query[$i] = "INSERT INTO tournaments VALUES (null,'$name','$name_translit','$date','0','$rounds','$protocol','$note')";			
			
			foreach($playerArray as $player) {
				$i++;
				$main->sql_query[$i] = "INSERT INTO in_tournament VALUES (null,'$tournament','$i','$player')";
			}
			
			switch($protocol) {
				case "krug":
					switch($membership) {
						case 3: {
							$matches = "2-3,3-1,1-2";
							break;
						}
						case 4: {
							$matches = "1-4,2-3,3-1,4-2,1-2,3-4";
							break;
						}
						case 5: {
							$matches = "2-5,3-4,5-1,2-3,1-4,5-3,3-1,4-2,1-2,4-5";
							break;
						}
						case 6: {
							$matches = "1-6,2-5,3-4,5-1,6-4,2-3,1-4,5-3,6-2,3-1,4-2,5-6,1-2,3-6,4-5";
							break;
						}
						case 7: {
							$matches = "2-7,3-6,4-5,7-1,2-5,3-4,1-6,7-5,2-3,5-1,6-4,7-3,1-4,5-3,6-2,3-1,4-2,6-7,1-2,4-7,5-6";
							break;
						}
						case 8: {
							$matches = "1-8,2-7,3-6,4-5,7-1,8-6,2-5,3-4,1-6,7-5,8-4,2-3,5-1,6-4,7-3,8-2,1-4,5-3,6-2,7-8,3-1,4-2,5-8,6-7,1-2,3-8,4-7,5-6";
							break;
						}
						case 9: {
							$matches = "2-9,3-8,4-7,5-6,9-1,2-7,3-6,4-5,1-8,9-7,2-5,3-4,7-1,8-6,9-5,2-3,1-6,7-5,8-4,9-3,5-1,6-4,7-3,8-2,1-4,5-3,6-2,8-9,3-1,4-2,6-9,7-8,1-2,4-9,5-8,6-7";
							break;
						}
						case 10: {
							$matches = "1-10,2-9,3-8,4-7,5-6,9-1,10-8,2-7,3-6,4-5,1-8,9-7,10-6,2-5,3-4,7-1,8-6,9-5,10-4,2-3,1-6,7-5,8-4,9-3,10-2,5-1,6-4,7-3,8-2,9-10,1-4,5-3,6-2,7-10,8-9,3-1,4-2,5-10,6-9,7-8,1-2,3-10,4-9,5-8,6-7";
							break;
						}
						case 11: {
							$matches = "2-11,3-10,4-9,5-8,6-7,11-1,2-9,3-8,4-7,5-6,1-10,11-9,2-7,3-6,4-5,9-1,10-8,11-7,2-5,3-4,1-8,9-7,10-6,11-5,2-3,7-1,8-6,9-5,10-4,11-3,1-6,7-5,8-4,9-3,10-2,5-1,6-4,7-3,8-2,10-11,1-4,5-3,6-2,8-11,9-10,3-1,4-2,6-11,7-10,8-9,1-2,4-11,5-10,6-9,7-8";
							break;
						}
						case 12: {
							$matches = "1-12,2-11,3-10,4-9,5-8,6-7,11-1,12-10,2-9,3-8,4-7,5-6,1-10,11-9,12-8,2-7,3-6,4-5,9-1,10-8,11-7,12-6,2-5,3-4,1-8,9-7,10-6,11-5,12-4,2-3,7-1,8-6,9-5,10-4,11-3,12-2,1-6,7-5,8-4,9-3,10-2,11-12,5-1,6-4,7-3,8-2,9-12,10-11,1-4,5-3,6-2,7-12,8-11,9-10,3-1,4-2,5-12,6-11,7-10,8-9,1-2,3-12,4-11,5-10,6-9,7-8";
							break;
						}
					}
					
					$match = explode(",",$matches);
					
					for($j=0;$j<count($match);$j++) {
						$i++;
						$players = explode("-",$match[$j]);
						$main->sql_query[$i] = "INSERT INTO matches VALUES (null,'$tournament','".($j+1)."','".$playerArray[$players[0]-1]."','".$playerArray[$players[1]-1]."',null,null,'','1')";
					}
					
					break;
				case "vib8":
					$p1 = 1;
					$p2 = 2;
					$k = 1; // счетчик для нумерации игр
					for($j=0;$j<($membership/2);$j++) {
						$i++;
						$main->sql_query[$i] = "INSERT INTO matches VALUES(null,'$tournament','$k','".$playerArray[$p1-1]."','".$playerArray[$p2-1]."',null,null,null,'1')";
						$p1+=2;
						$p2+=2;
						$k++;
					}
					for($j=0;$j<10;$j++) {
						$i++;
						$main->sql_query[$i] = "INSERT INTO matches VALUES(null,'$tournament','$k',null,null,null,null,null,'0')";
						$k++;
					}
					break;
				case "vib16":
					exit("vib16 в разработке");
					break;
			}
			for($j=0;$j<count($main->sql_query);$j++) {
				$main->sql_execute($j);
			}
			
			$main->sql_close();
			
			Header ("Location: tournaments.php?id=$tournament");
		}
	} else {
		// ============================
		// если никаких данных не было отправлено
		// ============================
		$main->sql_connect();
		echo <<<ATATA
<h2>Добавить турнир</h2>
<form class="add-tournament" action="add_tournament.php" method="post" autocomplete="off">
<table>
	<tr>
		<td>Название турнира*:</td>
		<td><input class="name" name="name" type="text" value="" style="width: 250px;" tabindex="1"></td>
		<td rowspan="5" valign="top">
			<p>Выбрано игроков: <span class="selected-players" style="color:red;">0</span></p>
			<select name="players[]" class="player-select" data-placeholder="Выбор игроков" multiple="multiple" tabindex="6">
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
		<td>Дата*:</td>
		<td><input class="date" name="date" type="text" value="" style="width: 75px;" tabindex="2" READONLY></td>
	</tr>
	<tr>
		<td>Количество партий*:</td>
		<td>
			<select class="rounds" name="rounds" tabindex="3">
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
			<select class="protocol" name="protocol" tabindex="4">
				<option value=""></option>
				<option value="krug">По круговой системе (от 3 до 12 человек)</option>
				<option value="vib8">На выбывание (8 человек)</option>
				<option value="vib16">На выбывание (16 человек)</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Примечание:</td>
		<td>
			<textarea name="note" style="width: 250px;" tabindex="5"></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align:center;"><input class="submit" type="submit" value="Добавить" style="color:red;" tabindex="7"></td>
	</tr>
</table>
</form>
ATATA;
	$main->sql_close();
	}
	echo <<<ATATA
	<script type="text/javascript">
		check_atform();
	</script>
ATATA;
	include "2_footer.php";
?>
