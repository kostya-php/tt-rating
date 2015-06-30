<?php
	if(isset($_POST['submit'])) {
		
		if((isset($_POST['id']))and(is_numeric($_POST['id'])))
			if(($_POST['submit']=="OK")and(is_numeric($_POST['id']))) {
				require_once "main.class.php";
				$main = new Main();
				$id = $_POST['id'];
				$main->sql_connect();
				$main->sql_query[1] = "DELETE FROM tournaments WHERE id='$id'";
				$main->sql_execute(1);
				$main->sql_query[1] = "DELETE FROM in_tournament WHERE tournament='$id'";
				$main->sql_execute(1);
				$main->sql_query[1] = "DELETE FROM matches WHERE tournament='$id'";
				$main->sql_execute(1);
				$main->sql_close();
				Header ("Location: tournaments.php");
			} else {
				$id = $_POST['id'];
				Header ("Location: tournaments.php?id=$id");
			}
		} else {
			if((isset($_POST['name']))and(is_numeric($_POST['id']))) {
				$page_name = "Главная";
				include "1_header.php";
				$id = $_POST['id'];
				$name = $_POST['name'];
				echo <<<ATATA
<table>
	<tr>
		<td colspan="2" style="border:0px;text-align:center;"><h2 style="color:red;">Удалить турнир?</h2><h3>"$name"</h3></td>
	</tr>
	<tr>
		<td style="border:0px;text-align:center;">
			<form action="delete_tournament.php" method="post">
				<input name="id" type="hidden" value="$id">
				<input name="submit" type="submit" value="OK">
			</form>
		</td>
		<td style="border:0px;text-align:center;">
			<form action="delete_tournament.php" method="post">
				<input name="id" type="hidden" value="$id">
				<input name="submit" type="submit" value="Отмена">
			</form>
		</td>
	</tr>
</table>
ATATA;
				include "2_footer.php";
			} else {
				Header ("Location: tournaments.php");
			}
		}
?>