<form name="showform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input name="custom" type="hidden" value="1" />

    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["status"]?></td>
                <td class="value">
                    <input id="s[1]" name="s[1]" type="checkbox" <?=(isset($_SESSION["garmentdespeckles"]["show"][1]))?" checked=\"checked\"":""?> onClick="submit()"><label for="s[1]"><?=$lang["open_despeckles"]?></label>&nbsp;&nbsp;|&nbsp;
                    <input id="s[2]" name="s[2]" type="checkbox" <?=(isset($_SESSION["garmentdespeckles"]["show"][2]))?" checked=\"checked\"":""?> onClick="submit()"><label for="s[2]"><?=$lang["despeckled"]?></label>
                </td>
            </tr>
            <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
        </table>
    </div>

    <? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>

</form>

<div class="clear" />

<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

	$rows = "";

	while ($row = db_fetch_assoc($listdata)){
		echo "<form id=\"". $row["garments_despeckles_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["garments_despeckles_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

		$rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["garments_despeckles_id"] ."').submit();\">
			<td class=\"list\">". $row["garments_despeckles_date_in"] ."</td>
			". (isset($_SESSION["garmentdespeckles"]["show"][2]) ? "<td class=\"list\">". (($row["garments_despeckles_date_out"]) ? $row["garments_despeckles_date_out"] : "<span class=\"empty\">". $lang["unknown"] ."</span>") : "" ."</td>") ."
			<td class=\"list\">". $row["garments_tag"] ."</td>
			<td class=\"list\">". $row["articles_description"] ."</td>
			<td class=\"list\">". $row["despeckles_description"] ."</td>
			<td class=\"list\">". (($row["garments_despeckles_status"]) ? $lang["open"] : $lang["despeckled"]) ."</td>
		</tr>";
	} ?>

	<table class="list">
		<tr class="listtitle">
			<td class="list"><?=$sortlinks["date_in"]?></td>
			<? if (isset($_SESSION["garmentdespeckles"]["show"][2])):?><td class="list"><?=$sortlinks["date_out"]?></td><?endif?>
			<td class="list"><?=$sortlinks["tag"]?></td>
			<td class="list"><?=$sortlinks["article"]?></td>
			<td class="list"><?=$sortlinks["despeckle"]?></td>
			<td class="list"><?=$sortlinks["status"]?></td>
		</tr>
		<?=$rows?>
	</table>

	<?=$pagination?>
<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>