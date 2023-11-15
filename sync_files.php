<?php

/**
 * Sync files
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["import_files"];
$pi["filename_list"] = "sync_files.php";
$pi["filename_this"] = "sync_files.php";
$pi["template"] = "layout/pages/sync_files.tpl";
$pi["page"] = "simple";

$urlinfo = array();

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * When form is submitted
 */
if(isset($_POST["file"])) {
    $file = $_POST["file"];

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

/**
 * Get all articles
 */
$importers_query = "SELECT `name`, `value` FROM `settings` WHERE `name` LIKE '_S%' ORDER BY `name` ASC";
$importers_sql = db_query($importers_query) or die("ERROR LINE ". __LINE__);

while ($importers_result = db_fetch_row($importers_sql)) {
    $importers[$importers_result[0]] = $importers_result[1];
}

/**
 * Generate the page
 */
$cv = array(
    "importers" => $importers
);

template_parse($pi, $urlinfo, $cv);

?>
