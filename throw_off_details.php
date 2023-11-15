<?php

/**
 * Circulation Management Details
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["title"] = $lang["throw_off_garments"];
$pi["access"] = array("linen_service", "throw_off_garments");
$pi["group"] = $lang["linen_service"];
$pi["filename_list"] = "throw_off.php";
$pi["filename_details"] = "throw_off_details.php";
$pi["template"] = "layout/pages/throw_off_details.tpl";

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
    "id" => (!empty($_POST["id"])) ? trim($_POST["id"]) : null,
    "active" => (!empty($_POST["active"])) ? 1 : 0,
    "distributorlocation_id" => (!empty($_POST["distributorlocation_id"])) ? trim($_POST["distributorlocation_id"]) : null,
    "garment_id" => (!empty($_POST["garment_id"])) ? trim($_POST["garment_id"]) : null,
    "arsimo_id" => null,
    "article_id" => (!empty($_POST["article_id"])) ? trim($_POST["article_id"]) : null,
    "amount" => (!empty($_POST["amount"])) ? trim($_POST["amount"]) : null,
    "max_washcount" => (!empty($_POST["max_washcount"])) ? trim($_POST["max_washcount"]) : null,
    "date_from" => (!empty($_POST["date_from"])) ? trim($_POST["date_from"]) : date("Y-m-d"),
    "date_to" => (!empty($_POST["date_to"])) ? trim($_POST["date_to"]) : null
);

$bindingdata = array(
    "tag" => (!empty($_POST["tag"])) ? convertTag(trim($_POST["tag"])) : null,
    "certain_date" => (!empty($_POST["certain_date"])) ? 1 : null,
    "multiple_dates" => (!empty($_POST["multiple_dates"])) ? 1 : null,
    "size_id" => (!empty($_POST["size_id"])) ? trim($_POST["size_id"]) : null,
    "modification_id" => (!empty($_POST["modification_id"])) ? trim($_POST["modification_id"]) : null
);

$error = "0";
$requiredfields = array();
$tag_error = "";
$urlinfo = array();
$table = "throw_off_rules";

/** Cancel button **/
if (isset($_POST["detailssubmitnone"])) {
	redirect($pi["filename_list"]);
}

/**
 * Search for tag
 */
if (isset($_POST["searchsubmit"])) {
    if (!empty($bindingdata["tag"])) {
        $garment_id = tag_to_garment_id($bindingdata["tag"]);
        if ($garment_id) {
            $garmentdata = db_fetch_assoc(db_read_row_by_id("garments", $garment_id));
            if ($garmentdata) {
                /** Retrieve the arsimo data from garmentlink **/
                $arsimo_conditions["where"]["1"] = "id = " . $garmentdata["arsimo_id"];
                $arsimo_data = db_read("arsimos", "id article_id size_id modification_id", $arsimo_conditions);
                $arsimo_data = db_fetch_assoc($arsimo_data);

                $detailsdata["article_id"] = $arsimo_data["article_id"];
                $bindingdata["size_id"] = $arsimo_data["size_id"];
                $bindingdata["modification_id"] = $arsimo_data["modification_id"];
            }
        } else {
            $tag_error = $lang["garment_not_found"];
            $detailsdata["article_id"] = null;
            $bindingdata["size_id"] = null;
            $bindingdata["modification_id"] = null;
        }
    } else {
        $detailsdata["article_id"] = null;
        $bindingdata["size_id"] = null;
        $bindingdata["modification_id"] = null;
    }
}

if (!isset($_POST["id"])) {
    $pi["page"] = "add";
} else {
    $pi["page"] = "details";
}

/**
 * Form is submit
 */
