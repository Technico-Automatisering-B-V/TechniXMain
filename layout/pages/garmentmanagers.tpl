<form name="showform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input name="custom" type="hidden" value="1" />
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["status"]?>:</td>
                <td class="value"><input id="del" name="del" type="checkbox" <?=(!empty($urlinfo["del"])) ? " checked=\"checked\"" : ""?> onClick="submit()"><label for="del"><?=$lang["deleted"]?></label></td>
            </tr>
        </table>
    </div>
</form>

<div class="clear" />

<?=$resultinfo?>

<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["supergarmentusers_garmentuser_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"garmentuser_id\" value=\"". $row["supergarmentusers_garmentuser_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        if ($row["supergarmentusers_limit_to_profession"] == 1) {
            $limitation = $lang["to_profession"];
        } elseif ($row["supergarmentusers_limit_to_articles"] == 1) {
            $limitation = $lang["to_articles"];
        } else {
            $limitation = $lang["none"];
        }

        $rows .= "<tr class=\"". (!empty($row["supergarmentusers_deleted_on"]) ? "listgrey" : "list") ."\" onClick=\"document.getElementById('". $row["supergarmentusers_garmentuser_id"] ."').submit();\">";
        $rows .= "<td class=\"list\">";
        $rows .= generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]);
        $rows .= "</td>";
        $rows .= "<td class=\"list\">". $row["garmentusers_personnelcode"] ."</td>";
        $rows .= "<td class=\"list\">". $limitation ."</td>";
        $rows .= "<td class=\"midlist\">". $row["supergarmentusers_maxcredit"] ."</td>";
        $rows .= "<td class=\"midlist\">". (($row["supergarmentusers_allow_normaluser"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "<td class=\"midlist\">". (($row["supergarmentusers_allow_supercard"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "<td class=\"midlist\">". (($row["supergarmentusers_allow_supername"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "<td class=\"midlist\">". (($row["supergarmentusers_allow_station"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "<td class=\"midlist\">". (($row["supergarmentusers_allow_overloaded"]==='y') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>";
        $rows .= "</tr>";

    } ?>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks["garmentuser"]?></td>
            <td class="list"><?=$sortlinks["personnelcode"]?></td>
            <td class="list"><?=$sortlinks["limitation"]?></td>
            <td class="midlist"><?=$sortlinks["maxcredit"]?></td>
            <td class="midlist"><?=$sortlinks["allow_normaluser"]?></td>
            <td class="midlist"><?=$sortlinks["allow_supercard"]?></td>
            <td class="midlist"><?=$sortlinks["allow_supername"]?></td>
            <td class="midlist"><?=$sortlinks["allow_station"]?></td>
            <td class="midlist"><?=$sortlinks["allow_overloaded"]?></td>
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