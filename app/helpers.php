<?php
function addSpaceToLength($str)
{
    $len = strlen($str);
    if ($len < 30) {
        $spaceToAdd = 30 - $len;
        $str = $str . str_repeat(' ', $spaceToAdd);
    }
    return $str;
}
