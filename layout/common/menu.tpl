<div id="menu">

    <? if (!empty($_SESSION["user_privileges"]["common"])): ?>
    <h3><a href="#"><?=$lang["common"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["common"]["dashboard"])): ?>
                <li<? if ($pi["filename_list"] == "dashboard.php"): ?> class="current"<? endif ?>><a href="dashboard.php"><?=$lang["dashboard"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["garmentusers"])): ?>
                <li<? if ($pi["filename_list"] == "garmentusers.php"): ?> class="current"<? endif ?>><a href="garmentusers.php"><?=$lang["garmentusers"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["common"]["garments"])): ?>
                <li<? if ($pi["filename_list"] == "garments.php"): ?> class="current"<? endif ?>><a href="garments.php"><?=$lang["garments"]?></a></li>
            <? endif ?>
			<!--<li<? if ($pi["filename_list"] == "extra_garment_tag.php"): ?> class="current"<? endif ?>><a href="extra_garment_tag.php">Chipcode toevoegen</a></li> -->
        </ul>
    </div>
    <? endif ?>
    
    <? if (!empty($_SESSION["user_privileges"]["load"])): ?>
    <h3><a href="#"><?=$lang["load"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["load"]["current_sizebound_load"])): ?>
                <li<? if ($pi["filename_list"] == "report_current_load.php"): ?> class="current"<? endif ?>><a href="report_current_load.php"><?=$lang["current_sizebound_load"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["load"]["current_userbound_load"])): ?>
                <li<? if ($pi["filename_list"] == "report_current_userbound_load.php"): ?> class="current"<? endif ?>><a href="report_current_userbound_load.php"><?=$lang["current_userbound_load"]?></a></li>
            <? endif ?>            
            <? if (isset($_SESSION["user_privileges"]["load"]["load_per_station"])): ?>
                <li<? if ($pi["filename_list"] == "report_machines_load.php"): ?> class="current"<? endif ?>><a href="report_machines_load.php"><?=$lang["load_per_station"]?></a></li>
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
            <? if (isset($_SESSION["user_privileges"]["reports"]["misseized_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_misseized_by_date.php"): ?> class="current"<? endif ?>><a href="report_misseized_by_date.php"><?=$lang["misseized_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["rejected_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_rejected_garments.php"): ?> class="current"<? endif ?>><a href="report_rejected_garments.php"><?=$lang["rejected_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["userbound"])): ?>
                <li<? if ($pi["filename_list"] == "report_carrierbound.php"): ?> class="current"<? endif ?>><a href="report_carrierbound.php"><?=$lang["userbound"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["garmentusers_per_arsimo"])): ?>
                <li<? if ($pi["filename_list"] == "report_garmentusers_per_arsimo.php"): ?> class="current"<? endif ?>><a href="report_garmentusers_per_arsimo.php"><?=$lang["garmentusers_per_arsimo"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["garmentprofiles"])): ?>
                <li<? if ($pi["filename_list"] == "report_arsimos_per_garmentuser.php"): ?> class="current"<? endif ?>><a href="report_arsimos_per_garmentuser.php"><?=$lang["garmentprofiles"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["disconnected_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_disconnected_garments.php"): ?> class="current"<? endif ?>><a href="report_disconnected_garments.php"><?=$lang["disconnected_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["inactive_garmentusers_garments_in_use"])): ?>
                <li<? if ($pi["filename_list"] == "report_inactive_garmentusers_garments_in_use.php"): ?> class="current"<? endif ?>><a href="report_inactive_garmentusers_garments_in_use.php"><?=$lang["inactive_garmentusers_garments_in_use"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["garmentuser_modifications"])): ?>
                <li<? if ($pi["filename_list"] == "report_user_modifications.php"): ?> class="current"<? endif ?>><a href="report_user_modifications.php"><?=$lang["garmentuser_modifications"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["extra_dirty_garment"])): ?>
                <li<? if ($pi["filename_list"] == "report_extra_dirty_garments.php"): ?> class="current"<? endif ?>><a href="report_extra_dirty_garments.php"><?=$lang["extra_dirty_garment"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["email_warning"])): ?>
                <li<? if ($pi["filename_list"] == "garmentusers_email_warnings.php"): ?> class="current"<? endif ?>><a href="garmentusers_email_warnings.php"><?=$lang["email_warning"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["reports"]["found_items"])): ?>
                <li<? if ($pi["filename_list"] == "found_items.php"): ?> class="current"<? endif ?>><a href="found_items.php"><?=$lang["found_items"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>
    
    <? if (!empty($_SESSION["user_privileges"]["lists"])): ?>
    <h3><a href="#"><?=$lang["lists"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["lists"]["garments_in_use"])): ?>
                <li<? if ($pi["filename_list"] == "report_garments_in_use.php"): ?> class="current"<? endif ?>><a href="report_garments_in_use.php"><?=$lang["garments_in_use"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["lists"]["distributed_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_distribution_by_date.php"): ?> class="current"<? endif ?>><a href="report_distribution_by_date.php"><?=$lang["distributed_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["lists"]["deposited_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_deposits_by_date.php"): ?> class="current"<? endif ?>><a href="report_deposits_by_date.php"><?=$lang["deposited_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["lists"]["loaded_garments"])): ?>
                <li<? if ($pi["filename_list"] == "report_loaded_by_date.php"): ?> class="current"<? endif ?>><a href="report_loaded_by_date.php"><?=$lang["loaded_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["lists"]["packinglists"])): ?>
                <li<? if ($pi["filename_list"] == "report_packinglists.php"): ?> class="current"<? endif ?>><a href="report_packinglists.php"><?=$lang["packinglists"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>
    
    <? if (!empty($_SESSION["user_privileges"]["linen_service"])): ?>
    <h3><a href="#"><?=$lang["linen_service"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["extra_load"])): ?>
                <li<? if ($pi["filename_list"] == "extraload.php"): ?> class="current"<? endif ?>><a href="extraload.php"><?=$lang["extra_load"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["clear_depositlocation"])): ?>
                <li<? if ($pi["filename_list"] == "clear_depositlocation.php"): ?> class="current"<? endif ?>><a href="clear_depositlocation.php"><?=$lang["clear_depositlocation"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["packinglist_generate"])): ?>
                <li<? if ($pi["filename_list"] == "packinglist_generate.php"): ?> class="current"<? endif ?>><a href="packinglist_generate.php"><?=$lang["packinglist_generate"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["despecklesandrepairs"])): ?>
                <li<? if ($pi["filename_list"] == "garmentdespecklesandrepairs.php" || $pi["filename_list"] == "despeckles.php" || $pi["filename_list"] == "repairs.php"): ?> class="current"<? endif ?>><a href="garmentdespecklesandrepairs.php"><?=$lang["despecklesandrepairs"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["tag_replacements"])): ?>
                <li<? if ($pi["filename_list"] == "garmenttagreplacements.php"): ?> class="current"<? endif ?>><a href="garmenttagreplacements.php"><?=$lang["tag_replacements"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["throw_off_garments"])): ?>
                <li<? if ($pi["filename_list"] == "throw_off.php"): ?> class="current"<? endif ?>><a href="throw_off.php"><?=$lang["throw_off_garments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["linen_service"]["pocket_control_add"])): ?>
                <li<? if ($pi["filename_list"] == "pocket_control_add.php"): ?> class="current"<? endif ?>><a href="pocket_control_add.php"><?=$lang["pocket_control"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["circulation_management"])): ?>
    <h3><a href="#"><?=$lang["circulation_management"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["circulationadvice"])): ?>
                <li<? if ($pi["filename_list"] == "report_circulationadvice.php"): ?> class="current"<? endif ?>><a href="report_circulationadvice.php"><?=$lang["circulationadvice"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["beyond_and_in_circulation"])): ?>
                <li<? if ($pi["filename_list"] == "report_beyond_and_in_circulation.php"): ?> class="current"<? endif ?>><a href="report_beyond_and_in_circulation.php"><?=$lang["beyond_and_in_circulation"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["circulation_management"]["lead_time"])): ?>
                <li<? if ($pi["filename_list"] == "report_lead_time.php"): ?> class="current"<? endif ?>><a href="report_lead_time.php"><?=$lang["lead_time"]?></a></li>
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
            <? if (isset($_SESSION["user_privileges"]["master_data"]["articlegroups"])): ?>
                <li<? if ($pi["filename_list"] == "articlegroups.php"): ?> class="current"<? endif ?>><a href="articlegroups.php"><?=$lang["articlegroups"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["professions"])): ?>
                <li<? if ($pi["filename_list"] == "professions.php"): ?> class="current"<? endif ?>><a href="professions.php"><?=$lang["professions"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["garmentmodifications"])): ?>
                <li<? if ($pi["filename_list"] == "garmentmodifications.php"): ?> class="current"<? endif ?>><a href="garmentmodifications.php"><?=$lang["garmentmodifications"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["departments"])): ?>
                <li<? if ($pi["filename_list"] == "clientdepartments.php"): ?> class="current"<? endif ?>><a href="clientdepartments.php"><?=$lang["clientdepartments"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["costplaces"])): ?>
                <li<? if ($pi["filename_list"] == "costplaces.php"): ?> class="current"<? endif ?>><a href="costplaces.php"><?=$lang["costplaces"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["functions"])): ?>
                <li<? if ($pi["filename_list"] == "functions.php"): ?> class="current"<? endif ?>><a href="functions.php"><?=$lang["functions"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["information_screens"])): ?>
                <li<? if ($pi["filename_list"] == "information_screens.php"): ?> class="current"<? endif ?>><a href="information_screens.php"><?=$lang["information_screens"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["loading_screens"])): ?>
                <li<? if ($pi["filename_list"] == "loading_screens.php"): ?> class="current"<? endif ?>><a href="loading_screens.php"><?=$lang["loading_screens"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["master_data"]["found_item_types"])): ?>
                <li<? if ($pi["filename_list"] == "found_item_types.php"): ?> class="current"<? endif ?>><a href="found_item_types.php"><?=$lang["found_item_type"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["inputdata"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_input.php"): ?> class="current"<? endif ?>><a href="workwear_input.php"><?=$lang["inputdata"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["categories"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_category.php"): ?> class="current"<? endif ?>><a href="workwear_category.php"><?=$lang["categories"]?></a></li>
            <? endif ?>
        <ul>
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
    
    <? if (!empty($_SESSION["user_privileges"]["manager"])): ?>
    <h3><a href="#"><?=$lang["manager"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["manager"]["supercard"])): ?>
                <li<? if ($pi["filename_list"] == "garmentmanagers.php"): ?> class="current"<? endif ?>><a href="garmentmanagers.php"><?=$lang["supercard"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["manager"]["disconnect_garmentusers"])): ?>
                <li<? if ($pi["filename_list"] == "disconnect_garmentusers.php"): ?> class="current"<? endif ?>><a href="disconnect_garmentusers.php"><?=$lang["disconnect_garmentusers"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["manager"]["settings"])): ?>
                <li<? if ($pi["filename_list"] == "circulationgroups_settings.php"): ?> class="current"<? endif ?>><a href="circulationgroups_settings.php"><?=$lang["settings"]?></a></li>
            <? endif ?>
        </ul>
    </div>
    <? endif ?>

    <? if (!empty($_SESSION["user_privileges"]["workwearmanagement"])): ?>
    <h3><a href="#"><?=$lang["workwearmanagement"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["dashboard"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_dashboard.php"): ?> class="current"<? endif ?>><a href="workwear_dashboard.php"><?=$lang["dashboard"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["valueanalysis"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_valueanalysis.php"): ?> class="current"<? endif ?>><a href="workwear_valueanalysis.php"><?=$lang["valueanalysis"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["garmentageanalysis"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_ageanalysis.php"): ?> class="current"<? endif ?>><a href="workwear_ageanalysis.php"><?=$lang["garmentageanalysis"]?></a></li>
            <? endif ?>
            <? if (isset($_SESSION["user_privileges"]["workwearmanagement"]["ordermanagement"])): ?>
                <li<? if ($pi["filename_list"] == "workwear_order.php"): ?> class="current"<? endif ?>><a href="workwear_order.php"><?=$lang["ordermanagement"]?></a></li>
            <? endif ?> 
        </ul>
    </div>
    <? endif ?>
    <? if (!empty($_SESSION["user_privileges"]["technix_gsf"])): ?>
    <h3><a href="#"><?=$lang["technix_gsf"]?></a></h3>
    <div>
        <ul>
            <? if (isset($_SESSION["user_privileges"]["technix_gsf"]["station_cells"])): ?>
                <li<? if ($pi["filename_list"] == "gsf_station_cells.php"): ?> class="current"<? endif ?>><a href="gsf_station_cells.php"><?=$lang["station_cells"]?></a></li>
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
            <!--<li<? if ($pi["filename_list"] == "garmentuser_manual_import.php"): ?> class="current"<? endif ?>><a href="garmentuser_manual_import.php"><?=$lang["garmentuser_manual_import"]?></a></li>-->
            <li<? if ($pi["filename_list"] == "exportcodes.php"): ?> class="current"<? endif ?>><a href="exportcodes.php"><?=$lang["exportcodes"]?></a></li>
            <li<? if ($pi["filename_list"] == "emailsettings.php"): ?> class="current"<? endif ?>><a href="emailsettings.php"><?=$lang["email_settings"]?></a></li>
            <li<? if ($pi["filename_list"] == "emailaddresses.php"): ?> class="current"<? endif ?>><a href="emailaddresses.php"><?=$lang["email_addresses"]?></a></li>
            <!--<li<? if ($pi["filename_list"] == "management_info.php"): ?> class="current"<? endif ?>><a href="management_info.php">Edwin test</a></li>-->
            <li<? if ($pi["filename_list"] == "fifo_distribution.php"): ?> class="current"<? endif ?>><a href="fifo_distribution.php"><?=$lang["fifo_distribution"]?></a></li>
            <li<? if ($pi["filename_list"] == "monitor-hard.php"): ?> class="current"<? endif ?>><a href="monitor-hard.php"><?=$lang["hardware_monitor"]?></a></li>
            <li<? if ($pi["filename_list"] == "sync_files.php"): ?> class="current"<? endif ?>><a href="sync_files.php"><?=$lang["import_files"]?></a></li>
            <li<? if ($pi["filename_list"] == "errormessages.php"): ?> class="current"<? endif ?>><a href="errormessages.php"><?=$lang["failures"]?></a></li>
            <li<? if ($pi["filename_list"] == "distributors.php"): ?> class="current"<? endif ?>><a href="distributors.php"><?=$lang["distributors"]?></a></li>
        </ul>
    </div>
    <? endif ?>
</div>
<? if (!empty($_SESSION["user_privileges"]["contact"])): ?>
<h3 class="contact ui-accordion-header ui-helper-reset ui-state-default ui-corner-all">
    <a href="contact.php" class="contact">Contact</a>
</h3>
<? endif ?>
