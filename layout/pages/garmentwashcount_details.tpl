<? if (!empty($pi["note"])) echo $pi["note"] ?>

<div class="clear" />

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">

	<div class="filter">
        <input type="hidden" name="page" value="<?=$pi["page"]?>" />

        <? if (!empty($detailsdata["id"])){ ?>
            <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
            <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
        <? } ?>
		<input type="hidden" name="hassubmit" value="1" />

		<table>
			<tr>
				<td colspan="2"><strong><?=$lang["check_max_washes"]?></strong></td>
			</tr>
			<tr>
				<td class="name" width="130"><?=$lang["enable_check"]?></td>
				<td class="value"><input type="checkbox" name="washcount_check_enabled" value="1" onClick="submit()" <?=(($detailsdata["washcount_check_enabled"] == "1") ? "checked=\"checked\"" : "")?>></td>
			</tr>
			<? if ($detailsdata["washcount_check_enabled"] == "1"){ ?>
				<tr>
					<td class="name"><?=$lang["count_washes"]?></td>
					<td><input type="text" name="washcount_check_max" value="<?=$detailsdata["washcount_check_max"]?>" /></td>
				</tr>
				<tr>
					<td class="name"><?=$lang["certain_date"]?></td>
					<td class="value"><input type="checkbox" name="certain_date" value="1" onClick="submit()" <?=(($detailsdata["certain_date"] == "1") ? "checked=\"checked\"" : "")?>></td>
				</tr>
				<? if ($detailsdata["certain_date"] == "1"): ?>
				<tr>
					<td class="name"><label for="multiple_days"><?=$lang["multiple_dates"]?></label></td>
					<td class="value"><input type="checkbox" name="multiple_days" id="multiple_days" value="1" onClick="submit()" <?=(($detailsdata["multiple_days"] == "1") ? "checked=\"checked\"" : "")?>></td>
				</tr>
				<tr>
					<td class="name"><?=$lang["check_from"]?></td>
					<td class="value">
						<input class="date" name="washcount_check_from" type="text" value="<?=$detailsdata["washcount_check_from"]?>" /> <? if ($detailsdata["multiple_days"] == "1"): ?> t/m <input class="date" name="washcount_check_to" type="text" value="<?=$detailsdata["washcount_check_to"]?>" /><? endif ?>
					</td>
				</tr>
				<?
				endif;
			}else{ print("<input type=\"hidden\" name=\"washcount_check_max\" value=\"". $detailsdata["washcount_check_max"] ."\" />"); }
			?>
		</table>

	</div>

	<div class="clear" />

	<?=html_submitbuttons_detailsscreen($pi)?>

</form>
