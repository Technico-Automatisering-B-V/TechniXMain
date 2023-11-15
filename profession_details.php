<?php

/**
 * Profession details
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
$pi["access"] = array("master_data", "professions");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "professions.php";
$pi["filename_details"] = "profession_details.php";
$pi["template"] = "layout/pages/profession_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$detailsdata = array(
    "name" => (!empty($_POST["name"])) ? trim($_POST["name"]) : "",
    "timelock" => (isset($_POST["timelock"])) ? trim($_POST["timelock"]) : 0,
    "daysbeforelock" => (!empty($_POST["daysbeforelock"])) ? trim($_POST["daysbeforelock"]) : null,
    "daysbeforewarning" => (!empty($_POST["daysbeforewarning"])) ? trim($_POST["daysbeforewarning"]) : null,
    "importcode" => (!empty($_POST["importcode"])) ? trim($_POST["importcode"]) : null
);

$error = "0";
$requiredfields = array();
$urlinfo = array();
$article_selected = array();

$articles_selected = (!empty($_POST["articles_selected"])) ? $_POST["articles_selected"] : array();
$articles_selected_c = (!empty($_POST["articles_selected_c"])) ? $_POST["articles_selected_c"] : array();

$table = "professions";

if (empty($detailsdata["name"])) array_push($requiredfields, $lang["name"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"]))
{
    if (isset($_POST["page"]) && $_POST["page"] == "add")
    {
        if (empty($requiredfields))
        {

            if (!empty($detailsdata["daysbeforelock"]) && !empty($detailsdata["daysbeforewarning"]))
            {
                if ($detailsdata["daysbeforelock"] < $detailsdata["daysbeforewarning"])
                {
                    $error = "1";
                    $pi["note"] = html_warning($lang["warning_must_before_blockage"]);
                }
            }

            if ($error == "0"){
                //insert the given profession
                db_insert($table, $detailsdata);

                $db_last_insert_id = db_fetch_row(db_read_last_insert_id());
                foreach ($articles_selected as $article_id => $credit)
                {
                    db_query("INSERT INTO `garmentprofiles` (`article_id`, `profession_id`, `credit`) VALUES (". $article_id .", " . $db_last_insert_id[0] . ", " . $credit ." )");
                }

                //redirect to list
                redirect($pi["filename_list"]);
            }

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_profession"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {

            if ($error == "0"){
                //update the profession
                db_update($table, $_POST["id"], $detailsdata);

                //delete garment profile
                db_query("DELETE FROM `garmentprofiles` WHERE `profession_id` = ". $_POST["id"]);

                // insert new garment profile
                foreach ($articles_selected as $article_id => $f) { 
                    db_query("INSERT INTO `garmentprofiles` (`article_id`, `profession_id`, `credit`) VALUES (". $article_id .", " . $_POST["id"] . ", " . $articles_selected_c[$article_id] .")");
                }
                //redirect to list
                redirect($pi["filename_list"]);
            }

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                //delete the profession
                db_delete($table, $_POST["id"]);

                //delete profession profile
                db_query("DELETE FROM `garmentprofiles` WHERE `profession_id` = ". $_POST["id"]);

                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["profession"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["profession_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

$columns = "description id";
$ui["order_by"] = geturl_order_by($columns);
$ui["order_direction"] = geturl_order_direction();

//required for all articles
$articles_all = db_read("articles", $columns, $ui);

//all selected articles
$articles_selected_columns = "article_id credit";
$articles_selected_conditions["where"]["1"] = "garmentprofiles.profession_id = " . $detailsdata["id"];
$articles_selected_resource = db_read("garmentprofiles", $articles_selected_columns, $articles_selected_conditions);
while ($row = db_fetch_num($articles_selected_resource)) {
    $articles_selected[$row[0]] = $row[1];
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "articles_all" => $articles_all,
    "articles_selected" => $articles_selected
);

template_parse($pi, $urlinfo, $cv);

?>
