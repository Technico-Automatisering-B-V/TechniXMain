<? if (!($circulationgroup_count <= 1 && empty($depositbatches_all))): ?>
<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	<div class="filter">
		<table>
			<? if ($circulationgroup_count > 1): ?>
				<tr>
					<td class="name"><?=$lang["location"]?>:</td>
					<td class="value" width="200"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
				</tr>
			<? endif ?>
			<? if (!empty($depositbatches_all)): ?>
			<tr>
				<td class="top right"><?=$lang["depositbatches"]?>:</td>
				<td class="value"><?=html_checkboxlist_array_submit("depositbatches_selected", $depositbatches_all, $depositbatches_selected, 1)?></td>
			</tr>
			<? endif ?>
		</table>
		<? if (!empty($listdata) && db_num_rows($listdata) > 0): ?>
		<div class="buttons">
			<input type="submit" name="export" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
                        <input type="submit" name="finalize" value="<?=$lang["checkout_only"]?>" title="<?=$lang["checkout_only"]?>" />
                        <input type="submit" name="email" value="<?=$lang["email_and_checkout"]?>" title="<?=$lang["email_and_checkout"]?>" />
                        <input type="submit" name="print" value="<?=$lang["print_and_checkout"]?>" title="<?=$lang["print_and_checkout"]?>" />      
		</div>
		<? endif ?>
	</div>
	<? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>
</form>
<? endif ?>
<div class="clear" />

<? if (!empty($listdata) && db_num_rows($listdata) > 0): ?>

<table class="list">
	<tr class="listtitle">
		<td class="midlist"><?=$lang["sb_ub"]?></td>
		<td class="list"><?=$lang["articlenumber"]?></td>
		<td class="list"><?=$lang["description"]?></td>
		<td class="midlist"><?=$lang["size"]?></td>
		<td class="midlist"><?=$lang["count"]?></td>
	</tr>
	<? $total_garments = 0 ?>
	<? while ($row = db_fetch_assoc($listdata)): ?>
	<? $total_garments += $row['count'] ?>
	<tr class="listnc">
		<td class="midlist"
			<? if ($row['userbound'] == $lang["garmentuser"]): ?> bgcolor="#CCCCCC"<? endif ?>
			>
			<?=$row['userbound']?>
		</td>
		<td class="midlist"><?=$row['articlecode']?></td>
		<td class="list"><?=$row['description']?></td>
		<td class="list"><?=$row['size']?></td>
		<td class="midlist"><?=$row['count']?></td>
	</tr>
	<? endwhile ?>
	<tr>
		<td class="right" colspan="4"><b><?=$lang["total"]?></b></td>
		<td class="mid"><b><?=$total_garments?></b></td>
	</tr>
</table>

<? elseif (!empty($depositbatches_all)): ?>
<table>
	<tr>
		<td class="midvalue">
			<?=$lang["no_depositbatches_selected"]?>
		</td>
	</tr>
</table>

<? else: ?>
<table>
	<tr>
		<td class="midvalue">
			<?=$lang["all_depositbatches_are_processed"]?>
		</td>
	</tr>
</table>
<? endif ?>

<? if ($print): ?><?=$print?>
<META HTTP-EQUIV="refresh" CONTENT="0;URL=<?=$pi['filename_this']?>">
<? endif ?>
