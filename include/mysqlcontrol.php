<?php

/**
 * MySQL control functions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */


function db_query($sql) {
    //return the query
    return mysqli_query($_SESSION["conn"],$sql);
}

function db_error() {
    return mysqli_error();
}

function db_free_result($result) {
    return mysqli_free_result($result);
}

function db_fetch_array($result) {
    return mysqli_fetch_array($result, MYSQLI_BOTH);
}

function db_fetch_num($result) {
    return mysqli_fetch_array($result, MYSQLI_NUM);
}

function db_fetch_assoc($result) {
    return mysqli_fetch_assoc($result);
}

function db_fetch_row($result) {
    return mysqli_fetch_row($result);
}

function db_num_rows($result) {
    return mysqli_num_rows($result);
}

function db_affected_rows() {
    return mysqli_affected_rows();
}

function db_num_fields($result) {
    return mysqli_num_fields($result);
}

function db_data_seek($result, $row) {
    return mysqli_data_seek($result, $row);
}

function db_read_last_insert_id() {
        $sql = "SELECT LAST_INSERT_ID( )";

        //return the query
        return db_query($sql);
}

function db_verify_existence($table, $column, $value, $column2 = null, $value2 = null) {
    $sql = "SELECT COUNT(*) FROM `" . $table . "` WHERE `" . $column . "` " . ((isset($value)) ? "= '" . $value . "'" : "IS NULL")
            . ((!empty($column2)) ? " AND `" . $column2 . "` " . ((isset($value2)) ? "= '" . $value2 . "'" : "IS NULL") : "");
    $result = db_query($sql);
    $count = db_fetch_array($result);
    db_free_result($result);

    //when no count, return false, else return true
    if ($count[0] == 0) return false; else return true;
}

function db_read_max_value_from($table, $column) {
    $sql = "SELECT MAX(`" . $column . "`) FROM `" . $table . "`";

    //return the query
    return db_query($sql);
}

function db_read_max_value_from_where($table, $column, $wherecolumn, $wherevalue) {
    $sql = "SELECT MAX(`" . $column . "`) FROM `" . $table . "` WHERE `" . $wherecolumn . "` = '" . $wherevalue . "'";

    //return the query
    return db_query($sql);
}

function db_count($table, $columns = null, $conditions = null) {
    return db_read($table, "[COUNT]" . $columns, $conditions);
}

