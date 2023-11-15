<?php

/**
 * Extra load
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
$pi["access"] = array("linen_service", "extra_load");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["extra_load"];
$pi["filename_list"] = "extraload.php";
$pi["filename_details"] = "extraload_details.php";
$pi["template"] = "layout/pages/extraload.tpl";
$pi["page"] = "list";
$pi["toolbar"]["export"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Cancel extra load on request
 */
$arsimo_id_to_cancel = (!empty($_GET["arsimo_id_to_cancel"])) ? trim($_GET["arsimo_id_to_cancel"]) : false;
$distributorlocation_id_to_cancel = (!empty($_GET["distributorlocation_id_to_cancel"])) ? trim($_GET["distributorlocation_id_to_cancel"]) : false;
$distributorlocation_id = (!empty($_GET["distributorlocation_id"])) ? trim($_GET["distributorlocation_id"]) : null;

/** required for selectbox: distributorlocations **/
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);
$distributorlocation_count = db_num_rows($distributorlocations);

if ($distributorlocation_count == 1) {
    $distributorlocation_id = 1;
}

if (!empty($_GET["distributorlocation_id"])){
	$urlinfo["distributorlocation_id"] = trim($_GET["distributorlocation_id"]);
} else {
	$urlinfo["distributorlocation_id"] = null;
}

if (is_numeric($arsimo_id_to_cancel) && is_numeric($distributorlocation_id_to_cancel)) {
    $sql = "
        DELETE FROM `distributorlocations_loadadvice`
              WHERE `arsimo_id` = ". $arsimo_id_to_cancel ."
                AND `distributorlocation_id` = $distributorlocation_id_to_cancel
                AND `type` = 'manual'";
    db_query($sql);
}

/**
 * Collect page content
 */
$table = "distributorlocations_loadadvice";

$columns = "circulationgroups.name articles.description distributorlocations_loadadvice.arsimo_id distributorlocations_loadadvice.distributorlocation_id sizes.name modifications.name distributorlocations_loadadvice.demand";

$urlinfo["left_join"]["1"] = "arsimos distributorlocations_loadadvice.arsimo_id arsimos.id";
$urlinfo["left_join"]["2"] = "articles arsimos.article_id articles.id";
$urlinfo["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
$urlinfo["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
$urlinfo["left_join"]["5"] = "distributorlocations distributorlocations_loadadvice.distributorlocation_id distributorlocations.id";
$urlinfo["left_join"]["6"] = "circulationgroups distributorlocations.circulationgroup_id circulationgroups.id";

$urlinfo["where"]["1"] = "distributorlocations_loadadvice.type = manual";

if(!empty($distributorlocation_id)) {
    $urlinfo["where"]["2"] = "distributorlocations_loadadvice.distributorlocation_id = " . $distributorlocation_id;
}

$urlinfo["search"] = geturl_search();


$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_GET["export"]) && $_GET["export"] == "yes") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"]["0"];

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["circulationgroup"] = generate_sortlink("circulationgroups.name", $lang["circulationgroup"], $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
$sortlinks["modification"] = generate_sortlink("modifications.name", $lang["modification"], $pi, $urlinfo);
$sortlinks["demand"] = generate_sortlink("distributorlocations_loadadvice.demand", $lang["demand"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
* Export
*/
if (isset($_GET["export"]) && $_GET["export"] == "yes") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["circulationgroup"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["modification"]."\t".$lang["demand"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["circulationgroups_name"],
            $row["articles_description"],
            $row["sizes_name"],
            (isset($row["modifications_name"]) ? $row["modifications_name"] : ""),
            $row["distributorlocations_loadadvice_demand"]    
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

    print "$header\n$data";
    die();
 }

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "resultinfo" => $resultinfo,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "distributorlocation_count" => $distributorlocation_count,
    "distributorlocation_id" => $distributorlocation_id,
    "distributorlocations" => $distributorlocations 
);

template_parse($pi, $urlinfo, $cv);

?>
