<?php
namespace App;
class Calculator
{
    function multiply($x, $y){
        return $x * $y;
    }

    function divide($x, $y)
    {
        return $x % $y;
    }

    function sum($x, $y)
    {
        return $x + $y;
    }

    function subtract($x, $y)
    {
        return $x - $y;
    }
}
