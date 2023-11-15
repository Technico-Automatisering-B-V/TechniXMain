<?php

/**
 * Check depositlocation full
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

$users_sql = "SELECT d.id,d.name,d.send_ful_mail_amount, COUNT(*) cc FROM `depositlocations` d
        INNER JOIN log_depositlocations_garments l ON l.depositlocation_id = d.id
        where l.date > d.last_full_email_sent
        GROUP BY d.id
        HAVING cc > d.send_ful_mail_amount";
$users = db_query($users_sql);


while ($row = db_fetch_assoc($users)) {
    $data = 'Storing: ' . $row["name"] .' vol<br /><br />'
            . '<strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - '.date("Y-m-d");

    $current_emailaddress = "gabor@technico.nl";
    $current_name = "gabor@technico.nl";

    try {
        $m = new Email();
        $m->setRecepients(array($current_emailaddress => $current_name));
        $m->setSubject("Storing");
        $m->addBody($data, "text/html");
        $m->send();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    
    $sql = "UPDATE `depositlocations` `d`
     SET `d`.`last_full_email_sent` = NOW()
         WHERE `d`.`id` = ". $row["id"];
 
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error()); 
}

?>
