<? if ($auth_num_results != 0){ ?>

    <form action="<?=$_SERVER["PHP_SELF"]?>" method="GET">
        <input type="submit" name="emptyUserlogin" value="Authorisatielijst legen" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false" />
    </form>

    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$lang["date"]?></td>
            <td class="list"><?=$lang["username"]?></td>
            <td class="list"><?=$lang["iP_address"]?></td>
            <td class="list"><?=$lang["hostname"]?></td>
            <td class="list"><?=$lang["result"]?></td>
        </tr>
        <?php
        while ($row = db_fetch_assoc($auth_data)){
            print("<tr class=\"listnc\">
                <td class=\"list\">". $row["datetime"] ."</td>
                <td class=\"list\">". (!empty($row["username"]) ? $row["username"] : "<span class=\"empty\">". $lang["none"] ."</span>") ."</td>
                <td class=\"list\">". $row["ip"] ."</td>
                <td class=\"list\">". (!empty($row["hostname"]) ? $row["hostname"] : "<span class=\"empty\">". $lang["unknown"] ."</span>") ."</td>
                <td class=\"midlist\">". (($row["result"] == "1") ? "<img alt=\"\" height=\"16\" src=\"layout/images/dialog-ok.png\" title=\"\" width=\"16\" />" : "<img alt=\"\" height=\"16\" src=\"layout/images/dialog-error.png\" title=\"\" width=\"16\" />") ."</td>
            </tr>");
        } ?>
    </table>

<? }else{

    print($lang["no_items_found"]);

}

?>
