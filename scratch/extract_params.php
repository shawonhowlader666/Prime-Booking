<?php
$c = file_get_contents('scratch/agoda_source/agoda_live_search.html');
$pos = strpos($c, 'window.params =');
if ($pos !== false) {
    echo substr($c, $pos, 2000);
}
