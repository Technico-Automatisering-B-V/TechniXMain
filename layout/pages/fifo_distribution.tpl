<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

    $forms = "";
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        $forms .= "<form id=\"". $row["circulationgroups_fifo_distribution_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\">
            <input type=\"hidden\" name=\"id\" value=\"". $row["circulationgroups_fifo_distribution_id"] ."\">
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\">
        </form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["circulationgroups_fifo_distribution_id"] ."').submit();\">";
        $rows .= "<td class=\"list\">". $row["circulationgroups_name"] ."</td>";
        $rows .= "<td class=\"list\">";
            if (!empty($row["circulationgroups_fifo_distribution_dayofweek"])) {
                $rows .= $row["circulationgroups_fifo_distribution_dayofweek"];
            }
        $rows .= "</td>";
        $rows .= "<td class=\"list\">". $row["circulationgroups_fifo_distribution_from_hours"] ."</td>";
        $rows .= "<td class=\"list\">". $row["circulationgroups_fifo_distribution_to_hours"] ."</td>";
        $rows .= "</tr>";

    } ?>

    <?=$forms?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks["location"]?></td>
            <td class="list"><?=$sortlinks["dayofweek"]?></td>
            <td class="list"><?=$sortlinks["distribution_from"]?></td>
            <td class="list"><?=$sortlinks["distribution_to"]?></td>
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