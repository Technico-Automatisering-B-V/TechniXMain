<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<?php
function rgb_to_hex($rgb)
{
    $hex = "";
    foreach ($rgb as $color => $value) {
        $hex .= (strlen(dechex($value)) == 1) ? "0" . dechex($value) : dechex($value);
    }
    return strtoupper($hex);
}

function percentage_bg($color, $value)
{
    if ($color == "red") return "#" . rgb_to_hex(array("R" => 255, "G" => 220 - ($value * 1.2), "B" => 220 - ($value * 1.2)));
    else if ($color == "green") return "#" . rgb_to_hex(array("R" => 220 - ($value * 1.3), "G" => 255, "B" => 210 - ($value * 1.2)));
    else if ($color == "blue") return "#" . rgb_to_hex(array("R" => 220 - ($value * 0.8), "G" => 220 - ($value * 0.8), "B" => 255));
    else if ($color == "gray") return "#" . rgb_to_hex(array("R" => 235 - ($value * 0.8), "G" => 235 - ($value * 0.8), "B" => 235 - ($value * 0.8)));
}

if (!empty($menu)){ ?>
    <table>
        <tr>
            <td class="small" style="margin:0; padding:0">
                <?=$menu?>
            </td>
        </tr>
    </table>
<? } ?>

<? if (!empty($caption)){ ?>
    <table>
        <tr class="name">
            <td>
                <?=$caption?>
            </td>
        </tr>
    </table>
<? } ?>
<? if (!empty($listdata) && db_num_rows($listdata) > 0): ?>
<table id="t1" class="list float" <?=($pi["filename_this"] == "report_totals.php")?"style=\"table-layout:fixed;\"":""?>>
    <thead>
        <?
        $h = "<tr>";
        $firstrow = db_fetch_assoc($listdata);
        $count = 0;

        foreach ($firstrow as $name => $value) {
            if (isset($show[$name])) {
                $count++;
                $h .= "<th class=\"listtitle ". (($count>$columns_left)?"midlistsmall":"listsmall") . "\">". lang($name) ."</th>";           
            }
        }
        $h .= "</tr>";
        
        if (isset($tableheader)) {
            $h = "<tr class=\"listtitle\"><th class=\"muColTitle\" colspan=\"". $count ."\">". $lang["$tableheader"] ."</th></tr>". $h;
        }
        ?>
        <?=$h?>
    </thead>

    <? db_data_seek($listdata, 0) ?>

    <tbody>
    <?php
    while ($row = db_fetch_assoc($listdata)):
        if (!isset($row["ref__id"])){ $ref__id = null; } else { $ref__id = $row["ref__id"]; }
        if (!isset($row["sec__id"])){ $sec__id = null; } else { $sec__id = $row["sec__id"]; }

        if (!empty($pi["filename_next"])){
            $attrs = "class=\"listlt\" onClick=\"document.location.href='" . $pi["filename_next"] . "?" . ((!empty($urlinfo["urlparams"]))?$urlinfo["urlparams"]:"") . "ref=" . $ref__id ."&sec=" . $sec__id . "'\"";
        }else{
            $attrs = "class=\"listnc\"";
        }

        if (!isset($only_true) || isset($row[$only_true])): ?>
        <tr <?=$attrs?>>
            <?php
            $count = 0;

            $percentize_total = 0;
            foreach ($row as $name => $value) {
                if (isset($percentize[$name])) {
                    $percentize_total += $value;
                }
            }

            $count = 0;

            foreach ($row as $name => $value) {
                if (isset($show[$name])) {
                    $count++;

                    if (isset($percentize[$name])) $value_percentage = ($value != 0) ? ($value / $percentize_total * 100) : 0;

                    if (isset($percentize[$name])) {
                        if (isset($rounding[$name])) {
                            if ($rounding[$name] > 0) {
                                $showvalue = round($value_percentage, $rounding[$name]);
                                $altvalue = round($value, $rounding[$name]);
                            } else {
                                $showvalue = round($value_percentage);
                                $altvalue = round($value);
                            }
                        } else {
                            $showvalue = $value_percentage;
                            $altvalue = $value;
                        }
                    } else {
                        if (isset($rounding[$name])) {
                            if ($rounding[$name] > 0) {
                                $showvalue = round($value, $rounding[$name]);
                            } else {
                                $showvalue = round($value);
                            }
                        } else {
                            if (isset($translate[$name])) {
                                $showvalue = lang($value);
                            } else {
                                $showvalue = $value;
                            }
                        }
                    }
                    ?>

                    <td class="<?=($count>$columns_left)?"midlist":"list"?><?=(isset($row_onclick[$name])&&(!empty($ref__id)||!empty($sec__id)))?" lpointer":""?>" <?=(isset($row_onclick[$name])&&(!empty($ref__id)||!empty($sec__id)))?$row_onclick[$name]."?ref=" . $ref__id . "&sec=" . $sec__id . "'\"":""?>
                        <?php
                        
                        $mouseover_content = "";

                        if (isset($mouseover_columns[$name]) && !isset($mouseover_canceled))
                        {
                            foreach ($mouseover_columns[$name] as $mouseover_content_column => $always_visible)
                            {
                                $mouseover_notice = "";

                                if (($always_visible ||
                                    (!$always_visible &&
                                      is_numeric($row[$mouseover_content_column]) &&
                                      $row[$mouseover_content_column] > 0)) &&
                                      !isset($mouseover_canceled))
                                {
                                    $mouseover_content .= lang($mouseover_content_column) . ": " . $row[$mouseover_content_column] . "<br />";
                                    if (!is_numeric($row[$mouseover_content_column]) || $row[$mouseover_content_column] > 0)
                                    {
                                        $mouseover_notice .= ((!empty($mouseover_notices[$name][$mouseover_content_column])) ? $mouseover_notices[$name][$mouseover_content_column] : null);
                                    }
                                } else $mouseover_canceled = true;
                            }

                            if (!empty($mouseover_content) && !isset($mouseover_canceled))
                            {
                                if (!empty($mouseover_title))
                                {
                                    $title = $mouseover_title . "<br />";
                                }else{
                                    $title = "";
                                }
                                ?>
                                onmouseover="popup('<?=$title?> <?=$mouseover_content?>')" onmouseout="kill()"
                            <?php
                            }
                        }

                        if (!empty($row["ref__rowcolor"]))
                        {
                        ?>
                            style="background-color:<?=$row["ref__rowcolor"]?>; color:#000;"
                        <?php
                        }else if (isset($percentize[$name]) && $value_percentage > 0){
                        ?>
                            style="background-color:<?=percentage_bg($background[$name], $value_percentage)?>;color:#000;"
                        <?php
                        }

                        if (empty($showvalue))
                        {
                        ?>
                            align="center"
                        <?php
                        }
                        ?>
                    >
                        <?=((isset($prefix[$name])) ? $prefix[$name] : "") . $showvalue . ((isset($suffix[$name])) ? $suffix[$name] : "")?>
                        <?=((!empty($mouseover_notice)) ? $mouseover_notice : "") ?>
                    </td>
                    <?
                    if (isset($altvalue)) unset($altvalue);
                    if (isset($showvalue)) unset($showvalue);
                    if (isset($mouseover_content)) unset($mouseover_content);
                    if (isset($mouseover_notice)) unset($mouseover_notice);
                    if (isset($mouseover_canceled)) unset($mouseover_canceled);
                }
            }
            ?>
        </tr>
        <? endif ?>
    <? endwhile ?>

        <?
        db_data_seek($listdata, 0);

        if (isset($only_true))
        {
            while ($row = db_fetch_assoc($listdata))
            {
                $count = 0;

                if (!isset($row[$only_true]))
                {
                ?>
                    <tr class="listtitle">
                        <?php
                        foreach ($row as $name => $value)
                        {
                            if (isset($show[$name]))
                            {
                                $count++; ?>
                                <td class="<?=($count>$columns_left)?"midlistsmall":"listsmall"?>">
                                    <?=$row[$name]?>
                                </td>
                            <? }
                        }
                        ?>
                    </tr>
                <?php
                }
            }
        }
        ?>
    </tbody>
