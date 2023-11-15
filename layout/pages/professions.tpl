<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\" />
            <input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\" />
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\" />
        </form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">
                    <td class=\"list\">". $row["name"] ."</td>
                    <td class=\"list\">". $row["timelock"] ." ". strtolower($lang["minutes"]) ."</td>
                    <td class=\"midlist\">". (($row["daysbeforewarning"] !== null) ? $row["daysbeforewarning"] ." ". strtolower($lang["days_garments_in_possession"]) : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
                    <td class=\"midlist\">". (($row["daysbeforelock"] !== null) ? $row["daysbeforelock"] ." ". strtolower($lang["days_after_warning"]) : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
                    <td class=\"midlist\">". (($row["importcode"] !== null) ? $row["importcode"] : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
                </tr>";
    } ?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks["name"]?></td>
            <td class="list"><?=$sortlinks["timelock"]?></td>
            <td class="list"><?=$sortlinks["warning"]?></td>
            <td class="list"><?=$sortlinks["blockage"]?></td>
            <td class="list"><?=$sortlinks["importcode"]?></td>
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