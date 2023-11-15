<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF']?>">

<input type="hidden" name="page" value="<?=$pi['page']?>">
<? if (!empty($detailsdata['id'])): ?><input type="hidden" name="id" value="<?=$detailsdata['id']?>"><? endif ?>

<? if (!empty($pi['note'])) echo $pi['note'] ?>

<table class="details">
	<tr>
		<td class="name"><?=$lang["day"]?>:</td>
		<td class="value"><? html_selectbox_array("day", $days, $detailsdata['day']) ?></td>
	</tr>
	<tr>
		<td class="name"><?=$lang["time"]?>:</td>
		<td class="value"><? html_selectbox_array("hours", $hours, $detailsdata['hours']) ?>:<? html_selectbox_array("minutes", $minutes, $detailsdata['minutes']) ?></td>
	</tr>
	<tr>
		<td class="name"><?=$lang["description"]?>:</td>
		<td class="value"><input type="text" name="description" value="<?=$detailsdata['description']?>" size="25" /></td></td>
	</tr>

</table>

<?=html_submitbuttons_detailsscreen($pi)?>

<script type="text/javascript">document.dataform.name.focus();</script>

</form>
