<?php
$c = file_get_contents('public/css/agoda/agoda-all-inline-styles.css');
echo substr($c, 4000, 2500);
