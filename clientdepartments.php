<?php

/**
 * Clientdepartements
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

/**
 * Page settings
 */
$pi = array();
$pi["access"] = array("master_data", "departments");
$pi["filename_list"] = "clientdepartments.php";
$pi["filename_details"] = "clientdepartment_details.php";
$pi["group"] = $lang["master_data"];
$pi["page"] = "list";
$pi["title"] = $lang["clientdepartments"];
$pi["template"] = "layout/pages/clientdepartments.tpl";
$pi["toolbar"]["export"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "clientdepartments";
$columns = "clientdepartments.id clientdepartments.name";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = "clientdepartments.name";
$urlinfo["order_direction"] = "ASC";

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

/**
 * Export
 */
if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $export_filename = "export_departments_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["name"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["clientdepartments_name"]
        );

        foreach($in as $value) {
            if (!isset($value) || $value == "") {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = $value . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata
);

template_parse($pi, $urlinfo, $cv);

?>
