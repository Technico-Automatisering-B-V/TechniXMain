<?php

/**
 * emailaddresses
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
$pi["group"] = $lang["technico"];
$pi["title"] = $lang["email_addresses"];
$pi["filename_list"] = "emailaddresses.php";
$pi["filename_details"] = "emailaddress_details.php";
$pi["template"] = "layout/pages/emailaddresses.tpl";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * Collect page content
 */

//required for selectbox: groups
$groups["ALERT"] = $lang["ALERT"];
$groups["BACKUP"] = $lang["BACKUP"];
$groups["CALIBRATION"] = $lang["CALIBRATION"];
$groups["CONTACT_FORM"] = $lang["CONTACT_FORM"];
$groups["FAILURE"] = $lang["FAILURE"];
$groups["GARMENT_WARNING"] = $lang["GARMENT_WARNING"];
$groups["IMPORTER"] = $lang["IMPORTER"];
$groups["LOAD_FAILURE"] = $lang["LOAD_FAILURE"];
$groups["MANAGEMENT_INFO"] = $lang["MANAGEMENT_INFO"];
$groups["PACKINGLIST"] = $lang["PACKINGLIST"];
$groups["SYNCHRONISER"] = $lang["SYNCHRONISER"];
$groups["TESTMSG"] = $lang["TESTMSG"];
$groups["WARNING"] = $lang["WARNING"];

if (!empty($_GET["emailaddresses_group"])) {
    $urlinfo["emailaddresses_group"] = $_GET["emailaddresses_group"];
} else {
    $urlinfo["emailaddresses_group"] = $groups["ALERT"];
}

$table = "emailaddresses";
$columns = "name email_address id group";

$urlinfo["where"]["1"] = "group = " . $urlinfo["emailaddresses_group"];

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

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);
$urlinfostring = generate_urlinfo_string($pi);

$sortlinks["name"] = generate_sortlink("emailaddresses.name", $lang["name"], $pi, $urlinfo);
$sortlinks["email_address"] = generate_sortlink("emailaddresses.email_address", $lang["email_address"], $pi, $urlinfo);
$sortlinks["group"] = generate_sortlink("emailaddresses.group", $lang["group"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

// Required for selectbox: groups
//$emailaddresses_conditions["group_by"] = "group";
//$groups = db_read("emailaddresses", "group group", $emailaddresses_conditions);

/**
* Export

if (isset($_POST["export"]) && $_POST["export"] == "yes")
{
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $data = "";
    while($row = db_fetch_array($listdata))
    {
        $line = "";
        $in = array(
            $row["position"],
            $row["name"]
        );

        foreach($in as $value) {
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

    $data = str_replace("\r","",$data);

    print "$data";
    die();
 }


 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "groups" => $groups,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "urlinfostring" => $urlinfostring,
    "sortlinks" => $sortlinks
);

template_parse($pi, $urlinfo, $cv);

?>
