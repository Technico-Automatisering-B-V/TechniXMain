<?php

/**
 * Fetch image from database
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

require_once 'database.php';

if ($name = $_GET['name'])
{
    $sql = mysqli_query($_SESSION["conn"], "SELECT * FROM `images` WHERE `name` = '". $name ."' LIMIT 1");

    if($row = mysqli_fetch_array($sql))
    {
        Header("Content-type: " . $row['filetype']);
        echo $row['bin_data'];
    }
}

