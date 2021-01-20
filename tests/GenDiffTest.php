<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function App\run;

class GenDiffTest extends TestCase
{
    public function testGetName(): void
    {
        $args = [
            '<firstFile>' => __DIR__ . '/fixtures/before.json',
            '<secondFile>' => __DIR__ . '/fixtures/after.json'
        ];
        $diff = run($args);
        $expected = file_get_contents(__DIR__ . '/fixtures/diff.txt');

        $this->assertEquals($diff, $expected);
    }
}
