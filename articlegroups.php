<?php

/**
 * Articlegroups
 *
 * @author    Gabor Voros <gabor@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Require necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["access"] = array("master_data", "articlegroups");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["articlegroups"];
$pi["filename_list"] = "articlegroups.php";
$pi["filename_details"] = "articlegroup_details.php";
$pi["template"] = "layout/pages/articlegroups.tpl";
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
$table = "tmp_articlegroups";
$urlinfo = array();

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}

/**
 * Create view
 */

$query = "CREATE VIEW `". $table ."` AS (SELECT 
        `ag`.`id`,`ag`.`article_1_id`,
        `ag`.`article_2_id`,`ag`.`extra_credit`,
        `ag`.`combined_credit`,
        `ag`.`only_main_article`,
        `a1`.`description` `article_1`,
        `a2`.`description` `article_2`,
        `p`.`name` `profession`
        FROM `articlegroups` `ag`
  INNER JOIN `articles` `a1` ON `a1`.`id` = `ag`.`article_1_id`
  INNER JOIN `articles` `a2` ON `a2`.`id` = `ag`.`article_2_id`
   LEFT JOIN `professions` `p` ON `p`.`id` = `ag`.`profession_id`
    ORDER BY `p`.`name`, `a1`.`description`, `a2`.`description`
    )";

db_query("DROP VIEW IF EXISTS `". $table ."`");
db_query($query);

$columns = "id article_1_id article_2_id extra_credit combined_credit only_main_article article_1 article_2 profession";

/**
 * Collect page content
 */

if (!empty($_GET["col-article_1"])){ $urlinfo["col-article_1"] = $_GET["col-article_1"]; }else{ $urlinfo["col-article_1"] = ""; }
if (!empty($_GET["col-article_2"])){ $urlinfo["col-article_2"] = $_GET["col-article_2"]; }else{ $urlinfo["col-article_2"] = ""; }
if (!empty($_GET["col-combined_credit"])){ $urlinfo["col-combined_credit"] = $_GET["col-combined_credit"]; }else{ $urlinfo["col-combined_credit"] = ""; }
if (!empty($_GET["col-only_main_article"])){ $urlinfo["col-only_main_article"] = $_GET["col-only_main_article"]; }else{ $urlinfo["col-only_main_article"] = ""; }
if (!empty($_GET["col-extra_credit"])){ $urlinfo["col-extra_credit"] = $_GET["col-extra_credit"]; }else{ $urlinfo["col-extra_credit"] = ""; }
if (!empty($_GET["col-profession"])){ $urlinfo["col-profession"] = $_GET["col-profession"]; }else{ $urlinfo["col-profession"] = ""; }

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

$listdata = db_read($table, $columns, $urlinfo);

$limit_total_res = db_count($table, $columns, $urlinfo);
if ($limit_total_res) {
    $urlinfo["limit_total"] = db_fetch_row($limit_total_res);
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string
}

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["article_1"] = generate_sortlink("article_1", $lang["article"]." 1", $pi, $urlinfo);
$sortlinks["article_2"] = generate_sortlink("article_2", $lang["article"]." 2", $pi, $urlinfo);
$sortlinks["combined_credit"] = generate_sortlink("combined_credit", $lang["combined_credit"], $pi, $urlinfo);
$sortlinks["only_main_article"] = generate_sortlink("only_main_article", $lang["only_main_article"], $pi, $urlinfo);
$sortlinks["extra_credit"] = generate_sortlink("extra_credit", $lang["extra_credit"], $pi, $urlinfo);
$sortlinks["profession"] = generate_sortlink("profession", $lang["profession"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Export
 */
if ($hassubmit == "export") {
    $exname = "export_articlegroups_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $exname);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["article"]." 1\t".$lang["article"]." 2\t".$lang["combined_credit"]."\t".$lang["only_main_article"]."\t".$lang["extra_credit"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["article_1"],
            $row["article_2"],
            $row["profession"],
            $row["combined_credit"],
            $row["only_main_article"],
            $row["extra_credit"]
        );

        foreach($in as $value) {
            if (!isset($value) || $value == "") {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = $value . "\t";
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
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $ui, $cv);