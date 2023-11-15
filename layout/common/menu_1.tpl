<div id="menu">

    <? if (!empty($_SESSION["user_privileges"]["common"])): ?>
    <h3><a href="#"><?=$lang["common"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["common"]["departments"])): ?>
                <li<? if ($pi["filename_list"] == "clientdepartments.php"): ?> class="current"<? endif ?>><a href="clientdepartments.php"><?=$lang["clientdepartments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["costplaces"])): ?>
                <li<? if ($pi["filename_list"] == "costplaces.php"): ?> class="current"<? endif ?>><a href="costplaces.php"><?=$lang["costplaces"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["functions"])): ?>
                <li<? if ($pi["filename_list"] == "functions.php"): ?> class="current"<? endif ?>><a href="functions.php"><?=$lang["functions"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["garmentusers"])): ?>
                <li<? if ($pi["filename_list"] == "garmentusers.php"): ?> class="current"<? endif ?>><a href="garmentusers.php"><?=$lang["garmentusers"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["garments"])): ?>
                <li<? if ($pi["filename_list"] == "garments.php"): ?> class="current"<? endif ?>><a href="garments.php"><?=$lang["garments"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["reports"])): ?>
    <h3><a href="#"><?=$lang["reports"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["reports"]["total_report"])): ?>
                <li<? if ($pi["filename_list"] == "report_totals.php"): ?> class="current"<? endif ?>><a href="report_totals.php"><?=$lang["total_report"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["garments_in_use"])): ?>
                <li<? if ($pi["filename_list"] == "report_garments_in_use.php"): ?> class="current"<? endif ?>><a href="report_garments_in_use.php"><?=$lang["garments_in_use"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["distribution_by_date"])): ?>
                <li<? if ($pi["filename_list"] == "report_distribution_by_date.php"): ?> class="current"<? endif ?>><a href="report_distribution_by_date.php"><?=$lang["distribution_by_date"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["current_load"])): ?>
                <li<? if ($pi["filename_list"] == "report_current_load.php"): ?> class="current"<? endif ?>><a href="report_current_load.php"><?=$lang["current_load"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["misseized_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_misseized_by_date.php"): ?> class="current"<? endif ?>><a href="report_misseized_by_date.php"><?=$lang["misseized_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["rejected_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_rejected_garments.php"): ?> class="current"<? endif ?>><a href="report_rejected_garments.php"><?=$lang["rejected_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["deposited_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_deposits_by_date.php"): ?> class="current"<? endif ?>><a href="report_deposits_by_date.php"><?=$lang["deposited_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["userbound"])): ?>
                <li<? if ($pi["filename_list"] == "report_carrierbound.php"): ?> class="current"<? endif ?>><a href="report_carrierbound.php"><?=$lang["userbound"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["garmentusers_per_arsimo"])): ?>
                <li<? if ($pi["filename_list"] == "report_garmentusers_per_arsimo.php"): ?> class="current"<? endif ?>><a href="report_garmentusers_per_arsimo.php"><?=$lang["garmentusers_per_arsimo"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["arsimos_per_garmentuser"])): ?>
                <li<? if ($pi["filename_list"] == "report_arsimos_per_garmentuser.php"): ?> class="current"<? endif ?>><a href="report_arsimos_per_garmentuser.php"><?=$lang["arsimos_per_garmentuser"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["machines_load"])): ?>
                <li<? if ($pi["filename_list"] == "report_machines_load.php"): ?> class="current"<? endif ?>><a href="report_machines_load.php"><?=$lang["machines_load"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["disconnected_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_disconnected_garments.php"): ?> class="current"<? endif ?>><a href="report_disconnected_garments.php"><?=$lang["disconnected_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["packinglists"])): ?>
                <li<? if ($pi["filename_list"] == "report_packinglists.php"): ?> class="current"<? endif ?>><a href="report_packinglists.php"><?=$lang["packinglists"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["manually"])): ?>
    <h3><a href="#"><?=$lang["manually"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["manually"]["distribution"])): ?>
                <li<? if ($pi["filename_list"] == "manual_distribution.php"): ?> class="current"<? endif ?>><a href="manual_distribution.php"><?=$lang["distribution"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["manually"]["deposit"])): ?>
                <li<? if ($pi["filename_list"] == "manual_deposit.php"): ?> class="current"<? endif ?>><a href="manual_deposit.php"><?=$lang["deposit"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["circulation_management"])): ?>
    <h3><a href="#"><?=$lang["circulation_management"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["circulationadvice_old"])): ?>
                <li<? if ($pi["filename_list"] == "report_circulationadvice_old.php"): ?> class="current"<? endif ?>><a href="report_circulationadvice_old.php"><?=$lang["circulationadvice_old"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["circulationadvice"])): ?>
                <li<? if ($pi["filename_list"] == "report_circulationadvice.php"): ?> class="current"<? endif ?>><a href="report_circulationadvice.php"><?=$lang["circulationadvice"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["beyond_circulation"])): ?>
                <li<? if ($pi["filename_list"] == "report_beyond_circulation.php"): ?> class="current"<? endif ?>><a href="report_beyond_circulation.php"><?=$lang["beyond_circulation"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["in_circulation"])): ?>
                <li<? if ($pi["filename_list"] == "report_in_circulation.php"): ?> class="current"<? endif ?>><a href="report_in_circulation.php"><?=$lang["in_circulation"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["throw_off_garments"])): ?>
                <li<? if ($pi["filename_list"] == "throw_off.php"): ?> class="current"<? endif ?>><a href="throw_off.php"><?=$lang["throw_off_garments"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["linen_service"])): ?>
    <h3><a href="#"><?=$lang["linen_service"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["repairs"])): ?>
                <li<? if ($pi["filename_list"] == "garmentrepairs.php" || $pi["filename_list"] == "repairs.php"): ?> class="current"<? endif ?>><a href="garmentrepairs.php"><?=$lang["repairs"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["despeckles"])): ?>
                <li<? if ($pi["filename_list"] == "garmentdespeckles.php" || $pi["filename_list"] == "despeckles.php"): ?> class="current"<? endif ?>><a href="garmentdespeckles.php"><?=$lang["despeckles"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["tag_replacements"])): ?>
                <li<? if ($pi["filename_list"] == "garmenttagreplacements.php"): ?> class="current"<? endif ?>><a href="garmenttagreplacements.php"><?=$lang["tag_replacements"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["extra_load"])): ?>
                <li<? if ($pi["filename_list"] == "extraload.php"): ?> class="current"<? endif ?>><a href="extraload.php"><?=$lang["extra_load"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["washcount_garments"])): ?>
                <li<? if ($pi["filename_list"] == "garmentwashcount.php"): ?> class="current"<? endif ?>><a href="garmentwashcount.php"><?=$lang["washcount_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["errormessages"])): ?>
                <li<? if ($pi["filename_list"] == "errormessages.php"): ?> class="current"<? endif ?>><a href="errormessages.php"><?=$lang["failures"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["clear_depositlocation"])): ?>
                <li<? if ($pi["filename_list"] == "clear_depositlocation.php"): ?> class="current"<? endif ?>><a href="clear_depositlocation.php"><?=$lang["clear_depositlocation"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["packinglist_generate"])): ?>
                <li<? if ($pi["filename_list"] == "packinglist_generate.php"): ?> class="current"<? endif ?>><a href="packinglist_generate.php"><?=$lang["packinglist_generate"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["disconnect_garmentusers"])): ?>
                <li<? if ($pi["filename_list"] == "disconnect_garmentusers.php"): ?> class="current"<? endif ?>><a href="disconnect_garmentusers.php"><?=$lang["disconnect_garmentusers"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["master_data"])): ?>
    <h3><a href="#"><?=$lang["master_data"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["sizes"])): ?>
                <li<? if ($pi["filename_list"] == "sizes.php"): ?> class="current"<? endif ?>><a href="sizes.php"><?=$lang["sizes"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["articles"])): ?>
                <li<? if ($pi["filename_list"] == "articles.php"): ?> class="current"<? endif ?>><a href="articles.php"><?=$lang["articles"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["professions"])): ?>
                <li<? if ($pi["filename_list"] == "professions.php"): ?> class="current"<? endif ?>><a href="professions.php"><?=$lang["professions"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["garmentmodifications"])): ?>
                <li<? if ($pi["filename_list"] == "garmentmodifications.php"): ?> class="current"<? endif ?>><a href="garmentmodifications.php"><?=$lang["garmentmodifications"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["garmentmanagers"])): ?>
                <li<? if ($pi["filename_list"] == "garmentmanagers.php"): ?> class="current"<? endif ?>><a href="garmentmanagers.php"><?=$lang["garmentmanagers"]?></a></li>
            <? endif ?>
        <ul>
    </div>
    <? endif ?>
    
    <? if (!empty($_SESSION["user_privileges"]["technix_gsx"])): ?>
    <h3><a href="#"><?=$lang["technix_gsx"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["technix_gsx"]["articles_description_short"])): ?>
                <li<? if ($pi["filename_list"] == "articles_description_short.php"): ?> class="current"<? endif ?>><a href="articles_description_short.php"><?=$lang["articles_description"]?></a></li>
            <? endif ?>
             <? if (isset($_SESSION["user_privileges"]["technix_gsx"]["sizes_description_short"])): ?>
                <li<? if ($pi["filename_list"] == "sizes_description_short.php"): ?> class="current"<? endif ?>><a href="sizes_description_short.php"><?=$lang["sizes_description"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["technix_gsx"]["station_cells"])): ?>
                <li<? if ($pi["filename_list"] == "station_cells.php"): ?> class="current"<? endif ?>><a href="station_cells.php"><?=$lang["station_cells"]?></a></li>
            <? endif ?>
        <ul>
    </div>
    <? endif ?>

    <? if ($_SESSION["username"] === "Technico"): ?>
    <h3><a href="#"><?=$lang["technico"]?></a></h3>
    <div>
        <ul>
            <li<? if ($pi["filename_list"] == "client.php"): ?> class="current"<? endif ?>><a href="client.php"><?=$lang["client"]?></a></li>
            <li<? if ($pi["filename_list"] == "clients.php"): ?> class="current"<? endif ?>><a href="clients.php"><?=$lang["locations"]?></a></li>
            <li<? if ($pi["filename_list"] == "users.php"): ?> class="current"<? endif ?>><a href="users.php"><?=$lang["users"]?></a></li>
            <li<? if ($pi["filename_list"] == "report_mupapu.php" || $pi["filename_list"] == "report_mupapu_mud.php" || $pi["filename_list"] == "report_mupapu_mud_details.php"): ?> class="current"<? endif ?>><a href="report_mupapu.php">MUPAPU</a></li>
            <li<? if ($pi["filename_list"] == "alternative_sizes.php"): ?> class="current"<? endif ?>><a href="alternative_sizes.php"><?=$lang["alternative_sizes"]?></a></li>
            <!--<li<? if ($pi["filename_list"] == "logs.php"): ?> class="current"<? endif ?>><a href="logs.php"><?=$lang["logs"]?></a></li> -->
            <li<? if ($pi["filename_list"] == "carrierbound.php"): ?> class="current"<? endif ?>><a href="carrierbound.php"><?=$lang["userbound"]?></a></li>
            <li<? if ($pi["filename_list"] == "garmentuser_manual_import.php"): ?> class="current"<? endif ?>><a href="garmentuser_manual_import.php"><?=$lang["garmentuser_manual_import"]?></a></li>
            <li<? if ($pi["filename_list"] == "exportcodes.php"): ?> class="current"<? endif ?>><a href="exportcodes.php"><?=$lang["exportcodes"]?></a></li>
            <li<? if ($pi["filename_list"] == "emailsettings.php"): ?> class="current"<? endif ?>><a href="emailsettings.php"><?=$lang["email_settings"]?></a></li>
            <li<? if ($pi["filename_list"] == "emailaddresses.php"): ?> class="current"<? endif ?>><a href="emailaddresses.php"><?=$lang["email_addresses"]?></a></li>
            <!--<li<? if ($pi["filename_list"] == "management_info.php"): ?> class="current"<? endif ?>><a href="management_info.php">Edwin test</a></li>-->
            <li<? if ($pi["filename_list"] == "monitor-hard.php"): ?> class="current"<? endif ?>><a href="monitor-hard.php"><?=$lang["hardware_monitor"]?></a></li>
            <li<? if ($pi["filename_list"] == "distributorlocations.php"): ?> class="current"<? endif ?>><a href="distributorlocations.php"><?=$lang["distributorlocations"]?></a></li>
            <li<? if ($pi["filename_list"] == "distributors.php"): ?> class="current"<? endif ?>><a href="distributors.php"><?=$lang["distributors"]?></a></li>
        </ul>
    </div>
    <? endif ?>
</div>