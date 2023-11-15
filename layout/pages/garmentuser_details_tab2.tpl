<?php

if ($articlelink["enabled"] == "1"){
    $aldisplay = "block";
    $alon = " checked=\"checked\"";
    $aloff = "";
}else{
    $aldisplay = "none";
    $alon = "";
    $aloff = " checked=\"checked\"";
}

foreach($sizes_selected as $id => $value){
  echo '<input type="hidden" name="sizes_selected_history['.$id.']" value="'. $value. '">';
}

?>

<table class="detailstab">
	<tr><td class="value" width="250"><?=$lang["profession"]?>: <?=html_selectbox_submit("profession_id", $professions, $gu_data["profession_id"], $lang["make_a_choice"]) ?></td></tr>
</table>

<? $alts = (($alternatives == "1") ? true : false); ?>
<? if (!empty($articles_all) || ($_POST["page"] == "add" && !empty($_POST["profession_id"]))): ?>
<input type="hidden" id="select_article_id" name="select_article_id" value="0" />
<table class="detailstab">
    <tr>
        <td class="name"><?=$lang["allow_alternative_sizes"]?>:</td>
        <td class="value"><? html_radiobuttons_submit("alternatives", $alternativesswitch, $alternatives) ?></td>
    </tr>
</table>
    
<article id="dataset-wrap">
<table class="detailstab" id="dataset">
    <thead>
	<tr>
		<td class="listsmall"><?=$lang["access"]?></td>
		<td class="listsmall"><?=$lang["article"]?></td>
		<td class="listsmall" width="110"><?=$lang["size"]?></td>
		<td class="listsmall"><?=$lang["modification"]?></td>
		<? if ($alts): ?>
		<td class="listsmall"><?=$lang["alt_size"]?></td>
		<td class="listsmall"><?=$lang["alt_mod"]?></td>
		<? endif ?>
		<td class="listsmall"><?=$lang["credit_per_article"]?></td>
		<td class="listsmall"></td>
	</tr>
    </thead>
    <tbody>
	<?

	$article_checked = "";

	if ($alts) {
		$alt_article_checked = "";
	}

	foreach ($articles_all as $id => $name) {
		if (!empty($articles_selected[$id]) && $articles_selected[$id] > 0){
			$disabled = false;
			$disabledAttr = "";
			$class = "";
			$article_checked = " checked=\"checked\"";
		}else{
			$disabled = true;
			$disabledAttr = " disabled=\"disabled\"";
			$class = "class=\"disabled\" ";
			$article_checked = "";
		}
		?>
		<tr>
			<td class="midvalue"><input id="article<?=$id?>" name="articles_selected[<?=$id?>]" type="checkbox" value="<?=$id?>"<?=$article_checked?>></td>
			<td class="value"><label for="article<?=$id?>"><?=$name?></label></td>
			<td class="midvalue"><? html_selectbox_array_submit_onchange("sizes_selected[" . $id . "]", $articles_eachart_sizes[$id], "setSelectSubmit(this)", $sizes_selected[$id], null, true, $disabled) ?></td>
			<td class="midvalue"><? html_selectbox_array_submit("modifications_selected[" . $id . "]", $modifications_all[$id][$sizes_selected[$id]], $modifications_selected[$id], $lang["none"], true, $disabled) ?></td>

			<? if ($alts): ?>
			<td class="midvalue"><? html_selectbox_array_submit("alt_sizes_selected[" . $id . "]", $articles_eachart_sizes[$id], $alt_sizes_selected[$id], null, true, $disabled) ?></td>
			<td class="midvalue"><? html_selectbox_array_submit("alt_modifications_selected[" . $id . "]", $alt_modifications_all[$id][$alt_sizes_selected[$id]], $alt_modifications_selected[$id], $lang["none"], true, $disabled) ?></td>
			<? endif ?>

			<td><input <?=$class?>id="count[<?=$id?>]" <?=$disabledAttr?>type="text" name="count[<?=$id?>]" value="<?=((!empty($count[$id]))?$count[$id]:$articles_all_credit[$id])?>" size="10" /></td>
                        <td class="midvalue" style="visibility: hidden;"><?=((!empty($garmentusers_articles[$id]))?$garmentusers_articles[$id]:"")?></td>
		</tr>
		<?
	}
	?>
    </tbody>
</table>
</article>

<script type="text/javascript">
	$(function() {
		$("input[name*='articles_selected']").click(function(){
			var value = $(this).attr('checked');
			if (value == "checked"){
				$(this).closest("tr").find('select,input[type="text"]').attr('disabled', false).removeClass("disabled");
			}else{
				$(this).closest("tr").find('select,input[type="text"]').attr('disabled', true).addClass("disabled");
			}
		});
	});
</script>

<script type="text/javascript">
function setSelectSubmit(article_id)
{
    $("[id='select_article_id']").val(article_id.name.substring(article_id.name.indexOf('[')+1,article_id.name.indexOf(']')));
    document.dataform.submit();
}
</script>  

<? endif ?>
