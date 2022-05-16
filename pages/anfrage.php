<?php
include '../ajax/ajaxAnfrage.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<!-- <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
	<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-business-days/1.2.0/index.js"></script>
	<script src="scripts/anfrage.js"></script>
	<title> Antrag </title>
	<style>
		#anfrage_page {
			width: 70%;
			margin: auto;
			display: grid;
			grid-template-columns: 30% auto;
			gap: 0px 10%;
		}

		#column1 {
			grid-column: 1;

		}
		#column2 {
			grid-column: 2;

		}
		

		main {
			background-color: #f1f3fa ;
			min-height: 100vh;
		}

		ul li {
			text-decoration: none;
			list-style-type: none;
		}
		h1{
			margin:auto;
			grid-column: 1/3;
			grid-row: 1;
			padding:1rem;
		}

		

		#legendEmployees {

			grid-column: 1;
			grid-row: 2;
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
			/* border: 1px solid #999; */
		}

		.legendEmployees a {
			color: #777;
		}

		#antraglist {

			margin-right: 6rem;


		}

		#antraglist ul {
			border-style: ridge;

		}

		#antraglist li:hover {
			cursor: pointer;
			font-weight: bold;
			color: #4d8eff;

		}

		/* #availableHolidaysTitle {
			border-style: ridge;
			;
		} */
	</style>
</head>

<body>
	<input type="hidden" id="hiddenField-id"></input>
	<input type="hidden" id="hiddenField-idval"></input>
	<input type="hidden" id="hiddenField-color"></input>
	
	<?php
	SESSION_START();
    if (isset($_SESSION["login"])) {
        if ($_SESSION["login"] == 0) {
            die();
        }
    } else {
        echo "<p>kein Zugang</p>";
        die();
    }

	$query = "select getAvailableHolidays($1)";
	$availableHolidays = pg_prepare($db_handle, "", $query);
	$availableHolidays = pg_execute($db_handle, "", array($_SESSION["user"]));
	$availableHolidays = pg_fetch_result($availableHolidays, 0, 0);
	?>
	
	<div id="anfrage_page">
	<h1>Urlaubsanfrage erstellen</h1>
		<div id="column1">
			<div id="legendEmployees">
				<div class='legend-title'></div>
				<ul class='legend-labels'>
					<li><span style='background:green;'></span>approved</li>
					<li><span style='background:#FFFFB3;'></span>pending</li>
					<li><span style='background:red;'></span>denied</li>
					
				</ul>

			</div>
			<div id="antraglist">
				<?php
				$query = "select * from getAllUserRequests($1)";
				$test = pg_prepare($db_handle, "getAllUserRequests", $query);
				$test = pg_execute($db_handle, "getAllUserRequests", array($_SESSION["user"]));
				$test = pg_fetch_all($test);
				echo "<ul>";
				if ($test != null) {
					foreach ($test as $item) {
						$id = $item['uid'];
						$startDate = new DateTime($item['ustart']);
						$EndDate = new DateTime($item['uend']);
						$status = $item['ustatus'];

						switch ($status) {
							case 'approved':
								$backgroundColor = 'green';
								break;
							case 'pending':
								$backgroundColor = '#FFFFB3';
								break;
							case 'denied':
								$backgroundColor = 'red';
								break;
							case 'aborted':
								$backgroundColor = '#23afff';
								break;
						}


						echo "<li id='holidaylist" . $id . "' idvalue='" . $id . "' style='background-color:" . $backgroundColor . "'>" . $startDate->format('d.m.Y') . " bis " . $EndDate->format('d.m.Y') . "</li>";
					}
				}

				echo "</ul>";
				?>
			</div>
		</div>

		<div id="column2">
			<div>Verfügbare Urlaubstage in diesem Jahr:</div>
			<?php
			echo "<div id='availableHolidays'> $availableHolidays </div>";
			?>
			<label for="holidayStartDate">Start</label>
			<input type="date" id="holidayStartDate" name="holidayStartDate" onkeydown="return false" /></br>
			<label for="holidayEndDate">End</label>
			<input type="date" id="holidayEndDate" name="holidayEndDate" onkeydown="return false" />

			<label for="bookdays">zu buchende Zeit in Tagen</label>
			</br>
			<label id="bookdays">...Days</label>
			</br>

			<button type="button" id="sendRequestButton" name="sendRequestButton">Anfrage abschicken</button>
			<button type="button" id="abortRequestButton" name="abortRequestButton">Bestehende Anfrage abbrechen und löschen</button>

		</div>




	</div>
</body>

</html>