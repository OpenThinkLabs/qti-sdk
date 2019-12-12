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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Julien Sébire <julien@taotesting.com>
 * @license GPLv2
 */

namespace qtism\runtime\tests;

use DateTime;

interface LastProcessingTimeAwareInterface
{
    /**
     * Updates the last processing time to the current time.
     */
    public function updateLastProcessingTime();

    /**
     * Returns the last processing time.
     *
     * @return DateTime
     */
    public function getLastProcessingTime();

    /**
     * Sets the last processing time to the given time.
     * 
     * @param DateTime|null $lastProcessingTime
     */
    public function setLastProcessingTime(DateTime $lastProcessingTime = null);
}