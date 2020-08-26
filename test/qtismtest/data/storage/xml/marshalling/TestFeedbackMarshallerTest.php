<?php

namespace qtismtest\data\storage\xml\marshalling;

use DOMDocument;
use DOMElement;
use qtism\data\ShowHide;
use qtism\data\TestFeedback;
use qtism\data\TestFeedbackAccess;
use qtismtest\QtiSmTestCase;
use ReflectionClass;
use qtism\data\storage\xml\marshalling\TestFeedbackMarshaller;
use ReflectionException;

/**
 * Class TestFeedbackMarshallerTest
 */
class TestFeedbackMarshallerTest extends QtiSmTestCase
{
    public function testMarshall()
    {
        $identifier = 'myTestFeedBack1';
        $outcomeIdentifier = 'myOutcomeIdentifier1';
        $access = TestFeedbackAccess::AT_END;
        $showHide = ShowHide::SHOW;
        $content = '<div><p>Hello World!</p></div>';

        $component = new TestFeedback($identifier, $outcomeIdentifier, $content);
        $component->setAccess($access);
        $component->setShowHide($showHide);

        $marshaller = $this->getMarshallerFactory()->createMarshaller($component);
        $element = $marshaller->marshall($component);

        $this->assertInstanceOf(DOMElement::class, $element);
        $this->assertEquals('testFeedback', $element->nodeName);
        $this->assertEquals($identifier, $element->getAttribute('identifier'));
        $this->assertEquals($outcomeIdentifier, $element->getAttribute('outcomeIdentifier'));
        $this->assertEquals('', $element->getAttribute('title'));
        $this->assertEquals('atEnd', $element->getAttribute('access'));
        $this->assertEquals('show', $element->getAttribute('showHide'));

        $content = $element->getElementsByTagName('div');
        $this->assertEquals($content->length, 1);
        $this->assertEquals($content->item(0)->getElementsByTagName('p')->length, 1);
    }

    public function testUnmarshall()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<testFeedback xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myIdentifier1" access="atEnd" outcomeIdentifier="myOutcomeIdentifier1" showHide="show" title="my title"><p>Have a nice test!</p></testFeedback>');
        $element = $dom->documentElement;

        $marshaller = $this->getMarshallerFactory()->createMarshaller($element);
        $component = $marshaller->unmarshall($element);

        $this->assertInstanceOf(TestFeedback::class, $component);
        $this->assertEquals($component->getIdentifier(), 'myIdentifier1');
        $this->assertEquals($component->getAccess(), TestFeedbackAccess::AT_END);
        $this->assertEquals($component->getShowHide(), ShowHide::SHOW);
        $this->assertEquals($component->getTitle(), 'my title');
        $this->assertEquals($component->getContent(), '<p>Have a nice test!</p>');
    }

    /**
     * @dataProvider feedbackContent
     * @param string $xmlData
     * @param string $expectedContent
     * @throws ReflectionException
     */
    public function testExtractContent($xmlData, $expectedContent)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xmlData);
        $element = $dom->documentElement;

        $class = new ReflectionClass(TestFeedbackMarshaller::class);
        $method = $class->getMethod('extractContent');
        $method->setAccessible(true);
        $this->assertEquals($method->invokeArgs(null, [$element]), $expectedContent);
    }

    /**
     * @return array
     */
    public function feedbackContent()
    {
        return [
            ['<testFeedback xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" access="during" identifier="myId1" outcomeIdentifier="myId2" showHide="show"><div><p>Hello there!</p></div></testFeedback>', '<div><p>Hello there!</p></div>'],
        ];
    }
}
