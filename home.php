<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="lib/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <script src="scripts/homescript.js"></script>
    <title>Nav</title>
    <style>
    
    </style>
</head>

<body>
    <?php
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
    echo "<nav><ul>";
    if ($_SESSION["role"] == "admin") {
        echo "<li id='createUserTab'>Benutzer erstellen</li>";
        echo "<li id='manageAnfragen'>Manage Anfragen</li>";
    }
    echo "<li id='Anfrage'>Anfrage</li>";
    echo "<li id='logoutTab'>Logout</li>";
    echo "</ul></nav>";
    echo "<main id='maincontent'>";

    echo "</main>";

    ?>
    <input type="hidden" id="homehiddenField-id"></input>
    <input type="hidden" id="homehiddenField-color"></input>
</body>

</html>