<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["group"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("emailaddresses_group", $groups, $urlinfo['emailaddresses_group'], $lang["make_a_choice"], $selected=null)?></td>
            </tr>
        </table>
    </div>
</form>

<div class="clear" />

<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){

        echo "<form id=\"" . $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"" . $pi["filename_details"] . "\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"" . $row["id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">";
        $rows .= "<td class=\"list\">". $row["name"] ."</td>";
        $rows .= "<td class=\"list\">". $row["email_address"] ."</td>";
        $rows .= "<td class=\"list\">". $lang[$row["group"]] ."</td>";
        $rows .= "</tr>";
    }
    ?>

    <table class="list">
        <tr class="listtitle">
            <th class="list"><?=$sortlinks["name"]?></th>
            <th class="list"><?=$sortlinks["email_address"]?></th>
            <th class="list"><?=$sortlinks["group"]?></th>
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