<script type="text/javascript">
$(document).ready(function(){
    $("form[name='searchform']").submit(function(){
        $("#dsearch").val($("#search").val());
        $("#showform").submit();
        return false;
    });
    
});
</script>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<form name="showform" id="showform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input name="dsearch" id="dsearch" type="hidden" value="<?=$urlinfo["dsearch"]?>" />
    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["type"]?>:</td>
                <td class="value">
                    <?=html_selectbox_array_submit("type", $types, $urlinfo['type'], $lang["(all)"], true, false, "style='width:100%'")?>
                </td>    
            </tr>
            <tr>
                <td class="name"><?=$lang["status"]?>:</td>
                <td class="value">
                    <?=html_selectbox_array_submit("status", $statuses, $urlinfo['status'], $lang["(all_statuses)"], true, false, "style='width:100%'")?>
                </td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="finalizesubmit" value="<?=$lang["finalize_selected"]?>" title="<?=$lang["finalize_selected"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>
</form>

<div class="clear" />

<?=$resultinfo?>

<? if ($urlinfo["limit_total"] != 0){

	$rows = "";

	while ($row = db_fetch_assoc($listdata)){
		echo "<form name=\"garmentdespecklesandrepairs_form\" id=\"". $row["garments_despecklesandrepairs_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["garments_despecklesandrepairs_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

		$rows .= "<tr class=\"list\">
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". $row["garments_despecklesandrepairs_date_in"] ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". (($row["garments_despecklesandrepairs_date_out"]) ? $row["garments_despecklesandrepairs_date_out"] : "<span class=\"empty\">". $lang["unknown"] ."</span>") ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". $row["garments_tag"] ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". $row["garments_tag2"] ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". $row["articles_description"] ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". $lang[$row["garments_despecklesandrepairs_type"]] ."</td>
                        <td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". (($row["garments_despecklesandrepairs_type"] == 'despeckle') ? $row["despeckles_description"] : $row["repairs_description"]) ."</td>
			<td class=\"list\" onClick=\"document.getElementById('". $row["garments_despecklesandrepairs_id"] ."').submit();\">". (($row["garments_despecklesandrepairs_date_out"]) ? $lang["inactive"] : $lang["active"]) ."</td>
                        <td class=\"midlist\">";
                        if(!$row["garments_despecklesandrepairs_date_out"]) {
                            $rows .= "<input id=\"garmentdespecklesandrepairs". $row["garments_despecklesandrepairs_id"] ."\" name=\"garmentdespecklesandrepairs_selected[". $row["garments_despecklesandrepairs_id"] ."]\" type=\"checkbox\" onclick=\"handleClick(this);\" value=\"". $row["garments_despecklesandrepairs_id"] ."\">";                    
                        }
                        $rows .= "</td>";
                $rows .= "</tr>";
	} ?>

	<table class="list">
		<tr class="listtitle">
			<td class="list"><?=$sortlinks["date_in"]?></td>
			<td class="list"><?=$sortlinks["date_out"]?></td>
			<td class="list"><?=$sortlinks["tag"]?></td>
			<td class="list"><?=$sortlinks["tag2"]?></td>
			<td class="list"><?=$sortlinks["article"]?></td>
			<td class="list"><?=$sortlinks["type"]?></td>
			<td class="list"><?=$lang["description"]?></td>
			<td class="list"><?=$sortlinks["status"]?></td>
                        <td class="list"><?=$lang["select"]?></td>
		</tr>
		<?=$rows?>
	</table>

	<?=$pagination?>
<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
    
    function addHidden(theForm, key, value) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = key;
      input.value = value;
      input.id = key;
      theForm.appendChild(input);
    }
    
    function removeHidden(theForm, id) {
      var input = document.getElementById(id);
      theForm.removeChild(input);
    }
    
    function handleClick(cb) {
      var theForm = document.forms['showform'];
      if(cb.checked) {
        addHidden(theForm, 'garmentdespecklesandrepairs_selected['+cb.value+']', 'true');
      } else {
        removeHidden(theForm, 'garmentdespecklesandrepairs_selected['+cb.value+']');
      }
    }
    
    
</script>