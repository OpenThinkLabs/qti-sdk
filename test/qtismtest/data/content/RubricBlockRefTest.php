<?php

namespace qtismtest\data\content;

use qtism\data\content\RubricBlockRef;
use qtismtest\QtiSmTestCase;

class RubricBlockRefTest extends QtiSmTestCase
{
    public function testCreateWrongIdentifierType()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'identifier' argument must be a valid QTI identifier, '999' given."
        );
        $rubricBlockRef = new RubricBlockRef('999', 'href.ref');
    }

    public function testCreateWrongHrefType()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'href' argument must be a valid URI, '999' given."
        );
        $rubricBlockRef = new RubricBlockRef('ref-1', 999);
    }
}
