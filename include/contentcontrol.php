<?php

/**
 * Content control functions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

function date_en_to_nl($date)
{
    $a = explode('-', $date);
    $date_year = $a[0];
    $date_month = $a[1];
    $date_day = $a[2];

    $new_date = $date_day . '-' . $date_month . '-' . $date_year;

    return $new_date;
}

function generate_urlinfo_string($urlinfo, $new_limit_start = null)
{
    $urlinfo_string = '';

    foreach ($urlinfo as $name => $value)
    {
        if (!is_array($value) && strlen($value) > 0)
        {
            if ($name == 'limit_start')
            {
                if (isset($multi_urlinfo))
                {
                    $multi_urlinfo = 1;
                }

                if (isset($new_limit_start))
                {
                    $urlinfo_string .= $name . '=' . $new_limit_start . '&';
                } else {
                    $urlinfo_string .= $name . '=' . $value . '&';
                }
            }
            elseif ($name != 'limit_total' && $name != '')
            {
                if (isset($multi_urlinfo))
                {
                    $multi_urlinfo = 1;
                }
                $urlinfo_string .= $name . '=' . $value . '&';
            }
        }

        if ($name == 'where')
        {
            foreach ($value as $wherenum => $value)
            {
                if (isset($multi_urlinfo))
                {
                    $urlinfo_string .= '&';
                    $multi_urlinfo = 1;
                }
                $urlinfo_string .= $name . $wherenum . '=' . $value . '&';
            }
        }
    }
    return $urlinfo_string;
}

function result_infoline($pi, $urlinfo)
{
    if ($urlinfo['limit_total'] == 0)
    {
        return lang('no_items_found');
    }

    if ($urlinfo['limit_total'] <= $urlinfo['limit_num'])
    {
        return(lang('you_see_all') . ' ' . $urlinfo['limit_total'] . ' ' . lang('items_found'));
    }

    $result_info = lang('you_see') . ' ' . ($urlinfo['limit_start'] + 1) . '-';

    if ($urlinfo['limit_start'] + $urlinfo['limit_num'] <= $urlinfo['limit_total'])
    {
        $result_info .= $urlinfo['limit_start'] + $urlinfo['limit_num'];
    } else {
        $result_info .= $urlinfo['limit_total'];
    }

    $result_info .= ' ' . lang('of') . ' ' . $urlinfo['limit_total'] . ' ' . lang('items_found');
    return $result_info;
}

function generate_sortlink($column, $text, $pi, $urlinfo)
{
    $filename = (!empty($pi['filename_this'])) ? $pi['filename_this'] : $pi['filename_list'];
    $current_order_image = "";

    if ($urlinfo['order_by'] == $column)
    {
        if ($urlinfo['order_direction'] == 'DESC')
        {
            $new_order_direction = 'ASC';
            $current_order_image = '<img src="layout/images/sort_desc.png" />';
        } else {
            $new_order_direction = 'DESC';
            $current_order_image = '<img src="layout/images/sort_asc.png" />';
        }
    } else {
        $new_order_direction = 'ASC';
    }

    $urlinfo_string  = generate_urlinfo_string($urlinfo);
    $urlinfo_string .= '&order_by=' . $column . '&order_direction=' . $new_order_direction;

    $newurl = '<a href="' . $filename . '?' . $urlinfo_string . '" title="' . lang('sort') . '">' . $text . $current_order_image . '</a>';

    return $newurl;
}

function generate_pagination($pi, $urlinfo)
{
    $filename = (!empty($pi['filename_this'])) ? $pi['filename_this'] : $pi['filename_list'];
    $numeric_pages = '';
    $goto_pages = '';
    
    if ($urlinfo['limit_total'] > $urlinfo['limit_num'])
    {
        $number_of_pages = (($urlinfo['limit_num']) ? ceil($urlinfo['limit_total'] / $urlinfo['limit_num']) : 0);
        $current_page = (($urlinfo['limit_num']) ? ceil($urlinfo['limit_start'] / $urlinfo['limit_num']) + 1 : 1);
        $goto_pages .= lang('go_to') . ": <select onchange=\"location = this.value;\">";

        if ($number_of_pages > 12)
        {
            if ($current_page < 4 || ($current_page + 3) > $number_of_pages) {
                $midpage = round($number_of_pages / 2);
            } else {
                $midpage = $current_page;
                if ($midpage == 4){
                    $midpage += 2;
                } elseif (($midpage + 3) == $number_of_pages) {
                    $midpage -= 2;
                } elseif ($midpage == 5) {
                    $midpage++;
                } elseif (($midpage + 4) == $number_of_pages) {
                    $midpage--;
                }
            }
            $allpages = array(
                1, 2, 3, 'split',
                $midpage - 2, $midpage - 1, $midpage, $midpage + 1, $midpage + 2, 'split',
                $number_of_pages - 2, $number_of_pages - 1, $number_of_pages
            );
        } else {
            $allpages = array();
            for ($i = 1; $i <= $number_of_pages; $i++)
            {
                array_push($allpages, $i);
            }
        }

        foreach ($allpages as $page)
        {
            if ($page == 'split')
            {
                $numeric_pages .= ' ... ';
            } else {
                $new_limit_start = $urlinfo['limit_num'] * ($page - 1);
                if ($new_limit_start == $urlinfo['limit_start'])
                {
                    $numeric_pages .= ' <u>' . $page . '</u>';
                } else {
                    $urlinfo_string = generate_urlinfo_string($urlinfo, $new_limit_start);
                    $numeric_pages .= ' ' . '<a href="' . $filename . '?' . $urlinfo_string . '">' . $page . '</a>';
                }
            }
        }
        
        for ($page = 1; $page <= $number_of_pages; $page++) {
            $new_limit_start = $urlinfo['limit_num'] * ($page - 1);
            if ($new_limit_start == $urlinfo['limit_start'])
            {
                $goto_pages .= ' '. '<option value="" selected>' . $page . '</option>';
            } else {
                $urlinfo_string = generate_urlinfo_string($urlinfo, $new_limit_start);
                $goto_pages .= ' '. '<option value="' . $filename . '?' . $urlinfo_string . '">' . $page . '</option>';
            }
        }
    }

    // Generate the Previous button
    $previous_page = '';
    if ($urlinfo['limit_start'] == 0 || $urlinfo['limit_total'] == 0)
    {
        $previous_page .= ('<font class="grayed">' . lang('previous_page') . '</font>');
    } else {
        $limit_start_prev = $urlinfo['limit_start'] - $urlinfo['limit_num'];
        if ($limit_start_prev < 0)
        {
            $limit_start_prev = 0;
        }

        $urlinfo_string = generate_urlinfo_string($urlinfo, $limit_start_prev);
        $previous_page .= '<a href="' . $filename . '?'. $urlinfo_string . '">' . lang('previous_page') . '</a>';
    }

    // Generate the Next button
    $next_page = '';
    $limit_start_next = $urlinfo['limit_start'] + $urlinfo['limit_num'];
    if (($limit_start_next == $urlinfo['limit_total'] || $urlinfo['limit_total'] == 0) || ($limit_start_next > $urlinfo['limit_total']))
    {
        $next_page .= ' <font class="grayed">' . lang('next_page') . '</font>';
    } else {
        $urlinfo_string = generate_urlinfo_string($urlinfo, $limit_start_next);
        $next_page .= ' <a href="' . $filename . '?'. $urlinfo_string . '">' . lang('next_page') . '</a>';
    }
    
    $goto_pages .= "</select>";

    return $previous_page . '&nbsp;&nbsp;' . $numeric_pages . '&nbsp;&nbsp;' . $next_page . '&nbsp;&nbsp;' .  $goto_pages;
}

function generate_garmentuser_label($title=null, $gender=null, $initials=null, $intermediate=null, $surname=null, $maidenname=null, $personnelcode=null, $showpersonnelcode=false)
{
    $name = $surname;

    if (!empty($maidenname))
    {
        $name .= '-' . $maidenname;
    }

    $name .= ', ';

    if (empty($title))
    {
        if (!empty($gender))
        {
            $name .= ($gender == 'male') ? lang('mr') : lang('ms');
            $name .= ' ';
        }
    }
    else
    {
        $name .= $title . ' ';
    }
    if (!empty($initials))
    {
        $name .= $initials . ' ';
    }
    if (!empty($intermediate))
    {
        $name .= $intermediate;
    }
    if (!empty($personnelcode) && $showpersonnelcode != false)
    {
        $name .= ' (' . $personnelcode . ')';
    }

    return $name;
}

function generate_garmentusers_select($selected = null, $header = null, $args = null, $showempty = true)
{
    $query = "SELECT g1.id, g1.title, g2.gender, g2.initials, g2.intermediate, g2.personnelcode, g2.surname, g2.maidenname
            FROM garmentusers as g1
            INNER JOIN garmentusers g2 ON g2.id = g1.id
            WHERE g1.deleted_on IS NULL AND g2.deleted_on IS NULL
            ORDER BY g1.surname";
    $sql = mysqli_query($_SESSION["conn"],$query);

    echo '<select name="garmentuser_id"' . (($args) ? ' ' . $args : '') . '>';

    if ($showempty)
    {
        echo '<option value="">' . ((!empty($header)) ? $header : '') . '</option>';
    }

    while ($data = db_fetch_assoc($sql))
    {
        $addselected = '';
        if ($selected != '' && $data['id'] == $selected)
        {
            $addselected = ' selected="selected"';
        }

        $value = generate_garmentuser_label($data['title'], $data['gender'], $data['initials'], $data['intermediate'], $data['surname'], $data['maidenname'], $data['personnelcode'], true);
        echo '<option value="' . $data['id'] . '"' . $addselected . '>' . $value . '</option>';
    }
    echo '</select>';
}

?>
