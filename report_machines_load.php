<?php

/**
 * Machine load
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("load", "load_per_station");
$pi["group"] = $lang["load"];
$pi["title"] = $lang["load_per_station"];
$pi["filename_list"] = "report_machines_load.php";
$pi["filename_details"] = "report_machines_load.php";
$pi["template"] = "layout/pages/report_machines_load.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Used variables
 */
$articles = null;
$distributors = null;
$listdata = null;
$pagination = null;
$resultinfo = null;
$sortlinks = null;
$types = null;
$sizes = null;
$urlinfo = array();

/**
 * Collect page content
 */

if (!empty($_GET["hassubmit"]))
{
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
}
else
{
    $hassubmit = "";
}

if (!empty($_GET["circulationgroup_id"])){ $urlinfo["circulationgroup_id"] = $_GET["circulationgroup_id"]; }else{ $urlinfo["circulationgroup_id"] = null; };
if (!empty($_GET["distributor_id"])){ $urlinfo["distributor_id"] = $_GET["distributor_id"]; }else{ $urlinfo["distributor_id"] = null; }
if (!empty($_GET["type"])){ $urlinfo["type"] = $_GET["type"]; }else{ $urlinfo["type"] = null; }
if (!empty($_GET["article_id"])){ $urlinfo["article_id"] = $_GET["article_id"]; }else{ $urlinfo["article_id"] = null; }
if (!empty($_GET["sid"])){$urlinfo["sid"] = $_GET["sid"];}else{$urlinfo["sid"] = null;}
$garment_id_to_free = (!empty($_GET["garment_id_to_free"])) ? $_GET["garment_id_to_free"] : null;
$hook_to_free = (!empty($_GET["hook_to_free"])) ? $_GET["hook_to_free"] : null;

$requiredfields = array();

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

if ($circulationgroup_count == 1)
{
    $urlinfo["circulationgroup_id"] = 1;
}

