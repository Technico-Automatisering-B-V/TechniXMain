<?php

/**
 * Report loaded garments by date
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
$pi["access"] = array("lists", "loaded_garments");
$pi["group"] = $lang["lists"];
$pi["title"] = $lang["loaded_garments"];
$pi["template"] = "layout/pages/report_loaded_by_date.tpl";
$pi["page"] = "list";
$pi["filename_this"] = "report_loaded_by_date.php";
$pi["filename_list"] = "report_loaded_by_date.php";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Used variables
 */
$sizes = null;
$listdata = "";
$resultinfo = "";

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]){ $hassubmit = "show"; }
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
    if ($_GET["hassubmit"] == $lang["print"]){ $hassubmit = "print"; }
} else {
    $hassubmit = null;
}

$urlinfo = array(
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "to_date" => (!empty($_GET["to_date"])) ? trim($_GET["to_date"]) : "",
    "lotsadays" => (!empty($_GET["lotsadays"])) ? trim($_GET["lotsadays"]) : "",
    "hassubmit" => (!empty($_GET["hassubmit"])) ? trim($_GET["hassubmit"]) : ""
);

if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }
if (!empty($_GET["cid"])){ $urlinfo["cid"] = trim($_GET["cid"]); }else{ $urlinfo["cid"] = 1; }
if (!empty($_GET["aid"])){ $urlinfo["aid"] = trim($_GET["aid"]); }else{ $urlinfo["aid"] = null; }
if (!empty($_GET["sid"])){ $urlinfo["sid"] = trim($_GET["sid"]); }else{ $urlinfo["sid"] = null; }

if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
    if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
        $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
    }
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

if (empty($pi["note"])) {
    $table = "log_distributors_load";
    $columns = "log_distributors_load.endtime log_distributors_load.garment_id log_distributors_load.hook ";
    $columns .= " articles.description sizes.name modifications.name garments.tag garmentusers.id garments.id circulationgroups.name distributors.doornumber ";
    $columns .= " garmentusers.title garmentusers.gender garmentusers.initials garmentusers.intermediate garmentusers.surname garmentusers.maidenname garmentusers.personnelcode ";
    
    $urlinfo["inner_join"]["1"] = "distributors log_distributors_load.distributor_id distributors.id";
    $urlinfo["inner_join"]["2"] = "distributorlocations distributors.distributorlocation_id distributorlocations.id";
    $urlinfo["inner_join"]["3"] = "circulationgroups distributorlocations.circulationgroup_id circulationgroups.id";
    $urlinfo["inner_join"]["4"] = "garments log_distributors_load.garment_id garments.id";
    $urlinfo["inner_join"]["5"] = "arsimos garments.arsimo_id arsimos.id";
    $urlinfo["inner_join"]["6"] = "articles arsimos.article_id articles.id ";
    $urlinfo["inner_join"]["7"] = "sizes arsimos.size_id sizes.id";
    $urlinfo["left_join"]["1"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $urlinfo["left_join"]["2"] = "modifications arsimos.modification_id modifications.id";    
    
    $urlinfo["search"] = geturl_search();
    $urlinfo["group_by"] = "";
    $urlinfo["order_by"] = "log_garmentusers_garments.endtime";
    $urlinfo["order_direction"] = "DESC";

    $urlinfo["where"] = geturl_where();

    if (!empty($urlinfo["cid"])) {
        $urlinfo["where"]["10"] = "circulationgroups.id = " . $urlinfo["cid"];
    }

    if (!empty($urlinfo["aid"])) {
        $urlinfo["where"]["11"] = "articles.id = " . $urlinfo["aid"];
        $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
        $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
        $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
        $sizes_conditions["order_by"] = "sizes.position";
        $sizes_conditions["group_by"] = "arsimos.size_id";
        $sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
        if (!empty($sizes_data)) {
            while ($row = db_fetch_num($sizes_data)) {
                $sizes[$row[0]] = $row[1];
            }
        } else {
            $sizes = null;
        }
    } else {
        $sizes = null;
    }

    if (!empty($urlinfo["sid"])) {
        $urlinfo["where"]["12"] = "sizes.id = " . $urlinfo["sid"];
    }
    
    if (!isset($urlinfo["where"]["0"])) {
        $urlinfo["where"]["0"] = "endtime isnot NULL";
        if (!empty($urlinfo["lotsadays"])) {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";

            $urlinfo["where"]["1"] = "endtime >= ". $from_date_db;
            $urlinfo["where"]["2"] = "endtime <= ". $to_date_db;
        } else {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

            $urlinfo["where"]["1"] = "endtime >= ". $from_date_db;
            $urlinfo["where"]["2"] = "endtime <= ". $to_date_db;
        }
    }

    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction("DESC");

    if ($hassubmit == "export") {
        $urlinfo["limit_start"] = 0;
        $urlinfo["limit_num"] = "65535";
    } else {
        $urlinfo["limit_start"] = geturl_limit_start();
        $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
    }

    $urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
    $urlinfo["limit_total"] = $urlinfo["limit_total"]["0"]; //array->string

    $listdata = db_read($table, $columns, $urlinfo);

    $resultinfo = result_infoline($pi, $urlinfo);

    $sortlinks["date"] = generate_sortlink("log_distributors_load.endtime", $lang["date"], $pi, $urlinfo);
    $sortlinks["location"] = generate_sortlink("circulationgroups.name", $lang["location"], $pi, $urlinfo);
    $sortlinks["station"] = generate_sortlink("distributors.doornumber", $lang["station"], $pi, $urlinfo);
    $sortlinks["hook"] = generate_sortlink("log_distributors_load.hook", $lang["hook"], $pi, $urlinfo);
    $sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
    $sortlinks["owner"] = generate_sortlink("garmentusers.surname", $lang["owner"], $pi, $urlinfo);
    
    $pagination = generate_pagination($pi, $urlinfo);

    // Export
    if ($hassubmit == "export") {
        $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

        $header = $lang["date"]."\t".$lang["location"]."\t".$lang["station"]."\t".$lang["hook"]."\t".$lang["tag"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["owner"]."\t";
        $data = "";
        while ($row = db_fetch_array($listdata)) {
            $line = "";
            $in = array(
                $row["log_distributors_load_endtime"],
                $row["circulationgroups_name"],
                $row["distributors_doornumber"],
                $row["log_distributors_load_hook"],
                "'".$row["garments_tag"],
                $row["articles_description"],
                $row["sizes_name"],
                generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"])
            );

            foreach ($in as $value) {
                if (!isset($value) || $value == "") {
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

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$export_filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        print "$header\n$data_r";
        die();
    }

}

/**
 * Generate the page
 */
$cv = array(
    "hassubmit" => $hassubmit,
    "pi" => $pi,
    "lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "resultinfo" => $resultinfo,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "articles" => $articles,
    "sizes" => $sizes,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups
);

template_parse($pi, $urlinfo, $cv);

?>
