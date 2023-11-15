<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])): ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? endif ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["user"]?></a></li>
            <li><a href="#tab2"><?=$lang["privileges"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["username"]?>:</td>
                    <td class="value">
                        <? if ($pi["page"] == "details"): ?>
                        <input type="hidden" id="username" name="username" value="<?=$detailsdata["username"]?>" />
                        <?=$detailsdata["username"]?>
                        <? else: ?>
                        <input type="text" id="username" name="username" placeholder="<?=$lang["username"]?>" value="<?=$detailsdata["username"]?>" size="30" /> <button class="required" title="<?=$lang["field_required"]?>">*</button>
                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["password"]?>:</td>
                    <td class="value">
                        <? if ($pi["page"] == "details"): ?>

                        <? if (!empty($optiondata["cpw"]) && $optiondata["cpw"]): ?>
                        <input type="password" id="password" name="password" value="" size="30" /> <button class="required" title="<?=$lang["field_required"]?>">*</button>
                        <input type="hidden" name="change" value="true" />
                        <? else: ?>
                        <input type="hidden" name="gosubmit" value="true" />
                        <input type="submit" name="cpw" value="<?=$lang["change"]?>" title="<?=$lang["change"]?>" />
                        <input type="hidden" name="change" value="false" />
                        <? endif ?>

                        <? else: ?>

                        <input type="password" id="password" name="password" placeholder="<?=$lang["password"]?>" value="" size="30" /> <button class="required" title="<?=$lang["field_required"]?>">*</button>

                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["locale"]?>:</td>
                    <td class="value">
                        <? html_selectbox("locale_id", $locales, $detailsdata["locale_id"], $lang["make_a_choice"]) ?>
                    </td>
                </tr>
            </table>
        </div>

        <div id="tab2">
            <div id="selectChb"><input type="button" value="<?=$lang['fill_all']?>" /></div><br />
            <div id="exportcodes">
                <span id="<?=$lang[common]?>"></span>
                <h3><a href="#"><?=$lang[common]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[dashboard]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_dashboard_enabled" name="privileges[common][dashboard]" <? if ($ar["common"]["dashboard"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentusers]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_garmentusers_enabled" name="privileges[common][garmentusers]" <? if ($ar["common"]["garmentusers"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_garments_enabled" name="privileges[common][garments]" <? if ($ar["common"]["garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>       
                <span id="<?=$lang[load]?>"></span>
                <h3><a href="#"><?=$lang[load]?></a></h3>
                <div>
                     <table class="detailstab">                 
                        <tr>
                            <td class="name"><?=$lang[current_sizebound_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="load_current_sizebound_load_enabled" name="privileges[load][current_sizebound_load]" <? if ($ar["load"]["current_sizebound_load"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[current_userbound_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="load_current_userbound_load_enabled" name="privileges[load][current_userbound_load]" <? if ($ar["load"]["current_userbound_load"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[load_per_station]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="load_load_per_station_enabled" name="privileges[load][load_per_station]" <? if ($ar["load"]["load_per_station"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>               
                <span id="<?=$lang[reports]?>"></span>
                <h3><a href="#"><?=$lang[reports]?></a></h3>
                <div>
                     <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[total_report]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_total_report_enabled" name="privileges[reports][total_report]" <? if ($ar["reports"]["total_report"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[misseized_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_misseized_garments_enabled" name="privileges[reports][misseized_garments]" <? if ($ar["reports"]["misseized_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[rejected_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_rejected_garments_enabled" name="privileges[reports][rejected_garments]" <? if ($ar["reports"]["rejected_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[userbound]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_userbound_enabled" name="privileges[reports][userbound]" <? if ($ar["reports"]["userbound"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentusers_per_arsimo]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_garmentusers_per_arsimo_enabled" name="privileges[reports][garmentusers_per_arsimo]" <? if ($ar["reports"]["garmentusers_per_arsimo"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentprofiles]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_garmentprofiles_enabled" name="privileges[reports][garmentprofiles]" <? if ($ar["reports"]["garmentprofiles"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[inactive_garmentusers_garments_in_use]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_inactive_garmentusers_garments_in_use_enabled" name="privileges[reports][inactive_garmentusers_garments_in_use]" <? if ($ar["reports"]["inactive_garmentusers_garments_in_use"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[disconnected_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_disconnected_garments_enabled" name="privileges[reports][disconnected_garments]" <? if ($ar["reports"]["disconnected_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentuser_modifications]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_garmentuser_modifications_enabled" name="privileges[reports][garmentuser_modifications]" <? if ($ar["reports"]["garmentuser_modifications"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[extra_dirty_garment]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_extra_dirty_garments_enabled" name="privileges[reports][extra_dirty_garment]" <? if ($ar["reports"]["extra_dirty_garment"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[email_warning]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_email_warning_enabled" name="privileges[reports][email_warning]" <? if ($ar["reports"]["email_warning"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[found_items]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_found_items_enabled" name="privileges[reports][found_items]" <? if ($ar["reports"]["found_items"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[lists]?>"></span>
                <h3><a href="#"><?=$lang[lists]?></a></h3>
                <div>
                     <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[garments_in_use]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="lists_garments_in_use_enabled" name="privileges[lists][garments_in_use]" <? if ($ar["lists"]["garments_in_use"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[distributed_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="lists_distributed_garments_enabled" name="privileges[lists][distributed_garments]" <? if ($ar["lists"]["distributed_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[deposited_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="lists_deposited_garments_enabled" name="privileges[lists][deposited_garments]" <? if ($ar["lists"]["deposited_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[loaded_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="lists_loaded_garments_enabled" name="privileges[lists][loaded_garments]" <? if ($ar["lists"]["loaded_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[packinglists]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="lists_packinglists_enabled" name="privileges[lists][packinglists]" <? if ($ar["lists"]["packinglists"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[linen_service]?>"></span>
                <h3><a href="#"><?=$lang[linen_service]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[extra_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_extra_load_enabled" name="privileges[linen_service][extra_load]" <? if ($ar["linen_service"]["extra_load"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[clear_depositlocation]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_clear_depositlocation_enabled" name="privileges[linen_service][clear_depositlocation]" <? if ($ar["linen_service"]["clear_depositlocation"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[packinglist_generate]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_packinglist_generate" name="privileges[linen_service][packinglist_generate]" <? if ($ar["linen_service"]["packinglist_generate"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[despecklesandrepairs]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_despecklesandrepairs_enabled" name="privileges[linen_service][despecklesandrepairs]" <? if ($ar["linen_service"]["despecklesandrepairs"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[tag_replacements]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_tag_replacements_enabled" name="privileges[linen_service][tag_replacements]" <? if ($ar["linen_service"]["tag_replacements"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[throw_off_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_throw_off_garments_enabled" name="privileges[linen_service][throw_off_garments]" <? if ($ar["linen_service"]["throw_off_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[pocket_control]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_pocket_control_add_enabled" name="privileges[linen_service][pocket_control_add]" <? if ($ar["linen_service"]["pocket_control_add"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[circulation_management]?>"></span>
                <h3><a href="#"><?=$lang[circulation_management]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang["circulationadvice"]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_circulationadvice_enabled" name="privileges[circulation_management][circulationadvice]" <? if ($ar["circulation_management"]["circulationadvice"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[beyond_and_in_circulation]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_beyond_and_in_circulation_enabled" name="privileges[circulation_management][beyond_and_in_circulation]" <? if ($ar["circulation_management"]["beyond_and_in_circulation"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[lead_time]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_lead_time_enabled" name="privileges[circulation_management][lead_time]" <? if ($ar["circulation_management"]["lead_time"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[master_data]?>"></span>
                <h3><a href="#"><?=$lang[master_data]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[sizes]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_sizes_enabled" name="privileges[master_data][sizes]" <? if ($ar["master_data"]["sizes"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[articles]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_articles_enabled" name="privileges[master_data][articles]" <? if ($ar["master_data"]["articles"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[articlegroups]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_articlegroups_enabled" name="privileges[master_data][articlegroups]" <? if ($ar["master_data"]["articlegroups"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[professions]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_professions_enabled" name="privileges[master_data][professions]" <? if ($ar["master_data"]["professions"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentmodifications]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_garmentmodifications_enabled" name="privileges[master_data][garmentmodifications]" <? if ($ar["master_data"]["garmentmodifications"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[clientdepartments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_departments_enabled" name="privileges[master_data][departments]" <? if ($ar["master_data"]["departments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[costplaces]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_costplaces_enabled" name="privileges[master_data][costplaces]" <? if ($ar["master_data"]["costplaces"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[functions]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_functions_enabled" name="privileges[master_data][functions]" <? if ($ar["master_data"]["functions"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[information_screens]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_information_screens_enabled" name="privileges[master_data][information_screens]" <? if ($ar["master_data"]["information_screens"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[loading_screens]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_loading_screens_enabled" name="privileges[master_data][loading_screens]" <? if ($ar["master_data"]["loading_screens"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[found_item_type]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_found_item_types_enabled" name="privileges[master_data][found_item_types]" <? if ($ar["master_data"]["found_item_types"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[inputdata]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_inputdata_enabled" name="privileges[workwearmanagement][inputdata]" <? if ($ar["workwearmanagement"]["inputdata"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[manually]?>"></span>
                <h3><a href="#"><?=$lang[manually]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[distribution]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="manually_distribution_enabled" name="privileges[manually][distribution]" <? if ($ar["manually"]["distribution"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[deposit]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="manually_deposit_enabled" name="privileges[manually][deposit]" <? if ($ar["manually"]["deposit"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[manager]?>"></span>
                <h3><a href="#"><?=$lang[manager]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[supercard]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="manager_supercard_enabled" name="privileges[manager][supercard]" <? if ($ar["manager"]["supercard"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[disconnect_garmentusers]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="manager_disconnect_garmentusers" name="privileges[manager][disconnect_garmentusers]" <? if ($ar["manager"]["disconnect_garmentusers"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[settings]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="manager_settings_enabled" name="privileges[manager][settings]" <? if ($ar["manager"]["settings"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                
                <span id="<?=$lang[technix_gsf]?>"></span>
                <h3><a href="#"><?=$lang[technix_gsf]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[station_cells]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="technix_gsf_station_cells_enabled" name="privileges[technix_gsf][station_cells]" <? if ($ar["technix_gsf"]["station_cells"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>   
                    </table>
                </div>
                
                <span id="<?=$lang[contact]?>"></span>
                <h3><a href="#"><?=$lang[contact]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[contact]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="contact_contact_enabled" name="privileges[contact][contact]" <? if ($ar["contact"]["contact"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>                        
                    </table>
                </div>
                
                <span id="<?=$lang[workwearmanagement]?>"></span>
                <h3><a href="#"><?=$lang[workwearmanagement]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[dashboard]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_dashboard_enabled" name="privileges[workwearmanagement][dashboard]" <? if ($ar["workwearmanagement"]["dashboard"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[valueanalysis]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_valueanalysis_enabled" name="privileges[workwearmanagement][valueanalysis]" <? if ($ar["workwearmanagement"]["valueanalysis"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[garmentageanalysis]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_garmentageanalysis_enabled" name="privileges[workwearmanagement][garmentageanalysis]" <? if ($ar["workwearmanagement"]["garmentageanalysis"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[ordermanagement]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_ordermanagement_enabled" name="privileges[workwearmanagement][ordermanagement]" <? if ($ar["workwearmanagement"]["ordermanagement"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>      
                        <tr>
                            <td class="name"><?=$lang[categories]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="workwearmanagement_categoeies_enabled" name="privileges[workwearmanagement][categories]" <? if ($ar["workwearmanagement"]["categories"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>  
                    </table>
                </div>

            </div>
        </div>
    </div>


    <?php
    if ($pi["page"] == "details") {
            echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . $lang["save_and_close"] . '" title="' . $lang["save_and_close"] . '" />';
            echo '<input type="submit" ' . $statements . 'name="detailssubmitnone" value="' . $lang["close"] . '" title="' . $lang["close"] . '" />';
    } else if ($pi["page"] == "add") {
            echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . $lang["add_and_close"] . '" title="' . $lang["add_and_close"] . '" />';
    }
    ?>

</form>

<script type="text/javascript">
    $(function() {
        <? if (!empty($optiondata['cpw'])): ?>
            $("#password").focus();
        <? else: ?>
            $("#username").focus();
        <? endif ?>
    });
    
    $('#selectChb').click(function(){ 
        $('#exportcodes').find('input[type=checkbox]').attr("checked", true);
    });
</script>