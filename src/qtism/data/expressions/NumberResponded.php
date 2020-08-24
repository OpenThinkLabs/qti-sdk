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

namespace qtism\data\expressions;

/**
 * From IMS QTI:
 *
 * This expression, which can only be used in outcomes processing, calculates the number
 * of items in a given sub-set that have been attempted (at least once) and for which a
 * response was given. In other words, items for which at least one declared response
 * has a value that differs from its declared default (typically NULL). The result is an
 * integer with single cardinality.
 */
class NumberResponded extends ItemSubset
{
    public function getQtiClassName()
    {
        return 'numberResponded';
    }
}
