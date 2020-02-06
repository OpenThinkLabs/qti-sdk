<?php

require_once(dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\utils\Arrays;

class ArraysTest extends QtiSmTestCase
{
    /**
     * @dataProvider isAssocValidProvider
     */
    public function testIsAssocValid(array $array)
    {
        $this->assertTrue(Arrays::isAssoc($array));
    }

    /**
     * @dataProvider isAssocInvalidProvider
     */
    public function testIsAssocInvalid(array $array)
    {
        $this->assertFalse(Arrays::isAssoc($array));
    }

    public function isAssocValidProvider()
    {
        return [
            [['test' => 0, 'bli' => 2]],
        ];
    }

    public function isAssocInvalidProvider()
    {
        return [
            [[0, 1]],
        ];
    }
}
