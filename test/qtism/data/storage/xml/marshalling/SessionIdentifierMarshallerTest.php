<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2018-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Moyon Camille <camille@taotesting.com>
 * @license GPLv2
 */

use qtism\data\results\SessionIdentifier;
use qtism\common\datatypes\QtiUri;
use qtism\common\datatypes\QtiIdentifier;

require_once(dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SessionIdentifierMarshallerTest extends QtiSmTestCase
{
    public function testUnmarshall()
    {
        /** @var SessionIdentifier $sessionIdentifier */
        $sessionIdentifier = $this->createComponentFromXml(
            '<sessionIdentifier sourceID="fixture-sourceID" identifier="fixture-id"/>'
        );

        $this->assertInstanceOf(SessionIdentifier::class, $sessionIdentifier);

        $this->assertInstanceOf(QtiUri::class, $sessionIdentifier->getSourceID());
        $this->assertEquals('fixture-sourceID', $sessionIdentifier->getSourceID()->getValue());
        $this->assertEquals('fixture-sourceID', $sessionIdentifier->getSourceID());

        $this->assertInstanceOf(QtiIdentifier::class, $sessionIdentifier->getIdentifier());
        $this->assertEquals('fixture-id', $sessionIdentifier->getIdentifier()->getValue());
        $this->assertEquals('fixture-id', $sessionIdentifier->getIdentifier());
    }
    
    public function testMarshall()
    {
        $sourceID = 'fixture-sourceID';
        $id = 'fixture-id';
        $component = new SessionIdentifier(new QtiUri($sourceID), new QtiIdentifier($id));

        /** @var DOMElement $element */
        $element = $this->getMarshallerFactory()->createMarshaller($component)->marshall($component);

        $this->assertInstanceOf(DOMElement::class, $element);
        $this->assertEquals($component->getQtiClassName(), $element->nodeName);
        $this->assertEquals($sourceID, $element->getAttribute('sourceID'));
        $this->assertEquals($id, $element->getAttribute('identifier'));
    }

    public function testGetExpectedQtiClassName()
    {
        $sourceID = 'fixture-sourceID';
        $id = 'fixture-id';
        $component = new SessionIdentifier(new QtiUri($sourceID), new QtiIdentifier($id));

        $marshaller = $this->getMarshallerFactory()->createMarshaller($component);
        $this->assertEquals($component->getQtiClassName(), $marshaller->getExpectedQtiClassName());
    }

    /**
     * @expectedException \qtism\data\storage\xml\marshalling\UnmarshallingException
     */
    public function testWrongSessionIdentifierIdentifier()
    {
        $xml = '<sessionIdentifier identifier="fixture-id"/>';
        $element = QtiSmTestCase::createDOMElement($xml);
        $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
    }

    /**
     * @expectedException \qtism\data\storage\xml\marshalling\UnmarshallingException
     */
    public function testWrongSessionIdentifierSourceID()
    {
        $xml = '<sessionIdentifier sourceID="fixture-sourceID"/>';
        $element = QtiSmTestCase::createDOMElement($xml);
        $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
    }

    /**
     * @expectedException \qtism\data\storage\xml\marshalling\UnmarshallingException
     */
    public function testEmptySessionIdentifier()
    {
        $xml = '<sessionIdentifier/>';
        $element = QtiSmTestCase::createDOMElement($xml);
        $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
    }
}
