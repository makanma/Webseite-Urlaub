<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$db_handle  = pg_connect("host=10.8.0.129 dbname=postgres user=Denis password=Start1234");
$holidayUserRequests = array();
$requestCountPerUser = array();


pg_prepare($db_handle, "", "select * from getRequestCountPerUser()");
$requestCountPerUser_ = pg_execute($db_handle, "", array());
while ($row = pg_fetch_row($requestCountPerUser_)) {
	array_push($requestCountPerUser, $row);
}


pg_prepare($db_handle, "", "select getPendingCountAll()");
$pendingCountAll = pg_execute($db_handle, "", array());
$pendingCountAll = pg_fetch_result($pendingCountAll, 0, 0);



if (isset($_POST["action"]) || $_SERVER["REQUEST_METHOD"] == "POST") {
	if ($_POST['action'] == "getHolidayRequestsOfUser") {
		$query = "select * from getHolidayRequestsOfUser($1)";
		
		pg_prepare($db_handle, "", $query);
		$availableHolidays = pg_execute($db_handle, "", array($_POST["userid"]));

		while ($row = pg_fetch_row($availableHolidays)) {
			array_push($holidayUserRequests, $row);
		}
		echo json_encode($holidayUserRequests);

		exit();
	}
	
	if ($_POST['action'] == "holidayStatusChange") {
		$query = "select holidayStatusChange($1,$2)";
		//$query = "update urlaub_antrag set status=$1 where id=$2;";
		pg_prepare($db_handle, "", $query);
		pg_execute($db_handle, "", array($_POST["holidayid"],$_POST["changeAction"]));

		exit();
	}

	exit();
}
