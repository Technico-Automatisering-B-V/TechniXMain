<table>
    <tr>
        <td>
            <form onsubmit="return confirm('<?=$lang["import_file_confirmation"]?>');" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
                <div id="tabs" class="filter">
                    <table>
                     <tr>
                         <td class="name">File:</td>
                         <td class="value">
                             <input type="text" id="filename"  value="" size="30" readonly>
                             <input type="file" id="uploadedfile" name="uploadedfile" style="display:none;"/>
                             <input type="button" value="<?=$lang["select_file"]?>" onclick="document.getElementById('uploadedfile').click();" />
                         </td>
                     </tr>
                    </table>
                    <div class="buttons">
                        <input type="submit" name="gosubmit" value="<?=$lang["import"]?>" />
                    </div>
                </div>
          </form>
        </td>
    </tr>
    <tr>
        <td>
            <?php if (isset($info) && $info != ""){echo $info;} ?>
        </td>
    </tr>
</table>

<script type="text/javascript">
    $("#uploadedfile").on("change", function() {
        var file = this.files[0],
        fileName = file.name;
        document.getElementById('filename').value = fileName;  
    });
</script>