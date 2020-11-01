<?php

namespace ShibuyaKosuke\LaravelDbSchema\Test;

use Exception;
use ShibuyaKosuke\LaravelDbSchema\Column;

/**
 * Class ColumnTest
 * @package ShibuyaKosuke\LaravelDbSchema\Test
 */
class ColumnTest extends TestCase
{
    /**
     * @var Column
     */
    private $column;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->useMySqlConnection($this->app);

        $this->column = new Column($this->app['config']);
    }

    /**
     * @return void
     */
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
