<?php

namespace GenDiff\Parsers\YamlParser;

use Symfony\Component\Yaml\Yaml;

function parse($data)
{
    return Yaml::parse($data);
}
