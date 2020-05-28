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
 * Copyright (c) 2013-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\marshalling;

use qtism\common\utils\Version;
use qtism\data\content\Direction;
use qtism\data\QtiComponent;
use qtism\data\content\BodyElement;
use qtism\data\storage\xml\Utils as XmlUtils;
use \DOMDocument;
use \DOMElement;
use \RuntimeException;
use \InvalidArgumentException;

abstract class Marshaller
{
    /**
     * The DOMCradle is a DOMDocument object which will be used as a 'DOMElement cradle'. It
     * gives the opportunity to marshallers to create DOMElement that can be imported in an
     * exported document later on.
     *
     * @var DOMDocument
     */
    private static $DOMCradle = null;

    /**
     * A reference to the Marshaller Factory to use when creating other marshallers
     * from this marshaller.
     *
     * @var MarshallerFactory
     */
    private $marshallerFactory = null;

    /**
     * The version on which the Marshaller operates.
     * 
     * @var string
     */
    private $version;
    
    /**
     * An array containing the name of classes
     * that are allowed to have their 'dir' attribute set.
     * 
     * @var array
     */
    private static $dirClasses = array(
        'associateInteraction',
        'choiceInteraction',
        'drawingInteraction',
        'extendedTextInteraction',
        'gapMatchInteraction',
        'graphicAssociateInteraction',
        'hotspotInteraction',
        'hottextInteraction',
        'matchInteraction',
        'mediaInteraction',
        'orderInteraction',
        'selectPointInteraction',
        'sliderInteraction',
        'uploadInteraction',
        'bdo',
        'caption',
        'colgroup',
        'gapImg',
        'gapText',
        'infoControl',
        'inlineChoice',
        'li',
        'prompt',
        'simpleAssociableChoice',
        'simpleChoice',
        'stimulusBody',
        'tbody',
        'tfoot',
        'thead',
        'td',
        'th',
        'tr',
        'customInteraction',
        'graphicGapMatchInteraction',
        'graphicOrderInteraction',
        'inlineChoiceInteraction',
        'positionObjecInteraction',
        'a',
        'dd',
        'div',
        'dl',
        'dt',
        'feedbackBlock',
        'feedbackInline',
        'hottext',
        'abbr',
        'acronym',
        'address',
        'b',
        'big',
        'cite',
        'code',
        'dfn',
        'em',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'i',
        'kbd',
        'p',
        'pre',
        'samp',
        'small',
        'span',
        'strong',
        'sub',
        'sup',
        'tt',
        'var',
        'br',
        'col',
        'hr',
        'img',
        'q',
        'label',
        'object',
        'ul',
        'rubricBlock',
        'table',
        'templateBlock',
        'templateInline',
        'hottext'
    );
    
    /**
     * An array containing the QTI class names that are allowed to be Web Component friendly.
     * 
     * @var array
     */
    public static $webComponentFriendlyClasses = array(
        'associableHotspot',
        'gap',
        'gapImg',
        'gapText',
        'simpleAssociableChoice',
        'hotspotChoice',
        'hottext',
        'inlineChoice',
        'simpleChoice',
        'associateInteraction',
        'choiceInteraction',
        'drawingInteraction',
        'extendedTextInteraction',
        'gapMatchInteraction',
        'graphicAssociateInteraction',
        'graphicGapMatchInteraction',
        'graphicOrderInteraction',
        'hotspotInteraction',
        'selectPointInteraction',
        'hottextInteraction',
        'matchInteraction',
        'mediaInteraction',
        'orderInteraction',
        'sliderInteraction',
        'uploadInteraction',
        'customInteraction',
        'endAttemptInteraction',
        'inlineChoiceInteraction',
        'textEntryInteraction',
        'positionObjectInteraction',
        'positionObjectStage',
        'printedVariable',
        'prompt',
        'feedbackBlock',
        'feedbackInline',
        'rubricBlock',
        'templateBlock',
        'templateInline',
        'infoControl'
    );
    
    /**
     * Create a new Marshaller object.
     * 
     * @param string $version The QTI version on which the Marshaller operates e.g. '2.1'.
     */
    public function __construct($version)
    {
        $this->setVersion($version);
    }
    
    /**
     * Get a DOMDocument to be used by marshaller implementations in order to create
     * new nodes to be imported in a currenlty exported document.
     *
     * @return DOMDocument A unique DOMDocument object.
     */
    protected static function getDOMCradle()
    {
        if (empty(self::$DOMCradle)) {
            self::$DOMCradle = new DOMDocument('1.0', 'UTF-8');
        }

        return self::$DOMCradle;
    }

