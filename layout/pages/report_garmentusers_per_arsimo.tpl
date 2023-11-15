<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value" width="150"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'"); ?></td>
                </tr>
            <? endif ?>
            <? if ($clientdepartments_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["clientdepartment"]?>:</td>
                <td class="value"><?=html_selectbox_submit("clientdepartment_id", $clientdepartments, $urlinfo["clientdepartment_id"], $lang["(all_clientdepartments)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <? if ($costplaces_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["costplace"]?>:</td>
                <td class="value"><?=html_selectbox_submit("costplace_id", $costplaces, $urlinfo["costplace_id"], $lang["(all_costplaces)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <? if ($functions_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["function"]?>:</td>
                <td class="value"><?=html_selectbox_submit("function_id", $functions, $urlinfo["function_id"], $lang["(all_functions)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><?=html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], " style=\"width:300px\"")?></td>
            </tr>
            <?if(!empty($sizes)):?>
            <tr>
                <td class="name"><?=$lang["size"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], true, false, "style='width:100%'")?></td>
            </tr>
                <?if(!empty($modifications)):?>
                <tr>
                    <td class="name"><?=$lang["modification"]?>:</td>
                    <td class="value"><?=html_selectbox_array_submit("mid", $modifications, $urlinfo["mid"], $lang["(all_modifications)"], true, false, "style='width:100%'")?></td>
                </tr>
                <? endif ?>
            <? endif ?>
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>
</form>

<div class="clear" />

<?php

$rows = "";

while ($row = db_fetch_assoc($listdata)){

    if ($row["c_s_gu"] > 0 || $row["c_s_g"] > 0 || $row["c_so_g"] > 0)
    {
        echo "<form id=\"". $row["arsimo_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["arsimo_id"] ."\">
        <input type=\"hidden\" name=\"cid\" value=\"". $urlinfo["cid"] ."\">
        <input type=\"hidden\" name=\"clientdepartment_id\" value=\"". $urlinfo["clientdepartment_id"] ."\">
        <input type=\"hidden\" name=\"costplace_id\" value=\"". $urlinfo["costplace_id"] ."\">
        <input type=\"hidden\" name=\"function_id\" value=\"". $urlinfo["function_id"] ."\">
        <input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["arsimo_id"] ."').submit();\">
            <td class=\"list\">". ucfirst($row["articlenumber"]) ."</td>
            <td class=\"list\">". ucfirst($row["article"]) ."</td>
            <td class=\"midlist\">". $row["size"] ."</td>";
                $rows .= "<td class=\"midlist\">";
                if ($row["modification"]) { $rows .= $row["modification"]; }else{ $rows .= "<span class=\"empty\">". $lang["none"] ."</span>"; }
                $rows .= "</td>";
            $rows .= "
            <td class=\"midlist\">". $row["c_s_gu"] ."</td>
            <td class=\"midlist\">". $row["c_s_g"] ."</td>
            <td class=\"midlist\">". (($row["c_s_g"]!=0 && $row["c_s_gu"]!=0)?(round(($row["c_s_g"]/$row["c_s_gu"]),1)):'0') ."</td>
            <td class=\"midlist\">". $row["c_so_g"] ."</td>
        </tr>";
    }
} ?>

<table class="list float">
    <thead>
        <tr class="listtitle">
            <th class="muColTitle" colspan="<?=(!empty($modifications)?'4':'3')?>" style="text-align:left;"><?=$lang["article"]?></th>
            <th class="muColTitle" colspan="4"><?=$lang["sizebound"]?></th>
        </tr>
        
        <tr class="listtitle">
            <th class="midlist"><?=$lang["articlenumber"]?></th>
            <th class="midlist"><?=$lang["description"]?></th>
            <th class="midlist"><?=$lang["size"]?></th>
            <th class="midlist"><?=$lang["modification"]?></th>
            <th class="midlist"><?=$lang["garmentusers"]?></th>
            <th class="midlist"><?=$lang["garments"]?></th>
            <th class="midlist"><?=$lang["average"]?></th>
            <th class="midlist"><?=$lang["out_circulation_garments"]?></th>  
        </tr>
    </thead>
    <tbody>
        <?=$rows?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function() {
        $.fn.dataTableExt.oPagination.iFullNumbersShowPages = 15;
        $("table.list").dataTable({
            "iDisplayLength": 16,
            "aaSorting": [],
            "sDom" : "itp",
            "sPaginationType": "full_numbers",
            "oLanguage": {
                "sInfoThousands": "",
                "sZeroRecords": "There are no records that match your search criterion",
                "sLengthMenu": "Display _MENU_ records per page",
                "sInfo": "<?php echo $lang["you_see"]; ?> _START_-_END_ <?php echo $lang["of"]; ?> _TOTAL_ <?php echo $lang["items_found"]; ?>",
                "sInfoEmpty": "Geen resultaten gevonden",
                "sInfoFiltered": "(filtered from _MAX_ total records)",
                "oPaginate": {
                    "sFirst": "&laquo;",
                    "sLast": "&raquo;",
                    "sNext": "<?php echo $lang["next_page"]; ?>",
                    "sPrevious": "<?php echo $lang["previous_page"]; ?>"
                }
            }
        });
    });
</script>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>