</table>

<? endif ?>

<? if (!empty($listdata2) && db_num_rows($listdata2) > 0): ?>
<table id="t2"  class="list float" <?=($pi["filename_this"] == "report_totals.php")?"style=\"table-layout:fixed;\"":""?>>
    <thead>
    <?
    $h = "<tr>";
    $firstrow = db_fetch_assoc($listdata2);
    $count = 0;

    foreach ($firstrow as $name => $value) {
        if (isset($show2[$name])) {
            $count++;
            $h .= "<th class=\"listtitle ". (($count>$columns_left2)?"midlistsmall":"listsmall") . "\">". lang($name) ."</th>";           
        }
    }
    $h .= "</tr>";

    if (isset($tableheader2)) {
        $h = "<tr class=\"listtitle\"><th class=\"muColTitle\" colspan=\"". $count ."\">". $lang["$tableheader2"] ."</th></tr>". $h;
    }
    ?>
    <?=$h?>
    </thead>

    <? db_data_seek($listdata2, 0) ?>

    <tbody>
    <?php
    while ($row = db_fetch_assoc($listdata2)):
        if (!isset($row["ref__id"])){ $ref__id = null; } else { $ref__id = $row["ref__id"]; }
        if (!isset($row["sec__id"])){ $sec__id = null; } else { $sec__id = $row["sec__id"]; }


        if (!empty($pi["filename_next2"])){
            $attrs = "class=\"listlt\" onClick=\"document.location.href='" . $pi["filename_next2"] . "?" . "ref=" . $ref__id ."&sec=" . $sec__id . "'\"";
        }else{
            $attrs = "class=\"listnc\"";
        }

        if (!isset($only_true) || isset($row[$only_true])): ?>
        <tr <?=$attrs?>>
            <?php
            $count = 0;

            $percentize_total2 = 0;
            foreach ($row as $name => $value) {
                if (isset($percentize2[$name])) {
                    $percentize_total2 += $value;
                }
            }

            $count = 0;

            foreach ($row as $name => $value) {
                if (isset($show2[$name])) {
                    $count++;

                    if (isset($percentize2[$name])) $value_percentage = ($value != 0) ? ($value / $percentize_total2 * 100) : 0;

                    if (isset($percentize2[$name])) {
                        if (isset($rounding2[$name])) {
                            if ($rounding2[$name] > 0) {
                                $showvalue = round($value_percentage, $rounding2[$name]);
                                $altvalue = round($value, $rounding2[$name]);
                            } else {
                                $showvalue = round($value_percentage);
                                $altvalue = round($value);
                            }
                        } else {
                            $showvalue = $value_percentage;
                            $altvalue = $value;
                        }
                    } else {
                        if (isset($rounding2[$name])) {
                            if ($rounding2[$name] > 0) {
                                $showvalue = round($value, $rounding2[$name]);
                            } else {
                                $showvalue = round($value);
                            }
                        } else {
                            if (isset($translate[$name])) {
                                $showvalue = lang($value);
                            } else {
                                $showvalue = $value;
                            }
                        }
                    }
                    ?>

                    <td class="<?=($count>$columns_left2)?"midlist":"list"?>"
                        <?php
                        $mouseover_content = "";

                        if (isset($mouseover_columns2[$name]) && !isset($mouseover_canceled))
                        {
                            foreach ($mouseover_columns2[$name] as $mouseover_content_column => $always_visible)
                            {
                                $mouseover_notice = "";

                                if (($always_visible ||
                                    (!$always_visible &&
                                      is_numeric($row[$mouseover_content_column]) &&
                                      $row[$mouseover_content_column] > 0)) &&
                                      !isset($mouseover_canceled))
                                {
                                    $mouseover_content .= lang($mouseover_content_column) . ": " . $row[$mouseover_content_column] . "<br />";
                                    if (!is_numeric($row[$mouseover_content_column]) || $row[$mouseover_content_column] > 0)
                                    {
                                        $mouseover_notice .= ((!empty($mouseover_notices2[$name][$mouseover_content_column])) ? $mouseover_notices2[$name][$mouseover_content_column] : null);
                                    }
                                } else $mouseover_canceled = true;
                            }

                            if (!empty($mouseover_content) && !isset($mouseover_canceled))
                            {
                                if (!empty($mouseover_title2))
                                {
                                    $title = $mouseover_title2 . "<br />";
                                }else{
                                    $title = "";
                                }
                                ?>
                                onmouseover="popup('<?=$title?> <?=$mouseover_content?>')" onmouseout="kill()"
                            <?php
                            }
                        }

                        if (!empty($row["ref__rowcolor"]))
                        {
                        ?>
                            style="background-color:<?=$row["ref__rowcolor"]?>; color:#000;"
                        <?php
                        }else if (isset($percentize2[$name]) && $value_percentage > 0){
                        ?>
                            style="background-color:<?=percentage_bg($background2[$name], $value_percentage)?>;color:#000;"
                        <?php
                        }

                        if (empty($showvalue))
                        {
                        ?>
                            align="center"
                        <?php
                        }
                        ?>
                    >
                        <?=((isset($prefix[$name])) ? $prefix[$name] : "") . $showvalue . ((isset($suffix2[$name])) ? $suffix2[$name] : "")?>
                        <?=((!empty($mouseover_notice)) ? $mouseover_notice : "") ?>
                    </td>
                    <?
                    if (isset($altvalue)) unset($altvalue);
                    if (isset($showvalue)) unset($showvalue);
                    if (isset($mouseover_content)) unset($mouseover_content);
                    if (isset($mouseover_notice)) unset($mouseover_notice);
                    if (isset($mouseover_canceled)) unset($mouseover_canceled);
                }
            }
            ?>
        </tr>
        <? endif ?>
    <? endwhile ?>

        <?
        db_data_seek($listdata2, 0);

        if (isset($only_true))
        {
            while ($row = db_fetch_assoc($listdata2))
            {
                $count = 0;

                if (!isset($row[$only_true]))
                {
                ?>
                    <tr class="listtitle">
                        <?php
                        foreach ($row as $name => $value)
                        {
                            if (isset($show2[$name]))
                            {
                                $count++; ?>
                                <td class="<?=($count>$columns_left2)?"midlistsmall":"listsmall"?>">
                                    <?=$row[$name]?>
                                </td>
                            <? }
                        }
                        ?>
                    </tr>
                <?php
                }
            }
        }
        ?>
    </tbody>
