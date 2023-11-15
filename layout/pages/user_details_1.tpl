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
            <div id="exportcodes">
                <span id="<?=$lang[common]?>"></span>
                <h3><a href="#"><?=$lang[common]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[clientdepartments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_departments_enabled" name="privileges[common][departments]" <? if ($ar["common"]["departments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[costplaces]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_costplaces_enabled" name="privileges[common][costplaces]" <? if ($ar["common"]["costplaces"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[functions]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="common_functions_enabled" name="privileges[common][functions]" <? if ($ar["common"]["functions"]){ echo " checked=\"checked\""; }?> />
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
                            <td class="name"><?=$lang[garments_in_use]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_garments_in_use_enabled" name="privileges[reports][garments_in_use]" <? if ($ar["reports"]["garments_in_use"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[distribution_by_date]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_distribution_by_date_enabled" name="privileges[reports][distribution_by_date]" <? if ($ar["reports"]["distribution_by_date"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[current_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_current_load_enabled" name="privileges[reports][current_load]" <? if ($ar["reports"]["current_load"]){ echo " checked=\"checked\""; }?> />
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
                            <td class="name"><?=$lang[deposited_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_deposited_garments_enabled" name="privileges[reports][deposited_garments]" <? if ($ar["reports"]["deposited_garments"]){ echo " checked=\"checked\""; }?> />
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
                            <td class="name"><?=$lang[arsimos_per_garmentuser]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_arsimos_per_garmentuser_enabled" name="privileges[reports][arsimos_per_garmentuser]" <? if ($ar["reports"]["arsimos_per_garmentuser"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[machines_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_machines_load_enabled" name="privileges[reports][machines_load]" <? if ($ar["reports"]["machines_load"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[disconnected_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_disconnected_garments_enabled" name="privileges[reports][disconnected_garments]" <? if ($ar["reports"]["disconnected_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[packinglists]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="reports_packinglists_enabled" name="privileges[reports][packinglists]" <? if ($ar["reports"]["packinglists"]){ echo " checked=\"checked\""; }?> />
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
                <span id="<?=$lang[circulation_management]?>"></span>
                <h3><a href="#"><?=$lang[circulation_management]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[circulationadvice_old]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_circulationadvice_old_enabled" name="privileges[circulation_management][circulationadvice_old]" <? if ($ar["circulation_management"]["circulationadvice_old"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang["circulationadvice"]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_circulationadvice_enabled" name="privileges[circulation_management][circulationadvice]" <? if ($ar["circulation_management"]["circulationadvice"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[beyond_circulation]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_beyond_circulation_enabled" name="privileges[circulation_management][beyond_circulation]" <? if ($ar["circulation_management"]["beyond_circulation"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[in_circulation]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_in_circulation_enabled" name="privileges[circulation_management][in_circulation]" <? if ($ar["circulation_management"]["in_circulation"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[throw_off_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="circulation_management_throw_off_garments_enabled" name="privileges[circulation_management][throw_off_garments]" <? if ($ar["circulation_management"]["throw_off_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[linen_service]?>"></span>
                <h3><a href="#"><?=$lang[linen_service]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[repairs]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_repairs_enabled" name="privileges[linen_service][repairs]" <? if ($ar["linen_service"]["repairs"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[despeckles]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_despeckles_enabled" name="privileges[linen_service][despeckles]" <? if ($ar["linen_service"]["despeckles"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[tag_replacements]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_tag_replacements_enabled" name="privileges[linen_service][tag_replacements]" <? if ($ar["linen_service"]["tag_replacements"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[extra_load]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_extra_load_enabled" name="privileges[linen_service][extra_load]" <? if ($ar["linen_service"]["extra_load"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[washcount_garments]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_washcount_garments_enabled" name="privileges[linen_service][washcount_garments]" <? if ($ar["linen_service"]["washcount_garments"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[failures]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_errormessages_enabled" name="privileges[linen_service][errormessages]" <? if ($ar["linen_service"]["errormessages"]){ echo " checked=\"checked\""; }?> />
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
                            <td class="name"><?=$lang[disconnect_garmentusers]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="linen_service_disconnect_garmentusers" name="privileges[linen_service][disconnect_garmentusers]" <? if ($ar["linen_service"]["disconnect_garmentusers"]){ echo " checked=\"checked\""; }?> />
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
                            <td class="name"><?=$lang[garmentmanagers]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="master_data_garmentmanagers_enabled" name="privileges[master_data][garmentmanagers]" <? if ($ar["master_data"]["garmentmanagers"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                    </table>
                </div>
                <span id="<?=$lang[technix_gsx]?>"></span>
                <h3><a href="#"><?=$lang[technix_gsx]?></a></h3>
                <div>
                    <table class="detailstab">
                        <tr>
                            <td class="name"><?=$lang[articles_description_short]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="technix_gsx_articles_description_short_enabled" name="privileges[technix_gsx][articles_description_short]" <? if ($ar["technix_gsx"]["articles_description_short"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[sizes_description_short]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="technix_gsx_sizes_description_short_enabled" name="privileges[technix_gsx][sizes_description_short]" <? if ($ar["technix_gsx"]["sizes_description_short"]){ echo " checked=\"checked\""; }?> />
                            <td>
                        </tr>
                        <tr>
                            <td class="name"><?=$lang[station_cells]?>:</td>
                            <td class="value">
                                <input type="checkbox" id="technix_gsx_station_cells_enabled" name="privileges[technix_gsx][station_cells]" <? if ($ar["technix_gsx"]["station_cells"]){ echo " checked=\"checked\""; }?> />
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
</script>