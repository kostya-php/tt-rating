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
					t2.patronymic as patronymic2,
					t3.name as name,
					t3.rounds as r
				FROM matches
				LEFT JOIN players AS t1 ON t1.id=matches.player1
				LEFT JOIN players AS t2 ON t2.id=matches.player2
				LEFT JOIN tournaments AS t3 ON t3.id=matches.tournament
				WHERE matches.id='$id'";
			$main->sql_execute(1);
			echo $main->sql_err[1];
			if(mysql_num_rows($main->sql_res[1])>0) {
				$row = mysql_fetch_array($main->sql_res[1]);
				$tournament = $row['tournament'];
				echo "<a href=\"tournaments.php?id=$tournament\">Назад</a>";
				$player1 = NULL;
				$player2 = NULL;
				if(is_null($row['player1'])) {
					$player1 = "?";
				} else {
					$player1 = $row['surname1']." ".$row['name1'];//." ".$row['patronymic1'];
				}
				if(is_null($row['player2'])) {
					$player2 = "?";
				} else {
					$player2 = $row['surname2']." ".$row['name2'];//." ".$row['patronymic2'];
				}
				$x = NULL;
				$y = NULL;
				$xx = Array();
				$yy = Array();
				$r = $row['r'];
				$rounds = explode(",",$row['rounds']);
				$status = $row['status'];
				$checked3_1 = "";
				$checked3_2 = "";
				$checked4 = "";
				$checked5 = "";
				switch($status) {
					case 0:
						// соперники не определены
						$x = "0";
						$y = "0";
						for($i=0;$i<$r;$i++) {
							$xx[$i] = "";
							$yy[$i] = "";
						}
						break;
					case 1:
						// не играли
						$x = "0";
						$y = "0";
						for($i=0;$i<$r;$i++) {
							$xx[$i] = "";
							$yy[$i] = "";
						}
						break;
					case 2:
						// сыграли
						$x = $row['x'];
						$y = $row['y'];
						for($i=0;$i<count($rounds);$i++) {
							$temp = explode(":",$rounds[$i]);
							$xx[$i] = $temp[0];
							$yy[$i] = $temp[1];
						}
						break;
					case 3:
						// техническое поражение
						$x = $row['x'];
						$y = $row['y'];
						if($y>$x)$checked3_1 = " CHECKED";
						if($x>$y)$checked3_2 = " CHECKED";
						for($i=0;$i<$r;$i++) {
							$xx[$i] = "";
							$yy[$i] = "";
						}						
						break;
					case 4:
						// неявка / не играли
						$x = "0";
						$y = "0";
						for($i=0;$i<$r;$i++) {
							$xx[$i] = "";
							$yy[$i] = "";
						}
						$checked4 = " CHECKED";
						break;
					case 5:
						// по результатам предыдущей встречи
						$x = $row['x'];
						$y = $row['y'];
						for($i=0;$i<$r;$i++) {
							$xx[$i] = "";
							$yy[$i] = "";
						}
						$checked5 = " CHECKED";
						break;
				}				
				//echo "<h2>$player1 $res $player2</h2>";
				//var_dump($row['r']);
				echo <<<ATATA
<form id="edit_match" action="edit_match.php" method="post">
<input id="id" name="id" type="hidden" value="$id">
<input id="status" name="status" type="hidden" value="$status">
<input id="match_rounds" name="match_rounds" type="hidden" value="0">
<input id="rounds" name="rounds" type="hidden" value="$r">
<table style="margin-top:10px;">
	<tr>
		<td></td>
		<td style="text-align:center;">
			<p><b>$player1</b></p>
			<p></p>
		</td>
		<td style="text-align:center;">
			<p><b>$player2</b></p>
			<p></p>
		</td>
	</tr>
	<tr>
		<td style="text-align:center;">Партия</td>
		<td style="text-align:center;">
			<input style="text-align:center;font-size:20px;" size="2" type="text" id="x" name="x" value="$x" tabindex="1" READONLY>
		</td>
		<td style="text-align:center;">
			<input style="text-align:center;font-size:20px;" size="2" type="text" id="y" name="y" value="$y" tabindex="1" READONLY>
		</td>
	</tr>
ATATA;
				$xxx = "";
				$yyy = "";
				for($i=0;$i<$r;$i++) {
					$j = $i+1;
					if(isset($xx[$i])) $xxx = $xx[$i];
						else $xxx = "";
					if(isset($yy[$i])) $yyy = $yy[$i];
						else $yyy = "";
					echo <<<ATATA
	<tr>
		<td style="text-align:center;">$j</td>
		<td style="text-align:center;">
			<input id="xx_$j" type="text" value="$xxx" name="xx_$j" style="text-align:center;" size="2" onkeyup="check_rounds();" tabindex="1">
		</td>
		<td style="text-align:center;">
			<input id="yy_$j" type="text" value="$yyy" name="yy_$j" style="text-align:center;" size="2" onkeyup="check_rounds();" tabindex="1">
		</td>
	</tr>
ATATA;
				}
				echo <<<ATATA
	<tr>
		<td colspan="3">
			<p>Дополнительные опции:</p>
			<ul type="none" style="margin:5px;padding:5px;">
				<li><input OnClick="tech('xy');" id="neyav" type="checkbox" value="1" name="neyav" tabindex="1"$checked4>Неявка игроков</li>
				<li><input OnClick="tech('x');" id="tech_x" name="tech_x" type="checkbox" value="1" tabindex="1"$checked3_1>Техническое поражение: $player1</li>
				<li><input OnClick="tech('y');" id="tech_y" name="tech_y" type="checkbox" value="1" tabindex="1"$checked3_2>Техническое поражение: $player2</li>
				<li>
					<input OnClick="tech('pred');" id="pred" name="pred" type="checkbox" value="1"$checked5>По результатам предыдущей встречи
				</li>
			</ul>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align:center;">
			<p></p>
			<input type="submit" value="Изменить" tabindex="1">
		</td>
	</tr>
</table>
</form>
ATATA;
			}
		} else {
			Header ("Location: index.php");
		}
	} else {
		Header ("Location: index.php");
	}
	$main->sql_close();
	echo <<<ATATA
	<script type="text/javascript">
	check_rounds();
	check_tech();
	</script>
ATATA;
	include "2_footer.php";
?>
