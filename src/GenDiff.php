<?php

namespace App;

const PATH_TO_FIRST_FILE = '<firstFile>';
const PATH_TO_SECOND_FILE = '<secondFile>';

function run($args)
{
    $firstFilepath = $args[PATH_TO_FIRST_FILE];
    $secondFilepath = $args[PATH_TO_SECOND_FILE];

    $differenceData = getDifferenceData($firstFilepath, $secondFilepath);
    $filesDifference = getStylishOutput($differenceData);

    return $filesDifference;
}

function getDifferenceData($firstFilepath, $secondFilepath)
{
    $firstArr = json_decode(getFileContents($firstFilepath), true);
    $secondArr = json_decode(getFileContents($secondFilepath), true);

    $unitedKeys = array_keys(array_merge($firstArr, $secondArr));

    $diff = array_map(function ($key) use ($firstArr, $secondArr) {
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

function changeBooleanIntoText($value)
{
    if (is_bool($value)) {
        return $value === true ? 'true' : 'false';
    }

    return $value;
}

function render($node)
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

function getStylishOutput($data)
{
    sort($data);
    $dataWithChangedBools = array_map(function ($node) {
        if ($node['status'] === 'changed') {
            return [
                'key' => $node['key'],
                'value' => [
                    changeBooleanIntoText($node['value'][0]),
                    changeBooleanIntoText($node['value'][1])
                ],
                'status' => $node['status']
            ];
        }

        return [
            'key' => $node['key'],
            'value' => changeBooleanIntoText($node['value']),
            'status' => $node['status']
        ];
    }, $data);

    $output = array_map(fn($node) => render($node), $dataWithChangedBools);

    $result = ["{", ...$output, "}\n"];

    return implode("\n", $result);
}
