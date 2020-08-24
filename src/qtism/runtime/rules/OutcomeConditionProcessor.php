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

namespace qtism\runtime\rules;

use qtism\data\rules\OutcomeCondition;

/**
 * From IMS QTI:
 *
 * If the expression given in the outcomeIf or outcomeElseIf evaluates to true then
 * the sub-rules contained within it are followed and any following outcomeElseIf or
 * outcomeElse parts are ignored for this outcome condition.
 *
 * If the expression given in the outcomeIf or outcomeElseIf does not evaluate to true
 * then consideration passes to the next outcomeElseIf or, if there are no more
 * outcomeElseIf parts then the sub-rules of the outcomeElse are followed
 * (if specified).
 */
class OutcomeConditionProcessor extends AbstractConditionProcessor
{
    public function getQtiNature()
    {
        return 'outcome';
    }

    protected function getRuleType()
    {
        return OutcomeCondition::class;
    }
}
