<?php

/**
 * Carrierbound
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("reports", "userbound");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["title_userbound"];
$pi["filename_list"] = "report_carrierbound.php";
$pi["filename_details"] = "report_carrierbound.php";
$pi["template"] = "layout/pages/report_carrierbound.tpl";
$pi["page"] = "simple";
$urlinfo = "";

$tablerows = "";
$forms = "";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Used variables
 */
$distributors = array();

/**
 * Collect page content
 */
$circulationgroup_id = (!empty($_GET["circulationgroup_id"])) ? trim($_GET["circulationgroup_id"]) : null;
$emplocle = (!empty($_GET["emplocle"])) ? trim($_GET["emplocle"]) : "employees";
$status = (!empty($_GET["status"])) ? trim($_GET["status"]) : "active";
$distributor_id = (!empty($_GET["distributor_id"])) ? trim($_GET["distributor_id"]) : null;

/** required for selectbox: circulationgroups **/
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

/** Required for selectbox: Type **/
$emplocles["employees"] = ucfirst($lang["employees"]);
$emplocles["articles"] = ucfirst($lang["garments"]);

/** Required for selectbox: Status **/
$statuses["active"] = $lang["active"];
$statuses["deleted"] = $lang["deleted"];

$result_count = 0;

if ($circulationgroup_count == 1) {
    $circulationgroup_id = 1;
}

