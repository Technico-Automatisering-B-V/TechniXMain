<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">

    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value" width="150"><?=html_selectbox_submit("circulationgroup_id", $circulationgroups, $circulationgroup_id, $lang["make_a_choice"], "style='width:100%'"); ?></td>
                </tr>
            <? endif ?>
            <? if (!empty($circulationgroup_id)): ?>
                <tr>
                    <td class="name"><?=$lang["type"]?>:</td>
                    <td class="value">
                        <?=html_selectbox_array("emplocle", $emplocles, $emplocle, $lang["make_a_choice"], "style='width:100%'", false); ?>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["status"]?>:</td>
                    <td class="value">
                        <?=html_selectbox_array("status", $statuses, $status, $lang["make_a_choice"], "style='width:100%'", false); ?>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["station"]?>:</td>
                    <td class="value"><?=html_selectbox_array("distributor_id", $distributors, $distributor_id, $lang["make_a_choice"], "style='width:100%'"); ?></td>
                </tr>
            <? endif ?>
        </table>

        <? if (!empty($circulationgroup_id)): ?>
        <div class="buttons">
            <input type="submit" name="submitreport" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
            <input type="submit" name="exportreport" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
        <? endif ?>
    </div>

</form>

<div class="clear"></div>

<?php
if (isset($pi["note"]) && $pi["note"] != "")
{
    echo $pi["note"];
}

if (!empty($_GET["distributor_id"]) && $result_count > 0)
{
    print($lang["you_see_all"] ." ". $result_count . " ". $lang["items_found"]);
    ?>
    <br />
    <?=$forms?>
    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$lang["station"]?></td>
            <?if($emplocle == "articles"){ print("<td class=\"list\">". $lang["position"] ."</td>"); } ?>
            <td class="list"><?=$lang["garmentuser"]?></td>
            <td class="list"><?=$lang["personnelcode"]?></td>
            <?if($emplocle == "employees"){ print("<td class=\"list\">". $lang["number_carriers_posible"] ."</td><td class=\"list\">". $lang["number_carriers_taken"] ."</td>"); } ?>
            <?if($emplocle == "articles"){ print("<td class=\"list\">". $lang["article"] ."</td><td class=\"list\">". $lang["size"] ."</td><td class=\"list\">". $lang["tag"] ."</td>"); }?>
            <td class="list"><?=$lang["deleted"]?></td>
        </tr>
        <?=$tablerows?>
    </table>
<?php } ?>

