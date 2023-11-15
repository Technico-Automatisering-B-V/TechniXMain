<?php

/**
 * Size details
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
$pi["access"] = array("master_data", "sizes");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "sizes.php";
$pi["filename_details"] = "size_details.php";
$pi["template"] = "layout/pages/size_details.tpl";

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
    "name" => (!empty($_POST["name"])) ? $_POST["name"] : "",
    "sizegroup_id" => (!empty($_POST["sizegroup_id"])) ? $_POST["sizegroup_id"] : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "sizes";
$columns = "position id name sizegroup_id";

if (empty($detailsdata["sizegroup_id"])) array_push($requiredfields, $lang["sizegroup"]);
if (empty($detailsdata["name"])) array_push($requiredfields, $lang["size"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //prepare the given size for insertion
            $lastposition = db_fetch_row(db_read_max_value_from_where($table, "position", "sizegroup_id", $detailsdata["sizegroup_id"]));
            $detailsdata["position"] = $lastposition[0] + 1;

            //insert the given size
            db_insert($table, $detailsdata);

            $sizes_last_insert_id = db_fetch_row(db_read_last_insert_id());
            $sizes_last_insert_id = $sizes_last_insert_id[0];

            //add for all articles in arsimos table
            db_query("
                INSERT INTO arsimos(arsimos.article_id, arsimos.size_id, arsimos.modification_id, arsimos.deleted_on)
                SELECT articles.id, ". $sizes_last_insert_id .", modifications.id,  CURDATE()
                FROM articles
                INNER JOIN modifications
                WHERE articles.sizegroup_id = ". $detailsdata["sizegroup_id"] ."
                ORDER BY articles.id, modifications.id
            ");

            db_query("
                INSERT INTO arsimos(arsimos.article_id, arsimos.size_id, arsimos.deleted_on)
                SELECT articles.id, ". $sizes_last_insert_id .", CURDATE()
                FROM articles
                WHERE articles.sizegroup_id = ". $detailsdata["sizegroup_id"] ."
                ORDER BY articles.id
            ");

            //redirect to list, remembering the sizegroup we just inserted in to
            redirect($pi["filename_list"] . "?sizegroup_id=" . $detailsdata["sizegroup_id"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_size"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            //read the sizegroup_id for the old size
            $old_sizegroup_id_conditions["where"]["1"] = "id = " . $_POST["id"];
            $old_sizegroup_id = db_fetch_row(db_read($table, "sizegroup_id", $old_sizegroup_id_conditions));
            $old_sizegroup_id = $old_sizegroup_id[0];

            //read the last position for the current sizegroup
            $new_position = db_fetch_row(db_read_max_value_from_where($table, "position", "sizegroup_id", $detailsdata["sizegroup_id"]));
            //increment for our new size position
            $detailsdata["position"] = $new_position[0] + 1;

            //update the size by id
            db_update($table, $_POST["id"], $detailsdata);

            //see if the old sizegroup_id is the same as the new one
            if ($old_sizegroup_id != $detailsdata["sizegroup_id"]) {
                //the sizegroup_id has changed. we need to rearrange positions for the old sizegroup
                //to remove any gaps (caused by our size leaving the old sizegroup)
                $sizes_conditions["where"]["1"] = "sizegroup_id = " . $old_sizegroup_id;
                $sizes_conditions["order_by"] = "position";
                $sizes_conditions["order_direction"] = "ASC";
                $sizes = db_read($table, $columns, $sizes_conditions);
                $new_data["position"] = 0;
                while ($row = db_fetch_assoc($sizes)) {
                    $new_data["position"]++;
                    db_update($table, $row["id"], $new_data);
                }
            }

            //redirect to list, remembering the sizegroup we just updated in
            redirect($pi["filename_list"] . "?sizegroup_id=" . $detailsdata["sizegroup_id"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                //read the sizegroup_id to remember which positions to rearrange
                $sizegroup_id_conditions["where"]["1"] = "id = " . $_POST["id"];
                $sizegroup_id = db_fetch_row(db_read($table, "sizegroup_id", $sizegroup_id_conditions));
                $sizegroup_id = $sizegroup_id[0];

                $sizes_used_sql_conditions["order_by"] = "id";
                $sizes_used_sql_conditions["where"]["1"] = "arsimos.size_id = " . $_POST["id"];
                $sizes_used_sql_conditions["where"]["2"] = "arsimos.deleted_on is null ";
                $sizes_used_sql_conditions["limit_start"] = 0;
                $sizes_used_sql_conditions["limit_num"] = 1;

                $sizes_used = db_fetch_row(db_read("arsimos", "id", $sizes_used_sql_conditions));
                $sizes_used = $sizes_used[0];

                if (empty($sizes_used)) {
                    db_query("DELETE FROM arsimos WHERE arsimos.size_id = ". $_POST["id"]);

                    db_delete($table, $_POST["id"]);

                    //rearrange positions to remove any gaps (caused by deleted sizes)
                    $sizes_conditions["where"]["1"] = "sizegroup_id = " . $sizegroup_id;
                    $sizes_conditions["order_by"] = "position";
                    $sizes_conditions["order_direction"] = "ASC";
                    $sizes = db_read($table, $columns, $sizes_conditions);
                    $new_data["position"] = 0;
                    while ($row = db_fetch_assoc($sizes)) {
                        $new_data["position"]++;
                        db_update($table, $row["id"], $new_data);
                    }
                }

                redirect($pi["filename_list"] . "?sizegroup_id=" . $sizegroup_id);

            } elseif (!isset($_POST["abort"])) {
                    $pi["note"] = html_delete($_POST["id"], $lang["size"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["size_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
	//we haven't got the correct page info, redirect to list
	redirect($pi["filename_list"]);
}

//required for selectbox: sizegroups
$sizegroups_conditions["order_by"] = "name";
$sizegroups = db_read("sizegroups", "id name", $sizegroups_conditions);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "sizegroups" => $sizegroups
);

template_parse($pi, $urlinfo, $cv);

?>
