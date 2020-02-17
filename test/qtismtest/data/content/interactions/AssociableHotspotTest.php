<?php

namespace qtismtest\data\content\interactions;

use qtism\common\datatypes\QtiCoords;
use qtism\common\datatypes\QtiShape;
use qtism\data\content\interactions\AssociableHotspot;
use qtismtest\QtiSmTestCase;

class AssociableHotspotTest extends QtiSmTestCase
{
    public function testCreateInvalidMatchMax()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'matchMax' argument must be a positive integer, 'boolean' given."
        );

        new AssociableHotspot('identifier', true, QtiShape::RECT, new QtiCoords(QtiShape::RECT, [0, 0, 1, 1]));
    }

    public function testCreateInvalidShape()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'shape' argument must be a value from the Shape enumeration, '1' given."
        );

        new AssociableHotspot('identifier', 1, true, new QtiCoords(QtiShape::RECT, [0, 0, 1, 1]));
    }

    public function testSetInvalidMatchMin()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'matchMin' argument must be a positive integer, 'boolean' given."
        );

        $associableHotspot = new AssociableHotspot('identifier', 1, QtiShape::RECT, new QtiCoords(QtiShape::RECT, [0, 0, 1, 1]));
        $associableHotspot->setMatchMin(true);
    }

    public function testSetInvalidHotspotLabel()
    {
        $this->setExpectedException(
            '\\InvalidArgumentException',
            "The 'hotspotLabel' argument must be a string value with at most 256 characters."
        );

        $associableHotspot = new AssociableHotspot('identifier', 1, QtiShape::RECT, new QtiCoords(QtiShape::RECT, [0, 0, 1, 1]));
        $associableHotspot->setHotspotLabel(true);
    }
}
