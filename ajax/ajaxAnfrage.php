<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$db_handle  = pg_connect("host=10.8.0.129 dbname=postgres user=Denis password=Start1234");

if (isset($_POST["action"]) || $_SERVER["REQUEST_METHOD"] == "POST") {

	if ($_POST['action'] == "getHolidays") {
		//https://date.nager.at/api/v2/publicHolidays/2021/DE
		$url = "https://date.nager.at/api/v2/publicHolidays/" . $_POST["year"] . "/" . $_POST["countrycode"];

		$a = file_get_contents($url);
		$b = json_decode($a, true);

		$response = array();

		foreach ($b as $item) {
			$uses = $item['date'];
			array_push($response, $uses);
		}
		echo json_encode($response); //datum array

		exit();
	}
	// request holiday
	if ($_POST['action'] == "setHolidayRequest") {
		$query = "select setHolidayRequest($1, $2, $3, $4)";
		pg_prepare($db_handle, "", $query);
		$result = pg_execute($db_handle, "", array($_POST['username'], $_POST['holidayStartDate'], $_POST['holidayEndDate'], $_POST['holidays']));
		exit();
	}

	if ($_POST['action'] == "getAvailableHolidays") {
		$query = "select getAvailableHolidays($1)";
		pg_prepare($db_handle, "", $query);
		$availableHolidays = pg_execute($db_handle, "", array($_POST['username']));
		$availableHolidays = pg_fetch_result($availableHolidays, 0, 0);
		echo $availableHolidays;
		exit();
	}
	// abort(delete) holiday request
	if ($_POST['action'] == "abortRequest") {
		$query = "select deleteHolidayRequest($1, $2)";
		pg_prepare($db_handle, "", $query);
		$result = pg_execute($db_handle, "", array($_POST['username'],$_POST['id']));
		$result = pg_fetch_result($result, 0, 0);
		echo $result;
		exit();
	}
	exit();
}
?>