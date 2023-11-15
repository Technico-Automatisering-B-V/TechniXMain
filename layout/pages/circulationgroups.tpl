<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

    $forms = "";
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        $forms .= "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\">
            <input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\">
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\">
        </form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">
            <td class=\"list\">". $row["name"] ."</td>";
            $rows .= "<td class=\"midlist\">". (($row["fifo_distribution"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "</tr>";

    } ?>

    <?=$forms?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks["location"]?></td>
            <td class="list"><?=$sortlinks["fifo_distribution"]?></td>
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