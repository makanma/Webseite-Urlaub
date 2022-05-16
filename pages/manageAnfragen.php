<?php
include '../ajax/manageAnfragenAjax.php';

// echo $_SESSION["login"];
// if (isset($_SESSION["login"])) {
//     if ($_SESSION["login"] == 0) {
//         die();
//     }
// } else {
//     echo "<p>kein Zugang</p>";
//     die();
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="scripts/manageAnfragen.js"></script>
    <title> Manage Anfragen </title>
    <style>
        #anfrage_page {
            display: grid;
            width: 70%;
            margin: auto;
            grid-template-columns: 30% auto;
            gap: 0px 10%;

        }

        #column1 {
            grid-column: 1;

        }

        #column2 {
            grid-column: 2;

        }

        ul li {
            text-decoration: none;
            list-style-type: none;
        }

        h1 {
            margin: auto;
            grid-column: 1/3;
            grid-row: 1;
            padding: 1rem;
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
            border: 1px solid #999;
        }

        .legendEmployees a {
            color: #777;
        }

        #requestList li:hover {
            cursor: pointer;
            font-weight: bold;
            color: #4d8eff;

        }
    </style>
</head>

<body>
    <div id="anfrage_page">
        <input type="hidden" id="hiddenField-id"></input>
        <input type="hidden" id="hiddenField-idval"></input>
        <input type="hidden" id="hiddenField-color"></input>

        <h1>Urlaubsanfragen bearbeiten</h1>
        <div id="column1">
            <?php
            echo "Anstehende Urlaubsanträge: "."<div id='pendingHolidayRequests'>" . $pendingCountAll ."</div>";
            ?>
            </br>
            <div id="employee-selectDiv">
                <label for="employee-select">Einen Angestellten auswählen</label>
                <select name="names" id="employee-select">
                    <option value="">--Person auswählen--</option>
                    <?php
                    foreach ($requestCountPerUser as $arrayitem) {
                        $firstName = $arrayitem[0];
                        $lastName = $arrayitem[1];
                        $userID = $arrayitem[2];
                        $pendingRequestCountForUser = $arrayitem[3];
                        echo '<option id="option' . $userID . '" pendingcount="' . $pendingRequestCountForUser . '" userid="' . $userID . '" ">' . $firstName . ' ' . $lastName . ' (' . $pendingRequestCountForUser . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div id="legendEmployees">
                <div class='legend-title'></div>
                <ul class='legend-labels'>
                    <li><span style='background:green;'></span>approved</li>
                    <li><span style='background:#FFFFB3;'></span>pending</li>
                    <li><span style='background:red;'></span>denied</li>

                </ul>

            </div>
            <div id="requestList">
                <ul>

                </ul>
            </div>
        </div>
        <div id="column2">
            <select name="names" id="holiday-select-aprovedeny">
                <option value="">--Urlaub genehmigen oder ablehnen--</option>
                <option id="option_approve">genehmigen</option>
                <option id="option_deny">ablehnen</option>
                <option id="option_pending">noch zu verarbeiten</option>
            </select>
            <button id="button-comfirm-changeSelectedRequest">Bestätigen</button>
        </div>
    </div>
</body>

</html>