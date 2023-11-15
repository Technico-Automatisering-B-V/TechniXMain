<form id="circulationadvice" enctype="multipart/form-data" method="GET" action="<?=$pi["filename_details"]?>">
    <div class="filter">
        <table>
            <tr>
                <td class="left">
                    <?php if ($circulationgroup_count > 1){ ?>&nbsp;<?=$lang["location"]?>: <?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"]); ?>&nbsp;<?php }else{ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>
                    <input type="submit" name="print_cir" value="<?=$lang["print_circulation_list"]?>" title="<?=$lang["print_circulation_list"]?>" onclick="window.open('<? if(!$urlinfo["filter_last_scanned"]){ print "report_circulationadvice_old.php?cid=".$urlinfo["cid"]."&print_cir"; }else{ print "$_SERVER[REQUEST_URI]&cid=".$urlinfo["cid"]."&print_cir"; } ?>'); return false;" />
                    <input type="submit" name="print_sto" value="<?=$lang["print_stock_list"]?>" title="<?=$lang["print_stock_list"]?>" onclick="window.open('<? if(!$urlinfo["filter_last_scanned"]){ print "report_circulationadvice_old.php?cid=".$urlinfo["cid"]."&print_sto"; }else{ print "$_SERVER[REQUEST_URI]&cid=".$urlinfo["cid"]."&print_sto"; } ?>'); return false;" />
                    <input type="submit" name="print_order" value="<?=$lang["print_order_list"]?>" title="<?=$lang["print_order_list"]?>" onclick="window.open('<? if(!$urlinfo["filter_last_scanned"]){ print "report_circulationadvice_old.php?cid=".$urlinfo["cid"]."&print_order"; }else{ print "$_SERVER[REQUEST_URI]&cid=".$urlinfo["cid"]."&print_order"; } ?>'); return false;" />
                    <input type="submit" name="export" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
                </td>
            </tr>
            <tr>
                <td class="left"><?=$lang["last_scanned_from"]?>: 
                    <input class="date" onchange="submit()" name="from_date" type="text" value="<?=$urlinfo["from_date"]?>" />
                    <input type="checkbox" name="filter_last_scanned" id="filter_last_scanned" onClick="submit()" <?=$filter_last_scanned?> /> <label for="filter_last_scanned"><?=$lang["filter_last_scanned"]?></label>
                </td>
            </tr>
        </table>
    </div>
</form>

<div class="clear" />

<table id="report" class="list" style="font-size:12px">
    <tr class="listtitle">
        <td class="muColTitle" colspan="2" style="text-align:left;"><?=$lang["article"]?></td>
        <td class="listspace" width="2"></td>
        <td class="muColTitle" colspan="3"><?=$lang["circulation"]?></td>
        <td class="listspace" width="2"></td>
        <td class="muColTitle" colspan="3"><?=$lang["stock"]?></td>
        <td class="listspace" width="2"></td>
        <td class="muColTitle">&nbsp;</td>
    </tr>

    <tr class="listtitle">
        <td class="muColHeader" style="text-align:left"><?=$lang["description"]?></td>
        <td class="muColHeader"><?=$lang["size"]?></td>
        <td class="listspace"></td>
        <td class="muColHeader"><?=$lang["measured"]?></td>
        <td class="muColHeader"><?=$lang["required"]?></td>
        <td class="muColHeader"><?=$lang["complement"]?></td>
        <td class="listspace"></td>
        <td class="muColHeader"><?=$lang["measured"]?></td>
        <td class="muColHeader"><?=$lang["required"]?></td>
        <td class="muColHeader"><?=$lang["complement"]?></td>
        <td class="listspace"></td>
        <td class="muColHeader"><?=$lang["order"]?></td>
    </tr>

    <? foreach ($mupapu["mup"] as $ars => $row): ?>

    <?
    if ($row['cir_diff'] > 0) $cir_diff_color = " style=\"color:black;\" bgcolor=\"#FFCCCC\"";
    elseif ($row['cir_diff'] < 0) $cir_diff_color = " style=\"color:black;\" bgcolor=\"#FFEEBB\"";
    else $cir_diff_color = null;

    $sto_diff_color = null;
    ?>

    <tr class="listnc">
        <td class="list"><?=$row["description"]?></td>
        <td class="midlist"><?=$row["size"]?><?=(!empty($row["modification"])) ? " " . $row["modification"] : ""?></td>
        <td class="listspace"></td>
        <td class="midlist"><?=$mupapu["mup"][$ars]["cir_cur"]?></td>
        <td class="midlist"><?=$mupapu["mup"][$ars]["cir_new"]?></td>
        <td class="midlist"<?=$cir_diff_color?>><?=$mupapu["mup"][$ars]["cir_diff"]?></td>
        <td class="listspace"></td>
        <td class="midlist"><?=$mupapu["mup"][$ars]["sto_cur"]?></td>
        <td class="midlist"><?=$mupapu["mup"][$ars]["sto_new"]?></td>
        <td class="midlist"<?=$sto_diff_color?>><?=$mupapu["mup"][$ars]["sto_diff"]?></td>
        <td class="listspace"></td>
        <td class="midlist"><?=$mupapu["mup"][$ars]["order"]?></td>
    </tr>
    <? endforeach ?>
</table>