if (!empty($garment_id_to_free))
{
    if (empty($urlinfo["distributor_id"]))
    {
        array_push($requiredfields, $lang["station"]);
    }
    else
    {
        $update_garment_query = "UPDATE `garments`
                                SET `garments`.`scanlocation_id` =
                            (SELECT `scanlocations`.`id` FROM `scanlocations` WHERE `scanlocations`.`name` = \"Vermist\")
                              WHERE `garments`.`id` = ".$garment_id_to_free;
        db_query($update_garment_query);

        $d_data = array(
            "garment_id" => $garment_id_to_free,
            "distributor_id" => $urlinfo["distributor_id"],
            "hook" => $hook_to_free,
            "date" => "NOW()"
        );

        db_delete_where("distributors_load", "garment_id", $garment_id_to_free);
        db_insert("log_garments_removed_distributor", $d_data);
    }
}

if (!empty($requiredfields))
{
    $pi["note"] = html_requiredfields($requiredfields);
}

if (!empty($urlinfo["circulationgroup_id"]))
{
    // Required for selectbox: Stations
    $distributors_sql = db_query("SELECT distributors.id, distributors.doornumber FROM distributors INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id WHERE distributorlocations.circulationgroup_id = ". $urlinfo["circulationgroup_id"]) or die("ERROR LINE ". __LINE__);
    while ($distributor_data = db_fetch_row($distributors_sql))
    {
        $distributors[$distributor_data[0]] = $lang["station"] ." ". $distributor_data[1];
    }
    db_free_result($distributors_sql);

    // Required for selectbox: Type
    $types["userbound"] = $lang["userbound"];
    $types["size"] = $lang["garment_by_size"];
    $types["deleted"] = $lang["deleted"];

    // Required for selectbox: Articles
    $articles_conditions["order_by"] = "description";
    $articles = db_read("articles", "id description", $articles_conditions);

    // Required for selectbox: sizes
    if (!empty($urlinfo["article_id"]))
    {
        $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
        $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["article_id"];
        $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
        $sizes_conditions["order_by"] = "sizes.position";
        $sizes_conditions["group_by"] = "arsimos.size_id";
        $sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
        if (!empty($sizes_data))
        {
            while ($row = db_fetch_num($sizes_data))
            {
                $sizes[$row[0]] = $row[1];
            }
        }
        else
        {
            $sizes = null;
        }
    }
    else
    {
        $sizes = null;
    }

    $table = "distributors_load";

    $columns = "circulationgroups.id circulationgroups.name distributorlocations.name distributors.doornumber distributors_load.hook distributors_load.date_in articles.description";
    $columns .= " sizes.name sizes.position modifications.name garments.id garments.garmentuser_id garments.tag garments.deleted_on garmentusers.id garmentusers.title garmentusers.gender";
    $columns .= " garmentusers.initials garmentusers.intermediate garmentusers.surname garmentusers.maidenname";

    $urlinfo["inner_join"]["1"] = "distributors distributors_load.distributor_id distributors.id";
    $urlinfo["inner_join"]["2"] = "distributorlocations distributors.distributorlocation_id distributorlocations.id";
    $urlinfo["inner_join"]["3"] = "circulationgroups distributorlocations.circulationgroup_id circulationgroups.id";
    $urlinfo["inner_join"]["4"] = "garments distributors_load.garment_id garments.id";
    $urlinfo["inner_join"]["5"] = "arsimos garments.arsimo_id arsimos.id";
    $urlinfo["inner_join"]["6"] = "articles arsimos.article_id articles.id";
    $urlinfo["inner_join"]["7"] = "sizes arsimos.size_id sizes.id";
    $urlinfo["left_join"]["1"] = "modifications arsimos.modification_id modifications.id";
    $urlinfo["left_join"]["2"] = "garmentusers garments.garmentuser_id garmentusers.id";

    if (!empty($urlinfo["circulationgroup_id"]) && empty($urlinfo["distributor_id"]))
    {
        $urlinfo["where"]["1"] = "circulationgroups.id = ". $urlinfo["circulationgroup_id"];
    }
    if (!empty($urlinfo["circulationgroup_id"]) && empty($urlinfo["distributor_id"]))
    {
        $urlinfo["where"]["2"] = "circulationgroups.id = ". $urlinfo["circulationgroup_id"];
    }
    if (!empty($urlinfo["distributor_id"]))
    {
        $urlinfo["where"]["3"] = "distributors.id = ". $urlinfo["distributor_id"];
    }
    if (!empty($urlinfo["article_id"]))
    {
        $urlinfo["where"]["4"] = "articles.id = ". $urlinfo["article_id"];
    }
    if (!empty($urlinfo["sid"]))
    {
        $urlinfo["where"]["5"] = "sizes.id = " . $urlinfo["sid"];
    }
    if (!empty($urlinfo["type"]))
    {
        if ($urlinfo["type"] == "userbound"){ $urlinfo["where"]["6"] = "garments.garmentuser_id isnot NULL"; }
        if ($urlinfo["type"] == "size"){ $urlinfo["where"]["6"] = "garments.garmentuser_id is NULL"; }
        if ($urlinfo["type"] == "deleted"){ $urlinfo["where"]["6"] = "garments.deleted_on isnot NULL"; }
    }
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

    $urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

    $listdata = db_read($table, $columns, $urlinfo);

    $resultinfo = result_infoline($pi, $urlinfo);

    $sortlinks["station"] = generate_sortlink("distributors.doornumber", $lang["station"], $pi, $urlinfo);
    $sortlinks["hook"] = generate_sortlink("distributors_load.hook", $lang["hook"], $pi, $urlinfo);
    $sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["garmentuser"] = generate_sortlink("garmentusers.surname", $lang["garmentuser"], $pi, $urlinfo);
    $sortlinks["article"] = generate_sortlink("articles.description", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
    $sortlinks["modification"] = generate_sortlink("modifications.name", $lang["modification"], $pi, $urlinfo);
    $sortlinks["deleted_on"] = generate_sortlink("garments.deleted_on", $lang["deleted"], $pi, $urlinfo);

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

        $header = $lang["station"]."\t".$lang["hook"]."\t".$lang["tag"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["garmentuser"]."\t".$lang["modification"]."\t";
        $data = "";
        while($row = db_fetch_array($listdata))
        {
            $line = "";
            $in = array(
                $row["distributors_doornumber"],
                $row["distributors_load_hook"],
                $row["garments_tag"],
                $row["articles_description"],
                $row["sizes_name"],
                ($type == "userbound") ? generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]) : "",
                (!empty($row["modifications_name"]) ? $row["modifications_name"] : "")
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

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "article_id" => $urlinfo["article_id"],
    "articles" => $articles,
    "circulationgroup_id" => $urlinfo["circulationgroup_id"],
    "circulationgroups" => $circulationgroups,
    "circulationgroup_count" => $circulationgroup_count,
    "distributor_id" => $urlinfo["distributor_id"],
    "distributors" => $distributors,
    "type" => $urlinfo["type"],
    "types" => $types,
    "sizes" => $sizes
);

template_parse($pi, $urlinfo, $cv);

?>
