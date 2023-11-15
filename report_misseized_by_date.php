<?php

/**
 * Report misseized by date
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
$pi["access"] = array("reports", "misseized_garments");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["misseized_garments"];
$pi["template"] = "layout/pages/report_misseized_by_date.tpl";
$pi["page"] = "list";
$pi["filename_this"] = "report_misseized_by_date.php";
$pi["filename_list"] = "report_misseized_by_date.php";
$pi["filename_details"] = "report_misseized_garments.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Used variables
 */
$listdata = "";
$pagination = "";
$resultinfo = "";
$sortlinks = array();

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]) $hassubmit = "show";
    if ($_GET["hassubmit"] == $lang["export"]) $hassubmit = "export";
    if ($_GET["hassubmit"] == $lang["print"]) $hassubmit = "print";
} else {
    $hassubmit = null;
}

$urlinfo = array(
    "cid" => (!empty($_GET["cid"])) ? $_GET["cid"] : "",
    "from_date" => (!empty($_GET["from_date"])) ? $_GET["from_date"] : date("Y-m-d"),
    "to_date" => (!empty($_GET["to_date"])) ? $_GET["to_date"] : "",
    "lotsadays" => (!empty($_GET["lotsadays"])) ? $_GET["lotsadays"] : "",
    "hassubmit" => (!empty($_GET["hassubmit"])) ? trim($_GET["hassubmit"]) : ""
);

if (!empty($_GET["aid"])){ $urlinfo["aid"] = trim($_GET["aid"]); }else{ $urlinfo["aid"] = null; }
if (!empty($_GET["sid"])){ $urlinfo["sid"] = trim($_GET["sid"]); }else{ $urlinfo["sid"] = null; }

if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }

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

$sizes = null;

$table = "tmp_misseized_by_date";

if (empty($pi["note"])) {
    if (isset($_GET["lotsadays"])) {
        $sql_where_date = "BETWEEN '" . $urlinfo["from_date"] . "' AND '" . $urlinfo["to_date"] ."'";
    } else {
        $sql_where_date = "= '" . $urlinfo["from_date"] . "'";
    }
    
    if (!empty($urlinfo["aid"])) {
        $sql_where = " AND articles.id = " . $urlinfo["aid"];
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
        }
    } else {
        $sql_where = "";
    }

    if (!empty($urlinfo["sid"])) {
        $sql_where .= " AND sizes.id = " . $urlinfo["sid"];
    }

    $sql = "
        SELECT
        DATE(misseized.`date`) AS 'date',
        circulationgroups.name AS 'circulationgroup_name',
        misseized.userbound AS 'userbound',
        articles.description AS 'article',
        misseized.arsimo_id AS 'arsimo_id',
        sizes.name AS 'size',
        modifications.name AS 'modification',
        COUNT(DISTINCT(misseized.garmentuser_id)) AS 'count'
        FROM log_distributorclients `misseized`
        INNER JOIN distributorlocations ON misseized.distributorlocation_id = distributorlocations.id
        INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
        INNER JOIN arsimos ON misseized.arsimo_id = arsimos.id
        INNER JOIN articles ON arsimos.article_id = articles.id
        INNER JOIN sizes ON arsimos.size_id = sizes.id
        LEFT JOIN modifications ON arsimos.modification_id = modifications.id
        WHERE misseized.numgarments = 0 "
             . $sql_where .
        "    AND DATE(misseized.`date`) " . $sql_where_date . " 
        ". ((!empty($urlinfo["cid"])) ? "AND circulationgroups.id = ". $urlinfo["cid"] : "") ."
        GROUP BY
        DATE(misseized.`date`),
        misseized.userbound,
        misseized.arsimo_id
        ORDER BY
        DATE(misseized.`date`) ASC,
        circulationgroups.id ASC,
        misseized.userbound,
        COUNT(misseized.garmentuser_id) DESC,
        articles.description ASC,
        sizes.position ASC,
        modifications.name ASC
    "; 
    
    $query = "CREATE VIEW `". $table ."` AS (" . $sql . ")";
    
    db_query("DROP VIEW IF EXISTS `". $table ."`");
    db_query($query);

    /**
     * Collect page content
     */
    $columns = "date circulationgroup_name userbound article arsimo_id size modification count";

    $urlinfo["search"] = geturl_search();
    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction();

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

    $sortlinks["date"] = generate_sortlink("date", $lang["date"], $pi, $urlinfo);
    $sortlinks["article"] = generate_sortlink("article", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("size", $lang["size"], $pi, $urlinfo);
    $sortlinks["type"] = generate_sortlink("userbound", $lang["type"], $pi, $urlinfo);
    $sortlinks["count"] = generate_sortlink("count", $lang["count"], $pi, $urlinfo);
    
    $pagination = generate_pagination($pi, $urlinfo);

    if ($hassubmit == "export") {
        $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

        $header = $lang["date"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["type"]."\t".$lang["count"]."\t";
        $data = "";
        while ($row = db_fetch_array($listdata)) {
            $line = "";
            $in = array(
                $row["date"],
                $row["article"],
                $row["size"] . ((!empty($row["modification"])) ? " " . $row["modification"] : ""),
                $row["userbound"] ? $lang["userbound"] : $lang["sizebound"],
                $row["count"]
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
        $data_p = str_replace("\r","",$data);

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$export_filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        print "$header\n$data_p";
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
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "articles" => $articles,
    "sizes" => $sizes,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
