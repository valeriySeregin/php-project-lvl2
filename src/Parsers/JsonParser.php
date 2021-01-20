<?php

namespace App\Parsers\JsonParser;

function parse($data)
{
    return json_decode($data, true);
}
