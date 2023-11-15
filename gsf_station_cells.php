<?php

/**
 * Station cells
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';

/**
 * Page settings
 */
$pi["access"] = array("technix_gsf", "station_cells");
$pi["group"] = $lang["technix_gsf"];
$pi["title"] = $lang["station_cells"];
$pi["filename_this"] = "gsf_station_cells.php";
$pi["filename_list"] = "gsf_station_cells.php";
$pi["template"] = "layout/pages/gsf_station_cells.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * When form is submitted
 */
if (isset($_POST["submit"])) {
    foreach ($_POST["position"] as $p => $arsimo_id) {
        if(!empty($arsimo_id)) {
            $max_loaded = (!empty($_POST["max_loaded"][$p])) ? $_POST["max_loaded"][$p] : 10;
            db_query("UPDATE `gsf_distributors_load` SET arsimo_id = " . $arsimo_id . ", max_loaded = " . $max_loaded . "  WHERE position = ". $p ." AND `distributor_id` = ". $_POST["distributor_id"]);
        } else {
            db_query("UPDATE `gsf_distributors_load` SET arsimo_id = 0, max_loaded = 0, loaded = 0,   WHERE position = ". $p ." AND `distributor_id` = ". $_POST["distributor_id"]);
        }
    }
}


/**
 * Collect page content
 */
$urlinfo = array();

$distributorlocations_sql = "SELECT `d`.`id`, dl.name, d.doornumber, c.id AS 'cid' FROM distributors d
                INNER JOIN `distributorlocations` dl ON dl.id = d.distributorlocation_id
                INNER JOIN circulationgroups c ON c.id = dl.circulationgroup_id
                WHERE c.type = 'GSF'";
$distributorlocations = db_query($distributorlocations_sql);

/**
 * Generate the page
 */
$cv = array(
	"pi" => $pi,
        "distributorlocations" => $distributorlocations
);

template_parse($pi, $urlinfo, $cv);

?>
