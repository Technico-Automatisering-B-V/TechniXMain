<?=$resultinfo?>

<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row['users_id'] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi['filename_details'] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\" />
            <input type=\"hidden\" name=\"id\" value=\"". $row['users_id'] ."\" />
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\" />
        </form>";
        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row['users_id'] ."').submit();\"><td class=\"list\">". $row['users_username'] ."</td><td class=\"list\">". $row['locales_name'] ."</td></tr>";
    } ?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks['username']?></td>
            <td class="list"><?=$sortlinks['locale']?></td>
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