<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

	$rows = "";

	while ($row = db_fetch_assoc($listdata)){
		echo "<form id=\"". $row["log_garments_tagreplacements_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["log_garments_tagreplacements_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
		$rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["log_garments_tagreplacements_id"] ."').submit();\"><td class=\"list\">". $row["log_garments_tagreplacements_datetime"] ."</td><td class=\"list\">". $row["log_garments_tagreplacements_old_tag"] ."</td><td class=\"list\">". $row["log_garments_tagreplacements_new_tag"] ."</td><td class=\"list\">". $row["articles_description"] ."</td></tr>";
	} ?>

	<table class="list">
		<tr class="listtitle">
			<td class="list"><?=$sortlinks["datetime"]?></td>
			<td class="list"><?=$sortlinks["old_tag"]?></td>
			<td class="list"><?=$sortlinks["new_tag"]?></td>
			<td class="list"><?=$sortlinks["article"]?></td>
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