<?php

/**
 * Report garments in use
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
$pi["access"] = array("lists", "garments_in_use");
$pi["group"] = $lang["lists"];
$pi["title"] = $lang["garments_in_use"];
$pi["template"] = "layout/pages/report_garments_in_use.tpl";
$pi["page"] = "list";
$pi["filename_this"] = "report_garments_in_use.php";
$pi["filename_list"] = "report_garments_in_use.php";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]){ $hassubmit = "show"; }
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
    if ($_GET["hassubmit"] == $lang["print"]){ $hassubmit = "print"; }
} else {
    $hassubmit = "";
}

$urlinfo = array(
    "daysback" => (!empty($_GET["daysback"])) ? trim($_GET["daysback"]) : 2
);

if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = $_GET["cid"];
    $_SESSION["filter"]["garments"]["cid"] = trim($_GET["cid"]);
} else {
    // We use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions["order_by"] = "name";
    $selected_circulationgroup_conditions["limit_start"] = 0;
    $selected_circulationgroup_conditions["limit_num"] = 1;
    $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
    $urlinfo["cid"] = "";
    if (empty($_GET["search"]))
    {
        unset($_SESSION["filter"]["garments_in_use"]["cid"]);
    }
}

if (!empty($_GET["clientdepartment_id"])) {
    $urlinfo["clientdepartment_id"] = $_GET["clientdepartment_id"];
    $_SESSION["filter"]["garments"]["clientdepartment_id"] = $_GET["clientdepartment_id"];
} else {
    $urlinfo["clientdepartment_id"] = "";
    if (empty($_GET["search"])) {
        unset($_SESSION["filter"]["garments_in_use"]["clientdepartment_id"]);
    }
}

if (!empty($_GET["aid"])) {
    $urlinfo["aid"] = $_GET["aid"];
    $_SESSION["filter"]["garments"]["aid"] = $_GET["aid"];
} else {
    $urlinfo["aid"] = "";
    if (empty($_GET["search"])) {
        unset($_SESSION["filter"]["garments_in_use"]["aid"]);
    }
}

if (!empty($_GET["sid"])) {
    $urlinfo["sid"] = $_GET["sid"];
    $_SESSION["filter"]["garments"]["sid"] = $_GET["sid"];
} else {
    $urlinfo["sid"] = "";
    if (empty($_GET["search"])) {
        unset($_SESSION["filter"]["garments_in_use"]["sid"]);
    }
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: clientdepartment_id
$clientdepartments_conditions["order_by"] = "name";
$clientdepartments = db_read("clientdepartments", "id name", $clientdepartments_conditions);
$clientdepartments_count = db_num_rows($clientdepartments);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

// Required for selectbox: sizes
if (!empty($urlinfo["aid"])) {
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

//if there is no valid daysback, use 0

$table = "garmentusers_garments";
$columns = "garmentusers_garments.date_received garmentusers_garments.garmentuser_id garmentusers_garments.garment_id clientdepartments.name";
$columns .= " articles.description sizes.name modifications.name garments.tag";
$columns .= " garmentusers.id garmentusers.title garmentusers.gender garmentusers.initials garmentusers.intermediate garmentusers.surname garmentusers.name garmentusers.maidenname garmentusers.personnelcode";

$urlinfo["inner_join"]["1"] = "distributors garmentusers_garments.distributor_id distributors.id";
$urlinfo["inner_join"]["2"] = "distributorlocations distributors.distributorlocation_id distributorlocations.id";
$urlinfo["inner_join"]["3"] = "circulationgroups distributorlocations.circulationgroup_id circulationgroups.id";
$urlinfo["inner_join"]["4"] = "garments garmentusers_garments.garment_id garments.id";
$urlinfo["inner_join"]["5"] = "arsimos garments.arsimo_id arsimos.id";
$urlinfo["inner_join"]["6"] = "articles arsimos.article_id articles.id ";
$urlinfo["inner_join"]["7"] = "sizes arsimos.size_id sizes.id";
$urlinfo["inner_join"]["8"] = "garmentusers garmentusers_garments.garmentuser_id garmentusers.id";
$urlinfo["left_join"]["1"] = "modifications arsimos.modification_id modifications.id";
$urlinfo["left_join"]["2"] = "clientdepartments garmentusers.clientdepartment_id clientdepartments.id";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = "garmentusers_garments.date_received";
$urlinfo["order_direction"] = "ASC";

if (isset($_SESSION["filter"]["garments_in_use"]["cid"])){ $urlinfo["cid"] = $_SESSION["filter"]["garments_in_use"]["cid"]; }
if (isset($_SESSION["filter"]["garments_in_use"]["aid"])){ $urlinfo["aid"] = $_SESSION["filter"]["garments_in_use"]["aid"]; }
if (isset($_SESSION["filter"]["garments_in_use"]["sid"])){ $urlinfo["sid"] = $_SESSION["filter"]["garments_in_use"]["sid"]; }
if (isset($_SESSION["filter"]["garments_in_use"]["clientdepartment_id"])){ $urlinfo["clientdepartment_id"] = $_SESSION["filter"]["garments_in_use"]["clientdepartment_id"]; }

if (!empty($urlinfo["cid"])) {
    $urlinfo["where"]["10"] = "circulationgroups.id = " . $urlinfo["cid"];
}

if (!empty($urlinfo["clientdepartment_id"])) {
    $urlinfo["where"]["11"] = "clientdepartments.id = " . $urlinfo["clientdepartment_id"];
}

if (!empty($urlinfo["aid"])) {
    $urlinfo["where"]["12"] = "articles.id = " . $urlinfo["aid"];
}

if (!empty($urlinfo["sid"])) {
    $urlinfo["where"]["13"] = "sizes.id = " . $urlinfo["sid"];
}

if (!isset($urlinfo["where"][0])) {
    $urlinfo["where"][0] = "date_received isnot NULL";
}

if (!empty($urlinfo["daysback"])) {
    list($day, $month, $year) = explode("-", date("d-m-Y"));
    $filterdate = date("d-m-Y", mktime(0, 0, 0, $month, $day-$urlinfo["daysback"], $year));

    $filtersql = date("Y-m-d", mktime(0, 0, 0, $month, $day-$urlinfo["daysback"]+1, $year));
    $urlinfo["where"][2] = "date_received <= " . $filtersql;
} else {
    $filterdate = "";
}

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
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["distribution"] = generate_sortlink("garmentusers_garments.date_received", $lang["distribution"], $pi, $urlinfo);
$sortlinks["surname"] = generate_sortlink("garmentusers.surname", $lang["surname"], $pi, $urlinfo);
$sortlinks["name"] = generate_sortlink("garmentusers.name", $lang["first_name"], $pi, $urlinfo);
$sortlinks["personnelcode"] = generate_sortlink("garmentusers.personnelcode", $lang["personnelcode"], $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
$sortlinks["clientdepartment"] = generate_sortlink("clientdepartments.name", $lang["clientdepartment"], $pi, $urlinfo);
$sortlinks["days"] = generate_sortlink("garmentusers_garments.date_received", $lang["days"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

if ($hassubmit == "export") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=$export_filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["distribution"]."\t".$lang["surname"]."\t".$lang["first_name"]."\t".$lang["personnelcode"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["clientdepartment"]."\t".$lang["tag"]."\t".$lang["days"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $days = ceil((strtotime("now") - strtotime($row["garmentusers_garments_date_received"])) / 86400);
        
        
        $in = array(
            $row["garmentusers_garments_date_received"],
            generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"]),
            $row["garmentusers_name"],
            $row["garmentusers_personnelcode"],
            $row["articles_description"],
            $row["sizes_name"],
            $row["clientdepartments_name"],
            $row["garments_tag"],
            $days
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
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}

/**
 * Generate the page
 */
$cv = array(
    "hassubmit" => $hassubmit,
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "resultinfo" => $resultinfo,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "filterdate" => $filterdate,
    "articles" => $articles,
    "sizes" => $sizes,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "clientdepartments" => $clientdepartments,
    "clientdepartments_count" => $clientdepartments_count
);

template_parse($pi, $urlinfo, $cv);

?>
