<?php

namespace App\Parsers\YamlParser;

use Symfony\Component\Yaml\Yaml;

function parse($data)
{
    return Yaml::parse($data);
}
