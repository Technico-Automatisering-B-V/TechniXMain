<script type="text/javascript">
<!--
function send_tag(myfield,e)
{
document.getElementById("tag_comments").innerHTML = "";

var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
      dataform.sent_a_tag.value = 1;
      myfield.form.submit();
      return false;
   }
else
   return true;
}
//-->
</script>

<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="page" value="<?=$pi["page"]?>">

	<? if (!empty($detailsdata["id"])): ?><input type="hidden" name="id" value="<?=$detailsdata["id"]?>"><? endif ?>

	<div id="tabs">
		<ul>
			<li><a href="#tab1"><?=$lang["despeckle"]?></a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<? if ($pi["page"] != "add"): ?>
				<tr>
					<td class="name"><?=$lang["created_on"]?>:</td>
					<td class="value">
						<?=(!empty($detailsdata["date_in"])) ? strftime($lang["dB_FULLDATE_FORMAT"], strtotime($detailsdata["date_in"])) : "<span class=\"empty\">". $lang["unknown"] ."</span>" ?>
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
						<input type="hidden" name="sent_a_tag" value="0">
						<input type="text" id="tag" name="tag" value="<?=(($garment["garments_tag"])?$garment["garments_tag"]:"")?>" size="30" onKeyPress="return send_tag(this,event)">
						<input type="submit" name="ok" value="<?=$lang["search"]?>" title="<?=$lang["search"]?>" onKeyPress="return submitenter(this,event)">
						<? endif ?>
					</td>
				</tr>
				<? if ($pi["page"] == "add"): ?>
				<?=(($tag_comments)? "<tr><td class=\"name\">&nbsp;</td><td class=\"small\" id=\"tag_comments\" valign=\"top\">". $tag_comments ."</td></tr>" : "") ?>
				<? endif ?>
				<? if (($pi["page"] == "add" && $tag_found) || ($pi["page"] == "details")): ?>
				<tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><?=(($tag_found)?$garment["articles_description"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><?=(($tag_found)?$garment["sizes_name"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["garmentmodification"]?>:</td><td class="value"><?=(($tag_found)?(($garment["modifications_name"])?$garment["modifications_name"]:"<span class=\"empty\">". $lang["none"] ."</span>"):"&nbsp;")?></td></tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<tr><td class="name"><?=$lang["current_washcount"]?>:</td><td class="value"><?=(($tag_found)?$garment["garments_washcount"]."x":"-")?></td></tr>
				<tr><td class="name"><?=$lang["times_repaired"]?>:</td><td class="value"><?=(($tag_found)?$counts["repairs"]."x":"-")?></td></tr>
				<tr><td class="name"><?=$lang["times_despeckled"]?>:</td><td class="value"><?=(($tag_found)?$counts["despeckles"]."x":"-")?></td></tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<tr>
					<td class="name">
						<? if ($pi["page"] != "add"): ?>
						<b><?=$lang["despeckle"]?>:</b>
						<? else: ?>
						<?=$lang["despeckle"]?>:
						<? endif ?>
					</td>
					<td class="value">
						<? if ($tag_found): ?>
							<? if ($pi["page"] != "add"): ?>
							<b><?=$despeckles[$detailsdata["despeckle_id"]]?></b>
							<? else: ?>
							<? html_selectbox_array("despeckle_id", $despeckles, $detailsdata["despeckle_id"]) ?>
							<? endif ?>
						<? else: ?>
							&nbsp;
						<? endif ?>
					</td>
				</tr>
				<tr>
					<td class="name">&nbsp;</td>
					<td class="value">
						<? if ($tag_found): ?>
							<input id="halt" name="halt" type="checkbox"<?=(isset($detailsdata["halt"]))?" checked=\"checked\"":""?><?=(($pi["page"]!="add")?" disabled=\"disabled\"":"")?>><label for="halt"><?=$lang["check_for_despeckle"]?></label>
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
	<input type="submit" name="detailssubmit" value="<?=$lang["add_and_close"]?>" title="<?=$lang["add_and_close"]?>"<?=($tag_found)?"":" disabled=\"disabled\""?>>
	<? endif ?>
	<input type="submit" name="detailssubmitnone" value="<?=$lang["close"]?>" title="<?=$lang["close"]?>">

	<? if ($pi["page"] == "details" && $detailsdata["status"] == 1): ?>
	&nbsp;<input type="submit" name="finalize" value="<?=$lang["finalize_despeckle"]?>" title="<?=$lang["finalize_despeckle"]?>">
	<? endif ?>

</form>

<script type="text/javascript">
	$(function() {
		$("#tag").focus();
	});
</script>