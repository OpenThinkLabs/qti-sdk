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
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\marshalling;

use DOMElement;
use DOMNode;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use qtism\data\rules\ResponseCondition;
use qtism\data\rules\ResponseElse;
use qtism\data\rules\ResponseElseIf;
use qtism\data\rules\ResponseElseIfCollection;
use qtism\data\rules\ResponseIf;
use qtism\data\rules\ResponseRuleCollection;

/**
 * Marshalling/Unmarshalling implementation of ResponseCondition
 * QTI components.
 */
class ResponseConditionMarshaller extends RecursiveMarshaller
{
    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::unmarshallChildrenKnown()
     */
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children)
    {
        if (count($children) > 0) {
            // The first element of $children must be a responseIf.
            $responseIf = $children[0];
            $responseCondition = new ResponseCondition($responseIf);

            if (isset($children[1])) {
                $responseElseIfs = new ResponseElseIfCollection();
                // We have at least one elseIf.
                for ($i = 1; $i < count($children) - 1; $i++) {
                    $responseElseIfs[] = $children[$i];
                }

                $responseCondition->setResponseElseIfs($responseElseIfs);
                $lastOutcomeControl = $children[count($children) - 1];

                if ($lastOutcomeControl instanceof ResponseElseIf) {
                    // There is no else.
                    $responseElseIfs[] = $lastOutcomeControl;
                } else {
                    // We have an else.
                    $responseCondition->setResponseElse($lastOutcomeControl);
                }
            }

            return $responseCondition;
        } else {
            $msg = "A 'responseCondition' element must contain at least one 'responseIf' element. None given.";
            throw new UnmarshallingException($msg, $element);
        }
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::marshallChildrenKnown()
     */
    protected function marshallChildrenKnown(QtiComponent $component, array $elements)
    {
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());

        foreach ($elements as $elt) {
            $element->appendChild($elt);
        }

        return $element;
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::isElementFinal()
     */
    protected function isElementFinal(DOMNode $element)
    {
        $exclusion = ['responseIf', 'responseElseIf', 'responseElse', 'responseCondition'];

        return !in_array($element->localName, $exclusion);
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::isComponentFinal()
     */
    protected function isComponentFinal(QtiComponent $component)
    {
        return (!$component instanceof ResponseIf &&
            !$component instanceof ResponseElseIf &&
            !$component instanceof ResponseElse &&
            !$component instanceof ResponseCondition);
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::getChildrenElements()
     */
    protected function getChildrenElements(DOMElement $element)
    {
        return self::getChildElementsByTagName($element, [
            'responseIf',
            'responseElseIf',
            'responseElse',
            'exitResponse',
            'lookupOutcomeValue',
            'setOutcomeValue',
            'responseCondition',
        ]);
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::getChildrenComponents()
     */
    protected function getChildrenComponents(QtiComponent $component)
    {
        if ($component instanceof ResponseIf || $component instanceof ResponseElseIf || $component instanceof ResponseElse) {
            // ResponseControl
            return $component->getResponseRules()->getArrayCopy();
        } else {
            // ResponseCondition
            $returnValue = [$component->getResponseIf()];

            if (count($component->getResponseElseIfs()) > 0) {
                $returnValue = array_merge($returnValue, $component->getResponseElseIfs()->getArrayCopy());
            }

            if ($component->getResponseElse() !== null) {
                $returnValue[] = $component->getResponseElse();
            }

            return $returnValue;
        }
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\RecursiveMarshaller::createCollection()
     */
    protected function createCollection(DOMElement $currentNode)
    {
        if ($currentNode->localName != 'responseCondition') {
            return new ResponseRuleCollection();
        } else {
            return new QtiComponentCollection();
        }
    }

    /**
     * @see \qtism\data\storage\xml\marshalling\Marshaller::getExpectedQtiClassName()
     */
    public function getExpectedQtiClassName()
    {
        return '';
    }
}
