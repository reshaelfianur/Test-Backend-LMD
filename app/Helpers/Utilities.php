<?php

function daysName($key = null)
{
    $daysName = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];

    if (empty($key)) {
        return $daysName;
    }

    return $daysName[$key];
}

function monthName($key)
{
    $monthName = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];

    if (empty($key)) {
        return $monthName;
    }

    return $monthName[$key];
}

function ddC($array)
{
    echo '</pre>';
    print_r($array);
    echo '</pre>';

    die;
}

function currentRoute($type)
{
    $path  = explode('@', \Route::currentRouteAction());
    $pathc = explode("\\", $path[0]);

    if ($type == 'class') return strtolower(end($pathc));
    else return end($path);
}
