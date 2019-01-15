<?php

class Helper
{
    public static function getCommand($direction, $length)
    {
        if ($direction == 'up') {
            return '@0,' . $length . "\n";
        }
        if ($direction == 'down') {
            return '@0,-' . $length . "\n";
        }
        if ($direction == 'left') {
            return '@-' . $length . ",0\n";
        }
        if ($direction == 'right') {
            return '@' . $length . ",0\n";
        }
    }
}