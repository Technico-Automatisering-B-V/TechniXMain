<?php

/**
 * HTML object functions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */


/**
 * html_selectbox_array()
 * Function to generate an HTML select box (dropdown list)
 * using an array as input instead of an SQL resource
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox_array($name, $options, $selected = null, $header = null, $showempty = true, $disabled = false, $args = '')
{
    $addselected = '';

    if (!empty($options))
    {
        echo '<select name="' . $name . '"' . (($args) ? ' ' . $args : '') . '>';
        if ($showempty)
        {
            echo '<option value="">' . ((!empty($header)) ? $header : '') . '</option>';
        }

        if (!empty($options))
        {
            foreach ($options as $id => $value)
            {
                if ($selected != '' && $id == $selected)
                {
                    $addselected = ' selected="selected"';
                }

                echo '<option value="' . $id . '"' . $addselected . '>' . $value . '</option>';
                $addselected = '';
            }
        }
        echo '</select>';
    } else {
        echo '<span class="empty">' . $lang['none'] . '</span>';
    }
}


/**
 * html_selectbox_array_submit()
 * Function to generate an HTML select box (dropdown list) using
 * an array as input and submits a form when the selection changes
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox_array_submit($name, $options, $selected = null, $header = null, $showempty = true, $disabled = false, $args = "") {
    $addselected = "";
    if (!empty($options)) {
        echo "<select". (($disabled) ? " disabled=\"disabled\" class=\"disabled\"" : "") ." name=\"". $name ."\" ". (($args) ? $args : "") ." onchange=\"submit();\">";
        if ($showempty){ echo "<option value=\"\">" . ((!empty($header)) ? $header : "") . "</option>"; }
        foreach ($options as $id => $value) {
            if ($selected != "" && $id == $selected) $addselected = " selected=\"selected\"";
            echo "<option value=\"". $id ."\"". $addselected .">". $value ."</option>";
            $addselected = "";
        }
        echo "</select>";
    } else {
        echo "<span class=\"empty\">". $lang["none"] ."</span>";
    }
}

/**
 * html_selectbox_array_out_submit()
 * Function to generate an HTML select box (dropdown list) using
 * an array as input and submits a form when the selection changes without echo
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox_array_out_submit($name, $options, $selected = null, $header = null, $showempty = true, $disabled = false, $args = "") {
    $addselected = "";
    if (!empty($options)) {
        $out = "<select". (($disabled) ? " disabled=\"disabled\" class=\"disabled\"" : "") ." name=\"". $name ."\" ". (($args) ? $args : "") ." onchange=\"submit();\">";
        if ($showempty){ $out .= "<option value=\"\">" . ((!empty($header)) ? $header : "") . "</option>"; }
        foreach ($options as $id => $value) {
            if ($selected != "" && $id == $selected) $addselected = " selected=\"selected\"";
            $out .= "<option value=\"". $id ."\"". $addselected .">". $value ."</option>";
            $addselected = "";
        }
        $out .= "</select>";
    } else {
        $out .= "<span class=\"empty\">". $lang["none"] ."</span>";
    }
    return $out;
}

/**
 * html_selectbox_array_submit_onchange()
 * Function to generate an HTML select box (dropdown list) using
 * an array as input and do the added method on the form when the selection changes
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox_array_submit_onchange($name, $options, $onchange, $selected = null, $header = null, $showempty = true, $disabled = false, $args = "") {
    $addselected = "";
    if (!empty($options)) {
        echo "<select". (($disabled) ? " disabled=\"disabled\" class=\"disabled\"" : "") ." name=\"". $name ."\" ". (($args) ? $args : "") ." onchange=\"".$onchange."\">";
        if ($showempty){ echo "<option value=\"\">" . ((!empty($header)) ? $header : "") . "</option>"; }
        foreach ($options as $id => $value) {
            if ($selected != "" && $id == $selected) $addselected = " selected=\"selected\"";
            echo "<option value=\"". $id ."\"". $addselected .">". $value ."</option>";
            $addselected = "";
        }
        echo "</select>";
    } else {
        echo "<span class=\"empty\">". $lang["none"] ."</span>";
    }
}


/**
 * html_selectbox()
 * Function to generate an HTML select box (dropdown list)
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox($name, $options, $selected = null, $header = null, $args = null) {
    $addselected = "";
    if (!empty($options)) {
        echo "<select name=\"". $name ."\"". (($args) ? " " . $args : "") .">";
        echo "<option value=\"\">". ((!empty($header)) ? $header : "") ."</option>";
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) {
            if ($row[0] == $selected) $addselected = " selected=\"selected\"";
            echo '<option value="' . $row[0] . '"' . $addselected . '>' . $row[1] . '</option>';
            $addselected = "";
        }
        echo "</select>";
    } else {
        echo "<span class=\"empty\">". $lang["none"] ."</span>";
    }
}

/**
 * html_selectbox_multi_value()
 * Function to generate an HTML select box (dropdown list)
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id in $options
 */

