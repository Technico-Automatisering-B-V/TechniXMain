<?php

/**
 * Template functions and classes
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

class Template
{
    var $vars; //holds the template variables

    //constructor: @param $file string, the filename to load
    function Template($file = null)
    {
        $this->file = $file;
    }

    //set a template variable
    function set($name, $value)
    {
		$this->vars[$name] = is_object($value) ? (get_class($value) != "mysqli_result" ? $value->fetch() : $value) : $value;	
    }

    //open, parse, and return the template file: @param $file string again
    function fetch($file = null)
    {
        if(!$file) $file = $this->file;

        extract($this->vars);
        ob_start();
        include($file);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
}

function template_parse($pi, $urlinfo, $cv)
{
    global $config;
    global $lang;

    $page = new Template("layout/common/page.tpl");
    $menu = new Template("layout/common/menu.tpl");
    $toolbar = new Template("layout/common/toolbar.tpl");
    $content = new Template($pi["template"]);

    $page->set("pi", $pi);
    $page->set("config", $config);
    $page->set("lang", $lang);

    $toolbar->set("pi", $pi);
    $toolbar->set("urlinfo", $urlinfo);
    $toolbar->set("lang", $lang);

    $menu->set("pi", $pi);
    $menu->set("lang", $lang);

    $content->set("lang", $lang);

    foreach ($cv as $name => $var)
    {
        $content->set($name, $var);
    }

    $page->set("menu_content", $menu);
    $page->set("toolbar_content", $toolbar);
    $page->set("page_content", $content);

    echo $page->fetch("layout/common/page.tpl");
}

function template_parse_popup($pi, $urlinfo, $cv)
{
    $page = new Template("layout/common/popup_page.tpl");
    $toolbar = new Template("layout/common/toolbar.tpl");
    $content = new Template($pi["template"]);

    $page->set("pi", $pi);
    $toolbar->set("pi", $pi);

    $toolbar->set("urlinfo", $urlinfo);

    $toolbar->set("lang", $lang);
    $content->set("lang", $lang);

    foreach ($cv as $name => $var)
    {
        $content->set($name, $var);
    }

    $page->set("toolbar_content", $toolbar);
    $page->set("page_content", $content);

    echo $page->fetch("layout/common/popup_page.tpl");
}

?>