</table>

<? endif ?>

<?php
if ($pi["filename_this"] == "report_current_load.php")
{
?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $("table.list").dataTable({
            "sDom": 't',
            "iDisplayLength": 500
        });
        var widthHeaderDiv = document.getElementById('headerDiv').offsetWidth;
        var widthTableDiv  = ((document.getElementById('t1')) ? document.getElementById('t1').offsetWidth : null);
        if(widthHeaderDiv && widthTableDiv && widthHeaderDiv > widthTableDiv ) {
            document.getElementById('t1').setAttribute('style','width:'+widthHeaderDiv+'px');
        } else {
            document.getElementById('headerDiv').setAttribute('style','width:'+widthTableDiv+'px');
        }
        
        
        var heightHeaderLeftDiv = document.getElementById('headerLeftDiv').offsetHeight;
        var heightHeaderRightDiv  = document.getElementById('headerRightDiv').offsetHeight;
        var heightHeaderLeftTopDiv  = document.getElementById('headerLeftTopDiv').offsetHeight;
        var heightHeaderLeftBottomDiv  = document.getElementById('headerLeftBottomDiv').offsetHeight;
        
        if(heightHeaderLeftDiv && heightHeaderRightDiv && heightHeaderLeftTopDiv && heightHeaderLeftBottomDiv && heightHeaderLeftDiv < heightHeaderRightDiv ) {
            var diff = heightHeaderRightDiv-heightHeaderLeftDiv+6;
            document.getElementById('headerLeftTopDiv').style.marginBottom = diff+'px';
            document.getElementById('headerLeftTopDiv').style.height = heightHeaderLeftTopDiv+'px';
            
        }
        
    });