if (!empty($circulationgroup_id)) {
    // Required for radio: stations
    $free_per_station_sql = db_query("SELECT distributors.id, distributors.doornumber FROM distributors INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id WHERE distributorlocations.circulationgroup_id = ". $circulationgroup_id) or die("ERROR LINE ". __LINE__);
    while ($free_per_station_data = db_fetch_row($free_per_station_sql)) {
        $distributors[$free_per_station_data[0]] = $lang["station"] ." ". $free_per_station_data[1];
    }

    if (isset($distributor_id)) {
        // Medewerkers
        if ($emplocle == "employees") {
            if ($status == "deleted") {
                $delWhere = "AND garmentusers.deleted_on IS NOT NULL";
            } else {
                $delWhere = "AND garmentusers.deleted_on IS NULL";
            }

            $users_sql = db_query("
                SELECT
                distributors.doornumber AS 'station',
                garmentusers.title AS 'title',
                garmentusers.gender AS 'gender',
                garmentusers.initials AS 'initials',
                garmentusers.intermediate AS 'intermediate',
                garmentusers.surname AS 'surname',
                garmentusers.maidenname AS 'maidenname',
                garmentusers.personnelcode AS 'personnelcode',
                garmentusers.id AS 'user_id',
                garmentusers.deleted_on,
                articles.description,
                CONCAT(sizes.name,' ', COALESCE(modifications.`name`,'')) 'size',
                SUM(COALESCE(positions_reserved.max_positions,0)) AS 'max_positions',
                SUM(COALESCE(`garments_in_use`.count_inuse,0)) AS 'count_inuse'
                FROM
                distributors
                INNER JOIN garmentusers ON distributors.id = garmentusers.distributor_id
                        OR distributors.id = garmentusers.distributor_id2
                        OR distributors.id = garmentusers.distributor_id3
                        OR distributors.id = garmentusers.distributor_id4
                        OR distributors.id = garmentusers.distributor_id5
                        OR distributors.id = garmentusers.distributor_id6
                        OR distributors.id = garmentusers.distributor_id7
                        OR distributors.id = garmentusers.distributor_id8
                        OR distributors.id = garmentusers.distributor_id9
                        OR distributors.id = garmentusers.distributor_id10
                INNER JOIN (
                    SELECT DISTINCT
                    distributors.id AS 'distributor_id',
                    garmentusers.id AS 'garmentuser_id',
                    arsimos.id AS 'arsimo_id'
                    FROM garmentusers
                    INNER JOIN distributors ON distributors.id = garmentusers.distributor_id
                            OR distributors.id = garmentusers.distributor_id2
                            OR distributors.id = garmentusers.distributor_id3
                            OR distributors.id = garmentusers.distributor_id4
                            OR distributors.id = garmentusers.distributor_id5
                            OR distributors.id = garmentusers.distributor_id6
                            OR distributors.id = garmentusers.distributor_id7
                            OR distributors.id = garmentusers.distributor_id8
                            OR distributors.id = garmentusers.distributor_id9
                            OR distributors.id = garmentusers.distributor_id10
                    INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                    INNER JOIN garmentusers_userbound_arsimos ON garmentusers_userbound_arsimos.garmentuser_id = garmentusers.id AND garmentusers_userbound_arsimos.circulationgroup_id = distributorlocations.circulationgroup_id
                    INNER JOIN arsimos ON garmentusers_userbound_arsimos .arsimo_id = arsimos.id
                    UNION
                    SELECT DISTINCT
                    distributors.id AS 'distributor_id',
                    garmentusers.id AS 'garmentuser_id',
                    arsimos.id AS 'arsimo_id'
                    FROM distributors_load
                    INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
                    INNER JOIN garmentusers ON garmentusers.distributor_id = distributors.id
                            OR garmentusers.distributor_id2 = distributors.id
                            OR garmentusers.distributor_id3 = distributors.id
                            OR garmentusers.distributor_id4 = distributors.id
                            OR garmentusers.distributor_id5 = distributors.id
                            OR garmentusers.distributor_id6 = distributors.id
                            OR garmentusers.distributor_id7 = distributors.id
                            OR garmentusers.distributor_id8 = distributors.id
                            OR garmentusers.distributor_id9 = distributors.id
                            OR garmentusers.distributor_id10 = distributors.id
                    INNER JOIN garments ON distributors_load.garment_id = garments.id AND garments.garmentuser_id = garmentusers.id
                    INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
                ) `arsimos_in_use` ON `arsimos_in_use`.distributor_id = distributors.id AND `arsimos_in_use`.garmentuser_id = garmentusers.id
                INNER JOIN arsimos ON `arsimos_in_use`.arsimo_id = arsimos.id
                INNER JOIN articles ON arsimos.article_id = articles.id
                INNER JOIN sizes ON arsimos.size_id = sizes.id
                LEFT JOIN modifications ON arsimos.modification_id = modifications.id
                LEFT JOIN (
                    SELECT
                    distributors.id AS 'distributor_id',
                    garmentusers.id AS 'garmentuser_id',
                    arsimos.id AS 'arsimo_id',
                    SUM(garmentusers_userbound_arsimos.max_positions) AS 'max_positions'
                    FROM garmentusers
                    INNER JOIN distributors ON distributors.id = garmentusers.distributor_id
                            OR distributors.id = garmentusers.distributor_id2
                            OR distributors.id = garmentusers.distributor_id3
                            OR distributors.id = garmentusers.distributor_id4
                            OR distributors.id = garmentusers.distributor_id5
                            OR distributors.id = garmentusers.distributor_id6
                            OR distributors.id = garmentusers.distributor_id7
                            OR distributors.id = garmentusers.distributor_id8
                            OR distributors.id = garmentusers.distributor_id9
                            OR distributors.id = garmentusers.distributor_id10
                    INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                    INNER JOIN garmentusers_userbound_arsimos ON garmentusers_userbound_arsimos.garmentuser_id = garmentusers.id AND garmentusers_userbound_arsimos.circulationgroup_id = distributorlocations.circulationgroup_id
                    INNER JOIN arsimos ON garmentusers_userbound_arsimos .arsimo_id = arsimos.id
                    GROUP BY distributors.id, garmentusers.id, arsimo_id
                ) `positions_reserved` ON `positions_reserved`. distributor_id = distributors.id AND `positions_reserved`.garmentuser_id = garmentusers.id AND `positions_reserved`.arsimo_id = arsimos.id
                LEFT JOIN (
                    SELECT
                    distributors.id AS 'distributor_id',
                    garmentusers.id AS 'garmentuser_id',
                    arsimos.id AS 'arsimo_id',
                    COUNT(garments.id) AS 'count_inuse'
                    FROM distributors_load
                    INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
                    INNER JOIN garmentusers ON garmentusers.distributor_id = distributors.id
                            OR garmentusers.distributor_id2 = distributors.id
                            OR garmentusers.distributor_id3 = distributors.id
                            OR garmentusers.distributor_id4 = distributors.id
                            OR garmentusers.distributor_id5 = distributors.id
                            OR garmentusers.distributor_id6 = distributors.id
                            OR garmentusers.distributor_id7 = distributors.id
                            OR garmentusers.distributor_id8 = distributors.id
                            OR garmentusers.distributor_id9 = distributors.id
                            OR garmentusers.distributor_id10 = distributors.id
                    INNER JOIN garments ON distributors_load.garment_id = garments.id AND garments.garmentuser_id = garmentusers.id
                    INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
                    GROUP BY distributors.id, garmentusers.id, arsimos.id
                ) `garments_in_use` ON `garments_in_use`. distributor_id = distributors.id AND `garments_in_use`.garmentuser_id = garmentusers.id AND `garments_in_use`.arsimo_id = arsimos.id
                WHERE distributors.id = ". $distributor_id ." ". $delWhere ."
                GROUP BY distributors.id, garmentusers.id
                ORDER BY distributors.doornumber, garmentusers.surname, garmentusers.personnelcode, articles.description, sizes.position, modifications.`name`");

            $result_count = db_num_rows($users_sql);

            if ($result_count == 0) {
                $pi["note"] = $lang["no_items_found"];
            } elseif (isset($_GET["exportreport"])) {

                $export_filename = "excel_export_". date("Y-m-d_His") .".xls";

                $header = $lang["station"]."\t".$lang["garmentuser"]."\t".$lang["personnelcode"]."\t".$lang["number_carriers_posible"]."\t".$lang["number_carriers_taken"]."\t".$lang["deleted"]."\t";
                $data = "";
                while ($users_data = db_fetch_assoc($users_sql)) {
                    $line = "";
                    $in = array(
                        $users_data["station"],
                        generate_garmentuser_label($users_data["title"], $users_data["gender"], $users_data["initials"], $users_data["intermediate"], $users_data["surname"], $users_data["maidenname"]),
                        $users_data["personnelcode"],
                        $users_data["max_positions"],
                        $users_data["count_inuse"],
                        ((empty($users_data["deleted_on"])) ? "-" : "V")
                    );

                    foreach ($in as $value) {
                        if (!isset($value) || $value == "") {
                                $value = "\t";
                        } else {
                                $value = str_replace('"', '""', $value);
                                $value = '"' . $value . '"' . "\t";
                        }
                        $line .= $value;
                    }
                    $data .= trim($line)."\n";
                }
                $data = str_replace("\r","",$data);

                header("Content-type: application/vnd-ms-excel");
                header("Content-Disposition: attachment; filename=". $export_filename);
                header("Pragma: no-cache");
                header("Expires: 0");

                print "$header\n$data";
                die();

            } else {
                while ($users_data = db_fetch_assoc($users_sql)) {
                    $forms .= "<form id=\"". $users_data["user_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garmentuser_details.php\">
                                    <input type=\"hidden\" name=\"page\" value=\"details\">
                                    <input type=\"hidden\" name=\"id\" value=\"". $users_data["user_id"] ."\">
                                    <input type=\"hidden\" name=\"gosubmit\" value=\"false\">
                                </form>";

                    $tablerows .= "<tr class=\"list\" onClick=\"document.getElementById('". $users_data["user_id"] ."').submit();\">
                                        <td class=\"list\" align=\"center\">". $users_data["station"] ."</td>
                                        <td class=\"list\">". generate_garmentuser_label($users_data["title"], $users_data["gender"], $users_data["initials"], $users_data["intermediate"], $users_data["surname"], $users_data["maidenname"]) ."</td>
                                        <td class=\"list\">". $users_data["personnelcode"] ."</td>
                                        <td class=\"list\" align=\"center\">". $users_data["max_positions"] ."</td><td class=\"list\" align=\"center\">". $users_data["count_inuse"] ."</td>
                                        <td class=\"list\" align=\"center\">". ((empty($users_data["deleted_on"])) ? "<span class=\"empty\">-</span>" : "<strong>V</strong>") ."</td>
                                    </tr>";
                }
            }
        }

        // Kledingstukken
        if ($emplocle == "articles") {
            if ($status == "deleted") {
                $delWhere = "AND garments.deleted_on IS NOT NULL";
            } else {
                $delWhere = "AND garments.deleted_on IS NULL";
            }

            $articles_sql = db_query("SELECT
            distributors.doornumber AS 'station',
            distributors_load.hook AS 'position',
            garmentusers.id AS 'user_id',
            garmentusers.surname,
            garmentusers.personnelcode,
            garmentusers.gender,
            garmentusers.initials,
            garmentusers.intermediate,
            garmentusers.maidenname,
            garmentusers.title,
            articles.description,
            garments.id AS 'garment_id',
            garments.circulationgroup_id AS 'garment_cid',
            garments.deleted_on,
            CONCAT(sizes.`name`,' ', COALESCE(modifications.`name`,'')) 'size', garments.tag
            FROM
            distributorlocations
            INNER JOIN distributors ON distributors.distributorlocation_id = distributorlocations.id
            INNER JOIN distributors_load ON distributors_load.distributor_id = distributors.id
            INNER JOIN garmentusers ON garmentusers.distributor_id = distributors.id
                    OR garmentusers.distributor_id2 = distributors.id
                    OR garmentusers.distributor_id3 = distributors.id
                    OR garmentusers.distributor_id4 = distributors.id
                    OR garmentusers.distributor_id5 = distributors.id
                    OR garmentusers.distributor_id6 = distributors.id
                    OR garmentusers.distributor_id7 = distributors.id
                    OR garmentusers.distributor_id8 = distributors.id
                    OR garmentusers.distributor_id9 = distributors.id
                    OR garmentusers.distributor_id10 = distributors.id
            INNER JOIN garments ON distributors_load.garment_id = garments.id AND garments.garmentuser_id = garmentusers.id
            INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
            INNER JOIN articles ON arsimos.article_id = articles.id
            INNER JOIN sizes ON arsimos.size_id = sizes.id
            LEFT JOIN modifications ON arsimos.modification_id = modifications.id
            WHERE distributors.id = ". $distributor_id ." ". $delWhere ."
            ORDER BY distributors.id, garmentusers.surname, garmentusers.personnelcode, articles.description, sizes.position, modifications.name, garments.tag, garments.deleted_on");

            $result_count = db_num_rows($articles_sql);

            if ($result_count == 0) {
                $pi["note"] = $lang["no_items_found"];
            } elseif (isset($_GET["exportreport"])) {

                $export_filename = "excel_export_". date("Y-m-d_His") .".xls";

                $header = $lang["station"]."\t".$lang["position"]."\t".$lang["garmentuser"]."\t".$lang["personnelcode"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["tag"]."\t".$lang["deleted"]."\t";
                $data = "";
                while ($articles_data = db_fetch_assoc($articles_sql)) {
                    $line = "";
                    $in = array(
                        $articles_data["station"],
                        $articles_data["position"],
                        generate_garmentuser_label($articles_data["title"], $articles_data["gender"], $articles_data["initials"], $articles_data["intermediate"], $articles_data["surname"], $articles_data["maidenname"]),
                        $articles_data["personnelcode"],
                        $articles_data["description"],
                        $articles_data["size"],
                        $articles_data["tag"],
                        ((empty($articles_data["deleted_on"])) ? "-" : "V")
                    );

                    foreach ($in as $value) {
                        if (!isset($value) || $value == "") {
                                $value = "\t";
                        } else {
                                $value = str_replace('"', '""', $value);
                                $value = '"' . $value . '"' . "\t";
                        }
                        $line .= $value;
                    }
                    $data .= trim($line)."\n";
                }
                $data = str_replace("\r","",$data);

                header("Content-type: application/vnd-ms-excel");
                header("Content-Disposition: attachment; filename=". $export_filename);
                header("Pragma: no-cache");
                header("Expires: 0");

                print "$header\n$data";
                die();

            } else {
                while ($articles_data = db_fetch_assoc($articles_sql)) {
                    $forms .= "<form id=\"g". $articles_data["garment_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\">
                                    <input type=\"hidden\" name=\"page\" value=\"details\">
                                    <input type=\"hidden\" name=\"cid\" value=\"". $articles_data["garment_cid"] ."\">
                                    <input type=\"hidden\" name=\"id\" value=\"". $articles_data["garment_id"] ."\">
                                    <input type=\"hidden\" name=\"gosubmit\" value=\"true\">
                                </form>";
                    $forms .= "<form id=\"gu". $articles_data["user_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garmentuser_details.php\">
                                    <input type=\"hidden\" name=\"page\" value=\"details\">
                                    <input type=\"hidden\" name=\"id\" value=\"". $articles_data["user_id"] ."\">
                                    <input type=\"hidden\" name=\"gosubmit\" value=\"true\">
                                </form>";

                    $tablerows .= "<tr class=\"listnc\">
                                        <td class=\"midlist\">". $articles_data["station"] ."</td>
                                        <td class=\"midlist\">". $articles_data["position"] ."</td>
                                        <td class=\"list lpointer\" onClick=\"document.getElementById('gu". $articles_data["user_id"] ."').submit();\">". generate_garmentuser_label($articles_data["title"], $articles_data["gender"], $articles_data["initials"], $articles_data["intermediate"], $articles_data["surname"], $articles_data["maidenname"]) ."</td>
                                        <td class=\"list lpointer\" onClick=\"document.getElementById('gu". $articles_data["user_id"] ."').submit();\">". $articles_data["personnelcode"] ."</td>
                                        <td class=\"list\">". $articles_data["description"] ."</td>
                                        <td class=\"midlist\">". $articles_data["size"] ."</td>
                                        <td class=\"list lpointer\" onClick=\"document.getElementById('g". $articles_data["garment_id"] ."').submit();\">". $articles_data["tag"] ."</td>
                                        <td class=\"list\" align=\"center\">". ((empty($articles_data["deleted_on"])) ? "<span class=\"empty\">-</span>" : "<strong>V</strong>") ."</td>
                                    </tr>";
                }
            }
        }
    }
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "circulationgroups" => $circulationgroups,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroup_id" => $circulationgroup_id,
    "distributor_id" => $distributor_id,
    "distributors" => $distributors,
    "forms" => $forms,
    "tablerows" => $tablerows,
    "emplocle" => $emplocle,
    "emplocles" => $emplocles,
    "status" => $status,
    "statuses" => $statuses,
    "result_count" => $result_count
);

template_parse($pi, $urlinfo, $cv);

?>
