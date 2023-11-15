<?php

/**
 * Garments to long with garmentuser
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

$users_sql = "SELECT GROUP_CONCAT(CONCAT(`g`.`tag`, ' - ', `ar`.`description`, ' ', `s`.`name`) SEPARATOR '<br />') AS 'garments',
          CONCAT(IF(ISNULL(`gu`.`name`),'', CONCAT(`gu`.`name`, ' ')), IF(ISNULL(`gu`.`surname`),'',`gu`.`surname`)) AS 'name',
						   `gu`.`personnelcode`, IF(ISNULL(`gu`.`email`),`e`.`email_address`,`gu`.`email`) AS 'email'
					  FROM `garmentusers_garments` `gg`
                INNER JOIN `garmentusers` `gu` ON `gg`.`garmentuser_id` = `gu`.`id`
                INNER JOIN `garments` `g` ON `g`.`id` = `gg`.`garment_id`
                INNER JOIN `arsimos` `a` ON `a`.`id` = `g`.`arsimo_id`
                INNER JOIN `articles` `ar` ON  `ar`.`id` = `a`.`article_id`
                INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
                 LEFT JOIN `emailaddresses` `e` ON `e`.`group` = 'GARMENT_WARNING' AND ISNULL(`gu`.`email`)
                     WHERE `gg`.`superuser_id` = 0
                  AND DATE(`gg`.`date_received`) = DATE_SUB(DATE(NOW()), INTERVAL 14 DAY)
                  GROUP BY `gu`.`id`,`e`.`id`";
$users = db_query($users_sql);

while ($row = db_fetch_assoc($users)) {
    $data ='Beste collega,<br />Uit het kledingregistratie systeem blijkt dat u al langere tijd kleding in bezit heeft.
            Vanwege aspecten als efficiënt kunnen inzetten van kleding en eisen vanuit Hygiëne en Infectiepreventie hierbij een vriendelijk doch dringend verzoek om de kleding die al langere tijd in bezit is, binnen een week, in te leveren. 
            Mocht deze mail niet voor u bestemd zijn of heeft u geen kleding meer in bezit dan vernemen we dat graag, binnen een week, via het meldpunt Linnenvoorziening, linnenvoorziening@znb.nl.<br />
            Kleding in bezit langer dan 14 dagen:<br />'. $row["garments"] .'<br /><br />'
            . '<strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - '.date("Y-m-d");

    $current_emailaddress = $row["email"];
    $current_name = $row["name"];

    try {
        $m = new Email();
        $m->setRecepients(array($current_emailaddress => $current_name));
        $m->setSubject("Kleding waarschuwing - ". $row["name"] ." (". $row["personnelcode"] .")");
        $m->addBody($data, "text/html");
        $m->send();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$users_sql = "SELECT GROUP_CONCAT(CONCAT(`g`.`tag`, ' - ', `ar`.`description`, ' ', `s`.`name`) SEPARATOR '<br />') AS 'garments',
                         CONCAT(IF(ISNULL(`gu`.`name`),'', CONCAT(`gu`.`name`, ' ')), IF(ISNULL(`gu`.`surname`),'',`gu`.`surname`)) AS 'name',
                        `gu`.`personnelcode`, IF(ISNULL(`gu`.`email`),`e`.`email_address`,`gu`.`email`) AS 'email'
                FROM `garmentusers_garments` `gg`
                INNER JOIN `garmentusers` `gu` ON `gg`.`garmentuser_id` = `gu`.`id`
                INNER JOIN `garments` `g` ON `g`.`id` = `gg`.`garment_id`
                INNER JOIN `arsimos` `a` ON `a`.`id` = `g`.`arsimo_id`
                INNER JOIN `articles` `ar` ON  `ar`.`id` = `a`.`article_id`
                INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
                LEFT JOIN `emailaddresses` `e` ON `e`.`group` = 'GARMENT_WARNING' AND ISNULL(`gu`.`email`)
                 WHERE `gg`.`superuser_id` = 0
                 AND DATE(`gg`.`date_received`) = DATE_SUB(DATE(NOW()), INTERVAL 28 DAY)
                GROUP BY `gu`.`id`,`e`.`id`";
$users = db_query($users_sql);

while ($row = db_fetch_assoc($users)) {
    $data ='Beste collega,<br />Uit het kledingregistratie systeem blijkt dat u al langere tijd kleding in bezit heeft.
            Vanwege aspecten als efficiënt kunnen inzetten van kleding en eisen vanuit Hygiëne en Infectiepreventie hierbij een vriendelijk doch dringend verzoek om de kleding die al langere tijd in bezit is, binnen een week, in te leveren.
            Gebeurt dit niet dan zal u geen toegang meer hebben tot de kledinguitgifte. Tevens zal uw leidinggevende een bericht krijgen en worden de gegevens meegenomen in de kwaliteitsrapportages van de afdeling Hygiëne en Infectiepreventie.
            Mocht deze mail niet voor u bestemd zijn of heeft u geen kleding meer in bezit dan vernemen we dat graag, binnen een week, via het meldpunt Linnenvoorziening, linnenvoorziening@znb.nl.<br />
            Kleding in bezit langer dan 28 dagen:<br />'. $row["garments"] .'<br /><br />'
            . '<strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - '.date("Y-m-d");

    $current_emailaddress = $row["email"];
    $current_name = $row["name"];

    try {
        $m = new Email();
        $m->setRecepients(array($current_emailaddress => $current_name));
        $m->setSubject("Kleding waarschuwing - ". $row["name"] ." (". $row["personnelcode"] .")");
        $m->addBody($data, "text/html");
        $m->send();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>
