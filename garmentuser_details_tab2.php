<?php

// Required for selectbox: professions
$professions_conditions["order_by"] = "name";
$professions = db_read("professions", "id name", $professions_conditions);

// Required for radiobuttons (use alternatives or not)
$alternativesswitch[1] = $lang["yes"];
$alternativesswitch[2] = $lang["no"];

// Alternatives
if (empty($alternatives) && !empty($alt_sizes_selected)){
    $alternatives = 1;
} elseif (empty($alternatives) || $alternatives == 1) {
    $alternatives = 1;
}

?>