function html_selectbox_multi_value($name, $options, $selected = null, $header = null, $args = null) {
    $addselected = "";
    if (!empty($options)) {
        echo "<select name=\"". $name ."\"". (($args) ? " " . $args : "") .">";
        echo "<option value=\"\">". ((!empty($header)) ? $header : "") ."</option>";
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) {
            if ($row[0] == $selected) $addselected = " selected=\"selected\"";
            echo '<option value="' . $row[0] . '"' . $addselected . '>' . $row[1] . ' - ' . $row[2] . '</option>';
            $addselected = "";
        }
        echo "</select>";
    } else {
        echo "<span class=\"empty\">". $lang["none"] ."</span>";
    }
}


/**
 * html_selectbox_disabled()
 * Function to generate a disabled HTML select box (dropdown list).
 * The only option available will be, of course, the selected option.
 * It is however no problem to send an entire list of options to this
 * function. Only the selected name will be used.
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 *                   $selected as preselected id name in $options
 */

function html_selectbox_disabled($name, $options, $selected) {
    if (!empty($options)) {
        echo "<select name=\"". $name ."\" disabled=\"disabled\" class=\"disabled\">";
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) if ($row[0] == $selected) {
            echo "<option value=\"". $row[0] ."\">" . $row[1] . "</option>";
        }
        echo "</select>";
    } else {
        echo "<span class=\"empty\">". $lang["none"] ."</span>";
    }
}

/**
 * html_selectbox_submit()
 * Function to generate an HTML select box (dropdown list)
 * submitting a form when the selection changes
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id name in $options
 */

function html_selectbox_submit($name, $options, $selected = null, $header = null, $args = null) {
    $addselected = "";
    if (!empty($options)) {
        $out = '<select name="' . $name . '"' . (($args) ? $args : ' ') .'onChange="submit();"' . '>';
        if (!is_null($header))
        {
            $out .= '<option value="">' . ((!empty($header)) ? $header : '') . '</option>';
        }
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) {
            if ($row[0] == $selected) $addselected = " selected=\"selected\"";
            $out .= '<option value="' . $row[0] . '"' . $addselected . '>' . $row[1] . '</option>';
            $addselected = "";
        }
        $out .= '</select>';
    } else {
        $out = "<span class=\"empty\">". lang("none") ."</span>";;
    }
    return $out;
}


/**
 * html_selectbox_style()
 * Function to generate an HTML select box (dropdown list) with style
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id name in $options
 */

function html_selectbox_style($name, $options, $selected = null, $header = null, $args = null, $style = null) {
    $addselected = "";
    if (!empty($options)) {
        $out = '<select name="' . $name . '"' . (($args) ? $args : ' ') . ((!empty($style)) ? 'style="'.$style.'"' : '') . '>';
        if (!is_null($header))
        {
            $out .= '<option value="">' . ((!empty($header)) ? $header : '') . '</option>';
        }
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) {
            if ($row[0] == $selected) $addselected = " selected=\"selected\"";
            $out .= '<option value="' . $row[0] . '"' . $addselected . '>' . $row[1] . '</option>';
            $addselected = "";
        }
        $out .= '</select>';
    } else {
        $out = "<span class=\"empty\">". lang("none") ."</span>";;
    }
    return $out;
}

