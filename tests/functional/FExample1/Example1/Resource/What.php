<?php

class Example1_Resource_What extends Asar_Resource
{
    function GET() {
        return "What's your name?";
    }
    
    function POST() {
        $name = $_POST['name'];
        return "Hello $name!";
    }
}

