<?php

/**
 * Garmentmodification details
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
$pi["access"] = array("master_data", "garmentmodifications");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "garmentmodifications.php";
$pi["filename_details"] = "garmentmodification_details.php";
$pi["template"] = "layout/pages/garmentmodification_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$detailsdata = array("name" => (!empty($_POST["name"])) ? trim($_POST["name"]) : "");
$requiredfields = array();
$urlinfo = array();

$table = "modifications";

if (empty($detailsdata["name"])){ array_push($requiredfields, $lang["name"]); }

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //insert the given garmentmodification
            db_insert($table, $detailsdata);
            $modification_last_insert_id = db_fetch_row(db_read_last_insert_id());
            $modification_last_insert_id = $modification_last_insert_id[0];

            //add for all articles in arsimos table
            db_query("
                INSERT INTO arsimos(arsimos.article_id, arsimos.size_id, arsimos.modification_id, arsimos.deleted_on)
                SELECT articles.id, sizes.id, ". $modification_last_insert_id .",  CURDATE()
                FROM articles
                INNER JOIN sizes ON articles.sizegroup_id = sizes.sizegroup_id
                ORDER BY articles.id, sizes.id
                ");

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_garmentmodification"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            //update the garmentmodification
            db_update($table, $_POST["id"], $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                $modifications_used_sql_conditions["order_by"] = "id";
                $modifications_used_sql_conditions["where"]["1"] = "arsimos.modification_id = " . $_POST["id"];
                $modifications_used_sql_conditions["where"]["2"] = "arsimos.deleted_on is null";
                $modifications_used_sql_conditions["limit_start"] = 0;
                $modifications_used_sql_conditions["limit_num"] = 1;

                $modifications_used = db_fetch_row(db_read("arsimos", "id", $modifications_used_sql_conditions));
                $modifications_used = $modifications_used[0];

                if (empty($modifications_used)) {
                    db_query("DELETE FROM arsimos WHERE arsimos.modification_id = ". $_POST["id"]);

                    //delete the garmentmodification
                    db_delete($table, $_POST["id"]);
                }

                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["garmentmodification"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["garmentmodification_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata
);

template_parse($pi, $urlinfo, $cv);

?>
