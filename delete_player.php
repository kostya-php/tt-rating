<?php
	if(isset($_POST['submit'])) {
		if(($_POST['submit']=="OK")and(is_numeric($_POST['id']))) {
			$id = $_POST['id'];
			$main->sql_connect();
			$main->sql_query[1] = "DELETE FROM players WHERE id='$id'";
			$main->sql_execute(1);
			$main->sql_close();
			Header ("Location: players.php");
		} else {
			Header ("Location: players.php");
		}
	} else {
		if((isset($_POST['fullname']))and(is_numeric($_POST['id']))) {
			$page_name = "Главная";
			include "1_header.php";
			$id = $_POST['id'];
			$fullname = $_POST['fullname'];
			echo <<<ATATA
<table>
	<tr>
		<td colspan="2" style="border:0px;text-align:center;"><h2 style="color:red;">Удалить игрока?</h2><h3>"$fullname"</h3></td>
	</tr>
	<tr>
		<td style="border:0px;text-align:center;">
			<form action="delete_player.php" method="post">
				<input name="id" type="hidden" value="$id">
				<input name="submit" type="submit" value="OK">
			</form>
		</td>
		<td style="border:0px;text-align:center;">
			<form action="delete_player.php" method="post">
				<input name="submit" type="submit" value="Отмена">
			</form>
		</td>
	</tr>
</table>
ATATA;
			include "2_footer.php";
		} else {
			Header ("Location: players.php");
		}
	}
?>
