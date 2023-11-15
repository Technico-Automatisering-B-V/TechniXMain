<form action="<?=$_SERVER["PHP_SELF"]?>" method="POST">
    <input type="submit" name="startImporterGarments" value="Start importer handmatig" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false" />
</form>

<?php if (!empty($start_imp_garments_result)){ print("<br /><strong>Resultaat:</strong><br /><pre>". $start_imp_garments_result ."</pre>"); }?>

<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$lang["date"]?></td>
        <td class="list">Gestart</td>
        <td class="list"><?=$lang["added"]?></td>
        <td class="list"><?=$lang["modified"]?></td>
        <td class="list"><?=$lang["deleted"]?></td>
        <td class="list"><?=$lang["result"]?></td>
        <td class="list"><?=$lang["comments"]?></td>
    </tr>
    <?php
    while ($row = db_fetch_assoc($imp_garments_data)){
        print("<tr class=\"listnc\">
            <td class=\"list\">". $row["datetime"] ."</td>
            <td class=\"list\">". (($row["started_by"] == "auto") ? "Automatisch" : $lang["manually"]) ."</td>
            <td class=\"midlist\">". (!empty($row["inserted"]) ? $row["inserted"] : "<span class=\"empty\">0</span>") ."</td>
            <td class=\"midlist\">". (!empty($row["modified"]) ? $row["modified"] : "<span class=\"empty\">0</span>") ."</td>
            <td class=\"midlist\">". (!empty($row["deleted"]) ? $row["deleted"] : "<span class=\"empty\">0</span>") ."</td>
            <td class=\"midlist\">");
                if ($row["result"] == "0"){ print("<img alt=\"\" height=\"16\" src=\"layout/images/dialog-error.png\" title=\"\" width=\"16\" />"); }
                if ($row["result"] == "1"){ print("<img alt=\"\" height=\"16\" src=\"layout/images/dialog-ok.png\" title=\"\" width=\"16\" />"); }
                if ($row["result"] == "2"){ print("<img alt=\"\" height=\"16\" src=\"layout/images/dialog-warning.png\" title=\"\" width=\"16\" />"); }
            print("</td>");
            print("<td class=\"list\">". (!empty($row["comments"]) ? $row["comments"] : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>");
        print("</tr>");
    } ?>
</table>