<? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>

<div id="exportcodes">
	<?php

    if ((count($articles)) !== 0){

        foreach ($articles as $article_id => $article_description) {
            echo "<span id=\"". $article_id ."\"></span>";
            echo "<h3><a href=\"#\">". $article_description ."</a></h3>";
            echo "<div>";

                $arsimos_query = "SELECT
                            `arsimos`.`id` AS 'arsimo_id',
                            `sizes`.`name` AS 'size_name',
                            `modifications`.`name` AS 'modification_name'
                        FROM `arsimos`
                        INNER JOIN `articles` ON `arsimos`.`article_id` = articles.id
                        INNER JOIN `sizes` ON `arsimos`.`size_id` = sizes.id
                        LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
                        WHERE `arsimos`.`article_id` = ". $article_id ."
                        ORDER BY `sizes`.`position` ASC";

                $arsimos_sql = db_query($arsimos_query) or die("ERROR LINE ". __LINE__);

                if (db_num_rows($arsimos_sql) > 0){

                    echo "<form action=\"". $_SERVER["PHP_SELF"] ."#". $article_id ."\" name=\"". $article_id ."\" method=\"POST\">";
                       echo "<input type=\"hidden\" name=\"aid\" value=\"" . $article_id . "\" />"; 
                       echo "<table>";
                            echo "<tr>";
                                echo "<th class=\"list\">". $lang["size"] ."</th>";
                                echo "<th class=\"list\">". $lang["modification"] ."</th>";
                                echo "<th class=\"list\">". $lang["exportcode"] ."</th>";
                                echo "<th class=\"list\">&nbsp;</th>";
                            echo "</tr>";

                            while ($arsimos_result = db_fetch_assoc($arsimos_sql)){

                                $codes_query = "SELECT
                                        `arsimos_importcodes`.`importcode`
                                   FROM `arsimos_importcodes`
                                  WHERE `arsimos_importcodes`.`arsimo_id` = ". $arsimos_result["arsimo_id"];

                                $codes_sql = db_query($codes_query) or die("ERROR LINE ". __LINE__ .": ". db_error());

                                echo "<tr>";
                                    echo "<td class=\"list midlist\">". $arsimos_result["size_name"] ."</td>";
                                    echo "<td class=\"list midlist\">". (empty($arsimos_result["modification_name"]) ? "<span class=\"empty\">". $lang["none"] ."</span>" : $arsimos_result["modification_name"]) ."</td>";
                                    echo "<td class=\"list codes\">";
                                        if (db_num_rows($codes_sql) == 0)
                                        {
                                            echo "<input name=\"arsimo[". $arsimos_result["arsimo_id"] ."][]\" type=\"text\" value=\"\" /> ";
                                        }
                                        else
                                        {
                                            $count = 0;

                                            while ($codes_result = db_fetch_assoc($codes_sql)){
                                                echo "<input name=\"arsimo[". $arsimos_result["arsimo_id"] ."][]\" type=\"text\" value=\"". $codes_result["importcode"] ."\" /><br />";
                                                $count++;
                                            }
                                        }
                                    echo "</td>";
                                    echo "<td class=\"list add\"><button class=\"add\" title=\"". $lang["add"] ."\">*</button></td>";
                                echo "</tr>";

                                db_free_result($codes_sql);
                            }

                        echo "</table>";
                        echo "<div class=\"buttons\">";
                            echo "<input type=\"reset\" name=\"reset\" value=\"". $lang["restore"] ."\" title=\"". $lang["restore"] ."\" /> ";
                            echo "<input type=\"submit\" name=\"submit\" value=\"". $lang["save"] ."\" title=\"". $lang["save"] ."\" /> ";
                            echo "<input type=\"submit\" name=\"fill\" value=\"". $lang["fill_all"] ."\" title=\"". $lang["fill_all"] ."\" />";
                        echo "</div>";
                    echo "</form>";

                }else{

                    echo $lang["no_items_found"];

                }

            echo "</div>";
        }

    }
    else
    {
        echo $lang["no_articles_found"];
    }
	?>
</div>

<script type="text/javascript">
    $(function() {
        $("button.add").button({
            icons:{
                primary: "ui-icon-circle-plus"
            },
            text: false
        }).click(function(e){
            e.preventDefault();

            var prevInput = $(this).parent().prev('td').find('input:first').clone().val('');
            prevInput.prependTo($(this).parent().prev('td'));

            $(this).parent('td').prev('td').find('input:first').after("<br />");
        });
    });
</script>