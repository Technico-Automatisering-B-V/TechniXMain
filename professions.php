<?php

/**
 * Professions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';

/**
 * Page settings
 */
$pi["access"] = array("master_data", "professions");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["professions"];
$pi["filename_list"] = "professions.php";
$pi["filename_details"] = "profession_details.php";
$pi["template"] = "layout/pages/professions.tpl";
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
$table = "professions";
$columns = "name timelock daysbeforelock daysbeforewarning importcode id";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_POST["export"]) && $_POST["export"] == "yes")
{
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0];

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["name"] = generate_sortlink("name", $lang["name"], $pi, $urlinfo);
$sortlinks["timelock"] = generate_sortlink("timelock", $lang["timelock"], $pi, $urlinfo);
$sortlinks["blockage"] = generate_sortlink("daysbeforelock", $lang["blockage"], $pi, $urlinfo);
$sortlinks["warning"] = generate_sortlink("daysbeforewarning", $lang["warning"], $pi, $urlinfo);
$sortlinks["importcode"] = generate_sortlink("importcode", $lang["importcode"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
* Export
*/
if (isset($_POST["export"]) && $_POST["export"] == "yes")
{
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["name"]."\t".$lang["timelock"]."\t".$lang["warning"]."\t".$lang["blockage"]."\t".$lang["importcode"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata))
    {
        $line = "";
        $in = array(
            $row["name"],
            ($row["timelock"] ." ". strtolower($lang["minutes"])),
            (($row["daysbeforewarning"] !== null) ? $row["daysbeforewarning"] ." ". strtolower($lang["days_garments_in_possession"]) : ""),
            (($row["daysbeforelock"] !== null) ? $row["daysbeforelock"] ." ". strtolower($lang["days_after_warning"]) : ""),
            (($row["importcode"] !== null) ? $row["importcode"] : "")
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
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
