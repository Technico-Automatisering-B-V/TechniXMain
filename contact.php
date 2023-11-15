<?php

/**
 * Contact
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';
require_once "library/bootstrap.php";

/**
 * Page settings
 */
$urlinfo = array();

$pi = array();
$pi["access"] = array("contact", "contact");
$pi["group"] = $lang["contact"];
$pi["title"] = $lang["contact"];
$pi["filename_list"] = "contact.php";
$pi["filename_this"] = "contact.php";
$pi["filename_details"] = "contact.php";
$pi["template"] = "layout/pages/contact.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

$requiredfields = array();
$urlinfo = array();

$detailsdata = array(
    "name" => (!empty($_POST["name"])) ? $_POST["name"] : "",
    "phone" => (!empty($_POST["phone"])) ? $_POST["phone"] : "",
    "mail" => (!empty($_POST["mail"])) ? $_POST["mail"] : "",
    "message" => (!empty($_POST["message"])) ? $_POST["message"] : "",
    "type" => (!empty($_POST["type"])) ? $_POST["type"] : ""
);

$issent = "false";

/**
 * Page is submitted
 */
if (isset($_POST["send"])) {
    if (empty($detailsdata["name"])){ array_push($requiredfields, $lang["name"]); }
    if (empty($detailsdata["message"])){ array_push($requiredfields, $lang["message_text"]); }
    
    if(empty($requiredfields)) {
        try {
            
            $recepients = array();
            $email_addresses_sql = "SELECT `email_address`, `name`
                                      FROM `emailaddresses`
                                     WHERE `group` = 'CONTACT_FORM'";
            $email_addresses = db_query($email_addresses_sql);

            while ($row = db_fetch_assoc($email_addresses)) {
                $recepients[$row["email_address"]] = $row["name"];
            }
            
            $m = new Email();
            $m->setRecepients($recepients);
            $m->setSubject($lang[$detailsdata["type"]] ." (" . date("Y-m-d") . ")");
            $m->addBody("Bericht type: ". $lang[$detailsdata["type"]] ."\r\n");
            $m->addBody("Naam contactpersoon: ". $detailsdata["name"] ."\r\n");
            $m->addBody("Telefoonnummer: ". $detailsdata["phone"] ."\r\n");
            $m->addBody("E-mailadres: ". $detailsdata["mail"] ."\r\n");
            $m->addBody("Bericht tekst: ". $detailsdata["message"] ."\r\n");
            $m->addBody("\r\n\r\nServer: <strong>". gethostname() ."</strong>");
            $m->addBody("\r\nDate: <strong>". date("Y-m-d H:i:s") ."</strong>");
            $m->setAttachment($_FILES["sendfile"]["tmp_name"], $_FILES["sendfile"]["name"]);
            $m->send();
            
            $issent = "true";
            $detailsdata = array();
        } catch (Exception $e) {
            $pi["note"] = $e->getMessage();
        }
    } else {
        $pi["note"] = html_requiredfields($requiredfields);
    }
}

/**
 * Generate the page
 */
$cv = array(
    'pi' => $pi,
    'urlinfo' => $urlinfo,
    'detailsdata' =>$detailsdata,
    'issent' =>$issent
);

template_parse($pi, $urlinfo, $cv);

?>
