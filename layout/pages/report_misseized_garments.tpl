<?php

$total = 0;
$rows = "";
$forms = "";

while ($row = db_fetch_assoc($listdata)){
    $forms .= "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garmentuser_details.php\">
        <input type=\"hidden\" name=\"page\" value=\"details\">
        <input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\">
        <input type=\"hidden\" name=\"gosubmit\" value=\"false\">
    </form>";

    $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">
        <td class=\"list\">". $row['date'] ."</td>
        <td class=\"list\">". $row['distributorlocation_name'] ."</td>
        <td class=\"list\">". generate_garmentuser_label($row['title'], $row['gender'], $row['initials'], $row['intermediate'], $row['surname'], $row['maidenname'], $row['personnelcode'], $showpersonnelcode=true) ."</td>
        <td class=\"midlist\">". (($row["alt_loaded"]!='0') ? "<img src=\"layout/images/dialog-ok.png\" />" : "<img src=\"layout/images/dialog-error.png\" />") ."</td>
    </tr>";

    $total++;

}

$article = db_fetch_assoc($article_info); ?>

<div class="filter">
    <table cellpadding="4"><tbody>
        <tr>
            <td class="name"><?=$lang['article']?>:</td>
            <td class="value"><strong><?=$article['description']?> (<?=$article['number']?>)</strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang['size']?>:</td>
            <td class="value"><strong><?=$article['size']?></strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang['modification']?>:</td>
            <td class="value"><strong><?=empty($article['modification']) ? "<span class=\"empty\">" . $lang['none'] . "</span>" : $article['modification']?></strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang['date']?>:</td>
            <td class="value"><strong><?=strftime($lang["dB_FULLDATE_FORMAT"], strtotime($_POST["date"]))?></strong></td>
        </tr>
        <tr>
            <td class="name"><?=$lang['total']?>:</td>
            <td class="value"><strong><?=$total?>x</strong></td>
        </tr>
    </tbody></table>
</div>

<div class="clear">
<?=$forms?>
<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$lang["date"]?></td>
        <td class="list"><?=$lang["distributorlocation"]?></td>
        <td class="list"><?=$lang["name"]?></td>
        <td class="list"><?=$lang["alternative_available"]?></td>
    </tr>
    <?php echo $rows; ?>
</table>
