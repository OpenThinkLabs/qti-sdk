<?php

namespace qtismtest\data\storage\xml\marshalling;

use DOMDocument;
use qtism\data\expressions\NullValue;
use qtism\data\state\TemplateDefault;
use qtismtest\QtiSmTestCase;

class TemplateDefaultMarshallerTest extends QtiSmTestCase
{
    public function testMarshall()
    {
        $templateIdentifier = 'myTemplate1';
        $expression = new NullValue();

        $component = new TemplateDefault($templateIdentifier, $expression);
        $marshaller = $this->getMarshallerFactory()->createMarshaller($component);
        $element = $marshaller->marshall($component);

        $this->assertInstanceOf(\DOMElement::class, $element);
        $this->assertEquals('templateDefault', $element->nodeName);
        $this->assertEquals($templateIdentifier, $element->getAttribute('templateIdentifier'));

        $expressionElt = $element->getElementsByTagName('null');
        $this->assertEquals(1, $expressionElt->length);
        $expressionElt = $expressionElt->item(0);
        $this->assertEquals('null', $expressionElt->nodeName);
    }

    public function testUnmarshall()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML(
            '
			<templateDefault xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" templateIdentifier="myTemplate1">
				<null/>
			</templateDefault>
			'
        );
        $element = $dom->documentElement;

        $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
        $component = $marshaller->unmarshall($element);

        $this->assertInstanceOf(TemplateDefault::class, $component);
        $this->assertEquals($component->getTemplateIdentifier(), 'myTemplate1');
        $this->assertInstanceOf(NullValue::class, $component->getExpression());
    }
}
