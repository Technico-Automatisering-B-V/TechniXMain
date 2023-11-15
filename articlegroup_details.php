<?php

/**
 * Articlegroup details
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
$pi = array();
$pi["access"] = array("master_data", "articlegroups");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "articlegroups.php";
$pi["filename_details"] = "articlegroup_details.php";
$pi["template"] = "layout/pages/articlegroup_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

if (isset($_POST["detailssubmitnone"])){ redirect($pi["filename_list"]); }

/**
 * Define used variables
 */
$requiredfields = array();
$urlinfo = array();

/**
 * Collect page content
 */

/** Articlelink data **/
$detailsdata = array(
    "id" => (!empty($_POST["id"])) ? trim($_POST["id"]) : false,
    "article_1_id" => (!empty($_POST["articlelink_article_1_id"])) ? trim($_POST["articlelink_article_1_id"]) : "",
    "article_2_id" => (!empty($_POST["articlelink_article_2_id"])) ? trim($_POST["articlelink_article_2_id"]) : "",
    "profession_id" => (!empty($_POST["articlelink_profession_id"])) ? trim($_POST["articlelink_profession_id"]) : NULL,
    "combined_credit" => (!empty($_POST["articlelink_combined_credit"])) ? $_POST["articlelink_combined_credit"] : "0",
    "extra_credit" => (!empty($_POST["articlelink_extra_credit"])) ? $_POST["articlelink_extra_credit"] : "0",
    "only_main_article" => (!empty($_POST["articlelink_only_main_article"])) ? $_POST["articlelink_only_main_article"] : "0",
);

$table = "articlegroups";

if (empty($detailsdata["article_1_id"])) {
    array_push($requiredfields, $lang["article"]);
} elseif(empty($detailsdata["article_2_id"])) {
    array_push($requiredfields, $lang["article"]);
} elseif ($detailsdata["article_1_id"] == $detailsdata["article_2_id"]) {
    $existence_note = $lang["the_article_2_cannot_be_the_same_as_the_article_1"];
} else {
    $q1 = "SELECT COUNT(*) FROM `articlegroups` WHERE `id` != ". $detailsdata["id"] ." AND ( `article_1_id` = ". $detailsdata["article_1_id"] ." OR `article_2_id` = ". $detailsdata["article_1_id"] .")";
    if(!empty($detailsdata["profession_id"])) {
        $q1 .= " AND (`profession_id` = " . $detailsdata["profession_id"] . " OR `profession_id` IS NULL)";
    }
        
    $s1 = db_fetch_num(db_query($q1));
    $ga1 = $s1[0];

    $q2 = "SELECT COUNT(*) FROM `articlegroups` WHERE `id` != ". $detailsdata["id"] ." AND ( `article_1_id` = ". $detailsdata["article_2_id"] ." OR `article_2_id` = ". $detailsdata["article_2_id"] .")";
    if(!empty($detailsdata["profession_id"])) {
        $q2 .= " AND (`profession_id` = " . $detailsdata["profession_id"] . " OR `profession_id` IS NULL)";
    }
    
    $s2 = db_fetch_num(db_query($q2));
    $ga2 = $s2[0];

    if(isset($ga1) && $ga1 > 0) {
        $existence_note = $lang["the_article_1_already_linked"];
    } elseif(isset($ga2) && $ga2 > 0) {
        $existence_note = $lang["the_article_2_already_linked"];
    }
}

// Add articlegroup
if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["detailssubmit"])) {
        if (!empty($requiredfields)) {
            $pi["note"] = html_requiredfields($requiredfields);
        } elseif (!empty($existence_note)) {
            $pi["note"] = html_requirednote($existence_note);
        } else {
            // Insert the given article
            db_insert($table, $detailsdata);

            // Redirect to list
            redirect($pi["filename_list"]);
        }
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["add_articlegroup"];

} elseif ((isset($_POST["page"])) && ($_POST["page"] == "details") && ($detailsdata["id"])) {
    if (isset($_POST["detailssubmit"])) {
        if (!empty($requiredfields)) {
            $pi["note"] = html_requiredfields($requiredfields);
        } elseif (!empty($existence_note)) {
            $pi["note"] = html_requirednote($existence_note);
        } else {
            $data["articlegroup_id"] = $detailsdata["id"];

            // Update the article
            db_update($table, $detailsdata["id"], $detailsdata);

            // Redirect to list
            redirect($pi["filename_list"]);
        }
    }
    
    if (isset($_POST["gosubmit"]) && isset($_POST["delete"]) && $_POST["delete"] == "yes") {
        if (isset($_POST["confirmed"])) {
            // Delete the article
            db_delete($table, $detailsdata["id"]);

            // Redirect to list
            redirect($pi["filename_list"]);

        } elseif (!isset($_POST["abort"])) {
            $pi["note"] = html_delete($_POST["id"], $lang["articlegroup"]);
        }
    }
    
    $pi["page"]  = "details";
    $pi["title"] = $lang["articlegroup_details"];

    // Continue showing the page with details
    $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $detailsdata["id"]));

    // We need the id for toolbar buttons
    $urlinfo["id"] = $detailsdata["id"];
} else {
    // We haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

// Required for articlelink selectbox: all articles related by profession
$articlelink_articles_columns = "articles.id articles.description";
$articlelink_articles_conditions["order_by"] = "articles.description";
$articlelink_articles_resource = db_read("articles", $articlelink_articles_columns, $articlelink_articles_conditions);
while ($row = db_fetch_num($articlelink_articles_resource)) {
    $articlelink_articles[$row[0]] = $row[1];
}

// Required for selectbox: professions
$professions_conditions["order_by"] = "name";
$professions_resource = db_read("professions", "id name", $professions_conditions);
while ($row = db_fetch_num($professions_resource)) {
    $professions[$row[0]] = $row[1];
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "articlelink_articles" => $articlelink_articles,
    "professions" => $professions
);

template_parse($pi, $urlinfo, $cv);

?>
