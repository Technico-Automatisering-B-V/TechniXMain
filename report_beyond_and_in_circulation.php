<?php

/**
 * Report beyond and in circulation
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
$pi["access"] = array("circulation_management", "beyond_and_in_circulation");
$pi["group"] = $lang["circulation_management"];
$pi["title"] = $lang["beyond_and_in_circulation"];
$pi["template"] = "layout/pages/report_beyond_and_in_circulation.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "report_beyond_and_in_circulation.php";
$pi["filename_list"] = "report_beyond_and_in_circulation.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}
/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"]))
{
    if ($_GET["hassubmit"] == $lang["export"]){
        $hassubmit = "export";
    }else{
        $hassubmit = "view";
    }
}
else
{
    $hassubmit = "";
}

$urlinfo["where"] = geturl_where();
$urlinfo = array(
    "daysback" => (!empty($_GET["daysback"])) ? trim($_GET["daysback"]) : 0,
    "hassubmit" => (!empty($_GET["hassubmit"])) ? trim($_GET["hassubmit"]) : ""
);

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = $_GET["cid"];
} else {
    $urlinfo["cid"] = "";
}

if (!empty($_GET["aid"])) {
    $urlinfo["aid"] = $_GET["aid"];
} else {
    $urlinfo["aid"] = "";
}

if (!empty($_GET["sid"])) {
    $urlinfo["sid"] = $_GET["sid"];
} else {
    $urlinfo["sid"] = "";
}

// Required for selectbox: sizes
if (!empty($urlinfo["aid"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
    $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
    $sizes_conditions["order_by"] = "sizes.position";
    $sizes_conditions["group_by"] = "arsimos.size_id";
    $sizes = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
} else {
    $sizes = null;
}

if (!empty($_GET["type"])) {
    $urlinfo["type"] = $_GET["type"];
} else {
    $urlinfo["type"] = "";
}

if (!empty($urlinfo["type"])) {
    if($urlinfo["type"] == "beyond_circulation") {
        $urlinfo["where"]["6"] = "scanlocations.circulationgroup_id is null";
        
        // Status selector
        if(isset($_SESSION["report_beyond_and_in_circulation"]["type"]) && $_SESSION["report_beyond_and_in_circulation"]["type"] == "beyond_circulation") {
            $_SESSION["report_beyond_and_in_circulation"]["show"] = (isset($_GET["s"])) ? $_GET["s"] : (isset($_GET["where7"]) ? $_SESSION["report_beyond_and_in_circulation"]["show"] : null);

            if (isset($_SESSION["report_beyond_and_in_circulation"]["show"]))
            {
                foreach ($_SESSION["report_beyond_and_in_circulation"]["show"] as $show => $value)
                {
                    if (!isset($urlinfo["where"]["7"])) {
                        switch ($show) {
                            case "1": $urlinfo["where"]["7"] = "scanlocationstatuses.name = missing";
                                    break;
                            case "2": $urlinfo["where"]["7"] = "scanlocationstatuses.name = stock_hospital";
                                    break;
                            case "3": $urlinfo["where"]["7"] = "scanlocationstatuses.name = stock_laundry";
                                    break;
                            case "4": $urlinfo["where"]["7"] = "scanlocationstatuses.name = homewash";
                                    break;
                            case "5": $urlinfo["where"]["7"] = "scanlocationstatuses.name = repair";
                                    break;
                            case "6": $urlinfo["where"]["7"] = "scanlocationstatuses.name = never_scanned";
                                    break;
                            case "7": $urlinfo["where"]["7"] = "scanlocationstatuses.name = despeckle";
                                    break;
                            case "8": $urlinfo["where"]["7"] = "scanlocationstatuses.name = disconnected_from_garmentuser";
                                    break;
                        }
                    } else {
                        switch ($show) {
                            case "1": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = missing";
                                    break;
                            case "2": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = stock_hospital";
                                    break;
                            case "3": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = stock_laundry";
                                    break;
                            case "4": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = homewash";
                                    break;
                            case "5": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = repair";
                                    break;
                            case "6": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = never_scanned";
                                    break;
                            case "7": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = despeckle";
                                    break;
                            case "8": $urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = disconnected_from_garmentuser";
                                    break;
                        }
                    }
                }
            }
        } else {
            $_SESSION["report_beyond_and_in_circulation"]["show"] = null;
        }
        
        $_SESSION["report_beyond_and_in_circulation"]["type"] = "beyond_circulation";
    }
    else if($urlinfo["type"] == "in_circulation") {
        $urlinfo["where"]["6"] = "scanlocations.circulationgroup_id isnot null";
        
        // Status selector
        if(isset($_SESSION["report_beyond_and_in_circulation"]["type"]) && $_SESSION["report_beyond_and_in_circulation"]["type"] == "in_circulation") {
            $_SESSION["report_beyond_and_in_circulation"]["show"] = (isset($_GET["s"])) ? $_GET["s"] : (isset($_GET["where7"]) ? $_SESSION["report_beyond_and_in_circulation"]["show"] : null);

            if (isset($_SESSION["report_beyond_and_in_circulation"]["show"]))
            {
                foreach ($_SESSION["report_beyond_and_in_circulation"]["show"] as $show => $value)
                {
                    if (!isset($urlinfo["where"]["7"])) {
                        switch ($show) {
                            case 1: $urlinfo["where"]["7"] = "scanlocationstatuses.name = conveyor";
                                    break;
                            case 2: $urlinfo["where"]["7"] = "scanlocationstatuses.name = loaded";
                                    break;
                            case 3: $urlinfo["where"]["7"] = "scanlocationstatuses.name = rejected";
                                    break;
                            case 4:	$urlinfo["where"]["7"] = "scanlocationstatuses.name = distributed";
                                    break;
                            case 5:	$urlinfo["where"]["7"] = "scanlocationstatuses.name = deposited";
                                    break;
                            case 6:	$urlinfo["where"]["7"] = "scanlocationstatuses.name = container";
                                    break;
                            case 7:	$urlinfo["where"]["7"] = "scanlocationstatuses.name = transport_to_laundry";
                                    break;
                            case 8:	$urlinfo["where"]["7"] = "scanlocationstatuses.name = laundry";
                                    break;
                        }
                    } else {
                        switch ($show) {
                            case 1:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = conveyor";
                                    break;
                            case 2:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = loaded";
                                    break;
                            case 3:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = rejected";
                                    break;
                            case 4:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = distributed";
                                    break;
                            case 5:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = deposited";
                                    break;
                            case 6:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = container";
                                    break;
                            case 7:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = transport_to_laundry";
                                    break;
                            case 8:	$urlinfo["where"]["7"] .= " OR scanlocationstatuses.name = laundry";
                                    break;
                        }
                    }
                }
            }
        } else {
            $_SESSION["report_beyond_and_in_circulation"]["show"] = null;
        }
        $_SESSION["report_beyond_and_in_circulation"]["type"] = "in_circulation";
    }   
}

//if there is no valid daysback, use 0
if (!isset($urlinfo["daysback"]) || (isset($urlinfo["daysback"]) && $urlinfo["daysback"] < 0)) $urlinfo["daysback"] = 0;

$filterdate = "";
$listdata = "";
$pagination = "";
$resultinfo = "";
$sortlinks = array();

if (!empty($_GET["hassubmit"]))
{
    $table = "garments";
    $columns = "garments.lastscan garments.id garments.tag articles.description sizes.name modifications.name";
    $columns .= " garments.garmentuser_id scanlocations.name scanlocations.translate sub_scanlocations.value sub_scanlocations.translate";

    $urlinfo["inner_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $urlinfo["inner_join"]["2"] = "articles arsimos.article_id articles.id";
    $urlinfo["inner_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $urlinfo["left_join"]["1"]  = "modifications arsimos.modification_id modifications.id";
    $urlinfo["inner_join"]["4"] = "scanlocations garments.scanlocation_id scanlocations.id";
    $urlinfo["inner_join"]["5"] = "scanlocationstatuses scanlocations.scanlocationstatus_id scanlocationstatuses.id";
    $urlinfo["left_join"]["2"]  = "sub_scanlocations garments.sub_scanlocation_id sub_scanlocations.id";

    $urlinfo["search"] = geturl_search();
    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction();

    if ($hassubmit == "export")
    {
        $urlinfo["limit_start"] = 0;
        $urlinfo["limit_num"] = "65535";
    } else {
        $urlinfo["limit_start"] = geturl_limit_start();
        $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
    }

    $urlinfo["where"]["1"] = "garments.deleted_on is null";
    
    if (!empty($urlinfo["cid"]))
    {
        $urlinfo["where"]["2"] = "garments.circulationgroup_id = ". $urlinfo["cid"];
    }

    if (!empty($urlinfo["aid"]))
    {
        $urlinfo["where"]["3"] = "articles.id = ". $urlinfo["aid"];
    }
    
    if (!empty($urlinfo["sid"]))
    {
        $urlinfo["where"]["4"] = "sizes.id = ". $urlinfo["sid"];
    }

    if (!empty($urlinfo["daysback"]))
    {
        list($day, $month, $year) = array(date("d"),date("m"),date("Y"));
        $filterdate = date("d-m-Y", mktime(0, 0, 0, $month, $day-$urlinfo["daysback"], $year));

        $filtersql = date("Y-m-d", mktime(0, 0, 0, $month, $day-$urlinfo["daysback"]+1, $year));
        $urlinfo["where"]["5"] = "garments.lastscan <= " . $filtersql;
    }

    $urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

    $listdata = db_read($table, $columns, $urlinfo);

    $resultinfo = result_infoline($pi, $urlinfo);

    $sortlinks["lastscan"] = generate_sortlink("garments.lastscan", $lang["last_scanned"], $pi, $urlinfo);
    $sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
    $sortlinks["modification"] = generate_sortlink("modifications.name", $lang["modification"], $pi, $urlinfo);
    $sortlinks["status"] = generate_sortlink("scanlocations.translate", $lang["status"], $pi, $urlinfo);
    $sortlinks["days"] = generate_sortlink("garments.lastscan", $lang["days"], $pi, $urlinfo);

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

        $header = $lang["last_scanned"]."\t".$lang["tag"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["modification"]."\t".$lang["status"]."\t".$lang["days"]."\t";
        $data = "";
        while($row = db_fetch_array($listdata))
        {
            $line = "";
            $in = array(
                $row["garments_lastscan"],
                "'".$row["garments_tag"],
                $row["articles_description"],
                $row["sizes_name"],
                (!empty($row["modifications_name"]) ? $row["modifications_name"] : ""),
                $lang[$row["scanlocations_translate"]],
                ceil((strtotime("now") - strtotime($row["garments_lastscan"])) / 86400)
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
}

/** Required for selectbox: Types **/
$types["beyond_circulation"] = $lang["beyond_circulation"];
$types["in_circulation"]    = $lang["in_circulation"];

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "articles" => $articles,
    "sizes" => $sizes,
    "filterdate" => $filterdate,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "types" => $types
);

template_parse($pi, $urlinfo, $cv);

?>
