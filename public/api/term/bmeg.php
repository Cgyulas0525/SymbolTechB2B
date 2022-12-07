<?php

$array = ["egy", "kettő", "három"];
$numberOfCount = 0;
foreach ($array as $item) {
    $numberOfCount++;

    itemName($item);
}
echo "Elem: $numberOfCount\n";

function itemName($item) {
    echo "Elem: $item \n";
}
