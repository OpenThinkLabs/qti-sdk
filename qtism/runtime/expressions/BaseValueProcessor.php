<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */

namespace qtism\runtime\expressions;

use InvalidArgumentException;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\Expression;
use qtism\runtime\common\Utils as RuntimeUtils;

/**
 * The BaseValueProcessor class aims at processing BaseValue expressions.
 *
 * From IMS QTI:
 *
 * The simplest expression returns a single value from the set defined by the given baseType.
 */
class BaseValueProcessor extends ExpressionProcessor
{
    public function setExpression(Expression $expression)
    {
        if ($expression instanceof BaseValue) {
            parent::setExpression($expression);
        } else {
            $msg = "The BaseValueProcessor class only processes BaseValue QTI Data Model objects.";
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Process the BaseValue.
     *
     * @return mixed A QTI Runtime compliant scalar value.
     * @see \qtism\runtime\common\Processable::process()
     */
    public function process()
    {
        $expression = $this->getExpression();

        return RuntimeUtils::valueToRuntime($expression->getValue(), $expression->getBaseType());
    }
}
