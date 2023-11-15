<?php

/**
 * Report circulationadvice
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';
require_once 'include/mupapu.php';

/**
 * Page settings
 */
$urlinfo = array();

$pi["access"] = array("circulation_management", "circulationadvice");
$pi['group'] = $lang['circulation_management'];
$pi['title'] = $lang['circulationadvice'];
$pi['filename_list'] = 'report_circulationadvice_old.php';
$pi['filename_this'] = 'report_circulationadvice_old.php';
$pi['filename_details'] = '';

if (isset($_GET['print_cir']))
{
    $pi['template'] = 'layout/pages/report_circulationadvice_old.print_cir.tpl';
    $pi['page'] = 'report';
}
elseif (isset($_GET['print_sto']))
{
    $pi['template'] = 'layout/pages/report_circulationadvice_old.print_sto.tpl';
    $pi['page'] = 'report';
}
elseif (isset($_GET['print_order']))
{
    $pi['template'] = 'layout/pages/report_circulationadvice_old.print_order.tpl';
    $pi['page'] = 'report';
}
else
{
    $pi['template'] = 'layout/pages/report_circulationadvice_old.tpl';
    $pi['page'] = 'simple';
}

$urlinfo = array(
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "filter_last_scanned" => (!empty($_GET["filter_last_scanned"])) ? trim($_GET["filter_last_scanned"]) : ""
);

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}
/**
 * Generate MUPAPU
 */
if (!empty($_GET['cid']) && is_numeric($_GET['cid'])) $urlinfo['cid'] = trim($_GET['cid']);
else {
    // We use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions['order_by'] = 'name';
    $selected_circulationgroup_conditions['limit_start'] = 0;
    $selected_circulationgroup_conditions['limit_num'] = 1;
    $urlinfo['cid'] = db_fetch_row(db_read('circulationgroups', 'id', $selected_circulationgroup_conditions));
    $urlinfo['cid'] = $urlinfo['cid'][0];
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions['order_by'] = 'name';
$circulationgroups = db_read('circulationgroups', 'id name', $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

if (!empty($circulationgroups)) {
    while ($row = db_fetch_num($circulationgroups)) {
        $circulationgroups_name[$row[0]] = $row[1];
    }
}
db_data_seek($circulationgroups, 0);

if (!empty($urlinfo["filter_last_scanned"])) {
    $from_date = str_replace("-", "", $urlinfo["from_date"]) ."000000";
    $mupapu = mupapu_generate_with_lastscan_date($urlinfo['cid'], $GLOBALS['config']['mupapu_default_weeks_history'],$calculate = true, $from_date);
} else {
    $mupapu = mupapu_generate_with_lastscan_date($urlinfo['cid'], $GLOBALS['config']['mupapu_default_weeks_history'],$calculate = true);
}

/**
* Export
*/
if (isset($_GET["export"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["article"]."\t"." \t".$lang["circulation"]."\t"." \t"." \t".$lang["stock"]."\t\n";
    $header .= $lang["description"]."\t".$lang["size"]."\t".$lang["measured"]."\t".$lang["required"]."\t".$lang["complement"]."\t".$lang["measured"]."\t".$lang["required"]."\t".$lang["complement"]."\t".$lang["order"]."\t";
    $data = "";
        
    foreach ($mupapu["mup"] as $ars => $row) {
        $line = "";
        $in = array(
            $row["description"],
            $row["size"].((!empty($row["modification"])) ? " " . $row["modification"] : ""),
            (!empty($mupapu["mup"][$ars]["cir_cur"])) ? $mupapu["mup"][$ars]["cir_cur"]: "0",
            (!empty($mupapu["mup"][$ars]["cir_new"])) ? $mupapu["mup"][$ars]["cir_new"]: "0",
            (!empty($mupapu["mup"][$ars]["cir_diff"])) ? $mupapu["mup"][$ars]["cir_diff"]: "0",
            (!empty($mupapu["mup"][$ars]["sto_cur"])) ? $mupapu["mup"][$ars]["sto_cur"]: "0",
            (!empty($mupapu["mup"][$ars]["sto_new"])) ? $mupapu["mup"][$ars]["sto_new"]: "0",
            (!empty($mupapu["mup"][$ars]["sto_diff"])) ? $mupapu["mup"][$ars]["sto_diff"]: "0",
            (!empty($mupapu["mup"][$ars]["order"])) ? $mupapu["mup"][$ars]["order"]: "0"
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
        'pageinfo' => $pi,
        'filter_last_scanned' => ($urlinfo["filter_last_scanned"] == true) ? "checked=\"checked\"" : "",
        'urlinfo' => $urlinfo,
        'circulationgroup_count' => $circulationgroup_count,
        'circulationgroups' => $circulationgroups,
        'circulationgroups_name' => $circulationgroups_name,
        'mupapu' => $mupapu
    );

    template_parse($pi, $urlinfo = array(), $cv);


?>
