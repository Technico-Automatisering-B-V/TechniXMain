<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<? if ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){

        echo "<form id=\"" . $row["clientdepartments_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"" . $pi["filename_details"] . "\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"" . $row["clientdepartments_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["clientdepartments_id"] ."').submit();\">";
        $rows .= "<td class=\"list\">". $row["clientdepartments_name"] ."</td>";
        $rows .= "</tr>";
    }
    ?>

    <table class="list">
        <thead>
            <tr class="listtitle">
                <th class="list"><?=$lang["name"]?></th>
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

<? }else{ echo $lang["no_items_found"]; } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>