/**
 * html_selectbox_translate_submit()
 * Function to generate an HTML select box (dropdown list) with translation
 * submitting a form when the selection changes
 *
 * Parameters:       $name as name to return our selection in
 *                   $options as an array of options
 *                           keys as id's
 *                           values as names
 * Optional:         $selected as preselected id name in $options
 */

function html_selectbox_translate_submit($name, $options, $selected = null, $header = null, $args = null) {
    $addselected = "";
    if (!empty($options)) {
        $out = '<select name="' . $name . '"' . (($args) ? $args : ' ') .'onChange="submit();"' . '>';
        if (!is_null($header))
        {
            $out .= '<option value="">' . ((!empty($header)) ? $header : '') . '</option>';
        }
        if (!empty($options) && db_num_rows($options)) while ($row = db_fetch_num($options)) {
            if ($row[0] == $selected) $addselected = " selected=\"selected\"";
            $out .= '<option value="' . $row[0] . '"' . $addselected . '>' .lang($row[1]) . '</option>';
            $addselected = "";
        }
        $out .= '</select>';
    } else {
        $out = "<span class=\"empty\">". lang("none") ."</span>";;
    }
    return $out;
}

/**
 * html_checkboxlist_array()
 * Function to generate an HTML table of selectable checkbox options
 *
 * Parameters:       $checklist_name as an array to return our selection in
 *                   $all_options as an array of checkbox data
 *                           keys as id's
 *                           values as names
 * Optional:         $selected_options as an array of options existing in $checklist_name
 *                           that should be preselected
 *                   $columns as the number of columns before a new row is added
 */

function html_checkboxlist_array($checklist_name, $all_options, $selected_options = null, $columns = null)
{
    $checkedboxes = array();
    $checkboxlist = array();
    $checked = '';

    //create a fresh array with all selected options
    if (!empty($selected_options)) while ($row = db_fetch_num($selected_options)) {
        $checkedboxes[$row[0]] = $row[0];
    }

    //generate a list of checkbox options
    if (!empty($all_options)) while ($row = db_fetch_num($all_options)) {
        if (isset($checkedboxes[$row[0]])) $checked = ' checked="checked"';
        array_push($checkboxlist, '<input name="' . $checklist_name . '[]" type="checkbox" value="' . $row[0] . '"' . $checked . '>' . $row[1]);
        $checked = '';
    }

    //now we're going to show our list of checkboxes in a nicely generated table.

    //the number of rows and columns
    if (!isset($columns))
    {
        $columns = 3;
    }
    $rows = ceil(count($checkboxlist) / $columns);

    //split the array of checkboxes into pieces (cols*rows)
    $i = 0;

    for ($c = 1; $c <= $columns; $c++)
    {
        for ($r = 1; $r <= $rows; $r++)
        {
            if (!empty($checkboxlist[$i])) $table[$c][$r] = $checkboxlist[$i];
            $i++;
        }
    }

    //echo the checkboxes into a table
    echo '<table class="checkboxlist">';
        for ($r = 1; $r <= $rows; $r++)
        {
            echo '<tr>';
            for ($c = 1; $c <= $columns; $c++)
            {
                if (!empty($table[$c][$r])) echo '<td class="checkboxlist">' . $table[$c][$r] . '</td>';
            }
            echo '</tr>';
        }
    echo '</table>';
}


/**
 * html_checkboxlist_array_submit()
 * Function to generate an HTML table of selectable checkbox options
 *
 * Parameters:       $checklist_name as an array to return our selection in
 *                   $all_options as an array of checkbox data
 *                           keys as id's
 *                           values as names
 * Optional:         $selected_options as an array of options existing in $checklist_name
 *                           that should be preselected
 *                   $columns as the number of columns before a new row is added
 */