    /**
     * Set the MarshallerFactory object to use to create other Marshaller objects.
     *
     * @param MarshallerFactory $marshallerFactory A MarshallerFactory object.
     */
    public function setMarshallerFactory(MarshallerFactory $marshallerFactory = null)
    {
        $this->marshallerFactory = $marshallerFactory;
    }

    /**
     * Return the MarshallerFactory object to use to create other Marshaller objects.
     * If no MarshallerFactory object was previously defined, a default 'raw' MarshallerFactory
     * object will be returned.
     *
     * @return MarshallerFactory A MarshallerFactory object.
     */
    public function getMarshallerFactory()
    {
        if ($this->marshallerFactory === null) {
            $this->setMarshallerFactory(new Qti21MarshallerFactory());
        }

        return $this->marshallerFactory;
    }
    
    /**
     * Set the version on which the Marshaller operates.
     * 
     * @param string $version A QTI version number e.g. '2.1'.
     */
    protected function setVersion($version)
    {
        $this->version = $version;
    }
    
    /**
     * Get the version on which the Marshaller operates.
     * 
     * @return string A QTI version number e.g. '2.1'.
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function __call($method, $args)
    {
        if ($method == 'marshall' || $method == 'unmarshall') {
            if (count($args) >= 1) {
                if ($method == 'marshall') {
                    $component = $args[0];
                    if ($component instanceof QtiComponent && ($this->getExpectedQtiClassName() === '' || ($component->getQtiClassName() == $this->getExpectedQtiClassName()))) {
                        return $this->marshall($component);
                    } else {
                        $componentName = $this->getComponentName($component);
                        throw new RuntimeException("No marshaller implementation found while marshalling component '${componentName}'.");
                    }
                } else {
                    $element = $args[0];
                    if ($element instanceof DOMElement && ($this->getExpectedQtiClassName() === '' || ($element->localName == $this->getExpectedQtiClassName()))) {
                        return call_user_func_array(array($this, 'unmarshall'), $args);
                    } else {
                        $nodeName = $this->getElementName($element);
                        throw new RuntimeException("No Marshaller implementation found while unmarshalling element '${nodeName}'.");
                    }
                }
            } else {
                throw new RuntimeException("Method '${method}' only accepts a single argument.");
            }
        }

        throw new RuntimeException("Unknown method Marshaller::'${method}'.");
    }
    
    /**
     * Get Attribute Name to Use for Marshalling
     *
     * This method provides the attribute name to be used to retrieve an element attribute value
     * by considering whether or not the Marshaller implementation is running in Web Component
     * Friendly mode.
     *
     * Examples:
     *
     * In case of the Marshaller implementation IS NOT running in Web Component Friendly mode,
     * calling this method on an $element "choiceInteraction" and a "responseIdentifier" $attribute, the
     * "responseIdentifier" value is returned.
     *
     * On the other hand, in case of the Marshaller implementation IS running in Web Component Friendly mode,
     * calling this method on an $element "choiceInteraction" and a "responseIdentifier" $attribute, the
     * "response-identifier" value is returned.
     *
     * @param \DOMElement $element
     * @param $attribute
     * @return string
     */
    protected function getAttributeName(DOMElement $element, $attribute)
    {
        if ($this->isWebComponentFriendly() === true && preg_match('/^qti-/', $element->localName) === 1) {
            $qtiFriendlyClassName = XmlUtils::qtiFriendlyName($element->localName);
        
            if (in_array($qtiFriendlyClassName, self::$webComponentFriendlyClasses) === true) {
                return XmlUtils::webComponentFriendlyAttributeName($attribute);
            }
        }
        
        return $attribute;
    }

    /**
     * Get the attribute value of a given DOMElement object, cast in a given datatype.
     *
     * @param DOMElement $element The element the attribute you want to retrieve the value is bound to.
     * @param string $attribute The attribute name.
     * @param string $datatype The returned datatype. Accepted values are 'string', 'integer', 'float', 'double' and 'boolean'.
     * @throws InvalidArgumentException If $datatype is not in the range of possible values.
     * @return mixed The attribute value with the provided $datatype, or null if the attribute does not exist in $element.
     */
    public function getDOMElementAttributeAs(DOMElement $element, $attribute, $datatype = 'string')
    {
        return XmlUtils::getDOMElementAttributeAs($element, $this->getAttributeName($element, $attribute), $datatype);
    }

