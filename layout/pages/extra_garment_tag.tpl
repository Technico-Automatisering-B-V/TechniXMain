<script TYPE="text/javascript">
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

function keep_tag(myfield,e)
{
document.getElementById("tag_comments").innerHTML = "";

var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
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
			<li><a href="#tab1">Chipcode toevoegen</a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<? if ($pi["page"] != "add"): ?>
				<tr>
					<td class="name"><?=$lang["replaced_on"]?>:</td>
					<td class="value">
						<?=(!empty($detailsdata["datetime"])) ? strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($detailsdata["datetime"])) : "<span class=\"empty\">". $lang["unknown"] ."</span>" ?>
						<?=(!empty($detailsdata["id"])) ? "(ID " . $detailsdata["id"] . "&" . $detailsdata["garment_id"] . ")" : "" ?>
					</td>
				</tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<? endif ?>
				<tr>
					<td class="name">Barcode:</td>
					<td class="value">
						<? if ($pi["page"] != "add"): ?>
						<?=$detailsdata["old_tag"]?>
						<? else: ?>
						<input type="hidden" name="sent_a_tag" value="0">
						<input type="text" id="old_tag" name="old_tag" value="<?=(($garment["garments_tag"])?$garment["garments_tag"]:"")?>" size="30" onKeyPress="return send_tag(this,event)">
						<input type="submit" name="ok" value="<?=$lang["search"]?>" title="<?=$lang["search"]?>">
						<? endif ?>
					</td>
				</tr>
				<? if ($pi["page"] == "add"): ?>
				<?=(($tag_comments)? "<tr><td class=\"name\">&nbsp;</td><td class=\"small\" id=\"tag_comments\" valign=\"top\">". $tag_comments ."</td></tr>" : "") ?>
				<? endif ?>
				<? if (($pi["page"] == "add" && $tag_found) || ($pi["page"] == "details")): ?>
				<tr>
					<td class="name"><?=$lang["new_tag"]?>:</td>
					<td class="value">
						<? if ($pi["page"] != "add"): ?>
						<?=$detailsdata["new_tag"]?>
						<? else: ?>
						<input type="text" id="new_tag" name="new_tag" value="<?=(($garment["garments_tag2"])?$garment["garments_tag2"]:"")?>" size="30" onKeyPress="return keep_tag(this,event)"<?=($tag_found)?"":" disabled=\"disabled\""?> /> <button class="required" title="<?=$lang["field_required"]?>">*</button>
						<? endif ?>
					</td>
				</tr>
				<tr><td class="name" colspan="2">&nbsp;</td></tr>
				<tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><?=(($tag_found)?$garment["articles_description"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><?=(($tag_found)?$garment["sizes_name"]:"&nbsp;")?></td></tr>
				<? endif ?>
			</table>
		</div>
	</div>

	<? if ($pi["page"] == "add"): ?>
	<input type="submit" name="detailssubmit" value="Chipcode toevoegen" title="Chipcode toevoegen"<?=($tag_found)?"":" disabled=\"disabled\""?>>
	<? endif ?>
	<input type="submit" name="detailssubmitnone" value="<?=$lang["close"]?>" title="<?=$lang["close"]?>">

</form>

<? if ($pi["page"] == "add"): ?>
	<script type="text/javascript">
		$(function() {
			<? if ($tag_found): ?>
				$("#new_tag").focus();
			<? else: ?>
				$("#old_tag").focus();
			<? endif ?>
			});
	</script>
<? endif ?>