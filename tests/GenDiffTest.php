<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function GenDiff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $argsWithJsonExt = [
            '<firstFile>' => __DIR__ . '/fixtures/before.json',
            '<secondFile>' => __DIR__ . '/fixtures/after.json'
        ];

        $argsWithYamlExt = [
            '<firstFile>' => __DIR__ . '/fixtures/before.yaml',
            '<secondFile>' => __DIR__ . '/fixtures/after.yaml'
        ];

        $diffForJsonExt = genDiff($argsWithJsonExt);
        $diffForYamlExt = genDiff($argsWithYamlExt);

        $expected = file_get_contents(__DIR__ . '/fixtures/diff.txt');

        $this->assertEquals($diffForJsonExt, $expected);
        $this->assertEquals($diffForYamlExt, $expected);
    }
}
