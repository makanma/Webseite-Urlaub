<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	$db_handle  = pg_connect("host=10.8.0.129 dbname=postgres user=Denis password=Start1234");
	
    $id_mitarbeiter="";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		//update
		//if(array_key_exists('update', $_POST))
		if(isset($_POST['update'])){
			$selectedMitarbeiter = $_POST['update'];
			
			$split_selectedMitarbeiter = preg_split ( "/[&]/", $selectedMitarbeiter);

			$query="Update mitarbeiter set vorname='" . $split_selectedMitarbeiter[1]."', nachname='".$split_selectedMitarbeiter[2]."' where id_mitarbeiter="."$split_selectedMitarbeiter[0]";
			pg_exec($db_handle, $query );

			
		}
		//create
		if(isset($_POST['create'])){
			$newMitarbeiter = $_POST['create'];
			$split_newMitarbeiter = preg_split ( "/[&]/", $newMitarbeiter);
			$query="insert into mitarbeiter (vorname,nachname) values( '" . $split_newMitarbeiter[0]."', '".$split_newMitarbeiter[1]."');";
			pg_exec($db_handle, $query );

		}

		exit();
	}
		
	



?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>title</title>
    <link rel="stylesheet" href="style.css">
	<script type="text/javascript" src="lib/jquery-3.6.0.min.js"></script>
	<script src="script.js"></script>
  </head>
  <body>
    <?php
        $getMitarbeiter = pg_exec($db_handle, "SELECT id_mitarbeiter,vorname,nachname FROM mitarbeiter order by id_mitarbeiter");
		echo "<div class='content' ";
		echo "<form method='post'>";
        echo "<div class='container__sidebar'>";
			
			//echo "<label>Namen</label>";
			echo "<select id='personenlist' name='names' size='".pg_numrows($getMitarbeiter)."'>";
			for ($row = 0; $row < pg_numrows($getMitarbeiter); $row++) {
				$id = pg_result($getMitarbeiter, $row, 'id_mitarbeiter');
				$vorname = pg_result($getMitarbeiter, $row, 'vorname');
				$nachname = pg_result($getMitarbeiter, $row, 'nachname');
				
				echo "<option id='list.$id' value=".$id."&".$vorname."&".$nachname.">".$vorname." ".$nachname."</option>";
				
			}
			echo "</select>";
		echo "</div>";
		//middle
		echo "<div class='container__main'>";
		echo "<div class='container1'>";
			echo "<div class='container_fnameInput'>";
				echo "<input type='hidden' id='nameID'></input>";
				echo "<label class='container__label1'>Vorname</label>";
				echo "<input type='text' name='fname' id='vnameInput'>";
				
			echo "</div>";
			echo "<div class='container_nnameInput'>";	
				echo "<label class='container__label2'>Nachname</label>";
				echo "<input type='text' name='nname' id='nnameInput'>";
				
			echo "</div>";
		echo "</div>";
			echo "<div class='button_group'>";
				echo "<input type='button' name='update' value='update' id='updateButton' >";
				echo "<input type='button' name='remove' value='remove entry' id='removeEntry' >";
			echo "</div>";
        echo "</div>";
		// create new entry
		echo "<div class='container__right'>";
		echo "<div class='container1'>";
		echo "<div class='container_fnameInput'>";
			echo "<label class='container__label1'>Vorname</label>";
			echo "<input type='text' name='fname' id='vnameCreate'>";
			
		echo "</div>";
		echo "<div class='container_nnameInput'>";	
			echo "<label class='container__label2'>Nachname</label>";
			echo "<input type='text' name='nname' id='nnameCreate'>";
			
		echo "</div>";
			echo "<input type='button' id='createbutton' name='create' value='create new entry'>";
		echo "</div>";
		echo "</form>";
		echo "</div>";
		









    ?>