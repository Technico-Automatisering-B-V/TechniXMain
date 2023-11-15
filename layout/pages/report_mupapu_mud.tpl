<? if (db_num_rows($listdata) > 0): ?>

<table class="list">
	<tr class="listtitle">
		<td class="list"><?=$lang["day"]?></td>
		<td class="midlist"><?=$lang["time"]?></td>
		<td class="list"><?=$lang["description"]?></td>
	</tr>
	<? while ($row = db_fetch_assoc($listdata)): ?>
	<form id="<?=$row['id']?>" enctype="multipart/form-data" method="POST" action="<?=$pi['filename_details']?>">
		<input type="hidden" name="page" value="details" />
		<input type="hidden" name="id" value="<?=$row['id']?>" />
		<input type="hidden" name="gosubmit" value="true" />
		<tr class="list" onClick="document.getElementById('<?=$row['id']?>').submit();">
			<td class="list">
				<?
				$days[1] = $lang["monday"];
				$days[2] = $lang["tuesday"];
				$days[3] = $lang["wednesday"];
				$days[4] = $lang["thursday"];
				$days[5] = $lang["friday"];
				$days[6] = $lang["saturday"];
				$days[7] = $lang["sunday"];
				?>
				<?=(!empty($days[$row['day']]))?$days[$row['day']]:'Error!'?>
			</td>
			<td class="midlist"><?=(($row['hours']>9)?'':'0').$row['hours']?>:<?=(($row['minutes']>9)?'':'0').$row['minutes']?></td>
			<td class="list"><?=$row['description']?></td>
		</tr>
	</form>
	<? endwhile ?>
</table>

<? else: ?>

<?=$lang["no_items_found"]?><br />

<? endif ?>

<br />
<form method="POST">
    <input type="submit" name="mupapu" value="<?=$lang["back_to_mupapu"]?>" title="<?=$lang["back_to_mupapu"]?>" onclick="this.form.action='report_mupapu.php'; this.form.target='_self';" />
</form>
