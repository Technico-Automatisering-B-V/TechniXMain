<?php

/**
 * Garments
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require("vendors/xlsxwriter.class.php");
/**
 * Page settings
 */
$pi["access"] = array("common", "garments");
$pi["group"] = $lang["common"];
$pi["title"] = $lang["garments"];
$pi["filename_list"] = "garments.php";
$pi["filename_details"] = "garment_details.php";
$pi["template"] = "layout/pages/garments.tpl";
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

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){
        $hassubmit = "export";
    }
} else {
    $hassubmit = "";
}

$table = "garments";

$columns = "garments.tag garments.tag2 garments.id garments.circulationgroup_id garments.deleted_on garments.garmentuser_id articles.description sizes.name sizes.position modifications.name";
$columns .= " garmentusers.title garmentusers.gender garmentusers.initials garmentusers.intermediate garmentusers.surname garmentusers.maidenname garmentusers.personnelcode garments.washcount scanlocations.scanlocationstatus_id scanlocations.name scanlocations.translate scanlocations.description sub_scanlocations.value sub_scanlocations.translate garments.lastscan garments.deleted_on";

$urlinfo["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
$urlinfo["left_join"]["2"] = "articles arsimos.article_id articles.id ";
$urlinfo["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
$urlinfo["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
$urlinfo["left_join"]["5"] = "garmentusers garments.garmentuser_id garmentusers.id";
$urlinfo["left_join"]["6"] = "scanlocations scanlocations.id garments.scanlocation_id";
$urlinfo["left_join"]["7"] = "sub_scanlocations sub_scanlocations.id garments.sub_scanlocation_id";

if (!empty($_GET["del"])){ $urlinfo["del"] = $_GET["del"]; }else{ $urlinfo["del"] = ""; }
if (!empty($_GET["aid"])){ $urlinfo["aid"] = $_GET["aid"]; }else{ $urlinfo["aid"] = ""; }
if (!empty($_GET["sid"])){ $urlinfo["sid"] = $_GET["sid"]; }else{ $urlinfo["sid"] = ""; }
if (!empty($_GET["mid"])){ $urlinfo["mid"] = $_GET["mid"]; }else{ $urlinfo["mid"] = ""; }
if (!empty($_GET["scid"])){ $urlinfo["scid"] = $_GET["scid"]; }else{ $urlinfo["scid"] = ""; }
if (!empty($_GET["cid"])){
    $urlinfo["cid"] = $_GET["cid"];
    $_SESSION["filter"]["garments"]["cid"] = $_GET["cid"];
} else {
    //we use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions["order_by"] = "name";
    $selected_circulationgroup_conditions["limit_start"] = 0;
    $selected_circulationgroup_conditions["limit_num"] = 1;
    $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
    $urlinfo["cid"] = "";
}

if (!empty($_GET["col-tag"])){ $urlinfo["col-tag"] = $_GET["col-tag"]; }else{ $urlinfo["col-tag"] = ""; }
if (!empty($_GET["col-tag2"])){ $urlinfo["col-tag2"] = $_GET["col-tag2"]; }else{ $urlinfo["col-tag2"] = ""; }
if (!empty($_GET["col-article"])){ $urlinfo["col-article"] = $_GET["col-article"]; }else{ $urlinfo["col-article"] = ""; }
if (!empty($_GET["col-size"])){ $urlinfo["col-size"] = $_GET["col-size"]; }else{ $urlinfo["col-size"] = ""; }
if (!empty($_GET["col-modification"])){ $urlinfo["col-modification"] = $_GET["col-modification"]; }else{ $urlinfo["col-modification"] = ""; }
if (!empty($_GET["col-washcount"])){ $urlinfo["col-washcount"] = $_GET["col-washcount"]; }else{ $urlinfo["col-washcount"] = ""; }
if (!empty($_GET["col-owner"])){ $urlinfo["col-owner"] = $_GET["col-owner"]; }else{ $urlinfo["col-owner"] = ""; }
if (!empty($_GET["col-lastscan"])){ $urlinfo["col-lastscan"] = $_GET["col-lastscan"]; }else{ $urlinfo["col-lastscan"] = ""; }
if (!empty($_GET["col-status"])){ $urlinfo["col-status"] = $_GET["col-status"]; }else{ $urlinfo["col-status"] = ""; }
if (!empty($_GET["col-deleted"])){ $urlinfo["col-deleted"] = $_GET["col-deleted"]; }else{ $urlinfo["col-deleted"] = ""; }
if (!empty($_GET["dsearch"])){ $urlinfo["dsearch"] = $_GET["dsearch"]; }else{ $urlinfo["dsearch"] = ""; }


// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

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
    
    if (!empty($urlinfo["sid"])) {
        $modifications_conditions["inner_join"]["1"] = "modifications modifications.id arsimos.modification_id";
        $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
        $modifications_conditions["where"]["2"] = "arsimos.size_id = " . $urlinfo["sid"];
        $modifications_conditions["where"]["3"] = "arsimos.deleted_on is null";
        $modifications_conditions["order_by"] = "modifications.id";
        $modifications_conditions["group_by"] = "arsimos.modification_id";
        $modifications_data = db_read("arsimos", "arsimos.modification_id modifications.name", $modifications_conditions);
        if (!empty($modifications_data)) {
            while ($row = db_fetch_num($modifications_data)) {
                $modifications[$row[0]] = $row[1];
            }
        } else {
            $modifications = null;
        }
    } else {
        $modifications = null;
    }
    
} else {
    $sizes = null;
    $modifications = null;
}

// Required for selectbox: statuses
$statuses_sql = "SELECT `scanlocationstatuses`.`id`, `scanlocationstatuses`.`name` from `scanlocationstatuses`
                  WHERE `scanlocationstatuses`.`name` NOT LIKE 'deleted'
               ORDER BY `scanlocationstatuses`.`description`";
$statuses = db_query($statuses_sql);



if (!empty($urlinfo["del"])){ $urlinfo["where"]["1"] = "garments.deleted_on isnot null"; }else{ $urlinfo["where"]["1"] = "garments.deleted_on is null"; }
if (!empty($urlinfo["cid"])){ $urlinfo["where"]["2"] = "garments.circulationgroup_id = " . $urlinfo["cid"]; }
if (!empty($urlinfo["aid"])){ $urlinfo["where"]["3"] = "arsimos.article_id = " . $urlinfo["aid"]; }
if (!empty($urlinfo["sid"])){ $urlinfo["where"]["4"] = "arsimos.size_id = " . $urlinfo["sid"]; }
if (!empty($urlinfo["mid"])){ $urlinfo["where"]["5"] = "arsimos.modification_id = " . $urlinfo["mid"]; }
if (!empty($urlinfo["scid"])){ $urlinfo["where"]["6"] = "scanlocations.scanlocationstatus_id = " . $urlinfo["scid"]; }

$urlinfo["search"] = trim(geturl_search(true, $urlinfo["cid"]),"'");

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

$sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
$sortlinks["tag2"] = generate_sortlink("garments.tag2", $lang["tag"]." 2", $pi, $urlinfo);
$sortlinks["description"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
$sortlinks["modification"] = generate_sortlink("modifications.name", $lang["modification"], $pi, $urlinfo);
$sortlinks["washcount"] = generate_sortlink("garments.washcount", $lang["washed"], $pi, $urlinfo);
$sortlinks["owner"] = generate_sortlink("garmentusers.surname", $lang["owner"], $pi, $urlinfo);
$sortlinks["lastscan"] = generate_sortlink("garments.lastscan", $lang["last_scanned"], $pi, $urlinfo);
$sortlinks["status"] = generate_sortlink("scanlocations.translate sub_scanlocations.translate", $lang["status"], $pi, $urlinfo);
$sortlinks["deleted"] = generate_sortlink("garments.deleted_on", $lang["deleted"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Export
 */
if ($hassubmit == "export") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    if(!empty($urlinfo["col-tag"]))$header.=$lang["tag"]."\t";
    if(!empty($urlinfo["col-tag2"]))$header.=$lang["tag2"]."\t";
    if(!empty($urlinfo["col-article"]))$header.=$lang["article"]."\t";
    if(!empty($urlinfo["col-size"]))$header.=$lang["size"]."\t";
    if(!empty($urlinfo["col-modification"]))$header.=$lang["modification"]."\t";
    if(!empty($urlinfo["col-washcount"]))$header.=$lang["washed"]."\t";
    if(!empty($urlinfo["col-owner"]))$header.=$lang["owner"]."\t";
    if(!empty($urlinfo["col-lastscan"]))$header.=$lang["last_scanned"]."\t";
    if(!empty($urlinfo["col-status"]))$header.=$lang["status"]."\t";
    if(!empty($urlinfo["col-deleted"]))$header.=$lang["deleted"]."\t";
    
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";

        $in = array();
        if(!empty($urlinfo["col-tag"]))array_push($in,"'".$row["garments_tag"]);
        if(!empty($urlinfo["col-tag2"]))array_push($in,"'".$row["garments_tag2"]);
        if(!empty($urlinfo["col-article"]))array_push($in,ucfirst($row["articles_description"]));
        if(!empty($urlinfo["col-size"]))array_push($in,$row["sizes_name"]);
        if(!empty($urlinfo["col-modification"]))array_push($in,(($row["modifications_name"]) ? $row["modifications_name"] : $lang["none"]));
        if(!empty($urlinfo["col-washcount"]))array_push($in,(($row["garments_washcount"] == 0) ? "-" : $row["garments_washcount"] . "x"));
        if(!empty($urlinfo["col-owner"]))array_push($in,((!empty($row["garmentusers_surname"])) ? generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]) : $lang["garment_by_size"]));
        if(!empty($urlinfo["col-lastscan"]))array_push($in,(($row["garments_lastscan"]) ? $row["garments_lastscan"] : $lang["never_scanned"]));
        if(!empty($urlinfo["col-status"]))array_push($in,(!empty($row["scanlocations_translate"])?(!empty($row["sub_scanlocations_translate"])?$lang[$row["sub_scanlocations_translate"]]:$lang[$row["scanlocations_translate"]]):$lang["none"]));     
        if(!empty($urlinfo["col-deleted"]))array_push($in,(($row["garments_deleted_on"]) ? $row["garments_deleted_on"] : $lang["none"]));

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
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "resultinfo" => $resultinfo,
    "articles" => $articles,
    "sizes" => $sizes,
    "modifications" => $modifications,
    "statuses" => $statuses,
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
