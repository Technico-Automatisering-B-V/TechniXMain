<?php

// Required for selectboxes
$creditoptions["professiondefault"] = $lang["default"];
$creditoptions["owncredit"] = $lang["own_credit"];

$timelockoptions["professiondefault"] = $lang["default"];
$timelockoptions["owntimelock"] = $lang["own_timelock"];

$blockageoptions["professiondefault"] = $lang["default"];
$blockageoptions["ownblockage"] = $lang["own_blockage"];

$warningoptions["professiondefault"] = $lang["default"];
$warningoptions["ownwarning"] = $lang["own_warning"];

// KOPPELEN
if ($garmentlink["enabled"] == "1" && $page !== "add") {

    // Required for selectbox: all articles related by profession (thru garmentprofiles)
    if (!empty($garmentlink["article_id"])){
        $garmentlink_articles_columns = "articles.id articles.description";
        $garmentlink_articles_conditions["where"]["1"] = "articles.id = " . $garmentlink["article_id"];
        $garmentlink_articles_resource = db_read("articles", $garmentlink_articles_columns, $garmentlink_articles_conditions);
        while ($row = db_fetch_num($garmentlink_articles_resource)) {
            $garmentlink_articles[$row[0]] = $row[1];
        }
    }

    // Required for selectbox: sizes
    if (!empty($garmentlink["article_id"])) {
        $garmentlink_sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
        $garmentlink_sizes_conditions["where"]["1"] = "arsimos.article_id = " . $garmentlink["article_id"];
        $garmentlink_sizes_conditions["order_by"] = "sizes.position";
        $garmentlink_sizes_conditions["group_by"] = "arsimos.size_id";
        $garmentlink_sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $garmentlink_sizes_conditions);
        if (!empty($garmentlink_sizes_data)) {
            while ($row = db_fetch_num($garmentlink_sizes_data)) {
                $garmentlink_sizes[$row[0]] = $row[1];
            }
        } else {
            $garmentlink_sizes = null;
        }
    } else {
        $garmentlink_sizes = null;
    }

	// Required for selectboxes: modifications
	if (!empty($garmentlink["article_id"]) && !empty($garmentlink["size_id"])) {
            $garmentlink_modifications_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
            $garmentlink_modifications_conditions["where"]["1"] = "arsimos.article_id = " . $garmentlink["article_id"];
            $garmentlink_modifications_conditions["where"]["2"] = "arsimos.size_id = " . $garmentlink["size_id"];
            $garmentlink_modifications_conditions["where"]["3"] = "arsimos.modification_id isnot NULL";
            $garmentlink_modifications_data = db_read("arsimos", "arsimos.modification_id modifications.name", $garmentlink_modifications_conditions);
            if (!empty($garmentlink_modifications_data)) {
                while ($row = db_fetch_num($garmentlink_modifications_data)) {
                    $garmentlink_modifications_all[$row[0]] = $row[1];
                }
                $showempty_mod_sql = "SELECT * FROM arsimos WHERE arsimos.article_id = ". $garmentlink["article_id"] ." AND arsimos.size_id = ". $garmentlink["size_id"] ." AND arsimos.modification_id IS NULL";
                $showempty_mod_query = db_query($showempty_mod_sql);
                if (db_num_rows($showempty_mod_query) == 0){
                    $showempty_mod = false;
                }else{
                    $showempty_mod = true;
                }
            } else {
                $garmentlink_modifications_all = null;
            }
	} else {
            $garmentlink_modifications_all = null;
        }

}

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles_data = db_read("articles", "id articlenumber description", $articles_conditions);
if (!empty($articles_data)) {
    while ($row = db_fetch_num($articles_data)) {
        $articles[$row[0]] = ((!empty($row[1])) ? "(" . $row[1] . ") " : "") . $row[2];
    }
} else {
    $articles = null;
}

?>
