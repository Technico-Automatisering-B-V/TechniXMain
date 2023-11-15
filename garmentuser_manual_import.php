<?php

/**
 * Garmentuser manual import
 *
 * @author    G. I. Voros <gaborvoros@technico.nl>
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
$pi["filename_list"] = "garmentuser_manual_import.php";
$pi["group"] = "Technico";
$pi["page"] = "simple";
$pi["title"] = $lang["garmentuser_manual_import"];
$pi["template"] = "layout/pages/garmentuser_manual_import.tpl";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */

$info = "<strong>Set the 'garmentuser_manual_import.php' before you use it!</strong>";

if (isset($_POST["gosubmit"])) {
    if($_FILES['uploadedfile']['name']) {
        $target_path = "/opt/downloads/";
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

        /*
        if(file_exists('/opt/downloads')) {
            $allowedExts = array("csv", "txt");
            $temp = explode(".", $_FILES["uploadedfile"]["name"]);
            $extension = end($temp);

            if (in_array($extension, $allowedExts)) {
                if ($_FILES["uploadedfile"]["error"] > 0) {
                    $info .= "Return Code: " . $_FILES["uploadedfile"]["error"] . "<br>Please contact the Technico helpdesk.";
                } else {
                    $info .= "<strong>Upload:</strong><br />";
                    $info .= "File: " . $_FILES["uploadedfile"]["name"] . "<br />";
                    $info .= "Size: " . round($_FILES["uploadedfile"]["size"] / 1024) . " kB<br />";
                    $info .= "Folder: /opt/downloads/<br />";
                    move_uploaded_file($_FILES["uploadedfile"]["tmp_name"],
                            "/opt/downloads/" . $_FILES["uploadedfile"]["name"]);
                    $info .= $lang["upload_succesfull"];

                    $info .= "<br /><br /><strong>Import:</strong><br />";

                    $filename = "/IMPORT-FOLDER/importer.php";
                    if (file_exists($filename)) {
                        $info .= "The file $filename exists";
                        $manual_import_file = $_FILES["uploadedfile"]["name"];
                        $info .= $execute =`php -q /var/www/xgs/scripts/cron/sync_users.php $manual_import_file`;
                    } else {
                        $info .= "Import failed! The file $filename does not exist.";
                    }
                }
            } else {
                $info = "Invalid file type!";
            }
        } else {
            $info = "Invalid folder! Please contact the Technico helpdesk!";
        }
        */
    } else {
        $info = "<div style=\"float: left;margin-left: 9px;margin-top: 7px;\">First select the file</div>";
    }

}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "info" => $info
);

template_parse($pi, $urlinfo, $cv);

?>
