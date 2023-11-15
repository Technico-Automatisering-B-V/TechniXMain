<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">

	<? $readonly = (isset($pi["useraccess"]["edit"])) ? null : " readonly" ?>

	<input type="hidden" name="page" value="<?=$pi["page"]?>">
	<? if (!empty($detailsdata["id"])): ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>">
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? endif ?>

	<div id="tabs">
		<ul>
			<li><a href="#tab1"><?=$lang["garmentprofile"]?></a></li>
		</ul>
		<div id="tab1">
			<table class="detailstab">
				<tr>
					<td class="name"><?=$lang["profession"]?>:</td>
					<td class="value"><? html_selectbox("profession_id", $professions, $detailsdata["profession_id"], $lang["make_a_choice"]) ?></td>
					<td class="value"> <button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
				<tr>
					<td class="name"><?=$lang["article"]?>:</td>
					<td class="value"><? html_selectbox("article_id", $articles, $detailsdata["article_id"], $lang["make_a_choice"]) ?></td>
					<td class="value"> <button class="required" title="<?=$lang["field_required"]?>">*</button></td>
				</tr>
			</table>
		</div>
	</div>

	<?=html_submitbuttons_detailsscreen($pi)?>

</form>