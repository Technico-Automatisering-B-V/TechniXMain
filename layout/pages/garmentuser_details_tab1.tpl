<table><tr><td valign="top">
<table class="detailstab">
    <tr><td class="head" colspan="2"><?=$lang["personal_data"]?></td></tr>
    <tr>
        <td class="name"><?=$lang["gender"]?>:</td>
        <td class="value">
            <?php
            if (empty($gu_data["gender"])){ $unknown_select = " checked=\"checked\""; }else{ $unknown_select = ""; }
            if ($gu_data["gender"] == "male"){ $male_select = " checked=\"checked\""; }else{ $male_select = ""; }
            if ($gu_data["gender"] == "female"){ $female_select = " checked=\"checked\""; }else{ $female_select = ""; }
            ?>
            <span class="radioset">
                <input name="gender" id="genderUnknown" type="radio" value=""<?=$unknown_select?>><label for="genderUnknown"><?=$lang["unknown"]?></label>
                <input name="gender" id="genderMale" type="radio" value="male"<?=$male_select?>><label for="genderMale"><?=$lang["male"]?></label>
                <input name="gender" id="genderFemale" type="radio" value="female"<?=$female_select?>><label for="genderFemale"><?=$lang["female"]?></label>
            </span>
        </td>
    </tr>
    <? if (count($clientdepartments_all) > 0): ?>
    <tr><td class="name"><?=$lang["clientdepartment"]?>:</td><td class="value"><? html_selectbox_array("clientdepartment_id", $clientdepartments_all, $gu_data["clientdepartment_id"], $lang["make_a_choice"]) ?></td></tr>
    <? endif ?>
    <? if (count($costplaces_all) > 0): ?>
    <tr><td class="name"><?=$lang["costplace"]?>:</td><td class="value"><? html_selectbox_array("costplace_id", $costplaces_all, $gu_data["costplace_id"], $lang["make_a_choice"]) ?></td></tr>
    <? endif ?>
    <? if (count($functions_all) > 0): ?>
    <tr><td class="name"><?=$lang["function"]?>:</td><td class="value"><? html_selectbox_array("function_id", $functions_all, $gu_data["function_id"], $lang["make_a_choice"]) ?></td></tr>
    <? endif ?>
    <tr><td class="name"><?=$lang["lockernumber"]?>:</td><td class="value"><input type="text" name="lockernumber" value="<?=$gu_data["lockernumber"]?>" size="20" maxlength="50"></td></tr>
    <tr><td class="name"><?=$lang["title"]?>:</td><td class="value"><input type="text" name="title" value="<?=$gu_data["title"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["initials"]?>:</td><td class="value"><input type="text" name="initials" value="<?=$gu_data["initials"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["first_name"]?>:</td><td class="value"><input type="text" name="name" value="<?=$gu_data["name"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["intermediate"]?>:</td><td class="value"><input type="text" name="intermediate" value="<?=$gu_data["intermediate"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["surname"]?>:</td><td class="value"><input type="text" name="surname" value="<?=$gu_data["surname"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["maiden_name"]?>:</td><td class="value"><input type="text" name="maidenname" value="<?=$gu_data["maidenname"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["personnelcode"]?>:</td><td class="value"><input type="text" name="personnelcode" value="<?=$gu_data["personnelcode"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["laundrynumber"]?>:</td><td class="value"><input type="text" name="exportcode" value="<?=$gu_data["exportcode"]?>" size="20"></td></tr>
    <tr><td class="name"><?=$lang["email_address"]?>:</td><td class="value"><input type="text" name="email" value="<?=$gu_data["email"]?>" size="20"></td></tr>
</table>

</td><td valign="top">

<?php if (count($circulationgroups_all) == 1){ print("<input name=\"circulationgroups_selected[]\" type=\"hidden\" value=\"1\" />"); } ?>

<table class="detailstab">
    <?php if (count($circulationgroups_all) > 1){ ?>
    <tr>
        <td colspan="2"><strong><?=$lang["bound_to_location"]?></strong></td>
    </tr>
    <tr>
        <td class="top"><?=$lang["circulationgroup"]?>:</td>
        <td class="value" style="width: 250px;">
            <?php
            foreach ($circulationgroups_all as $circulationgroup_id => $circulationgroup_name) {
                if (!empty($circulationgroups_selected)){
                    if (in_array($circulationgroup_id, $circulationgroups_selected)) { $checked = "checked = \"checked\""; }else{ $checked = ""; }
                }else{ $checked = ""; }
                print("<input name=\"circulationgroups_selected[]\" type=\"checkbox\" value=\"". $circulationgroup_id ."\"". $checked ." /> ". $circulationgroup_name ."<br />");
            }
            ?>
        </td>
    </tr>
    <?php } ?>

    <tr><td colspan="2">&nbsp;</td></tr>

    <tr><td colspan="2"><strong><?=$lang["access_control"]?></strong></td></tr>
    <tr>
        <td class="right"><?=$lang["passcode"]?>:</td>
        <td class="value" width="200">
            <input type="text" name="code" value="<?=$gu_data["code"]?>" size="20">
        </td>
    </tr>
    <tr>
        <td class="right"><?=$lang["passcode"]?> 2:</td>
        <td class="value" width="200">
            <input type="text" name="code2" value="<?=$gu_data["code2"]?>" size="20">
        </td>
    </tr>
    <tr>
        <td class="right"><?=$lang["passcode"]?> 3:</td>
        <td class="value" width="200">
            <input type="text" name="code3" value="<?=$gu_data["code3"]?>" size="20">
        </td>
    </tr>
    <tr>
        <td class="right"><?=$lang["distribution"]?>:</td>
        <td class="value">
            <? if ($deleted): ?>
            <span class="empty"><?=$lang["deleted_garmentuser"]?></span>
            <? else: ?>
            <? html_radiobuttons("active", $distribution, $gu_data["active"]) ?>
            <? endif ?>
        </td>
    </tr>
    <? if (!$deleted): ?>
    <tr>
        <td class="right"><?=$lang["service_on"]?>:</td>
        <td class="value">
            <? html_radiobuttons("service_on_switch", $service_on_switches, $service_on_selected) ?>
            <? if ($service_on_selected == "unlimited"){ $on_style = " style=\"display:none\""; }else{ $on_style = ""; } ?>
            <span id="date_service_on_wrapper"<?=$on_style?>><br /><input class="date" name="date_service_on" type="text" value="<?=$gu_data["date_service_on"]?>" /> <span class="empty">( jjjj-mm-dd )</span></span>
        </td>
    </tr>
    <tr>
        <td class="right"><?=$lang["service_off"]?>:</td>
        <td class="value">
            <? html_radiobuttons("service_off_switch", $service_off_switches, $service_off_selected) ?>
            <? if ($service_off_selected == "unlimited"){ $off_style = " style=\"display:none\""; }else{ $off_style = ""; } ?>
            <span id="date_service_off_wrapper"<?=$off_style?>><br /><input class="date" name="date_service_off" type="text" value="<?=$gu_data["date_service_off"]?>" /> <span class="empty">( jjjj-mm-dd )</span></span>
        </td>
    </tr>
</table>

<table class="detailstab">
    <tr><td><strong><?=$lang["comments"]?></strong></td><td><input type="checkbox" name="show_comments" id="show_comments" <?=$show_comments_checked?> /> <label for="show_comments"><?=$lang["show_on_screen"]?></label></td></tr>
    <tr><td colspan="2" class="value"><textarea rows="6" name="comments" cols="40"><?=$gu_data["comments"]?></textarea></td></tr>
    <? endif ?>
</table>

</td></tr></table>
