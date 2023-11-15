<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

    $forms = "";
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        $forms .= "<form id=\"". $row["information_screens_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\">
            <input type=\"hidden\" name=\"id\" value=\"". $row["information_screens_id"] ."\">
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\">
        </form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["information_screens_id"] ."').submit();\">
        <td class=\"list\">". $row["circulationgroups_name"] ."</td>
        <td class=\"list\">". $row["information_screens_message"] ."</td>
        <td class=\"list\">". $lang[$row["information_screens_color"]] ."</td>
        <td class=\"list\">". $lang[$row["information_screens_size"]] ."</td>
        <td class=\"list\">". $lang[$row["information_screens_speed"]] ."</td>
        <td class=\"list\">". $row["information_screens_sort"] ."</td></tr>";

    } ?>

    <?=$forms?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks["circulationgroup"]?></td>
            <td class="list"><?=$sortlinks["message"]?></td>
            <td class="list"><?=$sortlinks["color"]?></td>
            <td class="list"><?=$sortlinks["size"]?></td>
            <td class="list"><?=$sortlinks["speed"]?></td>
            <td class="list"><?=$sortlinks["sort"]?></td>
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