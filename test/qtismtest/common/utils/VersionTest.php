<?php

namespace qtismtest\common\utils;

use qtism\common\utils\Version;
use qtismtest\QtiSmTestCase;

class VersionTest extends QtiSmTestCase
{
    /**
     * @dataProvider versionCompareValidProvider
     *
     * @param string $version1
     * @param string $version2
     * @param string|null $operator
     * @param mixed $expected
     */
    public function testVersionCompareValid($version1, $version2, $operator, $expected)
    {
        $this->assertSame($expected, Version::compare($version1, $version2, $operator));
    }

    public function testVersionCompareInvalidVersion1()
    {
        $msg = "Version '2.1.4' is not a known QTI version. Known versions are '2.0.0, 2.1.0, 2.1.1, 2.2.0, 2.2.1'.";
        $this->setExpectedException('\\InvalidArgumentException', $msg);
        Version::compare('2.1.4', '2.1.1', '>');
    }

    public function testVersionCompareInvalidVersion2()
    {
        $msg = "Version '2.1.4' is not a known QTI version. Known versions are '2.0.0, 2.1.0, 2.1.1, 2.2.0, 2.2.1'.";
        $this->setExpectedException('\\InvalidArgumentException', $msg);
        Version::compare('2.1.0', '2.1.4', '<');
    }

    public function testUnknownOperator()
    {
        $msg = "Unknown operator '!=='. Known operators are '<, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne'.";
        $this->setExpectedException('\\InvalidArgumentException', $msg);
        Version::compare('2.1.1', '2.2.0', '!==');
    }

    public function versionCompareValidProvider()
    {
        return [
            ['2.0', '2.0', null, 0],
            ['2.0', '2.0.0', null, 0],
            ['2.0.0', '2.0', null, 0],
            ['2.1', '2.1', null, 0],
            ['2.1', '2.1.0', null, 0],
            ['2.1.0', '2.1', null, 0],
            ['2.1.0', '2.1.0', null, 0],
            ['2.1.1', '2.1.1', null, 0],
            ['2.2', '2.2', null, 0],
            ['2.2', '2.2.0', null, 0],
            ['2.2.0', '2.2', null, 0],
            ['2.2.0', '2.2.0', null, 0],
            ['2.0', '2.1', null, -1],
            ['2.0.0', '2.1', null, -1],
            ['2.0.0', '2.1.0', null, -1],
            ['2.0.0', '2.1.1', null, -1],
            ['2.0', '2.2', null, -1],
            ['2.2.0', '2.1.1', null, 1],
            ['2.2', '2.1.1', null, 1],
            ['2.2', '2.0.0', null, 1],
            ['2.0', '2.0.0', '=', true],
            ['2.0', '2.0', 'eq', true],
            ['2.0.0', '2.1.0', '<', true],
            ['2.0.0', '2.1.0', 'lt', true],
            ['2.1', '2.1.0', '<=', true],
            ['2.1.0', '2.1.0', 'le', true],
            ['2.2', '2.0', '>', true],
            ['2.2.0', '2.1', 'gt', true],
            ['2.2', '2.0.0', '>=', true],
            ['2.2', '2.2.0', 'ge', true],
            ['2.1', '2.1.0', '!=', false],
            ['2.1', '2.2', 'ne', true],
        ];
    }
}
