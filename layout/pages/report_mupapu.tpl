<?
$distributorlocations_string = "";
foreach ($circulationgroups_distributorlocations as $did => $val):
	if (isset($i)) $distributorlocations_string .= ", ";
	$distributorlocations_string .= ((isset($n)) ? "\n" : "") . $val["name"] . " (" . $val["hostname"] . ")";
	$n = true;
endforeach;
?>

<form id="mupapu" enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">

<script type="text/javascript">
<!--
  var showMode = "table-cell";
  if (document.all) showMode="block";

  function toggleVis(btn){
    btn   = document.forms["mupapu"].elements[btn];
    cells = document.getElementsByName("t"+btn.name);
    mode = btn.checked ? showMode : "none";

    for(j = 0; j < cells.length; j++) cells[j].style.display = mode;
  }
//-->
</script>

<script language="javascript"> 
<!--
  function toggle_info() {
    var toggle_this = document.getElementById("info");
    var toggle_button = document.getElementById("button_toggle_info");
    if (toggle_this.style.display == "block") {
      toggle_this.style.display = "none";
      toggle_button.innerHTML = "&darr; Periodeinformatie weergeven &darr;";
    }
    else {
      toggle_this.style.display = "block";
      toggle_button.innerHTML = "&uarr; Periodeinformatie verbergen &uarr;";
    }
  }
//-->
</script>

<div class="filter">
    <table class="filter">
        <tr>
            <td width="130"><?=$lang["weeks_in_past"]?>:</td>
            <td><input class="spinner" style="text-align: center" name="numweeks" value="<?=$mupapu["numweeks"]?>" size="1" onclick="document.getElementById('write_to_dist').disabled=true;" /></td>
            <td class="midright small">
                <strong>Laatste keer weggeschreven: <?=((!empty($GLOBALS["config"]["mupapu_last_update"]))?$GLOBALS["config"]["mupapu_last_update"] : $lang["unknown"])?></strong>
            </td>
        </tr>
        <tr>
            <td class="midleft"><?=$lang["locationgroup"]?>:</td>
            <td><? html_selectbox("cid", $circulationgroups, $urlinfo["cid"], null, " onclick=\"document.getElementById('write_to_dist').disabled=true;\"") ?></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="left">
                <input type="submit" name="generate" value="Weergeven" title="MUPAPU berekening weergeven voor de geselecteerde locatie" onclick="this.form.action='report_mupapu.php'; this.form.target='_self';" />
                <input id="write_to_dist" type="submit" name="write_to_dist" value="Wegschrijven" title="Beladingsadvies wegschrijven voor de geselecteerde locatiegroep" onClick="if(confirm('Het beladingsadvies zal voor de geselecteerde locatiegroep worden\nherberekend en overschreven op basis van <?=$mupapu["numweeks"]?> weken uitgifte-historie.\n\nDe geselecteerde locatiegroep bevat de machinelocatie<?=((count($circulationgroups_distributorlocations)==1)?"":"s")?>:\n &raquo; <?=str_replace("\n", "\n &raquo; ", $distributorlocations_string)?>\n\nHandmatig toegevoegde beladingen blijven behouden.\n\nDoorgaan?')){this.form.action='report_mupapu.php'; this.form.target='_self';}else{return false}">
            </td>
            <td>&nbsp;</td>
            <?/*
            <td class="midright">
                 <input style="width:100px" type="submit" name="settings" value="<?=$lang["settings"]?>" title="<?=$lang["settings"]?>" onclick="this.form.action='report_mupapu_mud.php'; this.form.target='_self';">
            </td> */?>
        </tr>
    </table>
</div>

<? if (!empty($mupapu["mup"])): ?>

<font style="font-size: 2px;"><br /></font>         

