<?php

namespace App;

use function App\Parsers\JsonParser\parse as parseJson;
use function App\Parsers\YamlParser\parse as parseYaml;

const PATH_TO_FIRST_FILE = '<firstFile>';
const PATH_TO_SECOND_FILE = '<secondFile>';
const FORMAT = '--format';

function getDiff($args)
{
    $firstFilepath = $args[PATH_TO_FIRST_FILE];
    $secondFilepath = $args[PATH_TO_SECOND_FILE];

    $differenceData = getDifferenceData($firstFilepath, $secondFilepath);
    $filesDifference = render($differenceData);

    return $filesDifference;
}

function getDifferenceData($firstFilepath, $secondFilepath)
{
    $parsers = [
        'json' => fn($json) => parseJson($json),
        'yaml' => fn($yaml) => parseYaml($yaml),
        'yml' => fn($yaml) => parseYaml($yaml)
    ];

    $firstFileExt = getFileExtension($firstFilepath);
    $secondFileExt = getFileExtension($secondFilepath);

    $firstArr = $parsers[$firstFileExt](getFileContents($firstFilepath));
    $secondArr = $parsers[$secondFileExt](getFileContents($secondFilepath));

    $diff = calculateDiff($firstArr, $secondArr);

    return $diff;
}

function getFileContents($filepath)
{
    $absolutePath = realpath($filepath);
    if (!file_exists($absolutePath)) {
        throw new \Exception('This file does not exist!');
    }

    return file_get_contents($filepath);
}

function changeInvisibleTypes($value)
{
    if (is_bool($value)) {
        return $value === true ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value) && strlen($value) === 0) {
        return '';
    }

    return $value;
}

function getStrByStatus($node)
{
    switch ($node['status']) {
        case 'added':
            return "  + {$node['key']}: {$node['value']}";
        case 'removed':
            return "  - {$node['key']}: {$node['value']}";
        case 'unchanged':
            return "    {$node['key']}: {$node['value']}";
        case 'changed':
            return "  - {$node['key']}: {$node['value'][0]}\n  + {$node['key']}: {$node['value'][1]}";
        default:
            throw new \Exception('Invalid node status!');
    }
}

function render($data)
{
    $dataWithChangedBools = array_map(function ($node) {
        if ($node['status'] === 'changed') {
            return [
                'key' => $node['key'],
                'value' => [
                    changeInvisibleTypes($node['value'][0]),
                    changeInvisibleTypes($node['value'][1])
                ],
                'status' => $node['status']
            ];
        }

        return [
            'key' => $node['key'],
            'value' => changeInvisibleTypes($node['value']),
            'status' => $node['status']
        ];
    }, $data);

    sort($dataWithChangedBools);

    $output = array_map(fn($node) => getStrByStatus($node), $dataWithChangedBools);

    $result = ["{", ...$output, "}\n"];

    return implode("\n", $result);
}

function getFileExtension($filepath)
{
    $pathParts = pathinfo($filepath);

    return $pathParts['extension'];
}

function calculateDiff($firstArr, $secondArr)
{
    $unitedKeys = array_keys(array_merge($firstArr, $secondArr));

    return array_map(function ($key) use ($firstArr, $secondArr) {
        if (array_key_exists($key, $firstArr) && !array_key_exists($key, $secondArr)) {
            $value = $firstArr[$key];
            $status = 'removed';
            return ['key' => $key, 'value' => $value, 'status' => $status];
        }

        if (!array_key_exists($key, $firstArr) && array_key_exists($key, $secondArr)) {
            $value = $secondArr[$key];
            $status = 'added';
            return ['key' => $key, 'value' => $value, 'status' => $status];
        }

        if (array_key_exists($key, $firstArr) && array_key_exists($key, $secondArr)) {
            if ($firstArr[$key] === $secondArr[$key]) {
                $value = $firstArr[$key];
                $status = 'unchanged';
                return ['key' => $key, 'value' => $value, 'status' => $status];
            } else {
                $value1 = $firstArr[$key];
                $value2 = $secondArr[$key];
                $status = 'changed';
                return ['key' => $key, 'value' => [$value1, $value2], 'status' => $status];
            }
        }
    }, $unitedKeys);
}
