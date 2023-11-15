<?php

/**
 * Get URL functions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Function to retrieve a 'search' value from the URI, or set to default
 * (null) is no value can be found
 */
function geturl_search($c = false, $cg = null)
{
    $return = null;

    if (isset($_GET['search']) && strlen($_GET['search']) > 0)
    {
        $return = trim($_GET['search']);
    }
    elseif (isset($_POST['search']) && strlen($_POST['search']) > 0)
    {
         $return = trim($_POST['search']);
    }
    elseif (isset($_GET['dsearch']) && strlen($_GET['dsearch']) > 0)
    {
         $return = trim($_GET['dsearch']);
    }
    elseif (isset($_POST['dsearch']) && strlen($_POST['dsearch']) > 0)
    {
         $return = trim($_POST['dsearch']);
    }
    
    if(!$c) {
        return $return;
    } else {
        return convertTag($return, $cg);
    }
}

/**
 * Function to retrieve an 'order' value from the URI, or set to default
 * (the first columnname in $columns) if no value can be found
 */
function geturl_order_by($columns)
{
    if (isset($_GET['order_by']))
    {
        $order_by = $_GET['order_by'];
    } else {
        //default: use the first column in our own $columns list
        $order_by = explode(' ', $columns);
        $order_by = $order_by[0];
    }
    return $order_by;
}

/**
 * Function to retrieve an 'order_direction' value from the URI, or set to
 * ascending by default ('ASC') if no value can be found
 */
function geturl_order_direction($default = null)
{
    return (isset($_GET['order_direction'])) ? $_GET['order_direction'] : ((strtoupper($default) == 'DESC') ? $order_direction = 'DESC' : $order_direction = 'ASC');
}

/**
 * Function to retrieve an 'limit_start' value from the URI, or set to
 * default (0) if no value can be found
 */
function geturl_limit_start()
{
    return (isset($_GET['limit_start'])) ? $_GET['limit_start'] : 0;
}

/**
 * Function to retrieve an 'order_direction' value from the URI, or set to
 * default ($default parameter) if no value can be found
 */
function geturl_limit_num($d)
{
    return (isset($_GET['limit_num'])) ? $_GET['limit_num'] : $d;
}

/**
 * Function to retrieve 'where' clauses from the URI
 */
function geturl_where()
{
    $where = array();
    for ($num = 0; isset($_GET['where' . $num]); $num++)
    {
        $where[$num] = $_GET['where' . $num];
    }
    return $where;
}
