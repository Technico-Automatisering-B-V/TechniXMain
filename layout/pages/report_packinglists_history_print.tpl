<html>
<head><title>TechniX - <?=$lang["packinglist"]?> <?=$header['date']?></title>
</head>
<body>

<? $count = 0 ?>

<? if (db_num_rows($listdata)): ?>

<? while ($row = db_fetch_assoc($listdata)): ?>
	<? $count++ ?>

	<? if ($count == 1): ?>

	<table style="font-size:12px; page-break-after:always;">

	<tr>
		<td class="listsmall" colspan="3" align="left">
			<b><?=$GLOBAL['config']['system_name']?></b><br />
			<br />
			<b><?=$lang["total"]?> <?=$header['total']?> <?=strtolower($lang["garments"])?></b>
		</td>
		<td class="rightlistsmall" colspan="4" align="right">
			<b><?=$header['date']?></b><br />
			<br />
			<b><?=$lang["packinglist_number"]?>: <?=$_GET['p']?></b>
		</td>
	</tr>

	<tr>
		<td style="font-size:12px;font-family:arial;" align="center" bgcolor="#CCCCCC">&nbsp;</td>
		<td width="2"></td>
		<td style="font-size:12px;font-family:arial;" align="center" bgcolor="#CCCCCC" colspan="3">&nbsp;</td>
		<td width="2"></td>
		<td style="font-size:12px;font-family:arial;" align="center" bgcolor="#CCCCCC">&nbsp;</td>
	</tr>
	<tr style="font-size:11px;font-weight:bold;font-family:arial;">
		<td width="40" align="center"><?=$lang["sb_ub"]?></td>
		<td></td>
		<td width="70"><?=$lang["code"]?></td>
		<td width="250"><?=$lang["description"]?></td>
		<td width="70" align="center"><?=$lang["size"]?></td>
		<td></td>
		<td width="50" align="center"><?=$lang["count"]?></td>
        </tr>
	<? endif ?>

	<?
	if (isset($desc_previous) && $desc_previous != $row['description']) {
		$style = 'border-top-color:black;border-top-style:solid;border-top-width:1px;';
	} else {
		$style = 'border-top:1px #BBBBBB solid;';
	}
	$desc_previous = $row['description'];
	?>

        <tr style="font-size:12px;font-family:arial;">
		<td style="<?=$style?>" align="center" height="23"
		<? if ($row['userbound'] == $lang["garmentuser"]): ?> bgcolor="#CCCCCC"<? endif ?>
		>
		<?=$row['userbound']?>
		</td>
		<td style="<?=$style?>"></td>
		<td style="<?=$style?>"><?=$row['articlecode']?></td>
		<td style="<?=$style?>"><?=$row['description']?></td>
		<td style="<?=$style?>" align="center"><?=$row['size']?></td>
		<td style="<?=$style?>"></td>
		<td style="<?=$style?>" align="center"><?=$row['count']?></td>
	</tr>

<? if ($count == 32): ?>
</table>
<br />
<? endif ?>

<? if ($count == 32) $count = 0; ?>

<? endwhile ?>

</table>

<? else: ?>
	<?=$lang["no_items_found"]?>
<? endif ?>

</body>
</html>

<? if (isset($_GET['print'])): ?>
<script type="text/javascript">
<!--
	window.print();
-->
</script>
<? endif ?>
