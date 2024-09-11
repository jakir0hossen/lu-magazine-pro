<?php
/* Database credentials. Update these values as needed for your environment. */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Consider using a less privileged user for security
define('DB_PASSWORD', '');    // Add a password if your MySQL setup requires it
define('DB_NAME', 'admission');

/* Attempt to connect to MySQL database using MySQLi with improved error handling. */
$con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

/* Check connection */
if ($con->connect_error) {
    // Log error details to a file or monitoring system
    error_log("Connection failed: " . $con->connect_error, 3, '/var/log/php_errors.log'); // Adjust path as needed
    die("ERROR: Could not connect. Please check the error log for details.");
}
?>