</script>

<?php
}
?>

<?php
if ($pi["filename_this"] == "report_totals.php")
{
?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {   
        var c = [];
        c[0] = 0;
        c[1] = 0;
        c[2] = 0;
        c[3] = 0;
        c[4] = 0;
        c[5] = 0;
        c[6] = 0;
        c[7] = 0;
        c[8] = 0;
        
        var table = document.getElementById("t1");
        for (var i = 1, row; row = table.rows[i]; i++) {
           for (var j = 0, col; col = row.cells[j]; j++) {
             if(col.offsetWidth > c[j]) c[j] =  col.offsetWidth;
           }  
        }
        table = document.getElementById("t2");
        for (var i = 1, row; row = table.rows[i]; i++) {
           for (var j = 0, col; col = row.cells[j]; j++) {
             if(col.offsetWidth > c[j]) c[j] =  col.offsetWidth;
           }  
        }
        
        var tr = document.getElementById("t1");
        var ths = tr.getElementsByTagName("th");
        var tds = tr.getElementsByTagName("td");
        
        for(var i = 0; i < tds.length; i++) {
           tds[i].setAttribute('style','width:'+c[i]+'px');
        }
        for(var i = 1; i < ths.length; i++) {
           ths[i].setAttribute('style','width:'+c[i-1]+'px');
        }
        ths[0].setAttribute('style','font-size:12px');

        tr = document.getElementById("t2");
        ths = tr.getElementsByTagName("th");
        tds = tr.getElementsByTagName("td");

        for(var i = 0; i < tds.length; i++) {
           tds[i].setAttribute('style','width:'+c[i]+'px');
        }
        for(var i = 1; i < ths.length; i++) {
           ths[i].setAttribute('style','width:'+c[i-1]+'px');
        }
        ths[0].setAttribute('style','font-size:12px');
        
        //var wt1 = document.getElementById('t1').offsetWidth;
        //var wt2  = document.getElementById('t2').offsetWidth;
        //if(wt1 && wt1 > wt2 ) {
        //    document.getElementById('t2').setAttribute('style','width:'+wt1+'px');
        //} else {
        //    document.getElementById('t1').setAttribute('style','width:'+wt2+'px');
        //}
    });
</script>

<?php
}
?>