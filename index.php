
<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    $db_handle  = pg_connect("host=10.8.0.129 dbname=postgres user=Denis password=Start1234");

    SESSION_START(); 

    if (isset($_POST["action"]) || $_SERVER["REQUEST_METHOD"] == "POST")
	 {
        if($_POST['action'] == "login")
		{
			$loginquery="select checkLoginData($1, $2)";
			$result_login = pg_prepare($db_handle, "login", $loginquery );
			$result_login = pg_execute($db_handle, "login", array($_POST['username'],$_POST['password']) );

			$bool = pg_fetch_result($result_login, 0, 0);
			if($bool=='t'){
                $loginquery_role="select getUserRole($1, $2)";
                $result_loginRole = pg_prepare($db_handle, "loginRole", $loginquery_role );
			    $result_loginRole = pg_execute($db_handle, "loginRole", array($_POST['username'],$_POST['password']) );
                $userRole = pg_fetch_result($result_loginRole, 0, 0);


                $_SESSION["login"] = 1;
                $_SESSION["user"] = $_POST['username'];
                $_SESSION["role"] = $userRole;
                setcookie ( "username" ,$_POST['username']);
                echo "TRUE";
            }else{
                echo "FALSE";
            }
            
           

			exit();
		}

        if($_POST['action'] == "endSession")
		{
            session_destroy(); 
        }
     }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="lib/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script></head>
	<script src="scripts/scriptLogin.js"></script>
    <title> Login Urlaubsverwaltung </title>  
<style>   
Body {  
  font-family: Calibri, Helvetica, sans-serif;  
  
}  
button {   
       background-color: #4CAF50;   
       width: 100%;  
        color: orange;   
        padding: 15px;   
        margin: 10px 0px;   
        border: none;   
        cursor: pointer;   
         }      
 input[type=text], input[type=password] {   
        width: 100%;   
        margin: 8px 0;  
        padding: 12px 20px;   
        display: inline-block;   
        border: 2px solid green;   
        box-sizing: border-box;   
    }  
 button:hover {   
        opacity: 0.7;   
    }   
  .cancelbtn {   
        width: auto;   
        padding: 10px 18px;  
        margin: 10px 5px;  
    }   
        
     
 .container {   
        padding: 25px;   
        background-color: lightblue;  
        width:20%;
        margin:auto;
    }   
</style>   
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

    echo "<center> <h1> Login Urlaubsverwaltung </h1> </center> ";
    echo "<div class='container'>   ";
        echo "<label>Username : </label>";   
        echo '<input type="text" placeholder="Enter Username" id="usernamefield" name="username" required>';  
        echo "<label>Password : </label>";
        echo '<input type="password" placeholder="Enter Password" id="passField" name="password" required>' ; 
        echo '<button id="submitButton" name="submitButtonName">Login</button>';   
        echo '<input type="checkbox" checked="checked"> Remember me' ;  
        //echo 'Forgot <a href="#"> password? </a>';   
    echo "</div>";



?>
</body>
</html>