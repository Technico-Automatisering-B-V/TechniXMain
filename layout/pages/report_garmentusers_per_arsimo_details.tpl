<?php
$articledata = db_fetch_assoc($arsimodata);
?>
<!--<div class="filter">
    <table>
        <tr>
            <td class="name"><?=$lang["article"]?>:</td>
            <td class="value"><strong><?=$articledata["article"]?></strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang["size"]?>:</td>
            <td class="value"><strong><?=$articledata["size"]?></strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang["modification"]?>:</td>
            <td class="value"><strong><?=($articledata["modification"]) ? $articledata["modification"] : "<span class=\"empty\">". $lang["none"] ."</span>"?></strong></td>
        </tr>
    </table>
</div>-->

<div class="clear" />
<div style="float: left;"><h3><?=$lang["garmentusers"]?></h3>

<?php

$rows = "";

while ($row = db_fetch_assoc($listdata)){
    echo "<form id=\"". $row["gu_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garmentuser_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["gu_id"] ."\"></form>";

    $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["gu_id"] ."').submit();\">
        <td class=\"list\">". generate_garmentuser_label($row["gu_title"], $row["gu_gender"], $row["gu_initials"], $row["gu_intermediate"], $row["gu_surname"], $row["gu_maidenname"], $row["gu_personnelcode"]) ."</td>
        <td class=\"list\">". $row["gu_personnelcode"] ."</td>
    </tr>";

}

if (empty($rows)){
    echo $lang["no_items_found"];
}else{
?>

<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$lang["garmentuser"]?></td>
        <td class="list"><?=$lang["personnelcode"]?></td>
    </tr>
    <?=$rows?>
</table>

<?php
}
?>
</div>
<div style="float: left;margin-left:10px;"><h3><?=$lang["garments"]?></h3>
<?php

$rows = "";

while ($row = db_fetch_assoc($listdata_garments)){
    echo "<form id=\"". $row["g_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["g_id"] ."\"></form>";

    $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["g_id"] ."').submit();\">
         <td name=\"tcol-tag\" id=\"tcol-tag\" class=\"list\">". $row["garments_tag"] ."</td>
            <td name=\"tcol-article\" id=\"tcol-article\" class=\"list\">". ucfirst($row["articles_description"]) ."</td>
            <td name=\"tcol-size\" id=\"tcol-size\" class=\"midlist\">". $row["sizes_name"] ."</td>
            <td name=\"tcol-modification\" id=\"tcol-modification\" class=\"midlist\">";

        if ($row["modifications_name"]) { $rows .= $row["modifications_name"]; }else{ $rows .= "<span class=\"empty\">". $lang["none"] ."</span>"; }
        $rows .= "</td>";
        $rows .= "<td name=\"tcol-status\" id=\"tcol-status\" class=\"list\">";
        if(!empty($row["scanlocations_translate"]))
        {
            $rows .= $lang[$row["scanlocations_translate"]];
        }else{
            $rows .= "<span class=\"empty\">". $lang["none"] ."</span>";
        }
        $rows .= "</td>
    </tr>";

}

if (empty($rows)){
    echo $lang["no_items_found"];
}else{
?>

<table class="list">
        <tr class="listtitle">
            <td class="list"><?=$lang["tag"]?></td>
            <td class="list"><?=$lang["description"]?></td>
            <td class="midlist"><?=$lang["size"]?></td>
            <td class="midlist"><?=$lang["modification"]?></td>
            <td class="list"><?=$lang["status"]?></td>
        </tr>
        <?=$rows?>
</table>
<?php
}
?>
</div>