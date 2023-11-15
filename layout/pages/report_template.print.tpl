<html>
<head>
<title>TechniX - <?=$pi['title']?></title>
</head>
<body>

<div id="bitpopup"></div>

<script type="text/javascript">
<!--

Xoffset=-50;
Yoffset= 20;

var old,skn,iex=(document.all),yyy=-1000;

var ns4=document.layers
var ns6=document.getElementById&&!document.all
var ie4=document.all

if (ns4)
	skn=document.bitpopup
else if (ns6)
	skn=document.getElementById("bitpopup").style
else if (ie4)
	skn=document.all.bitpopup.style

if(ns4)document.captureEvents(Event.MOUSEMOVE);
else{
	skn.visibility="visible"
	skn.display="none"
}

document.onmousemove=get_mouse;

function popup(msg,bak){
	var content="<TABLE WIDTH=100 BORDER=1 BORDERCOLOR=black CELLPADDING=2 CELLSPACING=0 "+
		"BGCOLOR="+bak+"><TD ALIGN=center><FONT COLOR=black SIZE=2>"+msg+"</FONT></TD></TABLE>";
	yyy=Yoffset;
	if(ns4){skn.document.write(content);skn.document.close();skn.visibility="visible"}
	if(ns6){document.getElementById("bitpopup").innerHTML=content;skn.display=''}
	if(ie4){document.all("bitpopup").innerHTML=content;skn.display=''}
}

function get_mouse(e){
	var x=(ns4||ns6)?e.pageX:event.x+document.body.scrollLeft;
	skn.left=x+Xoffset;
	var y=(ns4||ns6)?e.pageY:event.y+document.body.scrollTop;
	skn.top=y+yyy;
}

function kill(){
	yyy=-1000;
	if(ns4){skn.visibility="hidden";}
	else if (ns6||ie4)
	skn.display="none"
}

//-->
</script>


<?
function rgb_to_hex($rgb) {
        $hex = "";
        foreach ($rgb as $color => $value) {
                $hex .= (strlen(dechex($value)) == 1) ? '0' . dechex($value) : dechex($value);
        }
        return strtoupper($hex);
}

function percentage_bg($color, $value) {
	if ($color == 'red') return '#' . rgb_to_hex(array('R' => 255, 'G' => 220 - ($value * 1.2), 'B' => 220 - ($value * 1.2)));
	else if ($color == 'green') return '#' . rgb_to_hex(array('R' => 220 - ($value * 1.3), 'G' => 255, 'B' => 210 - ($value * 1.2)));
	else if ($color == 'blue') return '#' . rgb_to_hex(array('R' => 220 - ($value * 0.8), 'G' => 220 - ($value * 0.8), 'B' => 255));
	else if ($color == 'gray') return '#' . rgb_to_hex(array('R' => 235 - ($value * 0.8), 'G' => 235 - ($value * 0.8), 'B' => 235 - ($value * 0.8)));
}
?>

<? if (!empty($menu)): ?>
	<table>
		<tr>
			<td class="small">
				<?=$menu?>
			</td>
		</tr>
	</table>
	<br />
<? endif ?>

<? if (!empty($caption)): ?>
	<table class="list">
		<tr class="listtitle">
			<td class="small">
				<?=$caption?>
			</td>
		</tr>
	</table>
<? endif ?>

<table class="list">
	<tr class="listtitle">
		<?
		$firstrow = db_fetch_assoc($listdata);
		$count = 0;

		foreach ($firstrow as $name => $value) {
			if (isset($show[$name])) {
				$count++;
				?>
				<td class="<?=($count>$columns_left)?'midlistsmall':'listsmall'?>">
					<?=lang($name)?>
				</td>
			<?
			}
		}
		?>
	</tr>

	<? db_data_seek($listdata, 0) ?>

	<? if (db_num_rows($listdata) > 0): ?>
	<? while ($row = db_fetch_assoc($listdata)): ?>
		<? if (!isset($row['ref__id'])) $row['ref__id'] = null ?>

		<tr 
			<? if (!empty($pi['filename_next'])): ?>
				class="listlt" onClick="document.location.href='<?=$pi['filename_next']?>?ref=<?=$row['ref__id']?>'"
			<? else: ?>
				class="listnc" 
			<? endif ?>
			>
			<?
			$count = 0;

			$percentize_total = 0;
			foreach ($row as $name => $value) {
				if (isset($percentize[$name])) {
					$percentize_total += $value;
				}
			}

			$count = 0;

			foreach ($row as $name => $value) {
				if (isset($show[$name])) {
					$count++;

					if (isset($percentize[$name])) $value_percentage = $value / $percentize_total * 100;

					if (isset($percentize[$name])) {
						if (isset($rounding[$name])) {
							if ($rounding[$name] > 0) {
								$showvalue = round($value_percentage, $rounding[$name]);
								$altvalue = round($value, $rounding[$name]);
							} else {
								$showvalue = round($value_percentage);
								$altvalue = round($value);
							}
						} else {
							$showvalue = $value_percentage;
							$altvalue = $value;
						}
					} else {
						if (isset($rounding[$name])) {
							if ($rounding[$name] > 0) {
								$showvalue = round($value, $rounding[$name]);
							} else {
								$showvalue = round($value);
							}
						} else {
							$showvalue = $value;
						}
					}
					$showvalue .= (isset($suffix[$name])) ? $suffix[$name] : '';
					?>

					<td class="<?=($count>$columns_left)?'midlist':'list'?>"
						<?
						if (isset($row['ref__rowcolor']))
							echo ' bgcolor="' . $row['ref__rowcolor'] . '"';
						else if (isset($percentize[$name]) && $value_percentage > 0)
							echo ' bgcolor="' . percentage_bg($background[$name], $value_percentage) . '"';
						if (isset($altvalue))
							echo " onmouseover=\"popup('" . $lang["count"] . ":<br />" . $altvalue . "','#DEECE3');\" onmouseout=\"kill();\"";
						?>
					>
						<?=$showvalue?>
					</td>
					<?
					if (isset($altvalue)) unset($altvalue);
					if (isset($showvalue)) unset($showvalue);
				}
			}
			?>
		</tr>
	<? endwhile ?>
	<? else: ?>
		<?=$lang["nothing_to_display"]?>
	<? endif ?>
</table>

&nbsp;

</body>
</html>
