
<?php
if (isset($distributorlocations) && $distributorlocations != "") {
    $tab_li = "";
    $tab_div = "";

    while ($row = db_fetch_assoc($distributorlocations)){
        $tab_li .= "<li><a href=\"#tab-". $row["id"] ."\">". $row["name"] ."</a></li>";
        $tab_div .= "<div id=\"tab-". $row["id"] ."\"><iframe id=\"monitor-". $row["id"] ."\" src=\"http://". $row["external_ip_address"] ."/xgs/status/". $row["id"] ."\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" vspace=\"0\" hspace=\"0\" width=\"100%\" height=\"500\">". $lang['Cannot_display_content'] ."</iframe></div>";
    }
    ?>

    <div id="tabs">
        <ul>
            <?=$tab_li?>
        </ul>
        <?=$tab_div?>
    </div>
	<p id="demo"></p>

<? } ?>