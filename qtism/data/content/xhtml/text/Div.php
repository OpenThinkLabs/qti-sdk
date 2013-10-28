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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\content\xhtml\text;

use qtism\common\utils\Format;

use qtism\data\content\FlowCollection;
use qtism\data\content\FlowStatic;
use qtism\data\content\BlockStatic;
use qtism\data\content\BodyElement;
use \InvalidArgumentException;

/**
 * The XHTML div class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Div extends BodyElement implements BlockStatic, FlowStatic {
    
    /**
     * A base URI.
     * 
     * @var string
     * @qtism-bean-property
     */
    private $base = '';
    
    /**
     * The Flow objects composing the Div.
     * 
     * @var FlowCollection A collection of Flow objects.
     * @qtism-bean-property
     */
    private $components;
    
    /**
     * Get the collection of Flow objects composing the Div.
     * 
     * @return FlowCollection A collection of Flow objects.
     */
    public function getComponents() {
        return $this->components;
    }
    
    /**
     * Set the collection of Flow objects composing the Div.
     * 
     * @param FlowCollection $flows A collection of Flow objects.
     */
    public function setComponents(FlowCollection $flows) {
        $this->setComponents($components);
    }
    
    /**
     * Set the base URI of the Div.
     * 
     * @param string $base A URI.
     * @throws InvalidArgumentException if $base is not a valid URI nor an empty string.
     */
    public function setBase($base = '') {
        if (is_string($base) && (empty($base) || Format::isUri($base))) {
            $this->base = $base;
        }
        else {
            $msg = "The 'base' argument must be an empty string or a valid URI, '" . $base . "' given";
            throw new InvalidArgumentException($msg);
        }
    }
    
    /**
     * Get the base URI of the Div.
     * 
     * @return string An empty string or a URI.
     */
    public function getBase() {
        return $this->base;
    }
    
    public function getQtiClassName() {
        return 'div';
    }
}