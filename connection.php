<?php
   $db_server = "localhost";
   $db_user = "root";
   $db_pass = "";
   $db_name = "sklep-palety-php";

   $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    if(!$conn) {
     echo("Baza nie dziala");
   }
?>