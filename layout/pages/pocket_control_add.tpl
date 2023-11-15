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
			<li><a href="#tab1">Zakken controle</a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<tr>
					<td class="name"><?=$lang["tag"]?>:</td>
					<td class="value">
						<? if ($pi["page"] != "add"): ?>
						<?=$detailsdata["tag"]?>
						<? else: ?>
						<input type="hidden" name="sent_a_tag" value="0">
						<input type="text" id="tag" name="tag" value="<?=(($garment["garments_tag"])?$garment["garments_tag"]:"")?>" size="30" onKeyPress="return send_tag(this,event)">
						<input type="submit" name="ok" value="<?=$lang["search"]?>" title="<?=$lang["search"]?>">
						<? endif ?>
					</td>
				</tr>
				<? if ($pi["page"] == "add"): ?>
				<?=(($tag_comments)? "<tr><td class=\"name\">&nbsp;</td><td class=\"small\" id=\"tag_comments\" valign=\"top\">". $tag_comments ."</td></tr>" : "") ?>
				<? endif ?>
				<? if (($pi["page"] == "add" && $tag_found) || ($pi["page"] == "details")): ?>
				<tr><td><strong><?=$lang["info"]?> <?=$lang["garment"]?></strong></td></tr>
				<tr><td class="name"><?=$lang["last_scanned"]?>:</td>
                                    <td class="value"><?=(!empty($garment["garments_lastscan"])) ? strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($garment["garments_lastscan"])) : "<span class=\"empty\">". $lang["never_scanned"] ."</span>" ?></td>
                                </tr>
				<tr><td class="name"><?=$lang["status"]?>:</td><td class="value"><?=(($tag_found)?$lang[$garment["scanlocations_translate"]]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["location"]?>:</td><td class="value"><?=(($tag_found)?$garment["circulationgroups_name"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><?=(($tag_found)?$garment["articles_description"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><?=(($tag_found)?$garment["sizes_name"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["userbound"]?>:</td><td class="value"><?=(($tag_found)?($garment["garments_garmentuser_id"] > 0 ? $lang["yes"] : $lang["no"]):"&nbsp;")?></td></tr>
                               
                                <tr><td><strong><?=$lang["last_used_by"]?></strong></td></tr>
				<tr><td class="name"><?=$lang["first_name"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["name"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["surname"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["surname"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["personnelcode"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["personnelcode"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["clientdepartment"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["clientdepartment"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["profession"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["profession"]:"&nbsp;")?></td></tr>
				<tr><td class="name"><?=$lang["email_address"]?>:</td><td class="value"><?=(($tag_found)?$garmentuser["email"]:"&nbsp;")?></td></tr>
				
                                
                                <tr><td><strong><?=$lang["found_item_registration"]?></strong></td></tr>
                                <? if ($found_item_type_count > 0){ ?>
                                        <tr>
                                            <td class="name"><?=$lang["found_item"]?>:</td>
                                            <td class="value"><?=html_selectbox("found_item_type_id", $found_item_types, $detailsdata["found_item_type_id"], $lang["make_a_choice"]);?></td>
                                        </tr>
                                <? } ?>
                                
				<? endif ?>
			</table>
		</div>
	</div>

	<? if ($pi["page"] == "add"): ?>
	<input type="submit" name="detailssubmit" value="<?=$lang["add_and_close"]?>" title="<?=$lang["add_and_close"]?>"<?=($tag_found)?"":" disabled=\"disabled\""?>>
	<? endif ?>
	<input type="submit" name="detailssubmitnone" value="<?=$lang["close"]?>" title="<?=$lang["close"]?>">

</form>

<? if ($pi["page"] == "add"): ?>
	<script type="text/javascript">
		$(function() {
				$("#tag").focus();
			});
	</script>
<? endif ?>