    /**
     * Set the attribute value of a given DOMElement object. Boolean values will be transformed
     *
     * @param DOMElement $element A DOMElement object.
     * @param string $attribute An XML attribute name.
     * @param mixed $value A given value.
     */
    public function setDOMElementAttribute(DOMElement $element, $attribute, $value)
    {
        XmlUtils::setDOMElementAttribute($element, $this->getAttributeName($element, $attribute), $value);
    }

    /**
     * Set the node value of a given DOMElement object. Boolean values will be transformed as 'true'|'false'.
     *
     * @param DOMElement $element A DOMElement object.
     * @param mixed $value A given value.
     */
    public static function setDOMElementValue(DOMElement $element, $value)
    {
        switch (gettype($value)) {
            case 'boolean':
                $element->nodeValue = ($value === true) ? 'true' : 'false';
            break;

            default:
                $element->nodeValue = $value;
            break;
        }
    }

    /**
     * Get the first child DOM Node with nodeType attribute equals to XML_ELEMENT_NODE.
     * This is very useful to get a sub-node without having to exclude text nodes, cdata,
     * ... manually.
     *
     * @param DOMElement $element A DOMElement object
     * @return DOMElement|boolean A DOMElement If a child node with nodeType = XML_ELEMENT_NODE or false if nothing found.
     */
    public static function getFirstChildElement($element)
    {
        $children = $element->childNodes;
        for ($i = 0; $i < $children->length; $i++) {
            $child = $children->item($i);
            if ($child->nodeType === XML_ELEMENT_NODE) {
                return $child;
            }
        }

        return false;
    }

    /**
     * Get the children DOM Nodes with nodeType attribute equals to XML_ELEMENT_NODE.
     *
     * @param DOMElement $element A DOMElement object.
     * @param boolean $withText Wether text nodes must be returned or not.
     * @return array An array of DOMNode objects.
     */
    public static function getChildElements($element, $withText = false)
    {
        return XmlUtils::getChildElements($element, $withText);
    }

    /**
     * Get the child elements of a given element by tag name. This method does
     * not behave like DOMElement::getElementsByTagName. It only returns the direct
     * child elements that matches $tagName but does not go recursive.
     *
     * @param DOMElement $element A DOMElement object.
     * @param mixed $tagName The name of the tags you would like to retrieve or an array of tags to match.
     * @param boolean $exclude (optional) Wether the $tagName parameter must be considered as a blacklist.
     * @param boolean $withText (optional) Wether text nodes must be returned or not.
     * @return array An array of DOMElement objects.
     */
    public function getChildElementsByTagName($element, $tagName, $exclude = false, $withText = false)
    {
        if (is_array($tagName) === false) {
            $tagName = [$tagName];
        }
        
        if ($this->isWebComponentFriendly() === true) {
            foreach ($tagName as $key => $name) {
                if (in_array($name, self::$webComponentFriendlyClasses) === true) {
                    $tagName[$key] = XmlUtils::webComponentFriendlyClassName($name);
                }
            }
        }
        
        return XmlUtils::getChildElementsByTagName($element, $tagName, $exclude, $withText);
    }

    /**
     * Get the string value of the xml:base attribute of a given $element. The method
     * will return false if no xml:base attribute is defined for the $element or its value
     * is empty.
     *
     * @param DOMElement $element A DOMElement object you want to get the xml:base attribute value.
     * @return false|string The value of the xml:base attribute or false if it could not be retrieved.
     */
    public static function getXmlBase(DOMElement $element)
    {
        $returnValue = false;
        if (($xmlBase = $element->getAttributeNS('http://www.w3.org/XML/1998/namespace', 'base')) !== '') {
            $returnValue = $xmlBase;
        }

        return $returnValue;
    }

