<?php

use qtism\common\datatypes\QtiIdentifier;
use qtism\runtime\common\State;
use qtism\runtime\rendering\markup\AbstractMarkupRenderingEngine;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\TemplateVariable;
use qtism\data\storage\xml\XmlDocument;
use qtism\runtime\rendering\markup\xhtml\XhtmlRenderingEngine;

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

$doc = new XmlDocument();
$doc->load(dirname(__FILE__) . '/../samples/rendering/choiceinteraction_1.xml');

$renderer = new XhtmlRenderingEngine();

$shuffle = false;
if (isset($argv[1]) && $argv[1] === 'shuffle') {
    $renderer->setShufflingPolicy(XhtmlRenderingEngine::CONTEXT_AWARE);
    $shuffle = true;
}

if ((isset($argv[1]) && $shuffle === true && isset($argv[2])) || (isset($argv[1]) && $shuffle === false)) {
    $templateVariable = new TemplateVariable('SHOWBLACK', Cardinality::SINGLE, BaseType::IDENTIFIER);
    
    if ($shuffle === true) {
        $templateVariable->setValue(new QtiIdentifier($argv[2]));
    } else {
        $templateVariable->setValue(new QtiIdentifier($argv[1]));
    }
    
    $renderer->setChoiceShowHidePolicy(AbstractMarkupRenderingEngine::CONTEXT_AWARE);
    $state = new State(array($templateVariable));
    $renderer->setState($state);
}

$rendering = $renderer->render($doc->getDocumentComponent());
$rendering->formatOutput = true;

echo $rendering->saveXML();
