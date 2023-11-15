<?php

/**
 * Sizes
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
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
$pi["access"] = array("master_data", "sizes");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["sizes"];
$pi["filename_list"] = "sizes.php";
$pi["filename_details"] = "size_details.php";
$pi["template"] = "layout/pages/sizes.tpl";
$pi["toolbar"]["export"] = "yes";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
//catch the sizegroup_id we're viewing, if it was previously selected
if (!empty($_GET["sizegroup_id"])){
    $urlinfo["sizegroup_id"] = $_GET["sizegroup_id"];
} else {
    //we use the sizegroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_sizegroup_conditions["order_by"] = "name";
    $selected_sizegroup_conditions["limit_start"] = 0;
    $selected_sizegroup_conditions["limit_num"] = 1;
    $urlinfo["sizegroup_id"] = db_fetch_row(db_read("sizegroups", "id", $selected_sizegroup_conditions));
    $urlinfo["sizegroup_id"] = $urlinfo["sizegroup_id"][0];
}

$table = "sizes";
$columns = "position id name sizegroup_id";

//we use a where clause on sizegroup_id in every query using $urlinfo below
$urlinfo["where"]["1"] = "sizegroup_id = " . $urlinfo["sizegroup_id"];

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

if (!empty($_POST["movesize_position"]) && !empty($_POST["movesize_direction"])) {
    $from_position = $_POST["movesize_position"];
    $direction = $_POST["movesize_direction"];
    $to_position = ($direction == "up") ? $from_position - 1 : $from_position + 1;

    $from_data_conditions["where"]["1"] = "position = " . $from_position;
    $from_data_conditions["where"]["2"] = "sizegroup_id = " . $urlinfo["sizegroup_id"];
    $to_data_conditions["where"]["1"] = "position = " . $to_position;
    $to_data_conditions["where"]["2"] = "sizegroup_id = " . $urlinfo["sizegroup_id"];

    $from_data = db_fetch_assoc(db_read($table, "id position", $from_data_conditions));
    $to_data = db_fetch_assoc(db_read($table, "id position", $to_data_conditions));

    $new_to_position = $from_data["position"];
    $from_data["position"] = $to_data["position"];
    $to_data["position"] = $new_to_position;

    if (!empty($from_data["id"]) && !empty($to_data["id"])) {
        db_update($table, $from_data["id"], $from_data);
        db_update($table, $to_data["id"], $to_data);
    }
}

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);
$urlinfostring = generate_urlinfo_string($pi);

$pagination = generate_pagination($pi, $urlinfo);

// Required for selectbox: sizegroups
$sizegroups_conditions["order_by"] = "name";
$sizegroups = db_read("sizegroups", "id name", $sizegroups_conditions);


/**
* Export
*/
if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["position"]."\t".$lang["name"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["position"],
            $row["name"]
        );

        foreach ($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
               $value = "\t";
            } else {
               $value = str_replace('"', '""', $value);
               $value = '"' . $value . '"' . "\t";
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
    "sizegroups" => $sizegroups,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "urlinfostring" => $urlinfostring
);

template_parse($pi, $urlinfo, $cv);

?>
