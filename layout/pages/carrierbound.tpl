<table class="list">
	<tr class="listtitle">
		<td class="listsmall"><?=$lang["distributorlocations"]?></td>
		<td class="listsmall"><?=$lang["userbound_carriers_reserved"]?></td>
	</tr>

	<? while ($row = db_fetch_assoc($listdata)): ?>
	<form id="<?=$row["id"]?>" enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
		<input type="hidden" name="id" value="<?=$row["id"]?>">
		<input type="hidden" name="dbData" value="1">
		<tr class="list" onClick="document.getElementById('<?=$row["id"]?>').submit();">
			<td class="list"><?=$row["name"]?></td>
			<td class="list"><?=(($row["has_userbound"] == "0") ? "<font style=\"color:#FF0000\">". $lang["no"] ."</font>" : "<font style=\"color:#009900\">". $lang["yes"] ."</font>") ?></td>
		</tr>
	</form>
	<? endwhile ?>
</table>