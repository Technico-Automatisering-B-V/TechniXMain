<?php

if ($timelockoption == "owntimelock"){ $v_timelock = ($gu_data["timelock"] == "-1") ? $gu_data["timelock"] = "0" : $gu_data["timelock"]; $d_timelock = ""; }else{ $v_timelock = $profession_timelock; $d_timelock = " class=\"disabled\" disabled=\"disabled\""; }
if ($blockageoption == "ownblockage"){ $v_blockage = ($gu_data["daysbeforelock"] == null) ? $gu_data["daysbeforelock"] = "0" : $gu_data["daysbeforelock"]; $d_blockage = ""; }else{ $v_blockage = $profession_blockage; $d_blockage = " class=\"disabled\" disabled=\"disabled\""; }
if ($warningoption == "ownwarning"){ $v_warning = ($gu_data["daysbeforewarning"] == null) ? $gu_data["daysbeforewarning"] = "0" : $gu_data["daysbeforewarning"]; $d_warning = ""; }else{ $v_warning = $profession_warning; $d_warning = " class=\"disabled\" disabled=\"disabled\""; }

?>

<table class="detailstab">
    <? if ($_POST["page"] !== "add"): ?>
    <tr>
        <td class="name"><?=$lang["timelock"]?>:</td>
        <td class="value"><? html_radiobuttons("timelockoption", $timelockoptions, $timelockoption) ?></td>
        <td class="value"><input type="text" id="timelock" name="timelock" value="<?=$v_timelock?>" size="4"<?=$d_timelock?> /> <?=strtolower($lang["minutes"])?></td>
    </tr>
    <tr>
        <td class="name"><?=$lang["warning"]?>:</td>
        <td class="value"><? html_radiobuttons("warningoption", $warningoptions, $warningoption) ?></td>
        <td class="value"><input type="text" id="daysbeforewarning" name="daysbeforewarning" value="<?=$v_warning?>" size="4"<?=$d_warning?> /> <?=strtolower($lang["days_garments_in_possession"])?></td>
    </tr>
    <tr>
        <td class="name"><?=$lang["blockage"]?>:</td>
        <td class="value"><? html_radiobuttons("blockageoption", $blockageoptions, $blockageoption) ?></td>
        <td class="value"><input type="text" id="daysbeforelock" name="daysbeforelock" value="<?=$v_blockage?>" size="4"<?=$d_blockage?> /> <?=strtolower($lang["days_after_warning"])?></td>
    </tr>
    <? endif ?>
</table>