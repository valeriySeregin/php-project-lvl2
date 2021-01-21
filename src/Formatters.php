<?php

namespace GenDiff\Formatters;

use function GenDiff\Formatters\Stylish\render as renderInStylish;

function formatData(array $data, string $format): string
{
    $formatters = [
        'stylish' => fn($data) => renderInStylish($data),
    ];

    if (!array_key_exists($format, $formatters)) {
        throw new \Exception("Unsupported format: {$format}");
    }

    return $formatters[$format]($data);
}
