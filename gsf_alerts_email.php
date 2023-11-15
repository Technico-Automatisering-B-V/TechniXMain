<?php

/**
 * GSX alert email
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";
require_once "library/bootstrap.php";

/**
 * Collect page content
 */

$alerts_sql = "SELECT gg.alert AS 'alert', `e`.`email_address` AS 'email', `e`.`name` AS 'name'
					  FROM `gsx_alerts` `gg`
                   LEFT JOIN `emailaddresses` `e` ON `e`.`group` = 'ALERT'
                     WHERE `gg`.`email_sent` = 0";
$alerts = db_query($alerts_sql);

while ($row = db_fetch_assoc($alerts)) {
    $msg = $row["alert"];
    $subject .= "$msg";
    $message .= $msg .'<br /><br />'
            . '<strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - '.date("Y-m-d");

    $current_emailaddress = $row["email"];
    $current_name = $row["name"];

    try {
        $m = new Email();
        $m->setRecepients(array($current_emailaddress => $current_name));
        $m->setSubject(date("d-Hi") . " " . $subject);
        $m->addBody($message, "text/html");
        $m->send();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$alerts_sql = "UPDATE `gsx_alerts` `gg`
                 SET `gg`.`email_sent` = 1
               WHERE `gg`.`email_sent` = 0";
$alerts = db_query($alerts_sql);

?>
