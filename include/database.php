<?php

/**
 * Database connection
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include MySQL functions
 */
require_once "mysqlcontrol.php";

/**
 * Connect to the database server
 */
$conn = @mysqli_connect("localhost","root","t3chn1x.sql") or die("<br /><strong>Failed to connect to database server. Error returned:</strong><br /><br />" . mysqli_error());
$base = basename($_SERVER["PHP_SELF"]);
$_SESSION["conn"] = $conn;

if (!isset($_SESSION["database"])){
  $_SESSION["database"] = "technix_workwear";
}

$db_selected = mysqli_select_db($_SESSION["conn"], $_SESSION["database"]);

if (!$db_selected)
{
    die("Error selecting database ". $_SESSION["database"]);
}

