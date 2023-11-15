<html>
<head><title>TechniX - <?=$lang["stock_list"]?> <?=date('d-m-Y')?></title>
</head>
<body>

<? if ($mupapu['t']['sto_cur'] > 0 || $mupapu['t']['sto_new'] > 0): ?>

<? $count = 0 ?>

<? foreach ($mupapu['mup'] as $ars => $row): ?>
	<? if ($mupapu['mup'][$ars]['sto_cur'] > 0 || $mupapu['mup'][$ars]['sto_new'] > 0): ?>

	<? $count++ ?>

	<? if ($count == 1): ?>
	<table style="font-size:12px; page-break-after:always;">
		<tr>
			<td class="listsmall" colspan="2" align="left">
				<b><?=$lang["stock_list"]?> - <?=$circulationgroups_name[$urlinfo["cid"]]?></b><br />
				<br />
				<b><?=$lang["total"]?> <?=$mupapu['t']['sto_cur']?> <?=strtolower($lang["measured"])?>, <?=$mupapu['t']['sto_new']?> <?=strtolower($lang["required"])?></b>
			</td>
			<td class="rightlistsmall" colspan="3" align="right">
				<b><?=date('d-m-Y')?></b><br /><br /><br />
			</td>
		</tr>
		<tr>
			<td style="font-size:12px;font-family:arial;" align="center" bgcolor="#CCCCCC" colspan="3">&nbsp;</td>
			<td width="2"></td>
			<td style="font-size:12px;font-family:arial;" align="center" bgcolor="#CCCCCC" colspan="3">&nbsp;</td>
		</tr>
		<tr style="font-size:11px;font-weight:bold;font-family:arial;">
			<td width="70"><?=$lang["articlenumber"]?></td>
			<td width="250"><?=$lang["description"]?></td>
			<td width="70" align="center"><?=$lang["size"]?></td>
			<td></td>
			<td width="50" align="center"><?=$lang["measured"]?></td>
			<td width="50" align="center"><?=$lang["required"]?></td>
			<td width="50" align="center"><?=$lang["complement"]?></td>
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
		<td style="<?=$style?>"><?=$row['articlecode']?></td>
		<td style="<?=$style?>"><?=$row['description']?></td>
		<td style="<?=$style?>" align="center"><?=$row['size']?><?=(!empty($row['modification'])) ? ' ' . $row['modification'] : ''?></td>
		<td style="<?=$style?>"></td>
		<td style="<?=$style?>" align="center"><?=$mupapu['mup'][$ars]['sto_cur']?></td>
		<td style="<?=$style?>" align="center"><?=$mupapu['mup'][$ars]['sto_new']?></td>
		<td style="<?=$style?>" align="center"><?=$mupapu['mup'][$ars]['sto_diff']?></td>
	</tr>

<? if ($count == 32): ?>
</table>
<br />
<? endif ?>

<? if ($count == 32) $count = 0; ?>

<? endif ?>
<? endforeach ?>

</table>

<script type="text/javascript">
<!--
	window.print();
-->
</script>

<? else: ?>
	<?=$lang["no_items_found"]?>
<? endif ?>

</body>
</html>
