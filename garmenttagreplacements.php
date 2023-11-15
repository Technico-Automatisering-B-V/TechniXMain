<?php

/**
 * Garment replacements
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
$pi["access"] = array("linen_service", "tag_replacements");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["tag_replacements"];
$pi["filename_list"] = "garmenttagreplacements.php";
$pi["filename_details"] = "garmenttagreplacement_details.php";
$pi["template"] = "layout/pages/garmenttagreplacements.tpl";
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
$table = "log_garments_tagreplacements";

$columns = "log_garments_tagreplacements.datetime log_garments_tagreplacements.id log_garments_tagreplacements.old_tag log_garments_tagreplacements.new_tag articles.description";

$urlinfo["join"]["1"] = "garments log_garments_tagreplacements.garment_id garments.id";
$urlinfo["join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
$urlinfo["join"]["3"] = "articles arsimos.article_id articles.id";

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

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["datetime"] = generate_sortlink("log_garments_tagreplacements.datetime", $lang["date"], $pi, $urlinfo);
$sortlinks["old_tag"] = generate_sortlink("log_garments_tagreplacements.old_tag", $lang["old_tag"], $pi, $urlinfo);
$sortlinks["new_tag"] = generate_sortlink("log_garments_tagreplacements.new_tag", $lang["new_tag"], $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);

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

    $header = $lang["date"]."\t".$lang["old_tag"]."\t".$lang["new_tag"]."\t".$lang["article"]."\t";
    $data = "";
    while ($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["log_garments_tagreplacements_datetime"],
            $row["log_garments_tagreplacements_old_tag"],
            $row["log_garments_tagreplacements_new_tag"],
            $row["articles_description"]
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
