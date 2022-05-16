<?php
function debug_to_console($data)
{
	$output = $data;
	if (is_array($output))
		$output = implode(',', $output);

	echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
error_reporting(E_ALL);
ini_set("display_errors", 1);

SESSION_START();
if (isset($_SESSION["login"])) {
	if ($_SESSION["login"] == 0) {
		die();
	}
} else {
	echo "<p>kein Zugang</p>";
	die();
}

$db_handle  = pg_connect("host=10.8.0.129 dbname=postgres user=Denis password=Start1234");
$getMitarbeiter = pg_exec($db_handle, "SELECT id_mitarbeiter,vorname,nachname FROM mitarbeiter order by id_mitarbeiter");
$getUserIDs = pg_exec($db_handle, "SELECT getCreatedUsers()");

$userIds = array();
for ($row = 0; $row < pg_numrows($getUserIDs); $row++) {
	$UserId = pg_result($getUserIDs, $row, 'getCreatedUsers');
	array_push($userIds, $UserId);
}


if (isset($_POST["action"]) || $_SERVER["REQUEST_METHOD"] == "POST") {

	if ($_POST['action'] == "CreateNewUser") {
		$query = "select createUser($1, $2, $3, $4)";
		$result = pg_prepare($db_handle, "createUser", $query);
		$result = pg_execute($db_handle, "createUser", array($_POST['idvalue'], $_POST['username'], $_POST['password'], $_POST['userrole']));
		$result = pg_fetch_result($result, 0, 0);
		echo json_encode($result);
		exit();
	}

	if ($_POST['action'] == "resetPass") {
		$query = "select resetPassworUser($1, $2)";
		$result = pg_prepare($db_handle, "", $query);
		$result = pg_execute($db_handle, "", array($_POST['idvalue'], $_POST['newPassword']));

		exit();
	}

	if ($_POST['action'] == "changeUserRole") {
		//if admin then prevent change to non admin
		if($_SESSION["role"]=="admin" && $_POST['newUserRole']!="admin")
			exit();

		$query = "select changeRoleUser($1, $2)";
		$result = pg_prepare($db_handle, "", $query);
		$result = pg_execute($db_handle, "", array($_POST['idvalue'], $_POST['newUserRole']));

		exit();
	}

	if ($_POST['action'] == "deleteUser") {

		$query = "select deleteUser($1,$2)";
		$result = pg_prepare($db_handle, "", $query);
		$result = pg_execute($db_handle, "", array($_POST['idvalue'],$_SESSION["user"]));

		exit();
	}
}


?>
<!DOCTYPE html>
<html lang="en">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="scripts/createUserPage.js"></script>
</head>

<style>
	html {
		font-size: large;
	}

	#createUser_Page {
		margin: auto;
		display: grid;
		grid-column-gap: 50px;
		grid-template-columns: auto auto auto auto;
		grid-template-rows: 100px auto;

	}

	ul li {
		text-decoration: none;
		list-style-type: none;
	}

	#employeeList_leftColumn li:hover {
		cursor: pointer;
		font-weight: bold;
		color: #4d8eff;

	}

	#employeeList_leftColumn {

		grid-column: 1 / 2;
		grid-row: 2;
		margin: 10px;
		text-decoration: none;
		list-style-type: none;
		width: 50%;

	}

	#customize_rightColumn {
		display: grid;
		grid-column: 2;
		grid-row: 1/2;
		margin: 10px;
	}

	#legendEmployees {

		grid-column: 1;
		grid-row: 1;
		list-style: none;

	}

	.legendEmployees .legend-title {
		text-align: left;
		margin-bottom: 5px;
		font-weight: bold;
		font-size: 90%;
	}

	ul.legend-labels li span {
		display: block;
		float: left;
		height: 16px;
		width: 30px;
		margin-right: 5px;
		margin-left: 0;
		border: 1px solid #999;
	}

	.legendEmployees a {
		color: #777;
	}

	fieldset {
		border: 2px solid #C5D8E1;
		border-radius: 6px;
		background: white;
	}

	#customize_rightColumn #fieldset1 {
		grid-row: 2;
	}
</style>

<div id='createUser_Page'>
	<div id="legendEmployees">
		<div class='legend-title'></div>
		<ul class='legend-labels'>
			<li><span style='background:green;'></span>Benutzer bereits erstellt</li>

		</ul>

	</div>
	<div id='employeeList_leftColumn' <fieldset>
		<option value="none" selected disabled hidden>
			Select an Option</option>
		<?php

		echo "<ul id='personenlist' name='names'>";
		for ($row = 0; $row < pg_numrows($getMitarbeiter); $row++) {
			$id = pg_result($getMitarbeiter, $row, 'id_mitarbeiter');
			$vorname = pg_result($getMitarbeiter, $row, 'vorname');
			$nachname = pg_result($getMitarbeiter, $row, 'nachname');
			$mark = 'black';
			if (in_array($id, $userIds)) {
				$mark = 'green';
			}

			echo "<li id='list$id' value=" . $id . "&" . $vorname . "&" . $nachname . " idValue=" . $id . " firstName=" . $vorname . " lastName=" . $nachname . " style='color:" . $mark . ";'>" . $vorname . " " . $nachname . "</li>";
		}

		echo "</ul>";

		?>
		</fieldset>
		<input type="hidden" id="hiddenField-id"></input>
		<input type="hidden" id="hiddenField-idval"></input>
		<input type="hidden" id="hiddenField-firstName"></input>
		<input type="hidden" id="hiddenField-lastName"></input>
		
	</div>
	<div id='customize_rightColumn'>
		<fieldset id="fieldset1" style="text-align:center;">
			<form id="form_createNewUser">
				<legend style="padding:20px;text-align:center">Neuen Benutzer erstellen</legend>
				<label for="createUserName">Username</label>
				<input type='text' id='createUserName' name='createUserName'></input>
				</br>
				<label for="createUserPassword">Password</label>
				<input type='password' id='createUserPassword' name='createUserPassword'></input>
				</br>
				<label for="confirm_createUserPassword">Password bestätigen</label>
				<input type='password' id='confirm_createUserPassword' name='confirm_createUserPassword'></input>

				</br></br>
				<label for="createUserRole">Benutzerrole</label>
				<select id="createUserRole" name="createUserRole">
					<option>employee</option>
					<option>admin</option>
				</select>
				<!-- <input type='text' id='createUserRole'></input> -->
				<button id='createUserButton'>Benutzer erstellen</button>
			</form>
		</fieldset>
		<fieldset style="text-align:center">
			<form id="form_customizeUser">
				<legend style="padding:20px;text-align:center">Benutzer bearbeiten</legend>
				<label for="newPassword">Neues Passwort</label>
				<input type='password' id='newPassword' name='newPassword'></input>
				<button id='resetPassword'>Passwort zurücksetzen</button>

				</br></br>
				<label for="newUserRole">Role anpassen</label>
				<select id="newUserRole" name="newUserRole">
					<option>employee</option>
					<option>admin</option>
				</select>
				<button id='changeRole'>Role ändern</button>
				</br>
				<button id='deleteUserButton'>User Entfernen</button>
			</form>
		</fieldset>
	</div>
	<?php ?>
</div>

</html>