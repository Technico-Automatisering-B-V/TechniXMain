<style>
    .tab {
        display: inline-block;
        padding: 0px 12px;
        text-align: center;
        border: 1px solid #25824f;
        background-color: #1C5A39 ;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        width: 5em;
        }

    .tab p {
        display: inline-block;
        color: #f2f2f3;
    }

    .tabcontent {
        padding: 1.6em 1.4em;
        border: 1px solid #25824f;
        border-spacing: 7px;
        background-color: #f2f2f3;
        border-top-right-radius: 4px;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        }

    .fieldLabelTd {
        text-align: right;
    }

    .wwmInput {
        padding: 3px; 
        border: 1px solid #CCC;
        border-radius: 4px;
        width: 100%;
    }

    label {
        margin: 3px;
        color: #333333;
    }
</style>

<div class="tabcontent">
    <form method="POST">
        <table class="detailstab">
            <tr>
                <td class="fieldLabelTd"><label for="sInsertDate"><?=$lang["start_date_clothing_pack"]?>:</label></td>
                <td><input class="wwmInput" type="date" id="sInsertDate" name="sInsertDate" value="<?=$FormData['insert_date']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sLevertijd"><?=$lang["delivery_time_weeks"]?>:</label></td>
                <td><input class="wwmInput" type="number" id="sLevertijd" name="sLevertijd" value="<?=$FormData['delivery_time']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sInCirculatieBrengen"><?=$lang["bring_into_circulation_weeks"]?>:</label></td>
                <td><input class="wwmInput" type="number" id="sInCirculatieBrengen" name="sInCirculatieBrengen" value="<?=$FormData['in_circulation_time']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="iInterval"><?=$lang["order_instances"]?>:</label></td>
                <td><input class="wwmInput" type="number" min="1" max="52" id="iInterval" name="iInterval" value="<?=$FormData['checks_interval']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sInsertPrice"><?=$lang["data_input_cost"]?>(&euro;):</label></td>
                <td><input class="wwmInput" type="number" step="0.01" id="sInsertPrice" name="sInsertPrice" value="<?=$FormData['insert_price']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sIncrement"><?=$lang["opslag_cost_percent"]?>(%):</label></td>
                <td><input class="wwmInput" type="number" step="0.01" id="sIncrement" name="sIncrement" value="<?=$FormData['increment']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sPrijsVerhoging" title="<?=$lang["new_price_help_message"]?>"><?=$lang["price_increase_percent"]?>(%):</label></td>
                <td><input class="wwmInput" type="number" step="0.01" min="-100" max="100" id="sPrijsVerhoging" name="sPrijsVerhoging" value=""/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sBeheerPrijs" title="Beheer prijs">Beheer prijs (&euro;):</label></td>
                <td><input class="wwmInput" type="number" step="0.01" min="-100" max="100" id="sBeheerPrijs" name="sBeheerPrijs" value="<?=$FormData['maintenance_cost']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sControlePercentage" title="Controle bij">Controle bij (%):</label></td>
                <td><input class="wwmInput" type="number" step="1" min="0" max="100" id="sControlePercentage" name="sControlePercentage" value="<?=$FormData['checking_percentage']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sUiterste" title="Uiterste Grens">Uiterste grens:</label></td>
                <td><input class="wwmInput" type="number" step="1" min="0" max="250" id="sUiterste" name="sUiterste" value="<?=$FormData['maxlimit']?>"/></td>
            </tr>
            <tr>
                <td class="fieldLabelTd"><label for="sFlag" title="Flag">Flag:</label></td>
                <td><select class="wwmInput" id="sFlag" name="sFlag">
                        <option value='Wasserij' <?=(($FormData['flag_action'] == "Wasserij") ? 'selected' : null)?>>Wasserij</option>
                        <option value='Ziekenhuis' <?=(($FormData['flag_action'] == "Ziekenhuis") ? 'selected' : null)?>>Ziekenhuis</option>
                </select></td>
            </tr>
        </table>
</div>
        <table>
            <tr>
                <td>
                    <input class="wwmSubmit" type="submit" value="<?=$lang["save"]?>">
                    <input class="wwmSubmit" type="reset" value="<?=$lang["reset"]?>">
                </td>
            </tr>
        </table>
    </form>


<?php
    if(isset($pi['note']) && !empty($pi['note'])) {
        echo "<hr>";
        echo $pi['note'];
    }
?>