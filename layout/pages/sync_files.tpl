<table class="list">
    <tr class="listtitle">
        <td class="listsmall"><?=$lang["type"]?></td>
        <td class="listsmall"><?=$lang["file_available"]?></td>
        <td class="listsmall"><?=$lang["modified"]?></td>
        <td class="listsmall"><?=$lang["save"]?></td>
    </tr>

    <? foreach ($importers as $name => $file): ?>
    <form id="<?=$name?>" enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
        <input type="hidden" name="id" value="<?=$name?>">
        <input type="hidden" name="file" value="<?=$file?>">
        <tr class="listnc">
            <td class="list"><?=$lang[$name]?></td>
            <td class="list"><?=(!file_exists($file)?"<font style=\"color:#FF0000\">". $lang["no"] ."</font>" : "<font style=\"color:#009900\">". $lang["yes"] ."</font>") ?></td>
            <td class="list">
            <?
            if (file_exists($file)) {
                 echo date("d F Y H:i:s", filemtime($file));
            }
            ?>
            </td>
            <td style="text-align: center" class="list <?=(file_exists($file)?"lpointer\" onClick=\"document.getElementById('".$name."').submit();\"><span style=\"display: inline-block\" class=\"ui-icon ui-icon-arrowstop-1-s\"></span>":"\" >") ?> </td>
        </tr>
    </form>
    <? endforeach ?>
</table>