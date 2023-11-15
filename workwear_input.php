<?php

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["inputdata"];
$pi["template"] = "layout/pages/workwear_input.tpl";
$pi["filename_list"] = "workwear_input.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}
$urlinfo = array();

/**
 * Recursive xss prevention
 */

function xss_sanitize($x)
{
    if(is_array($x)) {
        foreach($x as $k=>$v) {
            $x[$k] = xss_sanitize($v);
        }
    }
    elseif(is_object($x)) {
        foreach($x as $k=>$v) {
            $x->$k = xss_sanitize($v);
        }
    }
    else {
        $x = htmlspecialchars($x, ENT_QUOTES);
    }
    
    return $x;
}

$workwearmanagement_sql = "SELECT * FROM workwearmanagement_data";
$workwearmanagement_result = db_query($workwearmanagement_sql);

$aResults = array();
while($row = $workwearmanagement_result->fetch_assoc()) {
    $row = xss_sanitize($row);
    $aResults = $row;
}

$update_fullprice = false;

if($_POST) {
//    echo "<pre>";
//    print_r($_POST);
//    echo "</pre>";
    $sUpdate = "UPDATE workwearmanagement_data SET delivery_time='". $_POST['sLevertijd']."', in_circulation_time='". $_POST['sInCirculatieBrengen']."', insert_date='". $_POST['sInsertDate']."', insert_price='". $_POST['sInsertPrice']."', checks_interval='". $_POST['iInterval']."', increment='". $_POST['sIncrement']."', "
            . "maintenance_cost='". $_POST['sBeheerPrijs']."', maxlimit='". $_POST['sUiterste']."', flag_action='". $_POST['sFlag']."', checking_percentage='". $_POST['sControlePercentage']."'";
    db_query($sUpdate);
    $workwearmanagement_sql = "SELECT * FROM workwearmanagement_data";
    $workwearmanagement_result = db_query($workwearmanagement_sql);
    
    if((float)$aResults['insert_price'] !== (float)$_POST['sInsertPrice'] || (float)$aResults['increment'] !== (float)$_POST['sIncrement']) {
        $update_fullprice = true;
    }

    $aResults = array();
    while($row = $workwearmanagement_result->fetch_assoc()) {
        $row = xss_sanitize($row);
        $aResults = $row;
    }
}

if($update_fullprice) {
    $sql = "UPDATE workwearmanagement_prices wp, workwearmanagement_data wd SET wp.fullprice = ((wp.price + wd.insert_price) * (1 + (wd.increment / 100)));";
    db_query($sql);
    $pi["note"] = "Prijzen zijn herberekend en aangepast!";
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
    "pagination" => $pagination,
    "FormData" => $aResults
);

template_parse($pi, $urlinfo, $cv);

?>