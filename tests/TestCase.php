<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledPath = env('VIEW_COMPILED_PATH');
        if (is_string($compiledPath) && $compiledPath !== '' && !is_dir($compiledPath)) {
            @mkdir($compiledPath, 0777, true);
        }
    }
}
