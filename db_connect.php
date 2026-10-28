<?php
function dbConnection(){
    $servername="localhost";
    $username="root";
    $password="";
    $dbname="hospitaldb";

    //create connection
    $conn=new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error){
        die("Connection failed: ".$conn->connect_error);
    }
    return $conn;
}
$conn=dbConnection()
?>