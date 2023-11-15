<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
} elseif ($urlinfo["limit_total"] != 0) {
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\"><td class=\"list\">". $row["name"] ."</td></tr>";
    } ?>

    <table class="list">
      <tr class="listtitle">
        <th class="list"><?=$sortlinks["name"]?></th>
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
