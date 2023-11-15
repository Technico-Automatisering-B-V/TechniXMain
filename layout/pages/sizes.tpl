<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["sizegroup"]?>:</td>
                <td class="value"><?=html_selectbox_submit("sizegroup_id", $sizegroups, $urlinfo['sizegroup_id'], $lang["make_a_choice"])?></td>
            </tr>
        </table>
    </div>
</form>

<div class="clear" />

<?=$resultinfo?>

<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
<table class="list">
	<tr class="listtitle">
		<td class="list"><?=$lang["position"]?></td>
		<td class="midlist"><?=$lang["size"]?></td>
		<? if (!isset($urlinfo['search'])): ?>
		<td class="list"><?=$lang["order_by"]?></td>
		<? endif ?>
	</tr>
	<? $count = 1 ?>
	<? while ($row = db_fetch_assoc($listdata)): ?>
	<tr class="list">
		<form id="<?=$row['id']?>" enctype="multipart/form-data" method="POST" action="<?=$pi['filename_details']?>?limit_start=<?=$urlinfo['limit_start']?>&limit_num=<?=$urlinfo['limit_num']?>">
		<input type="hidden" name="page" value="details" />
		<input type="hidden" name="id" value="<?=$row['id']?>" />
		<input type="hidden" name="sizegroup_id" value="<?=$urlinfo['sizegroup_id']?>" />
		<input type="hidden" name="gosubmit" value="true" />
		<td align="center" onClick="document.getElementById('<?=$row['id']?>').submit();">
			<?=$row['position']?>
		</td>
		<td align="center" onClick="document.getElementById('<?=$row['id']?>').submit();">
			<?=$row['name']?>
		</td>
		</form>
		<? if (!isset($urlinfo['search'])): ?>
		<td class="listbutts">
			<? if ($urlinfo['limit_start'] == 0 && $count == 1): ?>
			<img src="layout/images/sizes_noup.png">
			<? else: ?>
			<form onClick="submit()" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF']?>?limit_start=<?=$urlinfo['limit_start']?>&limit_num=<?=$urlinfo['limit_num']?>&sizegroup_id=<?=$urlinfo['sizegroup_id']?>">
				<input type="hidden" name="movesize_position" value="<?=$row['position']?>" />
				<input type="hidden" name="movesize_direction" value="up" />
				<input type="hidden" name="sizegroup_id" value="<?=$urlinfo['sizegroup_id']?>" />
				<img src="layout/images/sizes_up.png" title="<?=$lang["position_up"]?>" />
			</form>
			<? endif ?>
			<? if (($urlinfo['limit_start'] + $count) >= $urlinfo['limit_total']): ?>
			<img src="layout/images/sizes_nodown.png">
			<? else: ?>
			<form onClick="submit()" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF']?>?limit_start=<?=$urlinfo['limit_start']?>&limit_num=<?=$urlinfo['limit_num']?>&sizegroup_id=<?=$urlinfo['sizegroup_id']?>">
				<input type="hidden" name="movesize_position" value="<?=$row['position']?>" />
				<input type="hidden" name="movesize_direction" value="down" />
				<input type="hidden" name="sizegroup_id" value="<?=$urlinfo['sizegroup_id']?>" />
				<img src="layout/images/sizes_down.png" title="<?=$lang["position_down"]?>" />
			</form>
			<? endif ?>
		</td>
		<? endif ?>
	</tr>
	<? $count++; ?>
	<? endwhile ?>
</table>

<?=$pagination?>
<? endif ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>