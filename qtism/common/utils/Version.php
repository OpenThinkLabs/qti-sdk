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

namespace qtism\common\utils;

use InvalidArgumentException;
use qtism\data\storage\xml\versions\QtiVersion;

/**
 * This utility class provides utility classes about Semantic Versionning.
 *
 * @see http://semver.org Semantic Versioning
 */
class Version
{
    /**
     * Compare two version numbers of QTI, following the rules of Semantic Versioning.
     *
     * This method provides QTI version comparison. Two versions are compared using an optional operator.
     * When no $operator is provided, this method returns
     *
     * * -1 if $version1 is lower than $version2
     * * 0 if $version1 is equal to $version2
     * * 1 if $version1 is greater than $version2
     *
     * In case of using a specific $operator, the method returns true or false depending on
     * the $operator and versions given. Accepted operators are '<', 'lt', '<=', 'le', '>', 'gt',
     * '>=', 'ge', '==', '=', 'eq', '!=' and 'ne'.
     *
     * If an unknown QTI version is given for $version1 or $version2 arguments, or if $operator
     * is an unknown operator, an InvalidArgumentException is thrown.
     *
     * Important note: This metod will consider version '2.1' and '2.1.0' equal.
     *
     * @param string $version1 A version number.
     * @param string $version2 A version number
     * @param string $operator An operator.
     * @return mixed
     * @throws InvalidArgumentException
     * @see http://semver.org Semantic Versioning
     */
    public static function compare($version1, $version2, $operator = null)
    {
        $version1 = trim($version1);
        $version2 = trim($version2);

        // Because version_compare consider 2.1 < 2.1.0...
        $version1 = self::appendPatchVersion($version1);
        $version2 = self::appendPatchVersion($version2);

        // Check if the versions are known...
        QtiVersion::checkVersion($version1);
        QtiVersion::checkVersion($version2);

        // Check if operator is correct.
        $knownOperators = ['<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne'];
        if (is_null($operator) === true || in_array($operator, $knownOperators) === true) {
            return (is_null($operator) === true) ? version_compare($version1, $version2) : version_compare($version1, $version2, $operator);
        } else {
            $msg = "Unknown operator '${operator}'. Known operators are '" . implode(', ', $knownOperators) . "'.";
            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * Append patch version to $version if $version only contains
     * major and minor versions.
     *
     * @param string $versionNumber
     * @return string
     * @throws InvalidArgumentException when the given version is not semantic.
     */
    public static function appendPatchVersion($versionNumber): string
    {
        $versionNumber = trim($versionNumber);

        if (preg_match('/^\d+\.\d+\.\d+$/', $versionNumber)) {
            return $versionNumber;
        }
        if (preg_match('/^\d+\.\d+$/', $versionNumber)) {
            return $versionNumber . '.0';
        }
        if (preg_match('/^\d+$/', $versionNumber)) {
            return $versionNumber . '.0.0';
        }

        throw new InvalidArgumentException(
            sprintf("Provided version number '%s' is not compliant to semantic versioning.", $versionNumber)
        );
    }
}
