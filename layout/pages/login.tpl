<?php $reference = (!empty($pi["reference"])) ? "?ref=" . $pi["reference"] : "" ?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?><?=$reference?>">

    <?php
    if (!empty($pi["note"])){ echo $pi["note"]; }
    ?>

    <br /><br />

    <center>
        <table><tr><td>
        <div class="filter">
            <table>
                <tr>
                    <td class="name"><?=$lang["username"]?>:</td>
                    <td class="value"><input type="text" name="username" size="27" value="<?=$username?>" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["password"]?>:</td>
                    <td class="value"><input name="password" size="27" type="password" /></td>
                </tr>
                <tr>
                    <td class="submit" align="right" colspan="2">
                        <input type="submit" name="loginsubmit" value="<?=$lang["login"]?>" title="<?=$lang["login"]?>">
                    </td>
                </tr>
            </table>
        </div>
        </td></tr></table>
    </center>

    <script type="text/javascript">
        if (document.dataform.username.value.length > 0) {
            document.dataform.password.focus();
        } else {
            document.dataform.username.focus();
        }
    </script>

</form>
