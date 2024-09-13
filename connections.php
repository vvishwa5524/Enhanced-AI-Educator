<?php
$dblocalhost="localhost";
$dbuser = "root";
$dbpass="";
$dbname="users_info";


if(!$con=mysqli_connect($dblocalhost,$dbuser,$dbpass,$dbname)){

    die("failed to connect !");
}
?>