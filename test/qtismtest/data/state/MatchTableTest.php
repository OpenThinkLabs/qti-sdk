<?php

namespace qtismtest\data\state;

use qtism\data\state\MatchTable;
use qtism\data\state\MatchTableEntry;
use qtism\data\state\MatchTableEntryCollection;
use qtismtest\QtiSmTestCase;

class MatchTableTest extends QtiSmTestCase
{
    public function testCreateNotEnoughMatchTableEntries()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'A MatchTable object must contain at least one MatchTableEntry object.'
        );

        new MatchTable(new MatchTableEntryCollection());
    }

    public function testGetComponents()
    {
        $matchTable = new MatchTable(
            new MatchTableEntryCollection(
                [
                    new MatchTableEntry(1, 1.1),
                ]
            )
        );

        $components = $matchTable->getComponents();
        $this->assertCount(1, $components);
        $this->assertInstanceOf(MatchTableEntry::class, $components[0]);
    }
}
