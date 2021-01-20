<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function App\getDiff;

class GenDiffTest extends TestCase
{
    public function testGetDiff(): void
    {
        $argsWithJsonExt = [
            '<firstFile>' => __DIR__ . '/fixtures/before.json',
            '<secondFile>' => __DIR__ . '/fixtures/after.json'
        ];

        $argsWithYamlExt = [
            '<firstFile>' => __DIR__ . '/fixtures/before.yaml',
            '<secondFile>' => __DIR__ . '/fixtures/after.yaml'
        ];

        $diffForJsonExt = getDiff($argsWithJsonExt);
        $diffForYamlExt = getDiff($argsWithYamlExt);

        $expected = file_get_contents(__DIR__ . '/fixtures/diff.txt');

        $this->assertEquals($diffForJsonExt, $expected);
        $this->assertEquals($diffForYamlExt, $expected);
    }
}
