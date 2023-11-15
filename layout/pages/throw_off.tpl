<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($detailsdata_count != 0){

	$tablerows = "";

	while ($row = db_fetch_assoc($detailsdata)){
		echo "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

		$tablerows .= "
		<tr class=\"list\">
			<td class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". $row["distributor_name"] ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". (($row["active"] == 1) ? "<span style=\"color:#25814E\">Ja</span>" : "<span style=\"color:#FF0000\">Nee</span>") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["date_from"])) ? "Ja" : "Nee") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["date_from"])) ? $row["date_from"] : "<span class=\"empty\">-</span>") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["date_to"])) ? $row["date_to"] : "<span class=\"empty\">-</span>") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["tag"])) ? $row["tag"] : "<span class=\"empty\">-</span>") ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". $row["article_description"] ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["size"])) ? $row["size"] : "<span class=\"empty\">-</span>") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["modification"])) ? $row["modification"] : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
			<td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["max_washcount"])) ? $row["max_washcount"] : "<span class=\"empty\">-</span>") ."</td>
            <td class=\"midlist\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">". ((!empty($row["amount"])) ? $row["amount"] : "<span class=\"empty\">-</span>") ."</td>
            <td class=\"midlist\" width=\"25\" onClick=\"document.delform.id_to_cancel.value='". $row["id"] ."';document.delform.submit();\">
                <img src=\"layout/images/delete.png\" width=\"14\" height=\"14\" border=\"0\" title=\"". $lang["delete"] ."\" />
            </td>
		</tr>";
	} ?>

    <form name="delform" id="delform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
          <input type="hidden" name="id_to_cancel" value="">
    </form>

	<table class="list">
		<tr class="listtitle">
			<td class="list"><?=$lang["distributorlocation"]?></td>
			<td class="midlist"><?=$lang["throw_off"]?></td>
			<td class="midlist"><?=$lang["certain_date"]?></td>
			<td class="list"><?=$lang["from_date"]?></td>
			<td class="list"><?=$lang["to_date"]?></td>
			<td class="midlist"><?=$lang["tag"]?></td>
			<td class="list"><?=$lang["article"]?></td>
			<td class="midlist"><?=$lang["size"]?></td>
			<td class="midlist"><?=$lang["modification"]?></td>
            <td class="midlist"><?=$lang["max_washcount"]?></td>
			<td class="midlist"><?=$lang["count"]?></td>
            <td class="midlist">&nbsp;</td>

		</tr>
		<?=$tablerows?>
	</table>

<? }else{

	print($lang["no_items_found"]);

} ?>