function html_checkboxlist_array_submit($checklist_name, $all_options, $selected_options = null, $columns = null) {
    $checkedboxes = array();
    $checkboxlist = array();
    $checked = '';

    //generate a list of checkbox options
    if (!empty($all_options)) foreach ($all_options as $id => $value) {
        if (isset($selected_options[$id])) $checked = ' checked="checked"';
        array_push($checkboxlist, '<input name="' . $checklist_name . '[]" type="checkbox" multiple value="' . $id . '"' . $checked . ' onChange="submit()">' . $value);
        $checked = '';
    }

    //now we're going to show our list of checkboxes in a nicely generated table.

    //the number of rows and columns
    if (!isset($columns)) $columns = 3;
    $rows = ceil(count($checkboxlist) / $columns);

    //split the array of checkboxes into pieces (cols*rows)
    $i = 0;

    for ($c = 1; $c <= $columns; $c++) {
        for ($r = 1; $r <= $rows; $r++) {
            if (!empty($checkboxlist[$i])) $table[$c][$r] = $checkboxlist[$i];
            $i++;
        }
    }

    //echo the checkboxes into a table
    echo '<table class="checkboxlist">';
    for ($r = 1; $r <= $rows; $r++) {
        echo '<tr>';
        for ($c = 1; $c <= $columns; $c++) {
            if (!empty($table[$c][$r])) echo '<td>' . $table[$c][$r] . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}


/**
 * html_radiobuttons()
 * In use but needs some tuning (fixme!-tag)
 */

function html_radiobuttons($name, $options, $selected = null, $extraoptions = null) {
    $addselected = '';
    echo '<span class="radioset">';
    foreach ($options as $id => $value) {
        if (isset($extraoptions) && $extraoptions == 'onClick_function') {
            $extra = ' onClick="onClick_' . $name . '(\'' . $id . '\')"';
        } else $extra = '';

        if ($id == $selected) $addselected = ' checked="checked"';
        $radioid = 'radio' . $id . $value . rand(0, 100000);
        echo '<input type="radio" name="' . $name . '" id="' . $radioid . '" value="' . $id . '"' . $extra . $addselected . ' /><label for="' . $radioid . '">' . $value . '</label>';
        $addselected = '';
    }
    echo '</span>';
}


/**
 * html_radiobuttons_submit()
 * In use but needs some tuning (fixme!-tag)
 */

function html_radiobuttons_submit($name, $options, $selected = null, $extraoptions = null) {
    $addselected = '';
    echo '<span class="radioset">';
    foreach ($options as $id => $value) {
        if (isset($extraoptions) && $extraoptions == 'onClick_function') {
            $extra = ' onClick="onClick_' . $name . '(\'' . $id . '\')"';
            $extraoptions = null;
        } else $extra = null;

        if ($id == $selected) $addselected = ' checked="checked"';
        $radioid = 'radio' . $id . $value . rand(0, 100000);
        echo '<input type="radio" name="' . $name . '" id="' . $radioid . '" value="' . $id . '"' . (($extraoptions)?' '.$extraoptions:' onChange="submit()"') . $extra . $addselected . ' /><label for="' . $radioid . '">' . $value . '</label>';
        $addselected = '';
    }
    echo '</span>';
}

/**
 * html_error()
 * Function to generate an HTML table containing an error message
 *
 * Parameters:       $error as a message
 *
 */

function html_error($error) {
    return '<div id="usermessage" class="error"><strong>' . lang('error') . ':</strong><br /><br />' . $error . '</div>';
}

/**
 * html_info()
 * Function to generate an HTML table containing an error message
 *
 * Parameters:       $error as a message
 *
 */

function html_info($message) {
    return '<div id="usermessage" class="info">' . $message . '</div>';
}

/**
 * html_warning()
 * Function to generate an HTML table containing a warning message
 *
 * Parameters:       $warning as a message
 *
 */

function html_warning($warning) {
    return '<div id="usermessage" class="warning"><strong>' . lang('warning') . ':</strong><br /><br />' . $warning . '</div>';
}


/**
 * html_note()
 * Function to generate an HTML table containing a message (note)
 *
 * Parameters:       $note as a message
 *
 */

function html_note($note = null) {
    return '<table class="note"><tr><td>' . $note . '<br /></td></tr></table><br />';
}


/**
 * html_requirednote()
 * Function to generate an HTML table containing an important message (note)
 *
 * Parameters:       $note as a message
 *
 */

function html_requirednote($note = null) {
    return '<div id="usermessage" class="warning"><strong>' . lang('warning') . ':</strong><br /><br /> '. $note . '</div>';
}


/**
 * html_requiredfields()
 * Function to generate an HTML table containing information about
 * required fields
 *
 * Parameters:       $requiredfields as an array of required fields
 *                           keys as names of required fields
 *
 */

function html_requiredfields($requiredfields) {
    $prefix = lang('requiredfield_prefix') .' ';
    $suffix = (strlen(lang('requiredfield_suffix')) > 1) ? ' '. lang('requiredfield_suffix') : lang('requiredfield_suffix');

    $return = '<div id="usermessage" class="warning"><strong>'. lang('warning') .':</strong><ul>';
    foreach ($requiredfields as $object) {
        $return .= '<li>'. $prefix . strtolower($object) . $suffix .'</li>';
    }
    $return .= '</ul></div>';

    return $return;
}


/**
 * html_delete()
 * Function to generate an HTML table containing a question if the
 * object should be deleted, returning delete=yes if the yes-button
 * is clicked for the question
 *
 * Parameters:       $id as the id to be deleted
 *                   $object as a to-be-lowercased name
 *
 */

function html_delete($id, $object) {
    return '<div id="usermessage" class="info"><strong>'. lang('delete_prefix') . ' ' . strtolower($object) . ' ' . lang('delete_suffix') .'</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="deleteform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="delete" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

/**
 * html_delete_garment()
 * Function to generate an HTML table containing a question if the
 * object should be deleted, returning delete=yes if the yes-button
 * is clicked for the question
 *
 * Parameters:       $id as the id to be deleted
 *                   $object as a to-be-lowercased name
 *
 */

function html_delete_garment($id, $object) {
    return '<div id="usermessage" class="info"><strong>'. lang('delete_prefix') . ' ' . strtolower($object) . ' ' . lang('delete_suffix') .'</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="deleteform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="delete" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . lang('reason') . ': <input type="text" name="d_reason" value="" size="50">'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

        


/**
 * html_delete_fully()
 * Function to generate an HTML table containing a question if the
 * object should be deleted fully, returning delete=yes and fully=yes if the yes-button
 * is clicked for the question
 *
 * Parameters:       $id as the id to be deleted fully
 *                   $object as a to-be-lowercased name
 *
 */

function html_full_delete($id, $object) {
    return '<div id="usermessage" class="info"><strong>'. lang('full_delete_prefix') . ' ' . strtolower($object) . ' ' . lang('full_delete_suffix') .'</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="deleteform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="delete" value="yes" />'
        . '<input type="hidden" name="full_delete" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}


/**
 * html_submitbuttons_detailsscreen()
 * Function to generate a row of submit buttons for use in details screens
 *
 * Parameters:       $pi as an array
 *
 * Optional:         $statements as a string of extra options like onClick statements
 */

function html_submitbuttons_detailsscreen($pi, $statements = null) {
    $statements = (!empty($statements)) ? $statements ." " : "";

    if ($pi["page"] == "details") {
        echo '<input type="submit" ' . $statements . ' name="detailssubmit" value="' . lang('save_and_close') . '" title="' . lang('save_and_close') . '" />';
        echo '<input type="submit" ' . $statements . ' name="detailssubmitnone" value="' . lang('cancel') . '" title="' . lang('cancel') . '" />';
    } elseif ($pi["page"] == "add") {
        echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . lang('add_and_close') . '" title="' . lang('add_and_close') . '" />';
    }
}


/**
 * html_submitbuttons_detailsscreen_extra()
 * Function to generate a row of submit buttons for use in details screens, with
 * extra 'save and copy' and 'save and new' buttons
 *
 * Parameters:       $pi as an array
 *
 * Optional:         $statements as a string of extra options like onClick statements
 */

function html_submitbuttons_detailsscreen_extra($pi, $statements = null) {
    $statements = (!empty($statements)) ? $statements ." " : "";

    if ($pi["page"] == "details") {
        echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . lang('save_and_close') . '" title="' . lang('save_and_close') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitnew" value="' . lang('save_and_new') . '" title="' . lang('save_and_new') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitcopy" value="' . lang('save_and_copy') . '" title="' . lang('save_and_copy') . '" />';
    } elseif ($pi["page"] == "add") {
        echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . lang('add_and_close') . '" title="' . lang('add_and_close') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitnew" value="' . lang('add_and_new') . '" title="' . lang('add_and_new') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitcopy" value="' . lang('add_and_copy') . '" title="' . lang('add_and_copy') . '" />';
    }
    echo '<input type="submit" ' . $statements . 'name="detailssubmitnone" value="' . lang('cancel') . '" title="' . lang('cancel') . '" />';
}

function html_submitbuttons_detailsscreen_garmentuser($pi, $statements = null) {
    $statements = (!empty($statements)) ? $statements . " " : "";

    if ($pi["page"] == "details") {
        echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . lang('save_and_close') . '" title="' . lang('save_and_close') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitnew" value="' . lang('save_and_new') . '" title="' . lang('save_and_new') . '" />';
    } elseif ($pi['page'] == 'add') {
        echo '<input type="submit" ' . $statements . 'name="detailssubmit" value="' . lang('add_and_close') . '" title="' . lang('add_and_close') . '" />';
        echo '<input type="submit" ' . $statements . 'name="detailssubmitnew" value="' . lang('add_and_new') . '" title="' . lang('add_and_new') . '" />';
    }
    echo '<input type="submit" ' . $statements . 'name="detailssubmitnone" value="' . lang('cancel') . '" title="' . lang('cancel') . '" />';
}

/**
 * HTML to stock message
 *
 * @param  int $id
 * @return string
 */
function html_to_stock($id) {
    return '<div id="usermessage" class="info"><strong>'. lang('do_put_to_stock') . '</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="stockform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="stock" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

/**
 * HTML to finalize message
 *
 * @param  int $id
 * @return string
 */
function html_to_finalize($id) {
    return '<div id="usermessage" class="info"><strong>'. lang('do_put_to_finalize') . '</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="finalizeform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="finalize" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

/**
 * HTML to laundry message
 *
 * @param  int $id
 * @return string
 */
function html_to_laundry($id) {
    return '<div id="usermessage" class="info"><strong>'. lang('do_put_to_laundry') . '</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="laundryform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="laundry" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

/**
 * HTML to missing message
 *
 * @param  int $id
 * @return string
 */
function html_to_missing($id) {
    return '<div id="usermessage" class="info"><strong>'. lang('do_put_to_missing') . '</strong>'
        . '<form action="'. $_SERVER['PHP_SELF'] .'" enctype="multipart/form-data" method="POST" name="missingform">'
        . '<input type="hidden" name="page" value="details" />'
        . '<input type="hidden" name="id" value="' . $id . '" />'
        . '<input type="hidden" name="missing" value="yes" />'
        . '<input type="hidden" name="gosubmit" value="yes" />'
        . '<br /><br />'
        . '<input type="submit" name="confirmed" value="' . lang('yes') . '" title="' . lang('yes') . '" /> &nbsp;'
        . '&nbsp; <input type="submit" name="abort" value="' . lang('no') . '" title="' . lang('no') . '" />'
        . '</form>'
        . '</div>';
}

?>
