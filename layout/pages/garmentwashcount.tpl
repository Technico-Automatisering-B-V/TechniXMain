<table class="list">
	<tr class="listtitle">
		<td class="listsmall"><?=$lang["distributorlocations"]?></td>
		<td class="listsmall"><?=$lang["check_wash"]?></td>
		<td class="listsmall"><?=$lang["certain_date"]?></td>
		<td class="listsmall"><?=$lang["from_date"]?></td>
		<td class="listsmall"><?=$lang["to_date"]?></td>
	</tr>

	<? while ($row = db_fetch_assoc($listdata)): ?>
	<form id="<?=$row['id']?>" enctype="multipart/form-data" method="POST" action="<?=$pi['filename_details']?>">
		<input type="hidden" name="id" value="<?=$row['id']?>">
		<input type="hidden" name="dbData" value="1">
		<tr class="list" onClick="document.getElementById('<?=$row['id']?>').submit();">
			<td class="list"><?=$row['name']?></td>
			<td class="midlist"><?=(($row['washcount_check_enabled'] == "0") ? "<font style='color:#FF0000'>". $lang["no"] ."</font>" : "<font style='color:#009900'>". $lang["yes"] ."</font>") ?></td>
			<td class="midlist"><?=(($row['washcount_check_from'] == NULL) ? $lang["no"] : $lang["yes"]) ?></td>
			<td class="midlist"><?=(($row['washcount_check_from'] !== NULL) ? $row['washcount_check_from'] : "<span class=\"empty\">-</span>") ?></td>
			<td class="midlist"><?=(($row['washcount_check_to'] !== NULL) ? $row['washcount_check_to'] : "<span class=\"empty\">-</span>") ?></td>
		</tr>
	</form>
	<? endwhile ?>
</table>