if (isset($_POST["detailssubmit"]) || isset($_POST["detailssubmitnew"])) {
    if (empty($detailsdata["distributorlocation_id"])){ $error = "1"; array_push($requiredfields, $lang["distributorlocation"]); }
    if (empty($detailsdata["article_id"])){ $error = "1"; array_push($requiredfields, $lang["article"]); }

    if ($error == "0") {
        if (!empty($bindingdata["tag"]) && !isset($_POST["editsubmit"])) {
            $detailsdata["garment_id"] = tag_to_garment_id($bindingdata["tag"]);
            $detailsdata["amount"] = 1;
            $tag_existence_columns = "id";
            $tag_existence_conditions["where"]["1"] = "garment_id = " . $detailsdata["garment_id"];
            $tag_existence_conditions["where"]["2"] = "distributorlocation_id = " . $detailsdata["distributorlocation_id"];
            $tag_existence = db_fetch_assoc(db_read($table, $tag_existence_columns, $tag_existence_conditions));
            if (!empty($tag_existence["id"])) {
                $error = "1";
                $existence_note = $lang["tag_allready_inserted"];
            }
        }
    }

    /**
     * Datum controle
     */
    if ($error == "0") {
        if ($bindingdata["certain_date"] == 1) {
            if (!empty($detailsdata["date_from"]) && !empty($detailsdata["date_to"])) {
	        if ($detailsdata["date_from"] > $detailsdata["date_to"]) {
                    $error = "1";
                    $existence_note = $lang["error_date_from_greater_then_to"];
                }
            }
        } else {
            $detailsdata["date_from"] = null;
            $detailsdata["date_to"] = null;
        }
    }

    /**
     * Arsimo opzoeken
     */
    if ($error == "0") {
        if (!empty($bindingdata["size_id"]) && empty($bindingdata["tag"])) {
            $arsimos_conditions["where"]["1"] = "article_id = ". $detailsdata["article_id"];
            $arsimos_conditions["where"]["2"] = "size_id = ". $bindingdata["size_id"];
            $arsimos_conditions["where"]["3"] = "modification_id ". ((!empty($bindingdata["modification_id"])) ? "= ". $bindingdata["modification_id"] : "is NULL");
            $arsimos_resource = db_read("arsimos", "id", $arsimos_conditions);
            $arsimos_count = db_count("arsimos", "id", $arsimos_conditions);

            if ($arsimos_count > 0) {
                $arsimo = db_fetch_row($arsimos_resource);
                $detailsdata["arsimo_id"] = $arsimo[0];
                $detailsdata["garment_id"] = null;
                $detailsdata["article_id"] = null;
            } else {
                $error = "1";
                $existence_note = $lang["an_error_has_occurred"];
            }
        } else {
            $detailsdata["arsimo_id"] = null;
        }
    }

    /**
     * Laatste controle
     */
    if ($error == "0") {
        if (!empty($detailsdata["garment_id"]) && !empty($bindingdata["tag"])) {
            $detailsdata["arsimo_id"] = null;
            $detailsdata["article_id"] = null;
        }
    }

    /**
     * Toevoegen / bijwerken in database
     */
    if ($error == "0") {
        if (!isset($_POST["editsubmit"])) {
            db_insert($table, $detailsdata);
        } else {
            db_update($table, $_POST["id"], $detailsdata);
        }

        if (!isset($_POST["detailssubmitnew"])) {
            redirect($pi["filename_list"]);
        } elseif (isset($_POST["detailssubmitnew"])) {
            unset($detailsdata);
            unset($bindingdata);
        }
    }
}

/**
 * Detail pagina
 */
elseif ($_POST["page"] == "details" && !empty($detailsdata["id"])) {
    if (!isset($_POST["editsubmit"])) {
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $detailsdata["id"]));
        $bindingdata["certain_date"] = (!empty($detailsdata["date_from"])) ? 1 : null;
        $bindingdata["multiple_dates"] = ((!empty($detailsdata["date_from"]) && !empty($detailsdata["date_to"])) && ($detailsdata["date_from"] != $detailsdata["date_to"])) ? 1 : null;
    }

    if (!empty($detailsdata["garment_id"])) {
        $garment_conditions["where"]["1"] = "id = ". $detailsdata["garment_id"];
        $garment_resource = db_read("garments", "tag arsimo_id", $garment_conditions);
        $garment = db_fetch_row($garment_resource);
        $bindingdata["tag"] = $garment[0];
        $detailsdata["arsimo_id"] = $garment[1];
    }

    if (!empty($detailsdata["arsimo_id"]) && !isset($_POST["editsubmit"])) {
        $arsimos_conditions["where"]["1"] = "id = ". $detailsdata["arsimo_id"];
        $arsimos_resource = db_read("arsimos", "size_id modification_id article_id", $arsimos_conditions);
        $arsimos = db_fetch_row($arsimos_resource);
        $bindingdata["size_id"] = $arsimos[0];
        $bindingdata["modification_id"] = $arsimos[1];
        $detailsdata["article_id"] = $arsimos[2];
    }

    /**
     * Record verwijderen
     */
    if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
        if (isset($_POST["confirmed"])) {
            db_delete($table, $_POST["id"]);
            redirect($pi["filename_list"]);
        } elseif (!isset($_POST["abort"])) {
            $pi["note"] = html_delete($_POST["id"], $lang["rule"]);
        }
    }
    $urlinfo["id"] = $detailsdata["id"];
}

/** Required for selectbox: distributor_id **/
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations_data = db_read("distributorlocations", "id name", $distributorlocations_conditions);
if (!empty($distributorlocations_data)) {
    while ($row = db_fetch_num($distributorlocations_data)) {
        $distributorlocations[$row[0]] = $row[1];
    }
} else {
    $distributorlocations = null;
}

/** Required for selectbox: articles **/
$articles_conditions["order_by"] = "description";
$articles_data = db_read("articles", "id articlenumber description", $articles_conditions);
if (!empty($articles_data)) {
    while ($row = db_fetch_num($articles_data)) {
        $articles[$row[0]] = ((!empty($row[1])) ? "(" . $row[1] . ") " : "") . $row[2];
    }
} else {
    $articles = null;
}

/** Required for selectbox: sizes **/
if (!empty($detailsdata["article_id"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $detailsdata["article_id"];
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

/** Required for selectboxes: modifications **/
if (!empty($detailsdata["article_id"]) && !empty($bindingdata["size_id"])) {
    $modifications_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
    $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $detailsdata["article_id"];
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

if (!empty($requiredfields)) {
    $pi["note"] = html_requiredfields($requiredfields);
} elseif (!empty($existence_note)) {
    $pi["note"] = html_requirednote($existence_note);
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
    "detailsdata" => $detailsdata,
    "tag_error" => $tag_error
);

template_parse($pi, $urlinfo, $cv);

?>
