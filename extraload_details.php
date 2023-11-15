<?php

/**
 * Extra load details
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
$pi["access"] = array("linen_service", "extra_load");
$pi["group"] = $lang["linen_service"];
$pi["filename_list"] = "extraload.php";
$pi["filename_details"] = "extraload_details.php";
$pi["template"] = "layout/pages/extraload_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$bindingdata = array(
    "distributorlocation_id" => (!empty($_POST["distributorlocation_id"])) ? trim($_POST["distributorlocation_id"]) : "",
    "article_id" => (!empty($_POST["article_id"])) ? trim($_POST["article_id"]) : "",
    "size_id" => (!empty($_POST["size_id"])) ? trim($_POST["size_id"]) : "",
    "modification_id" => (!empty($_POST["modification_id"])) ? trim($_POST["modification_id"]) : ""
);

$detailsdata = array(
    "demand" => (!empty($_POST["demand"])) ? trim($_POST["demand"]) : null
);

$requiredfields = array();
$urlinfo = array();

$table = "distributorlocations_loadadvice";

if (empty($bindingdata["distributorlocation_id"])) array_push($requiredfields, $lang["distributorlocation"]);
if (empty($bindingdata["article_id"])) array_push($requiredfields, $lang["article"]);
if (empty($bindingdata["size_id"])) array_push($requiredfields, $lang["size"]);
if (empty($detailsdata["demand"])) array_push($requiredfields, $lang["demand"]);

if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"]) || isset($_POST["detailssubmitnew"])) {
        if (empty($requiredfields)) {
            //retrieve the arsimo_id from bindingdata
            $arsimo_conditions["where"]["1"] = "article_id = " . $bindingdata["article_id"];
            $arsimo_conditions["where"]["2"] = "size_id = " . $bindingdata["size_id"];
            $arsimo_conditions["where"]["3"] = "modification_id " . ((!empty($bindingdata["modification_id"])) ? "= " . $bindingdata["modification_id"] : "is NULL");
            $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
            $arsimo_id = db_fetch_num($arsimo_data);
            $detailsdata["arsimo_id"] = $arsimo_id[0];

            $detailsdata["distributorlocation_id"] = $bindingdata["distributorlocation_id"];
            $detailsdata["critical_percentage"] = "0.33";
            $detailsdata["type"] = "manual";

            $sql = "DELETE FROM `distributorlocations_loadadvice` WHERE `arsimo_id` = ". $detailsdata["arsimo_id"] ." AND `distributorlocation_id` = ". $detailsdata["distributorlocation_id"] ." AND `type` = 'manual'";
            $q = db_query($sql);

            db_insert($table, $detailsdata);

            if (isset($_POST["detailssubmitnew"])) {
                //we stay in details but we clear the detailsdata
                $bindingdata = array(
                    "article_id" => "",
                    "size_id" => "",
                    "modification_id" => "",
                );
                $detailsdata = array(
                    "demand" => "",
                );
            } else {
                //redirect to list
                redirect($pi["filename_list"]);
            }
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }
    } elseif (isset($_POST["detailssubmitnone"])){
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["add_extra_load"];

} else {
    //we haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

//required for selectbox: distributorlocations
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);

//required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles_data = db_read("articles", "id articlenumber description", $articles_conditions);
if (!empty($articles_data)) {
    while ($row = db_fetch_num($articles_data)) {
        $articles[$row[0]] = ((!empty($row[1])) ? "(" . $row[1] . ") " : "") . $row[2];
    }
} else {
    $articles = null;
}

//required for selectbox: sizes
if (!empty($bindingdata["article_id"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $bindingdata["article_id"];
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

//required for selectboxes: modifications
if (!empty($bindingdata["article_id"]) && !empty($bindingdata["size_id"])) {
    $modifications_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
    $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $bindingdata["article_id"];
    $modifications_conditions["where"]["2"] = "arsimos.size_id = " . $bindingdata["size_id"];
    $modifications_conditions["where"]["3"] = "arsimos.modification_id isnot NULL";
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

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "distributorlocations" => $distributorlocations,
    "articles" => $articles,
    "sizes" => $sizes,
    "modifications" => $modifications,
    "bindingdata" => $bindingdata,
    "detailsdata" => $detailsdata
);

template_parse($pi, $urlinfo, $cv);

?>