function db_read($table, $columns = null, $conditions = null) {
    /**
     * First part of the query (required): SELECT
     *
     * Requires variables: $table, $columns
     */

    if (isset($columns) && substr($columns, 0, 7) === "[COUNT]") {
        $columns = substr($columns, 0 - strlen($columns) + 7);
        $countonly = true;
    }

    if (isset($countonly)) {
        $select_columns = "COUNT(*)";
    } elseif (!empty($columns)) {

        //convert the string of columns to an array
        $select_columns = explode(" ", $columns);
        //therefore, for these columns we add an AS name, replacing the dot for an underscore.
        //we use this AS to avoid columnname collisions in the SELECT (eg: 2 tables with a "name" column).
        //while we're here, we add backquotes where necessary to validize our query.
        foreach ($select_columns as $column => &$value)
        {
            switch($value)
            {
                case (preg_match("(^COUNT\((.)*\)$)i", $value) ? $value : !$value) :
                    $alias = str_replace(".", "_", "count_" . substr($value, 6, -1));
                    $value = str_replace($patterns, $replacements, $value) ." AS '$alias'";
                    $patterns = array("(", ".", ")");
                    $replacements = array("(`", "`.`", "`)");
                    break;
                case (preg_match("(^MIN\((.)*\)$)i", $value) ? $value : !$value) :
                    $alias = str_replace(".", "_", "min_" . substr($value, 4, -1));
                    $patterns = array("(", ".", ")");
                    $replacements = array("(`", "`.`", "`)");
                    $value = str_replace($patterns, $replacements, $value) ." AS '$alias'";
                    break;
                case (preg_match("(^MAX\((.)*\)$)i", $value) ? $value : !$value) :
                    $alias = str_replace(".", "_", "max_" . substr($value, 4, -1));
                    $patterns = array("(", ".", ")");
                    $replacements = array("(`", "`.`", "`)");
                    $value = str_replace($patterns, $replacements, $value) ." AS '$alias'";
                    break;
                case (preg_match("(^SUM\((.)*\)$)i", $value) ? $value : !$value) :
                    $alias = str_replace(".", "_", "sum_" . substr($value, 4, -1));
                    $patterns = array("(", ".", ")");
                    $replacements = array("(`", "`.`", "`)");
                    $value = str_replace($patterns, $replacements, $value) ." AS '$alias'";
                    break;
                case (substr($value, 0, 14) == "LAST_INSERT_ID"):
                    $value = "LAST_INSERT_ID( )";
                    break;
                default:
                    $alias = str_replace(".", "_", $value);
                    $patterns = array(".");
                    $replacements = array("`.`");
                    $value = "`" . str_replace($patterns, $replacements, $value) . "`";
                    if (strpos($value, ".")) $value .= " AS '$alias'";
            }
        }

        //convert the array back to a string of columns prepared to SELECT
        $select_columns = implode(", ", $select_columns);

    } else {
        $select_columns = "*";
    }

    $sql = "SELECT " . $select_columns . " FROM `" . $table . "`";


    /**
     * Second part A of the query (optional): INNER JOIN(s)
     *
     * Requires 3-dimensional array: $conditions["inner_join"]["num"]
     * Example: $conditions["inner_join"]["1"] = "jointable wherevalue isvalue"
     *          $conditions["inner_join"]["2"] = "jointable wherevalue isvalue"
     *          $conditions["inner_join"]["3"] = "jointable wherevalue isvalue"
     */

    if (isset($conditions["inner_join"])) {
        //we've got JOIN input which needs to be a 3-dimensional array. we add each floor's data as an INNER JOIN.
        //don't forget any required columns in the jointable should have been added to $columns earlier, for the SELECT.
        foreach ($conditions["inner_join"] as $join => $value ) {
            //since we got one or more arrays in the processed ["join"] array, we explode the values of each array inside
            $join = explode(" ", $value);
            //add each LEFT JOIN to our query
            $sql .= " INNER JOIN `" . $join[0] . "` ON `" . str_replace(".", "`.`", $join[1]) . "` = `" . str_replace(".", "`.`", $join[2]) . "`";
        }
    }


    /**
     * Second part B of the query (optional): JOIN(s)
     *
     * Requires 3-dimensional array: $conditions["left_join"]["num"]
     * Example: $conditions["join"]["1"] = "jointable wherevalue isvalue"
     *          $conditions["join"]["2"] = "jointable wherevalue isvalue"
     *          $conditions["join"]["3"] = "jointable wherevalue isvalue"
     */

    if (isset($conditions["join"])) {
        //we've got JOIN input which needs to be a 3-dimensional array. we add each floor's data as a LEFT JOIN.
        //don't forget any required columns in the jointable should have been added to $columns earlier, for the SELECT.
        foreach ($conditions["join"] as $join => $value ) {
            //since we got one or more arrays in the processed ["join"] array, we explode the values of each array inside
            $join = explode(" ", $value);
            //add each LEFT JOIN to our query
            $sql .= " JOIN `" . $join[0] . "` ON `" . str_replace(".", "`.`", $join[1]) . "` = `" . str_replace(".", "`.`", $join[2]) . "`";
        }
    }

    /**
     * Second part C of the query (optional): LEFT JOIN(s)
     *
     * Requires 3-dimensional array: $conditions["left_join"]["num"]
     * Example: $conditions["left_join"]["1"] = "jointable wherevalue isvalue"
     *          $conditions["left_join"]["2"] = "jointable wherevalue isvalue"
     *          $conditions["left_join"]["3"] = "jointable wherevalue isvalue"
     */

    #if (isset($conditions["join"])) $conditions["left_join"] = $conditions["join"];
    if (isset($conditions["left_join"])) {
        //we've got JOIN input which needs to be a 3-dimensional array. we add each floor's data as a LEFT JOIN.
        //don't forget any required columns in the jointable should have been added to $columns earlier, for the SELECT.
        foreach ($conditions["left_join"] as $join => $value ) {
            //since we got one or more arrays in the processed ["join"] array, we explode the values of each array inside
            $join = explode(" ", $value);
            //add each LEFT JOIN to our query
            $sql .= " LEFT JOIN `" . $join[0] . "` ON `" . str_replace(".", "`.`", $join[1]) . "` = `" . str_replace(".", "`.`", $join[2]) . "`";
        }
    }


    /**
     * Third part of the query (optional): WHERE clause(s)
     *
     * Requires variables: $conditions["search"]
     * Example: $conditions["search"] = "any string you want";
     *
     * OR
     *
     * Requires 3-dimensional array: $conditions["where"]["num"]
     * Example: $conditions["where"]["1"] (syntax: "column = value")
     *          $conditions["where"]["2"] (syntax: "column NOT value")
     *          $conditions["where"]["3"] (syntax: "column is null")
     *          $conditions["where"]["4"] (syntax: "column = value OR column isnot null")
     */

    if (!empty($conditions["search"])) {
        //convert the string of columns to an array (again)
        $where_columns = explode(" ", $columns);

        //add slashes to quotes in our string of values (else we possibly break the query).
        //it contains the values we're going to search for.
        $where_values = addslashes($conditions["search"]);
        //convert the string of values to an array
        $where_values = explode(" ", $where_values);

        //now lets generate the clause(s).
        //its hard to explain whats going on here, so let me show you an example.
        //if we want to search for 'val1' and 'val2', and we were selecting the
        //columns 'col1' and 'col2', it is being parsed as follows:
        //where ((col1 = val1) or (col2 = val1)) and ((col1 = val2) or (col2 = val2))
        //this may seem a bit odd, but its actually quite smart. also, the query
        //automatically grows as you search for more words in your search string.
        foreach ($where_values as $where_value) {
            $morewhere = true;
            if (isset($multi_values)) $sql .= ") AND ("; else $sql .= " WHERE ("; $multi_values = 1;
            foreach ($where_columns as $where_column) {
                if(substr($where_column, -3, 3) !== ".id" && $where_column !== "id"){
                    if (isset($multi_columns)) $sql .= " OR "; $multi_columns = 1;
                    $sql .= "(`" . str_replace(".", "`.`", $where_column) . "` LIKE '%" . $where_value . "%')";
                }
            }
            unset($multi_columns);
        }
        $sql .= ")";
    }

    if (!empty($conditions["where"]) && is_array($conditions["where"])) {
        //we've got WHERE input which needs to be a multidimensional array. we add each floor's data as a WHERE clause.
        foreach ($conditions["where"] as $where => $value) {
            //add each WHERE clause to our query with possible AND operator
            if (isset($morewhere)) $sql .= " AND"; else { $sql .= " WHERE"; $morewhere = 1; }
		
            $moreor = false;
			for ($i = 0; $i < sizeof($where); $i = $i + 4) {
                //since we got one or more arrays in the processed ["where"] array, we explode the values of each sub-array inside
                $oper = null;
                $where = explode(" ", $value);
                if (isset($where[2+$i])) {
                    switch(strtolower($where[1+$i])) {
                        case "not":     $oper = "<>"; 		break;
                        case "<>":      $oper = "<>"; 		break;
                        case "<":       $oper = "<"; 		break;
                        case ">":       $oper = ">"; 		break;
                        case "<=":      $oper = "<="; 		break;
                        case ">=":      $oper = ">="; 		break;
                        case "like":    $oper = "LIKE"; 	break;
                        case "is":      $oper = "IS"; 		break;
                        case "isnot":   $oper = "IS NOT"; 	break;
                        case "notis":   $oper = "NOT IS";	break;
                        case "in":      $oper = "IN"; 		break;
                        default:        $oper = "=";
                    }
                } else {
                    $where[2+$i] = $where[1+$i];
                    $oper = "=";
                }

                $sql .= (($moreor) ? " " . strtoupper($where[$i-1]) . " " : " (") .  "`" . str_replace(".", "`.`", $where[$i]) . "` " . $oper . " ";

                //if where[2] is a numerical value or null, we want no leading and trailing "'", else we add them.
                if(strtolower($where[2+$i]) == "null" || $oper === "IN") $sql .= strtoupper($where[2+$i]);
                else $sql .= "'" . $where[2+$i] . "'";

                $moreor = true;
            }
            $sql .= ")";
            $morewhere = true;
        }
    }

    /** Fourth part of the query (optional): GROUP BY
         *
         * Requires variables: $conditions["group_by"]
         * Example: $conditions["group_by"] = "table.column column2 table2.column3"
         */

    if (!empty($conditions["group_by"])) {
        $group_bys = explode(" ", $conditions["group_by"]);
        $sql .= " GROUP BY ";
        foreach ($group_bys as $id => $group_by) {
            if (isset($multigroupby)) $sql .= ", ";
                           $sql .= "`" . str_replace(".", "`.`", $group_by) . "`";
            $multigroupby = true;
        }
    }

    /**
     * Fifth part of the query (optional and excluded from COUNTs): ORDER BY
     *
     * Requires variables: $conditions["order_by"]
     * Example: $conditions["order_by"] = "column"
     *
     * Optional variables: $conditions["order_direction"] (ASC or DESC only, defaults to ASC)
     * Example: $conditions["order_direction"] = "DESC";
     */

    if (!isset($countonly)) {
        if (!empty($conditions["order_by"])) {
            if (!isset($conditions["order_direction"])) $conditions["order_direction"] = "ASC";
            $order_bys = explode(" ", $conditions["order_by"]);
            $sql .= " ORDER BY ";
            foreach ($order_bys as $id => $order_by) {
                if (isset($multiorderby)) $sql .= ", ";
                            $sql .= "`" . str_replace(".", "`.`", $order_by) . "` " . $conditions["order_direction"];
                $multiorderby = true;
            }
        }
    }

    /**
     * Sixth part of the query (optional and excluded from COUNTs): LIMIT
     *
     * Requires variables: $conditions["limit_start"], $conditions["limit_num"]
     * Example: $conditions["limit_start"] = "10"; //start reading at the 11th row (counting from 0)
     *          $conditions["limit_num"] = "20"; //brings us row 11 to 30
     */

    if (!isset($countonly)) {
        if (isset($conditions["limit_start"]) && $conditions["limit_start"] >= 0 &&
            isset($conditions["limit_num"]) && $conditions["limit_num"] > 0) {
            $sql .= " LIMIT " . $conditions["limit_start"] . ", " . $conditions["limit_num"];
        }
    }

    /** Seventh part of the query (optional and excluded from COUNTs): HAVING
         *
         * Requires variables: $conditions["group_by"]
         * Example: $conditions["having"] = "expr"
         */

        if (!isset($countonly)) {
            if (!empty($conditions["having"])) {
                $sql .= " HAVING " . $conditions["having"];
            }
        }

    /**
     * Final moves
     */

    //return the query
    return db_query($sql);
}

