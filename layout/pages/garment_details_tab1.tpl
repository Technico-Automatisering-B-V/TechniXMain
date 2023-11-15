<table class="detailstab">

	<? if ($pi["page"] != "add"){
        if ($detailsdata["scanlocation_id"] !== '2'){
            $link_missing = '&raquo; <input type="button" id="missingButton" value="' . $lang["put_to_missing"] . '" title="' . $lang["put_to_missing"] . '" />';
        }else{ $link_missing = ""; }
        if ($detailsdata["scanlocation_id"] !== '3'){
            $link_stock = '&raquo; <input type="button" id="stockButton" value="' . $lang["put_to_stock"] . '" title="' . $lang["put_to_stock"] . '" />';
        }else{ $link_stock = ""; }
        if (!empty($status) && $status !== 'laundry'){
            $link_laundry = '&raquo; <input type="button" id="laundryButton" value="' . $lang["put_to_laundry"] . '" title="' . $lang["put_to_laundry"] . '" />';
        }else{ $link_laundry = ""; }
        ?>
	<tr><td class="name"><?=$lang["created_on"]?>:</td><td class="value"><?=(!empty($detailsdata["created_on"])) ? strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($detailsdata["created_on"])) : "<span class=\"empty\">". $lang["unknown"] ."</span>" ?></td></tr>
	<tr><td class="name"><?=$lang["last_scanned"]?>:</td><td class="value"><?=(!empty($detailsdata["lastscan"])) ? strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($detailsdata["lastscan"])) : "<span class=\"empty\">". $lang["never_scanned"] ."</span>" ?></td></tr>
	<tr><td class="name"><?=$lang["status"]?>:</td><td class="value"><?=(!empty($detailsdata["scanlocation_id"])) ? ((!empty($sub_status)) ? $lang[$sub_status] : $lang[$status] ) : "<span class=\"empty\">". $lang["unknown"] ."</span>" ?> <?=$link_stock?> <?=$link_missing?> <?=$link_laundry?></td></tr>
	<tr><td class="name" colspan="2">&nbsp;</td></tr>
	<? } ?>

	<tr>
		<td class="name"><?=$lang["tag"]?>:</td>
		<td class="value">
			<? if ($pi["page"] == "add"): ?>
			<input type="text" id="tag" name="tag" value="<?=$detailsdata["tag"]?>" size="30">
                        <input type="submit" style="display: none;">
                        <button class="required" title="<?=$lang["field_required"]?>">*</button>
			<? else: ?>
			<?=$detailsdata["tag"]?>
			<input type="hidden" name="tag" value="<?=$detailsdata["tag"]?>">
			<? endif ?>
		</td>
	</tr>
	<tr>
		<td class="name"><?=$lang["tag"]?> 2:</td>
		<td class="value">
                    <? if (!empty($detailsdata["tag2"])): ?>
			<?=$detailsdata["tag2"]?>
                    <? else: ?>
			<span class="empty"><?=$lang["none"]?></span>
                    <? endif ?>
		</td>
	</tr>
	<? if ($circulationgroup_count > 1){ ?>
		<tr><td class="name"><?=$lang["circulationgroup"]?>:</td><td class="value"><?=html_selectbox_submit("circulationgroup_id", $circulationgroups, $detailsdata["circulationgroup_id"], $lang["make_a_choice"]);?></td></tr>
	<? } ?>
	<tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><? html_selectbox_array_submit("article_id", $articles, $bindingdata["article_id"], $lang["make_a_choice"]) ?></td></tr>
	<? if (!empty($sizes)):?><tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><? html_selectbox_array_submit("size_id", $sizes, $bindingdata["size_id"], $lang["make_a_choice"]) ?></td></tr><? endif ?>
	<? if (!empty($modifications)):?><tr><td class="name"><?=$lang["garmentmodification"]?>:</td><td class="value"><? html_selectbox_array("modification_id", $modifications, $bindingdata["modification_id"], $lang["none"], true, $showempty_mod)?></td></tr><? endif ?>
	<tr><td class="name"><?=$lang["max_washcount"]?>:</td><td class="value"><input type="text" name="maxwashcount" value="<?=$detailsdata["maxwashcount"]?>" size="10"></td></tr>
	<tr><td class="name" colspan="2">&nbsp;</td></tr>
	<tr><td class="name"><?=$lang["bound_to_garmentuser"]?>:</td><td class="value"><? html_radiobuttons_submit("userbound", $userboundswitch, $bindingdata["userbound"]) ?></td></tr>

	<? if ($bindingdata["userbound"] == 1){ ?>

		<tr>
			<td class="name"><?=$lang["garmentuser"]?>:</td>
			<td class="value">
				<? generate_garmentusers_select($detailsdata["garmentuser_id"], $lang["make_a_choice"]); ?>
			</td>
		</tr>
		<tr><td class="name"><?=$lang["homewash"]?>:</td><td class="value"><? html_radiobuttons("homewash", $homewashswitch, $bindingdata["homewash"]) ?></td></tr>

	<? } ?>

</table>

<?php
if ($circulationgroup_count == 1){ print("<input name=\"circulationgroup_id\" type=\"hidden\" value=\"1\" />"); }
?>