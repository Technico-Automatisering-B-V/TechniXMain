<?php

/**
 * E-mail settings
 *
 * PHP version 5
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "library/bootstrap.php";

/**
 * Page settings
 */
$pi["group"] = "Technico";
$pi["title"] = $lang["email_settings"];
$pi["filename_list"] = "emailsettings.php";
$pi["template"] = "layout/pages/emailsettings.tpl";
$pi["page"] = "details";
$pi["toolbar"] = "no";
$pi["note"] = "";

$ui = array();

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * Page is submitted
 */
if (isset($_POST["detailssubmit"]) || isset($_POST["testmsg"])) {
    $d = $_POST["email"];

    try {
        Config::setEmailSettings($d);
    } catch (Exception $e) {
        $pi["note"] = html_note($e->getMessage());
    }

    if (isset($_POST["testmsg"]) && empty($pi["note"])) {
        try {
            $m = new Email();
            $m->setRecepients(array("gabor@technico.nl" => "Technico"));
            $m->setSubject("Testmessage (" . date("Y-m-d") . ")");
            $m->addBody("Dear Technico,\r\n\r\nThis is a availability test.");
            $m->addBody("\r\n\r\nServer: <strong>". gethostname() ."</strong>");
            $m->addBody("\r\nDate: <strong>". date("Y-m-d H:i:s") ."</strong>");
            $m->send();
        } catch (Exception $e) {
            $pi["note"] = $e->getMessage();
        }
    }
}

/**
 * Collect page content
 */
try {
    $r = Config::getEmailSettings();

    if (!$r["enabled"]) {
        $pi["note"] = html_info("E-mail is uitgeschakeld.");
    }
} catch (Exception $e) {
    $pi["note"] = html_note($e->getMessage());
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "ar" => $r
);

template_parse($pi, $ui, $cv);

?>
