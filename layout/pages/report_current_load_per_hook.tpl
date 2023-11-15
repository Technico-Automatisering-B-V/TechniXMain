<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER['PHP_SELF']?>">

<? if ($urlinfo['limit_total'] != 0): ?>

<table class="list">
	<tr class="listtitle">
		<td class="list"><?=$sortlinks['distributor']?></td>
		<td class="list"><?=$sortlinks['date']?></td>
		<td class="list"><?=$sortlinks['hook']?></td>
		<td class="list"><?=$sortlinks['tag']?></td>
	<!--//	<td class="list"><?=$sortlinks['articlegroup']?></td> -->
		<td class="list"><?=$sortlinks['article']?></td>
		<td class="list"><?=$sortlinks['size']?></td>
	</tr>
	<? while ($row = db_fetch_assoc($listdata)): ?>
	<form id="<?=$row['garments_id']?>" enctype="multipart/form-data" method="POST" action="garment_details.php">
		<input type="hidden" name="page" value="details">
		<input type="hidden" name="id" value="<?=$row['garments_id']?>">
		<input type="hidden" name="gosubmit" value="true">
		<? if (isset($pi['useraccess']['details'])): ?>
		<tr class="list" onClick="document.getElementById('<?=$row['garments_id']?>').submit();">
		<? else: ?>
		<tr class="list">
		<? endif ?>
			<td class="midlist"><?=$row['distributors_doornumber']?></td>
			<td class="list"><?=$row['distributors_load_date_in']?></td>
			<td class="midlist"><?=$row['distributors_load_hook']?></td>
			<td class="list"><?=$row['garments_tag']?></td>
		<!--//	<td class="list"><?=$row['articlegroups_name']?></td> -->
			<td class="list"><?=$row['articles_description']?></td>
			<td class="midlist"><?=$row['sizes_name']?></td>
		</tr>
	</form>
	<? endwhile ?>
</table>

<?=$pagination?>
<? endif ?>

</form>
