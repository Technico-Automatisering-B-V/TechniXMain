<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="page" value="<?=$pi["page"]?>">

	<?
	if (!empty($detailsdata["id"])){ echo "<input type=\"hidden\" name=\"id\" value=\"". $detailsdata["id"] ."\">"; }
	if (!empty($garment["garments_id"])){ echo "<input type=\"hidden\" name=\"garments_id\" value=\"". $garment["garments_id"] ."\">"; }
	?>

	<div id="tabs">
		<ul>
			<li><a href="#tab1"><?=$lang["repair"]?></a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<? if ($pi["page"] != "add"): ?>
				<tr>
					<td class="name"><?=$lang["created_on"]?>:</td>
					<td class="value">
						<?=(!empty($detailsdata["date_in"])) ? strftime($lang["dB_FULLDATE_FORMAT"], strtotime($detailsdata["date_in"])) : $lang["unknown"] ?>
						<?=(!empty($detailsdata["id"])) ? "(ID " . $detailsdata["id"] . "&" . $detailsdata["garment_id"] . ")" : "" ?>
					</td>
				</tr>
				<tr>
					<td class="name" colspan="2">&nbsp;</td>
				</tr>
				<? endif ?>
				<tr>
					<td class="name"><?=$lang["tag"]?>:</td>
					<td class="value">
						<? if ($pi["page"] != "add"): ?>
						<?=$garment["garments_tag"]?>
						<? else: ?>
						<input type="text" id="garments_tag" name="garments_tag" value="<?=(($garment["garments_tag"])?$garment["garments_tag"]:"")?>" size="30">
						<input type="submit" name="search" value="<?=$lang["search"]?>" title="<?=$lang["search"]?>">
						<? endif ?>
					</td>
				</tr>
				<? if ($pi["page"] == "add"): ?>
				<?=(($tag_comments) ? "<tr><td class=\"name\">&nbsp;</td><td class=\"small\" id=\"tag_comments\" valign=\"top\">". $tag_comments ."</td></tr>" : "")?>
				<? endif ?>
				<? if (!empty($garment["garments_id"])): ?>
				<tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><?=$garment["articles_description"]?></td></tr>
				<tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><?=$garment["sizes_name"]?></td></tr>
				<tr><td class="name"><?=$lang["garmentmodification"]?>:</td><td class="value"><?=((!empty($garment["modifications_name"])) ? $garment["modifications_name"] : "<span class=\"empty\">". $lang["none"] ."</span>") ?></td></tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<tr><td class="name"><?=$lang["current_washcount"]?>:</td><td class="value"><?=$garment["garments_washcount"]."x"?></td></tr>
				<tr><td class="name"><?=$lang["times_repaired"]?>:</td><td class="value"><?=$counts["repairs"]."x"?></td></tr>
				<tr><td class="name"><?=$lang["times_despeckled"]?>:</td><td class="value"><?=$counts["despeckles"]."x"?></td></tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<tr>
					<td class="name">
						<? if ($pi["page"] != "add"): ?>
						<strong><?=$lang["repair"]?>:</strong>
						<? else: ?>
						<?=$lang["repair"]?>:
						<? endif ?>
					</td>
					<td class="value">
							<? if (!empty($garment["garments_id"])): ?>
							<? if ($pi["page"] != "add"): ?>
							<strong><?=$repairs[$detailsdata["repair_id"]]?></strong>
							<? else: ?>
							<? html_selectbox_array("repair_id", $repairs, $detailsdata["repair_id"]) ?>
							<? endif ?>
						<? else: ?>
							&nbsp;
						<? endif ?>
					</td>
				</tr>
				<tr>
					<td class="name">&nbsp;</td>
					<td class="value">
						<? if (!empty($garment["garments_id"])): ?>
							<input id="halt" name="halt" type="checkbox"<?=(isset($detailsdata["halt"]))?" checked=\"checked\"":""?><?=(($pi["page"]!="add")?" disabled=\"disabled\"":"")?>><label for="halt"><?=$lang["check_for_repair"]?></label>
						<? else: ?>
							&nbsp;
						<? endif ?>
					</td>
				</tr>
				<? endif ?>
			</table>
		</div>
	</div>

	<? if ($pi["page"] == "add"): ?>
	<input type="submit" name="detailssubmit" value="<?=$lang["add_and_close"]?>" title="<?=$lang["add_and_close"]?>"<?=(!empty($garment["garments_id"]))?"":" disabled=\"disabled\""?>>
	<? endif ?>
	<input type="submit" name="detailssubmitnone" value="<?=$lang["close"]?>" title="<?=$lang["close"]?>">

	<? if ($pi["page"] == "details" && $detailsdata["status"] == 1): ?>
	&nbsp;<input type="submit" name="finalize" value="<?=$lang["finalize_repair"]?>" title="<?=$lang["finalize_repair"]?>">
	<? endif ?>

</form>

<script type="text/javascript">
	$(function() {
		$("#garments_tag").focus();
	});
</script>