function db_read_where($table, $name, $value) {
    $sql = "SELECT * FROM `" . $table . "` WHERE `" . $name . "` = '" . $value . "'";

    //return the query
    return db_query($sql);
}

function db_read_row_by_id($table, $id) {
    $sql = "SELECT * FROM `" . $table . "` WHERE `id` = '" . $id . "'";

    //return the query
    return db_query($sql);
}

function db_insert($table, $data) {
	$columns = ""; $values = "";
    foreach ($data as $column => $value) {
        if (isset($multi_insert)) { $columns .= ", "; $values .= ", "; } $multi_insert = 1;
        $columns .= "`" . $column . "`";
        if (substr($value, 0, 14) === "LAST_INSERT_ID") {
            $values .= "LAST_INSERT_ID( )";
        } elseif ($value === "NOW()") {
            $values .= "NOW()";
        } else {
            $values .= (is_null($value)) ? "NULL" : "'" . mysqli_real_escape_string($_SESSION["conn"], $value) . "'";
        }
    }

    $sql = "INSERT INTO `" . $table . "` (" . $columns . ") VALUES (" . $values . ")";

    //return the query
    return db_query($sql);
}

function db_update($table, $id, $data) {
    $columnsvalues = "";
    $values = "";

    foreach ($data as $column => $value) {
        if ($column != "id") {
            if (isset($multi_update)) {
                $columnsvalues .= ", ";
                $values .= ", ";
            }
            $multi_update = 1;

            $columnsvalues .= "`" . $column . "` = ";
            if ($value === "NOW()") {
                $columnsvalues .= "NOW()";
                $values .= "NOW()";
            } else {
                $columnsvalues .= (is_null($value)) ? "NULL" : "'" . mysqli_real_escape_string($_SESSION["conn"], $value) . "'";
                $values .= (is_null($value)) ? "NULL" : "'" . $value . "'";
            }
        }
    }

    $sql = "UPDATE `" . $table . "` SET " . $columnsvalues . " WHERE `id` = '" . $id . "'";

    //return the query
    return db_query($sql);
}

