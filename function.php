<?php
// Include functions and connect to the database using PDO MySQL
function pdo_connect_mysql() {
    // Our Database login Data
    $DATABASE_HOST = 'db';
    $DATABASE_USER = 'newuser';
    $DATABASE_PASS = 'password';
    $DATABASE_NAME = 'users';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	exit('Failed to connect to database!');
    }
}
?>