<table class="reportfilter" style="width: 100%;">
	<tr id="info" style="border-bottom:1px solid #cccccc; display: none;">
		<td class="top left small">
			<? foreach ($mupapu["periods"] as $week_key => $week_periods): ?>
			<? $i=1; ?>
                        <? $period_array = array(); ?>
                        <div style="min-width: 400px; float: left; padding: 5pt;" onmouseover="this.style.background='#25814E';this.style.color='#FFFFFF';" onmouseout="this.style.background='';this.style.color='#000000';">
				<strong><?=($week_key+1)?>e week:</strong><br />
				<? foreach ($week_periods as $key => $period): ?>
                                <? $current_period=$key+$last_period; ?>
                                
				<? $string="P";?><? if ($current_period<=$periods_num){ $string.=$current_period; }else{ $string.=$i; $i=$i+1; } ?><? $string.=": ";?> 
					<? $string.=lang(date("l", $period["from"]))." "?> 
					<? $string.=lang(date("d", $period["from"]))." "?> 
					<? $string.=strtolower(lang(date("F", $period["from"])))." "?> 
					<? $string.=lang(date("H", $period["from"]))?><? $string.=":";?><? $string.=lang(date("i", $period["from"]))." "?>
				    <? $string.="t/m"." ";?>
					<? $string.=strtolower(lang(date("l", $period["to"])))." "?> 
					<? $string.=lang(date("d", $period["to"]))." "?> 
					<? $string.=strtolower(lang(date("F", $period["to"])))." "?> 
					<? $string.=lang(date("H", $period["to"]))?><? $string.=":";?><? $string.=lang(date("i", $period["to"]))?><? $string.="<br />";?>
                                    <? array_push($period_array, $string); ?>
				<? endforeach ?>
                                <? sort($period_array); $arrlength=count($period_array); ?>
                                <? for($x=0;$x<$arrlength;$x++){ echo $period_array[$x];} ?>
			</div>
			<? endforeach ?>
		</td>
	</tr>
        
	<tr style="text-align: center;">
		<td class="midmid small">
			<div style="float: left; text-align: left"><a id="button_toggle_info" href="javascript:toggle_info();">&darr; Periodeinformatie weergeven &darr;</a></div>
			<div style="float: right; text-align: right">
			<?=$lang["rendering"]?>:
			<input type=checkbox name="repcol-id" id="repcol-id" onclick="toggleVis(this.name)" checked><label for="repcol-id">ID</label>
			<input type=checkbox name="repcol-dpp" id="repcol-dpp" onclick="toggleVis(this.name)" checked><label for="repcol-dpp"><?=$lang["distributions"]?></label>
			<input type=checkbox name="repcol-load" id="repcol-load" onclick="toggleVis(this.name)" checked><label for="repcol-load"><?=$lang["load"]?></label>
			<input type=checkbox name="repcol-circ" id="repcol-circ" onclick="toggleVis(this.name)" checked><label for="repcol-circ"><?=$lang["circulation"]?></label>
			</div>
		</td>
	</tr>
</table>

<? if (isset($_POST["write_to_dist"])): ?>

<font style="font-size: 2px;"><br /></font>

<table class="reportfilter" style="width: 100%;">
	<tr>
		<td class="midleft small">
			<iframe src="mupapu_to_distrib_now.php?cid=<?=$urlinfo["cid"]?>&numweeks=<?=$mupapu["numweeks"]?>" frameborder="0" height="120" width="100%" scrolling="no">Your browser was not capable to show content here.</iframe> 
		</td>
	</tr>
</table>

<? endif ?>

<input type="hidden" name="report_generated" value="true" />

