<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
} elseif ($urlinfo["limit_total"] != 0) {

    $rows = "";

    while ($row = db_fetch_assoc($listdata)) {
        echo "<form id=\"" . $row["costplaces_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"" . $pi["filename_details"] . "\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"" . $row["costplaces_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["costplaces_id"] ."').submit();\">";
        $rows .= "<td class=\"list\">". $row["costplaces_value"] ."</td>";
        $rows .= "</tr>";
    }
    ?>

    <table class="list">
      <tr class="listtitle">
        <th class="list"><?=$sortlinks["value"]?></th>
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