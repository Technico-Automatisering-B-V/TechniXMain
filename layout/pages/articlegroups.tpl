<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
} elseif ($urlinfo["limit_total"] != 0) {
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\">
            <input type=\"hidden\" name=\"page\" value=\"details\" />
            <input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\" />
            <input type=\"hidden\" name=\"gosubmit\" value=\"true\" />
        </form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">
                    <td class=\"list\">". $row["article_1"] ."</td>
                    <td class=\"list\">". $row["article_2"] ."</td>
                    <td class=\"list\">". ((empty($row["profession"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["profession"]) ."</td> 
                    <td class=\"list\">". ($row["combined_credit"] == null || $row["combined_credit"] == 0 ? $lang["inactive"] : $row["combined_credit"]) ."</td>
                    <td class=\"list\">". (($row["extra_credit"]) ? $lang["active"] : $lang["inactive"]) ."</td>
                    <td class=\"list\">". (($row["only_main_article"]) ? $lang["active"] : $lang["inactive"]) ."</td>
                </tr>";
    } ?>

    <table class="list">
      <tr class="listtitle">
        <th class="list"><?=$sortlinks["article_1"]?></th>
        <th class="list"><?=$sortlinks["article_2"]?></th>
        <th class="list"><?=$sortlinks["profession"]?></th>
        <th class="list"><?=$sortlinks["combined_credit"]?></th>
        <th class="list"><?=$sortlinks["extra_credit"]?></th>
        <th class="list"><?=$sortlinks["only_main_article"]?></th>
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
