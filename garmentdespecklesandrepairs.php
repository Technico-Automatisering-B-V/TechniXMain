<?php

/**
 * Garmentdespeckles
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
$pi["access"] = array("linen_service", "despecklesandrepairs");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["despecklesandrepairs"];
$pi["filename_list"] = "garmentdespecklesandrepairs.php";
$pi["filename_details"] = "garmentdespeckleandrepair_details.php";
$pi["template"] = "layout/pages/garmentdespecklesandrepairs.tpl";
$pi["page"] = "list";

$pi["toolbar_extra"] = "<form name=\"despeckles\" enctype=\"multipart/form-data\" method=\"GET\"><input type=\"submit\" name=\"goto_despeckles\" value=\"". $lang["despeckle_methods"] . "\" title=\"" . $lang["despeckle_methods"] . "\" onclick=\"this.form.action='despeckles.php'; this.form.target='_self';\"></form> <form name=\"repairs\" enctype=\"multipart/form-data\" method=\"GET\"><input type=\"submit\" name=\"goto_repairs\" value=\"". $lang["repairing_methods"] . "\" title=\"" . $lang["repairing_methods"] . "\" onclick=\"this.form.action='repairs.php'; this.form.target='_self';\"></form>";
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

if(!empty($_GET["finalizesubmit"])) {
    $_SESSION["garmentdespecklesandrepairs_selected"] = (!empty($_GET["garmentdespecklesandrepairs_selected"])) ? $_GET["garmentdespecklesandrepairs_selected"] : array();
    if(!empty($_SESSION["garmentdespecklesandrepairs_selected"])) {
        $pi["note"] = html_to_finalize("");
    }
} else {
   if (isset($_POST["confirmed"])) {
        foreach ($_SESSION["garmentdespecklesandrepairs_selected"] as $name => $value)
        {
            $update_data["status"] = NULL;
            $update_data["date_out"] = "NOW()";

            //update the garmentdespeckle as despeckleed
            db_update("garments_despecklesandrepairs", $name, $update_data);           
        }
   } else {
       $_SESSION["garmentdespecklesandrepairs_selected"] = array();
   }     
}


if (!empty($_GET["dsearch"])){ $urlinfo["dsearch"] = $_GET["dsearch"]; }else{ $urlinfo["dsearch"] = ""; }

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){
        $hassubmit = "export";
    }
} else {
    $hassubmit = "";
}

$table = "garments_despecklesandrepairs";

$columns = "garments_despecklesandrepairs.date_in garments_despecklesandrepairs.date_out garments_despecklesandrepairs.id garments.tag garments.tag2 articles.description garments_despecklesandrepairs.status despeckles.description repairs.description garments_despecklesandrepairs.type";

$urlinfo["join"]["1"] = "garments garments_despecklesandrepairs.garment_id garments.id";
$urlinfo["join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
$urlinfo["join"]["3"] = "articles arsimos.article_id articles.id";
$urlinfo["left_join"]["4"] = "despeckles garments_despecklesandrepairs.despeckle_id despeckles.id";
$urlinfo["left_join"]["5"] = "repairs garments_despecklesandrepairs.repair_id repairs.id";

$urlinfo["where"]["1"] = "garments.deleted_on is null";

$urlinfo["search"] = geturl_search(true);
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if ($hassubmit == "export") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = $_GET["cid"];
    $urlinfo["where"]["2"] = "garments.circulationgroup_id = " . $_GET["cid"];
} else {
    $urlinfo["cid"] = "";
}

if (!empty($_GET["type"])) {
    $urlinfo["type"] = $_GET["type"];
    $urlinfo["where"]["3"] = "garments_despecklesandrepairs.type = " . $urlinfo["type"];
} else {
    $urlinfo["type"] = "";
}

if (!empty($_GET["status"])) {
    $urlinfo["status"] = $_GET["status"];  
    if($urlinfo["status"] == "active") {
        $urlinfo["where"]["4"] = "garments_despecklesandrepairs.date_out is null";
    }
    else if($urlinfo["status"] == "inactive") {
        $urlinfo["where"]["4"] = "garments_despecklesandrepairs.date_out isnot null";
    }   
} else {
    $urlinfo["status"] = "";
}

$limit_total_res = db_count($table, $columns, $urlinfo);
if ($limit_total_res) {
    $urlinfo["limit_total"] = db_fetch_row($limit_total_res);
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string
}

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
$sortlinks["tag2"] = generate_sortlink("garments.tag2", $lang["tag"]."2", $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
$sortlinks["date_in"] = generate_sortlink("garments_despecklesandrepairs.date_in", $lang["date_in"], $pi, $urlinfo);
$sortlinks["date_out"] = generate_sortlink("garments_despecklesandrepairs.date_out", $lang["date_out"], $pi, $urlinfo);
$sortlinks["type"] = generate_sortlink("garments_despecklesandrepairs.type", $lang["type"], $pi, $urlinfo);
$sortlinks["status"] = generate_sortlink("garments_despecklesandrepairs.status", $lang["status"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
* Export
*/
if ($hassubmit == "export")
{
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["date_in"]."\t".$lang["date_out"]."\t".$lang["tag"]."\t".$lang["tag"]."2\t".$lang["article"]."\t".$lang["type"]."\t".$lang["description"]."\t".$lang["status"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["garments_despecklesandrepairs_date_in"],
            $row["garments_despecklesandrepairs_date_out"],
            $row["garments_tag"],
            $row["garments_tag2"],
            $row["articles_description"],
            $lang[$row["garments_despecklesandrepairs_type"]],
            (($row["garments_despecklesandrepairs_type"] == 'despeckle') ? $row["despeckles_description"] : $row["repairs_description"]),
            $lang[$row["garments_despecklesandrepairs_status"]]
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

/** required for selectbox: circulationgroups **/
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

/** Required for selectbox: Status **/
$statuses["active"]   = $lang["active"];
$statuses["inactive"] = $lang["inactive"];

/** Required for selectbox: Types **/
$types["despeckle"] = $lang["despeckle"];
$types["repair"]    = $lang["repair"];

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
    "pagination" => $pagination,
    "statuses" => $statuses,
    "types" => $types
);

template_parse($pi, $urlinfo, $cv);

?>
