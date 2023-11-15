<?php

/**
 * Garmentmanagers
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
$pi["access"] = array("manager", "supercard");
$pi["group"] = $lang["manager"];
$pi["title"] = $lang["supercard"];
$pi["filename_list"] = "garmentmanagers.php";
$pi["filename_details"] = "garmentmanager_details.php";
$pi["template"] = "layout/pages/garmentmanagers.tpl";
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
$table = "supergarmentusers";

$columns = "supergarmentusers.garmentuser_id garmentusers.title garmentusers.gender"
         . " garmentusers.initials garmentusers.intermediate garmentusers.surname"
         . " garmentusers.maidenname garmentusers.personnelcode"
         . " supergarmentusers.allow_normaluser supergarmentusers.allow_station supergarmentusers.allow_overloaded"
         . " supergarmentusers.allow_supercard supergarmentusers.allow_supername"
         . " supergarmentusers.deleted_on supergarmentusers.limit_to_profession"
         . " supergarmentusers.limit_to_articles supergarmentusers.maxcredit";

$urlinfo["left_join"]["1"] = "garmentusers supergarmentusers.garmentuser_id garmentusers.id";

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

if (isset($_GET["s"])) {
    $_SESSION["garmentmanagers"]["show"] = $_GET["s"];
} elseif (isset($_GET["custom"])) {
    $_SESSION["garmentmanagers"]["show"] = null;
}

if (isset($_SESSION["garmentmanagers"]["show"])) {
    foreach ($_SESSION["garmentmanagers"]["show"] as $show => $value) {
        $s[$show] = ((isset($_SESSION["garmentmanagers"]["show"][$show])) ? true : false);
    }
}

if (!empty($_GET["del"])){ $urlinfo["del"] = $_GET["del"]; }else{ $urlinfo["del"] = ""; }

if (!empty($urlinfo["del"])){ $urlinfo["where"]["1"] = "supergarmentusers.deleted_on isnot null"; }else{ $urlinfo["where"]["1"] = "supergarmentusers.deleted_on is null"; }

$urlinfo["where"]["2"] = "garmentusers.deleted_on is null";

$_SESSION["garmentmanagers"]["custom_selection"] = true;

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0];

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["location"] = generate_sortlink("circulationgroups.name", $lang["location"], $pi, $urlinfo);
$sortlinks["garmentuser"] = generate_sortlink("garmentusers.surname", $lang["garmentuser"], $pi, $urlinfo);
$sortlinks["personnelcode"] = generate_sortlink("garmentusers.personnelcode", $lang["personnelcode"], $pi, $urlinfo);
$sortlinks["limitation"] = generate_sortlink("supergarmentusers.limit_to_profession", $lang["limitation"], $pi, $urlinfo);
$sortlinks["maxcredit"] = generate_sortlink("supergarmentusers.maxcredit", $lang["credit"], $pi, $urlinfo);
$sortlinks["allow_normaluser"] = generate_sortlink("supergarmentusers.allow_normaluser", $lang["personal_distribution"], $pi, $urlinfo);
$sortlinks["allow_supercard"] = generate_sortlink("supergarmentusers.allow_supercard", $lang["super_distribution"], $pi, $urlinfo);
$sortlinks["allow_supername"] = generate_sortlink("supergarmentusers.allow_supername", $lang["distribution_by_name"], $pi, $urlinfo);
$sortlinks["allow_station"] = generate_sortlink("supergarmentusers.allow_station", $lang["super_distribution_per_station"], $pi, $urlinfo);
$sortlinks["allow_overloaded"] = generate_sortlink("supergarmentusers.allow_overloaded", $lang["super_distribution_overloaded"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
* Export
*/
if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["garmentuser"]."\t".$lang["personnelcode"]."\t".$lang["limitation"]."\t".$lang["credit"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";

        if ($row["supergarmentusers_limit_to_profession"] == 1) {
            $limitation = $lang["to_profession"];
        } elseif ($row["supergarmentusers_limit_to_articles"] == 1) {
            $limitation = $lang["to_articles"];
        } else {
            $limitation = $lang["none"];
        }

        $in = array(
            generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]),
            $row["garmentusers_personnelcode"],
            $limitation,
            $row["supergarmentusers_maxcredit"]
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
    "resultinfo" => $resultinfo,
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
