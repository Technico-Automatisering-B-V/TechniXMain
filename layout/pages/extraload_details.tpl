<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="page" value="<?=$pi["page"]?>">

	<div id="tabs">
		<ul>
			<li><a href="#tab1"><?=$lang["add_extra_load"]?></a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<tr>
					<td class="name"><?=$lang["distributorlocation"]?>:</td>
					<td class="value"><? html_selectbox("distributorlocation_id", $distributorlocations, $bindingdata["distributorlocation_id"], $lang["make_a_choice"], " style=\"width:300px\"") ?></td>
					<td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
				<tr>
					<td class="name"><?=$lang["article"]?>:</td>
					<td class="value"><? html_selectbox_array_submit("article_id", $articles, $bindingdata["article_id"], $lang["make_a_choice"], " style=\"width:300px\"") ?></td>
					<td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
				<? if (!empty($bindingdata["article_id"])): ?>
				<tr>
					<td class="name"><?=$lang["size"]?>:</td>
					<td class="value"><? html_selectbox_array_submit("size_id", $sizes, $bindingdata["size_id"], $lang["make_a_choice"]) ?></td>
					<td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
				<? if (!empty($bindingdata["size_id"])): ?>
				<tr>
					<td class="name"><?=$lang["garmentmodification"]?>:</td>
					<td class="value"><? html_selectbox_array("modification_id", $modifications, $bindingdata["modification_id"], $lang["none"]) ?></td>
					<td>&nbsp;</td>
				</tr>
				<? endif ?>
				<? endif ?>
				<tr>
					<td class="name"><?=$lang["demand"]?>:</td>
					<td class="value"><input type="text" name="demand" value="" size="4"> <?=$lang["pcs"]?></td>
					<td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
			</table>
		</div>
	</div>

	<?=html_submitbuttons_detailsscreen($pi)?>

</form>