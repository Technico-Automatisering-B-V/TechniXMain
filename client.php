<?php

/**
 * Clients
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "library/bootstrap.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["client"];
$pi["template"] = "layout/pages/client.tpl";
$pi["filename_list"] = "client.php";
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
if (isset($_POST["detailssubmit"])) {
    $d = $_POST["client"];

    try {
        Config::setClientSettings($d);
    } catch (Exception $e) {
        $pi["note"] = html_note($e->getMessage());
    }
}

/**
 * Collect page content
 */
try {
    $r = Config::getClientSettings();
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
