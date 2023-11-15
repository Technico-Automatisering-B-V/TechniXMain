<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input id="garment_id_to_free" name="garment_id_to_free" type="hidden" value="" />
    <input id="hook_to_free" name="hook_to_free" type="hidden" value="" />
    <input id="limit_start" name="limit_start" type="hidden" value="0" />
    <div class="filter">

        <table>
            <? if ($circulationgroup_count > 1): ?>
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value" width="150"><?=html_selectbox_submit("circulationgroup_id", $circulationgroups, $circulationgroup_id, $lang["make_a_choice"], "style='width:200px'")?></td>
                </tr>
            <? endif ?>
            <? if (!empty($circulationgroup_id)): ?>
                <tr>
                    <td class="name"><?=$lang["station"]?>:</td>
                    <td class="value"><?=html_selectbox_array_submit("distributor_id", $distributors, $distributor_id, $lang["(all_stations)"], true, false, "style='width:200px'")?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["type"]?> <?=strtolower($lang["garment"])?>:</td>
                    <td class="value"><?=html_selectbox_array_submit("type", $types, $type, $lang["(all)"], true, false, "style='width:200px'"); ?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["article"]?>:</td>
                    <td class="value"><?=html_selectbox_submit("article_id", $articles, $article_id, $lang["(all_articles)"], "style='width:200px'")?></td>
                </tr>
                <? if (!empty($sizes)): ?>
                    <tr>
                        <td class="name"><?=$lang["size"]?>:</td>
                        <td class="value"><?=html_selectbox_array_submit("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], true, false, "style='width:200px'")?></td>
                    </tr>
                <? endif ?>
            <? endif ?>
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>
</form>

<div class="clear" />

<? if (!isset($pi["note"])): ?><?=$resultinfo?><? endif ?>

<?php
if (isset($urlinfo["limit_total"]) && $urlinfo["limit_total"] != 0){

	$tablerows = "";

	while ($row = db_fetch_assoc($listdata)){
		echo "<form id=\"". $row["garments_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["garments_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

		$tablerows .= "<tr class=\"list\">
						<td class=\"midlist\">". $row["distributors_doornumber"] ."</td>
						<td class=\"midlist\">". $row["distributors_load_hook"] ."</td>
						<td class=\"list\" onClick=\"document.getElementById('". $row["garments_id"] ."').submit();\">". $row["garments_tag"] ."</td>
						". (($type == "userbound") ? "<td class=\"list\">". generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]) ."</td>" : "") ."
						<td class=\"list\">". $row["articles_description"] ."</td>
						<td class=\"midlist\">". $row["sizes_name"] ."</td>
						<td class=\"midlist\">". (!empty($row["modifications_name"]) ? $row["modifications_name"] : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
						". (($type == "deleted") ? "<td class=\"list\">". $row["garments_deleted_on"] ."</td>" : "") ."
                        <td class=\"midlist\" width=\"25\" onclick=\"if(confirm('". $lang["free_position_confirm"] ."')){document.dataform.garment_id_to_free.value='". $row["garments_id"] ."';document.dataform.hook_to_free.value='". $row["distributors_load_hook"] ."';document.dataform.limit_start.value='". $urlinfo["limit_start"] ."';document.dataform.submit();}else{return false}\">
                            <img src=\"layout/images/delete.png\" width=\"14\" height=\"14\" border=\"0\" title=\"". $lang["free_position"] ."\" style=\"cursor: default;\">
                        </td>
					</tr>";
	}

	?>

	<br />
	<table class="list">
		<tr class="listtitle">
			<td class="list"><?=$sortlinks["station"]?></td>
			<td class="list"><?=$sortlinks["hook"]?></td>
			<td class="list"><?=$sortlinks["tag"]?></td>
			<?if ($type == "userbound"):?> <td class="list"><?=$sortlinks["garmentuser"]?></td><? endif ?>
			<td class="list"><?=$sortlinks["article"]?></td>
			<td class="list"><?=$sortlinks["size"]?></td>
			<td class="list"><?=$sortlinks["modification"]?></td>
			<?if ($type == "deleted"):?> <td class="list"><?=$sortlinks["deleted_on"]?> </td><? endif ?>
            <td class="list">&nbsp;</td>
		</tr>
		<?=$tablerows?>
	</table>

    <?=$pagination?>

<? }?>