    /**
     * Set the value of the xml:base attribute of a given $element. If a value is already
     * defined for the xml:base attribute of the $element, the current value will be
     * overriden by $xmlBase.
     *
     * @param DOMElement $element The $element you want to set a value for xml:base.
     * @param string $xmlBase The value to be set to the xml:base attribute of $element.
     */
    public static function setXmlBase(DOMElement $element, $xmlBase)
    {
        $element->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'base', $xmlBase);
    }

    /**
     * Fill $bodyElement with the following bodyElement attributes:
     *
     * * id
     * * class
     * * lang
     * * label
     * * dir (QTI 2.2)
     *
     * @param BodyElement $bodyElement The bodyElement to fill.
     * @param DOMElement $element The DOMElement object from where the attribute values must be retrieved.
     * @throws UnmarshallingException If one of the attributes of $element is not valid.
     */
    protected function fillBodyElement(BodyElement $bodyElement, DOMElement $element)
    {
        try {
            $bodyElement->setId($element->getAttribute('id'));
            $bodyElement->setClass($element->getAttribute('class'));
            $bodyElement->setLang($element->getAttributeNS('http://www.w3.org/XML/1998/namespace', 'lang'));
            $bodyElement->setLabel($element->getAttribute('label'));
            
            $version = $this->getVersion();
            if (Version::compare($version, '2.2.0', '>=') === true && ($dir = $this->getDOMElementAttributeAs($element, 'dir')) !== null && in_array($element->localName, self::$dirClasses) === true) {
                $bodyElement->setDir(Direction::getConstantByName($dir));
            }
        } catch (InvalidArgumentException $e) {
            $msg = "An error occured while filling the bodyElement attributes (id, class, lang, label, dir).";
            throw new UnmarshallingException($msg, $element, $e);
        }
    }

    /**
     * Fill $element with the attributes of $bodyElement.
     *
     * @param DOMElement $element The element from where the atribute values will be
     * @param BodyElement $bodyElement The bodyElement to be fill.
     */
    protected function fillElement(DOMElement $element, BodyElement $bodyElement)
    {
        if (($id = $bodyElement->getId()) !== '') {
            $element->setAttribute('id', $id);
        }

        if (($class = $bodyElement->getClass()) !== '') {
            $element->setAttribute('class', $class);
        }

        if (($lang = $bodyElement->getLang()) !== '') {
            $element->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:lang', $lang);
        }

        if (($label = $bodyElement->getLabel()) != '') {
            $element->setAttribute('label', $label);
        }
        
        $version = $this->getVersion();
        if (Version::compare($version, '2.2.0', '>=') === true && ($dir = $bodyElement->getDir()) !== Direction::AUTO && in_array($bodyElement->getQtiClassName(), self::$dirClasses) === true) {
            $element->setAttribute('dir', Direction::getNameByConstant($dir));
        }
    }
    
    protected function createElement(QtiComponent $component)
    {
        $localName = $component->getQtiClassName();
        
        if ($this->isWebComponentFriendly() === true && in_array($localName, self::$webComponentFriendlyClasses) === true) {
            $localName = XmlUtils::webComponentFriendlyClassName($localName);
        }
        
        return self::getDOMCradle()->createElement($localName);
    }
    
    /**
     * Is Web Component Friendly
     *
     * Whether or not the Marshaller should work in Web Component Friendly mode.
     *
     * @return bool
     */
    protected function isWebComponentFriendly()
    {
        return $this->getMarshallerFactory()->isWebComponentFriendly();
    }

    /**
     * Marshall a QtiComponent object into its QTI-XML equivalent.
     *
     * @param QtiComponent $component A QtiComponent object to marshall.
     * @return DOMElement A DOMElement object.
     * @throws MarshallingException If an error occurs during the marshalling process.
     */
    abstract protected function marshall(QtiComponent $component);

    /**
     * Unmarshall a DOMElement object into its QTI Data Model equivalent.
     *
     * @param DOMElement $element A DOMElement object.
     * @return QtiComponent A QtiComponent object.
     */
    abstract protected function unmarshall(DOMElement $element);

    /**
     * Get the class name/tag name of the QtiComponent/DOMElement which can be handled
     * by the Marshaller's implementation.
     *
     * Return an empty string if the marshaller implementation does not expect a particular
     * QTI class name.
     *
     * @return string A QTI class name or an empty string.
     */
    abstract public function getExpectedQtiClassName();

    /**
     * @param QtiComponent|string $component
     * @return string
     */
    private function getComponentName($component)
    {
        if ($component instanceof QtiComponent) {
            return $component->getQtiClassName();
        }
        return $this->getElementName($component);
    }

    /**
     * @param DOMElement|string $element
     * @return string
     */
    private function getElementName($element)
    {
        if ($element instanceof DOMElement) {
            return $element->localName;
        }
        if (is_object($element)) {
            return get_class($element);
        }

        return $element;
    }
}