function db_update_where($table, $nam, $val, $data) {
    $columnsvalues = "";
    foreach ($data as $column => $value) {
        if ($column != "id") {
            if (isset($multi_update)) {
                $columnsvalues .= ", ";
                $values .= ", ";
            }
            $multi_update = 1;

            $columnsvalues .= "`" . $column . "` = ";
            if ($value === "NOW()") {
                $columnsvalues .= "NOW()";
                $values .= "NOW()";
            } else {
                $columnsvalues .= (is_null($value)) ? "NULL" : "'" . mysqli_real_escape_string($_SESSION["conn"], $value) . "'";
                $values .= (is_null($value)) ? "NULL" : "'" . $value . "'";
            }
        }
    }

    $sql = "UPDATE `" . $table . "` SET " . $columnsvalues . " WHERE `" . $nam . "` = '" . mysqli_real_escape_string($_SESSION["conn"], $val) . "'";

    //return the query
    return db_query($sql);
}

function db_delete($table, $id) {
    $sql = "DELETE FROM `" . $table . "` WHERE `id` = '" . $id . "'";

    //return the query
    return db_query($sql);
}

function db_delete_where($table, $column, $value) {
    $sql = "DELETE FROM `" . $table . "` WHERE `" . $column . "` = '" . $value . "'";

    //return the query
    return db_query($sql);
}

?>
