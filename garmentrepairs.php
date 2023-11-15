<?php

/**
 * Garmentrepairs
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
$pi["access"] = array("linen_service", "repairs");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["repairs"];
$pi["filename_list"] = "garmentrepairs.php";
$pi["filename_details"] = "garmentrepair_details.php";
$pi["template"] = "layout/pages/garmentrepairs.tpl";
$pi["page"] = "list";
$pi["toolbar"]["export"] = "yes";

$pi["toolbar_extra"] = "<form name=\"repairs\" enctype=\"multipart/form-data\" method=\"GET\"><input type=\"submit\" name=\"goto_repairs\" value=\"". $lang["repairing_methods"] . "\" title=\"" . $lang["repairing_methods"] . "\" onclick=\"this.form.action='repairs.php'; this.form.target='_self';\"></form>";
$s = array();

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */

$table = "garments_repairs";

$columns = "garments_repairs.date_in garments_repairs.date_out garments_repairs.id garments.tag garments.tag2 articles.description garments_repairs.status repairs.description";

$urlinfo["join"]["1"] = "garments garments_repairs.garment_id garments.id";
$urlinfo["join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
$urlinfo["join"]["3"] = "articles arsimos.article_id articles.id";
$urlinfo["join"]["4"] = "repairs garments_repairs.repair_id repairs.id";

$urlinfo["where"]["1"] = "garments.deleted_on is null";

$urlinfo["search"] = geturl_search(true);
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = $_GET["cid"];
    $urlinfo["where"]["1"] = "garments.circulationgroup_id = " . $_GET["cid"];
}

//required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

if (isset($_GET["s"])){
    $_SESSION["garmentrepairs"]["show"] = $_GET["s"];
} elseif (isset($_GET["custom"])) {
    $_SESSION["garmentrepairs"]["show"] = null;
}

if (isset($_SESSION["garmentrepairs"]["show"])) {
    foreach ($_SESSION["garmentrepairs"]["show"] as $show => $value) {
        $s[$show] = ((isset($_SESSION["garmentrepairs"]["show"][$show])) ? true : false);
    }
}

if (!$s[1] && !$s[2]) { $urlinfo["where"]["2"] = "true = false"; }
if (!$s[1] &&  $s[2]) { $urlinfo["where"]["2"] = "garments_repairs.status is null"; }
if ( $s[1] && !$s[2]) { $urlinfo["where"]["2"] = "garments_repairs.status = 1"; }

if (!isset($_SESSION["garmentrepairs"]["custom_selection"])) {
    $urlinfo["where"]["2"] = "garments_repairs.status isnot null";
    $_SESSION["garmentrepairs"]["show"][1] = true;
}

$_SESSION["garmentrepairs"]["custom_selection"] = true;

$limit_total_res = db_count($table, $columns, $urlinfo);

if ($limit_total_res) {
    $urlinfo["limit_total"] = db_fetch_row($limit_total_res);
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string
}

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
$sortlinks["repair"] = generate_sortlink("repairs.description", $lang["repair"], $pi, $urlinfo);
$sortlinks["date_in"] = generate_sortlink("garments_repairs.date_in", $lang["date_in"], $pi, $urlinfo);
$sortlinks["date_out"] = generate_sortlink("garments_repairs.date_out", $lang["date_out"], $pi, $urlinfo);
$sortlinks["status"] = generate_sortlink("garments_repairs.status", $lang["status"], $pi, $urlinfo);

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

    $header = $lang["date_in"]."\t".$lang["tag"]."\t".$lang["article"]."\t".$lang["repair"]."\t".$lang["status"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["garments_repairs_date_in"],
            $row["garments_tag"],
            $row["articles_description"],
            $row["repairs_description"],
            (($row["garments_repairs_status"]) ? $lang["open"] : $lang["repaired"])
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
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
