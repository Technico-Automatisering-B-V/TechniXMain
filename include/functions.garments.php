<?php

/**
 * Return garment ID from tag
 */

function tag_to_garment_id($tag)
{
    if (empty($tag))
    {
        return false;
    }
    else
    {
        $q = "SELECT * FROM `garments` WHERE (`tag` = '". $tag ."' OR `tag2` = '". $tag ."') && `deleted_on` IS NULL ORDER BY `id` DESC LIMIT 1";
        $garment_id_res = db_fetch_row(db_query($q));
        $garment_id = $garment_id_res[0];

        return (($garment_id) ? $garment_id : false);
    }
}

function garment_id_to_garment_status($garment_id)
{
    if (empty($garment_id))
    {
        return false;
    }
    else
    {
        $q = "SELECT `scanlocationstatuses`.`name`
                FROM `scanlocationstatuses`
          INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id`
          INNER JOIN `garments` ON `garments`.`scanlocation_id` = `scanlocations`.`id`
               WHERE `garments`.`id` = ". $garment_id;
        $garment_status_res = db_fetch_row(db_query($q));
        $garment_status = $garment_status_res[0];

        return (($garment_status) ? $garment_status : false);
    }
}

function convertTag($t, $cg = null)
{
    require_once "library/bootstrap.php";
    
    if(empty($cg)) { $cg = 1;}
    
    $q              = "SELECT `tagconverter` FROM `circulationgroups` WHERE `id`= ". $cg;
    $converter_res  = db_fetch_row(db_query($q));
    $converter      = $converter_res[0];
    
    $tc = TagConverterFactory::createTagConverter($converter);
    $tag = $tc->convert($t);

    return $tag; 
}

?>