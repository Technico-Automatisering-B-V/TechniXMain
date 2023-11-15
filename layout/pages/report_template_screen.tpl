<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<?php
function rgb_to_hex($rgb)
{
    $hex = "";
    foreach ($rgb as $color => $value) {
        $hex .= (strlen(dechex($value)) == 1) ? "0" . dechex($value) : dechex($value);
    }
    return strtoupper($hex);
}

function percentage_bg($color, $value)
{
    if ($color == "red") return "#" . rgb_to_hex(array("R" => 255, "G" => 220 - ($value * 1.2), "B" => 220 - ($value * 1.2)));
    else if ($color == "green") return "#" . rgb_to_hex(array("R" => 220 - ($value * 1.3), "G" => 255, "B" => 210 - ($value * 1.2)));
    else if ($color == "blue") return "#" . rgb_to_hex(array("R" => 220 - ($value * 0.8), "G" => 220 - ($value * 0.8), "B" => 255));
    else if ($color == "gray") return "#" . rgb_to_hex(array("R" => 235 - ($value * 0.8), "G" => 235 - ($value * 0.8), "B" => 235 - ($value * 0.8)));
}

if (!empty($menu)){ ?>
    <table>
        <tr>
            <td class="small" style="margin:0; padding:0">
                <?=$menu?>
            </td>
        </tr>
    </table>
<? } ?>

<? if (!empty($caption)){ ?>
    <table style="width: 100%;">
        <tr class="name">
            <td>
                <?=$caption?>
            </td>
        </tr>
    </table>
<? } ?>

<?php
if ($pi["filename_this"] == "report_current_load.php")
{
?>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
        $('.menubar').remove();
        $('.logout').remove();
		
		$('.head').html("Vernieuwen: <span id='countdown'>5</span>");
		$('.head').css('text-align', 'right');
		document.body.style.overflowY = "hidden";
        
        $("table.list").dataTable({
            "sDom": 't',
            "iDisplayLength": 500,
			"display": inlineblock
        });
        var widthHeaderDiv = document.getElementById('headerDiv').offsetWidth;
        var widthTableDiv  = document.getElementById('t1').offsetWidth;
        if(widthHeaderDiv && widthHeaderDiv > widthTableDiv ) {
            document.getElementById('t1').setAttribute('style','width:'+widthHeaderDiv+'px');
        } else {
            document.getElementById('headerDiv').setAttribute('style','width:'+widthTableDiv+'px');
        }
        
        
        var heightHeaderLeftDiv = document.getElementById('headerLeftDiv').offsetHeight;
        var heightHeaderRightDiv  = document.getElementById('headerRightDiv').offsetHeight;
        var heightHeaderLeftTopDiv  = document.getElementById('headerLeftTopDiv').offsetHeight;
        var heightHeaderLeftBottomDiv  = document.getElementById('headerLeftBottomDiv').offsetHeight;
        
        if(heightHeaderLeftDiv && heightHeaderRightDiv && heightHeaderLeftTopDiv && heightHeaderLeftBottomDiv && heightHeaderLeftDiv < heightHeaderRightDiv ) {
            var diff = heightHeaderRightDiv-heightHeaderLeftDiv+6;
            document.getElementById('headerLeftTopDiv').style.marginBottom = diff+'px';
            document.getElementById('headerLeftTopDiv').style.height = heightHeaderLeftTopDiv+'px';
            
        }
    
        
    });
	(function countdown(remaining) {
    if(remaining <= 0)
        location.reload(true);
    $('#countdown').html(remaining);
    setTimeout(function(){ countdown(remaining - 1); }, 1000);
})(30);
</script>

<?php
}
?>
