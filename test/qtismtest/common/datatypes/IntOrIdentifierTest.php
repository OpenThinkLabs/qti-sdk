<?php

namespace qtismtest\common\datatypes;

use InvalidArgumentException;
use qtism\common\datatypes\QtiIntOrIdentifier;
use qtismtest\QtiSmTestCase;

/**
 * Class IntOrIdentifierTest
 *
 * @package qtismtest\common\datatypes
 */
class IntOrIdentifierTest extends QtiSmTestCase
{
    public function testWrongValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $intOrIdentifier = new QtiIntOrIdentifier(13.37);
    }
}
