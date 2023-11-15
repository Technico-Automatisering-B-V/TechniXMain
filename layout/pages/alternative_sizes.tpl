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
                            IFNULL(CONCAT(`sizes`.`name`, ' ', `modifications`.`name`),`sizes`.`name`) AS 'size_name'
                        FROM `arsimos`
                        INNER JOIN `articles` ON `arsimos`.`article_id` = articles.id
                        INNER JOIN `sizes` ON `arsimos`.`size_id` = sizes.id
                        LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
                        WHERE `arsimos`.`article_id` = ". $article_id ."
                        ORDER BY `sizes`.`position` ASC";

                $arsimos_sql = db_query($arsimos_query) or die("ERROR LINE ". __LINE__);

                if (db_num_rows($arsimos_sql) > 0){
                        echo "<form action=\"". $_SERVER["PHP_SELF"] ."#". $article_id ."\" name=\"". $article_id ."\" method=\"POST\">";
                        echo "<table>";
                            echo "<tr>";
                                echo "<th class=\"list\">". $lang["size"] ."</th>";
                                echo "<th class=\"list\">". $lang["alternative_size"] ."</th>";
                            echo "</tr>";

                            while ($arsimos_result = db_fetch_assoc($arsimos_sql)){

                                $codes_query = "SELECT
                                                    `arsimos`.`id` AS 'arsimo_id',
                                                    IFNULL(CONCAT(`sizes`.`name`, ' ', `modifications`.`name`),`sizes`.`name`) AS 'size_name'
                                    FROM `arsimos`
                                    INNER JOIN `articles` ON `arsimos`.`article_id` = articles.id
                                    INNER JOIN `sizes` ON `arsimos`.`size_id` = sizes.id
                                    INNER JOIN `alt_arsimos` ON `alt_arsimos`.`alt_arsimo_id` = `arsimos`.`id`
                                    LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
                                    WHERE `alt_arsimos`.`arsimo_id` = ". $arsimos_result["arsimo_id"] ."
                                    ORDER BY `sizes`.`position`, `modifications`.`id` ASC";

                                $codes_sql = db_query($codes_query) or die("ERROR LINE ". __LINE__ .": ". db_error());

                                echo "<tr>";
                                    echo "<td class=\"list midlist\">". $arsimos_result["size_name"] ."</td>";
                                    echo "<td class=\"list midlist\">";
                                        if (db_num_rows($codes_sql) == 0)
                                        {
                                             echo html_selectbox_array_submit("arsimo[". $arsimos_result["arsimo_id"] ."]", $article_sizes[$article_id], null, null, true) ;
                                        }
                                        else
                                        {
                                            while ($codes_result = db_fetch_assoc($codes_sql)){
                                                echo html_selectbox_array_submit("arsimo[". $arsimos_result["arsimo_id"] ."]", $article_sizes[$article_id], $codes_result["arsimo_id"], null, true) ;
                                            }
                                        }
                                    echo "</td>";
                                echo "</tr>";

                                db_free_result($codes_sql);
                            }

                        echo "</table>";
                        echo "<div class=\"buttons\">";
                            echo "<input type=\"reset\" name=\"reset\" value=\"". $lang["restore"] ."\" title=\"". $lang["restore"] ."\" />";
                            echo "<input type=\"submit\" name=\"submit\" value=\"". $lang["save"] ."\" title=\"". $lang["save"] ."\" />";
                        echo "</div>";
                        echo "</form>";

                }else{
                    echo $lang["no_items_found"];
                }

                echo "</div>";
        }

    }
?>
</div>