<table id="report" class="list" style="width: 100%; font-size: 12px">
	<thead>
	<tr class="listtitle">
		<th name="trepcol-id" id="trepcol-id" class="muColTitle">&nbsp;</th>
		<th name="trepcol-id" id="trepcol-id" class="listspace" width="2"></th>
		<th class="muColTitle" colspan="2"><?=$lang["article"]?></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="listspace" width="2"></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="muColTitle" style="min-width: 150px;" colspan="<?=sizeof($mupapu["periods"][0])?>"><?=$lang["distributions_per_period"]?></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="listspace" width="2"></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="muColTitle" style="min-width: 110px;" colspan="2"><?=$lang["distributions"]?> <?=strtolower($lang["total"])?></th>
		<th name="trepcol-load" id="trepcol-load" class="listspace" width="2"></th>
		<th name="trepcol-load" id="trepcol-load" class="muColTitle" colspan="2"><?=$lang["load"]?></th>
		<th name="trepcol-circ" id="trepcol-circ" class="listspace" width="2"></th>
		<th name="trepcol-circ" id="trepcol-circ" class="muColTitle" colspan="3"><?=$lang["circulation"]?></th>
	</tr>
	<tr class="listtitle">
		<th name="trepcol-id" id="trepcol-id" class="muColHeader">ID</th>
		<th name="trepcol-id" id="trepcol-id" class="listspace"></th>
		<th class="muColHeader" style="width:80%; text-align:left"><?=$lang["description"]?></th>
		<th class="muColHeader" style="width:15%;" title="<?=$lang["size_and_modification"]?>"><?=$lang["size"]?></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="listspace"></th>
                <? $i=1; ?>
		<? foreach ($mupapu["periods"][0] as $id => $period): ?>
                        <? $current_period=$id+$last_period; ?> 
			<th name="trepcol-dpp" id="trepcol-dpp" class="muColHeader">P<? if ($current_period<=$periods_num){ print($current_period); }else{ print($i); $i=$i+1; } ?></th>
		<? endforeach ?>

		<th name="trepcol-dpp" id="trepcol-dpp" class="listspace"></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["distributions"]?> <?strtolower($lang["total"])?></u></strong><br /><br /><?=$mupapu["t"]["hit.t"]?> <?=strtolower($lang["distributions"])?><br /><?=$mupapu["t"]["miss.t"]?> <?=strtolower($lang["misseized"])?><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu["t"]["hitmiss.t"]?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["total"]?></a></th>
		<th name="trepcol-dpp" id="trepcol-dpp" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["highest_period_total"]?></u></strong><br /><br /><?=$mupapu["t"]["hit.ht"]?> <?=strtolower($lang["distributions"])?><br /><?=$mupapu['t']['miss.ht']?> <?=strtolower($lang["misseized"])?><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu['t']['hitmiss.ht']?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["highest"]?></a></th>
		<th name="trepcol-load" id="trepcol-load" class="listspace"></th>
		<th name="trepcol-load" id="trepcol-load" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["current_required_load"]?></u></strong><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu['t']['demand']?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["required"]?><br /><?=strtolower($lang["now"])?></a></th>
		<th name="trepcol-load" id="trepcol-load" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["new_required_load"]?></u></strong><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu['t']['demand_new']?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["required"]?><br /><?=strtolower($lang["new"])?></a></th>
		<th name="trepcol-circ" id="trepcol-circ" class="listspace"></th>
		<th name="trepcol-circ" id="trepcol-circ" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["current_circulation"]?></u></strong><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu['t']['cir_cur']?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["measured"]?></a></th>
		<th name="trepcol-circ" id="trepcol-circ" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["new_circulation"]?></u></strong><br /><br /><strong><?=$lang["total"]?>:</strong><br /><?=$mupapu['t']['cir_new']?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><a><?=$lang["calculated"]?></a></th>
		<th name="trepcol-circ" id="trepcol-circ" class="muColHeader" onmouseover="popup('<strong><u><?=$lang["complement"]?></u></strong><br /><br /><strong><?=$lang["total"]?>:</strong><br />Er mogen <?=$mupapu['t']['cir_diff_neg']?> kledingstukken uit roulatie.<br /><br />Er is een aanvulling van <?=$mupapu["t"]["cir_diff_pos"]?> kledingstukken benodigd.')" onmouseout="kill()"><a><?=$lang["complement"]?></a></th>
	</tr>
	</thead>

	<? $i = 1 ?>
	<? foreach ($mupapu["mup"] as $ars => $row): ?>

	<input type="hidden" name="mup[<?=$ars?>][arsimo_id]" value="<?=$row["arsimo_id"]?>">
	<input type="hidden" name="mup[<?=$ars?>][demand_new]" value="<?=$row["demand_new"]?>">

	<tbody>
	<tr <?=(($i % 2 == 0)?'class="listnc"':'class="listnc2"')?>>
		<? $i++ ?>

		<td name="trepcol-id" id="trepcol-id" class="midlistsmall"><?=$ars?></td>
		<td name="trepcol-id" id="trepcol-id" class="listspace"></td>
		<td class="listsmall" style="width:80%; height:16px;">
			<?/*
			<? if (strlen($row['description']) > 23): ?>
				<?=substr($row['description'], 0, 23)?>...
			<? else: ?>
				<?=$row['description']?>
			<? endif ?>
			*/?>
			<?=ucfirst($row["description"])?>
		</td>
		<td class="midlistsmall"><?=$row['size']?><?=(!empty($row["modification"])) ? " ". $row["modification"] : ""?></td>
		<td name="trepcol-dpp" id="trepcol-dpp" class="listspace"></td>
                
                <? $i=1; ?>
		<? for ($p=0; isset($mupapu["mup"][$ars]["hitmiss_p". $p .".h"]); $p++): ?>
                 <? $current_period=$p+$last_period; ?> 
		<td name="trepcol-dpp" id="trepcol-dpp" class="midlistsmall" onmouseover="popup('<strong><u><?=$lang["period"]?> <? if ($current_period<=$periods_num){ print($current_period); }else{ print($i); $i=$i+1; } ?></u></strong><br /><br /><strong><?=$lang["total_weekly"]?>:</strong><br /><?=$mupapu["mup"][$ars]["weeks_info.p". $p]?><br /><strong>De hoogste is <?=$mupapu["mup"][$ars]["hitmiss_p". $p .".h"]?>, waarvan:</strong><br /><?=$lang["distributed"]?>: <?=$mupapu["mup"][$ars]["hit_p". $p .".h"]?><br /><?=$lang["misseized"]?>: <?=$mupapu["mup"][$ars]["miss_p". $p .".h"]?>')" onmouseout="kill()">
			<? $red = ($mupapu["mup"][$ars]["miss_p". $p .".h"] > 0) ? true : false ?>
			<?=($red)?'<font color="red">':''?><?=$mupapu["mup"][$ars]["hitmiss_p".$p.".h"]?><?=($red)?"</font>":""?>
		</td>
		<? endfor ?>

		<td name="trepcol-dpp" id="trepcol-dpp" class="listspace"></td>
		<td name="trepcol-dpp" id="trepcol-dpp" class="midlistsmall" onmouseover="popup('<strong><u><?=$lang["article_total"]?></u></strong><br /><br /><?=$lang["distributed"]?>: <?=$mupapu["mup"][$ars]["hit.ht"]?><br /><?=$lang["misseized"]?>: <?=$mupapu['mup'][$ars]['miss.ht']?>')" onmouseout="kill()">
			<? $red = ($mupapu["mup"][$ars]["miss.ht"] > 0) ? true : false ?>
			<?=($red)?'<font color="red">':''?><?=$mupapu["mup"][$ars]["hitmiss.ht"]?><?=($red)?"</font>":""?>
		</td>
		<td name="trepcol-dpp" id="trepcol-dpp" class="midlistsmall" onmouseover="popup('<strong><u><?=$lang["highest_period"]?></u></strong><br /><br /><?=$lang["highest"]?> <?=strtolower($lang["distributed"])?>: <?=$mupapu["mup"][$ars]["hit.h"]?><br /><?=$lang["highest"]?> <?=strtolower($lang["misseized"])?>: <?=$mupapu["mup"][$ars]["miss.h"]?>')" onmouseout="kill()">
			<? $red = ($mupapu["mup"][$ars]["miss.h"] > 0) ? true : false ?>
			<?=($red)?'<font color="red">':''?><?=$mupapu["mup"][$ars]["hitmiss.h"]?><?=($red)?"</font>":""?>
		</td>

		<td name="trepcol-load" id="trepcol-load" class="listspace"></td>
		<td name="trepcol-load" id="trepcol-load" class="midlistsmall" onmouseover="popup('<?=$lang["current_required_load"]?>')" onmouseout="kill()">
			<?=$mupapu["mup"][$ars]["demand"]?>
		</td>
		<td name="trepcol-load" id="trepcol-load" class="midlistsmall" onmouseover="popup('<?=$lang["new_required_load"]?>')" onmouseout="kill()">
			<?=$mupapu["mup"][$ars]["demand_new"]?>
		</td>

		<td name="trepcol-circ" id="trepcol-circ" class="listspace"></td>
		<td name="trepcol-circ" id="trepcol-circ" class="midlistsmall" onmouseover="popup('<?=$lang["current_circulation"]?>')" onmouseout="kill()">
			<?=$mupapu["mup"][$ars]["cir_cur"]?>
		</td>
		<td name="trepcol-circ" id="trepcol-circ" class="midlistsmall" onmouseover="popup('<?=$lang["new_circulation"]?>')" onmouseout="kill()">
			<?=$mupapu["mup"][$ars]["cir_new"]?>
		</td>
		<td name="trepcol-circ" id="trepcol-circ" class="midlistsmall" onmouseover="popup('<?=$lang["difference_current_new"]?>')" onmouseout="kill()"
            <? if ($row["cir_diff"] > 0): ?> bgcolor="#FFCCCC"<? endif ?>
            <? if ($row["cir_diff"] < 0): ?> bgcolor="#FFEEBB"<? endif ?>
            >
			<?=$mupapu["mup"][$ars]["cir_diff"]?>
		</td>

		<? $desc_previous = $row["description"]; ?>
	</tr>
	<? endforeach ?>
	</tbody>
</table>

<br />
<div name="trepcol-dpp" id="trepcol-dpp">
<p style="page-break-before: auto">
	<?=$mupapu["date_info"]?>
</p>
</div>

<? endif ?>

</form>
