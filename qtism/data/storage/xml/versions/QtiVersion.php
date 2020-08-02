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
 * @author Julien Sébire <julien@taotesting.com>
 * @license GPLv2
 */

namespace qtism\data\storage\xml\versions;

use DOMDocument;
use InvalidArgumentException;
use qtism\common\utils\Version;
use qtism\data\storage\xml\Utils;
use qtism\data\storage\xml\XmlStorageException;

/**
 * Generic QTI version.
 */
class QtiVersion extends Version
{
    const SUPPORTED_VERSIONS = [
        '2.0.0' => QtiVersion200::class,
        '2.1.0' => QtiVersion210::class,
        '2.1.1' => QtiVersion211::class,
        '2.2.0' => QtiVersion220::class,
        '2.2.1' => QtiVersion221::class,
        '2.2.2' => QtiVersion222::class,
        '3.0.0' => QtiVersion300::class,
    ];

    const UNSUPPORTED_VERSION_MESSAGE = 'QTI version "%s" is not supported.';

    /** @var string */
    private $versionNumber;

    public function __construct(string $versionNumber)
    {
        $this->versionNumber = $versionNumber;
    }

    public function __toString(): string
    {
        return $this->versionNumber;
    }

    /**
     * Creates a new Version given the version number.
     *
     * @param string $versionNumber
     * @return $this
     */
    public static function create(string $versionNumber): self
    {
        $versionNumber = self::sanitize($versionNumber);
        $class = static::SUPPORTED_VERSIONS[$versionNumber];
        return new $class($versionNumber);
    }

    /**
     * Checks that the given version is supported.
     *
     * @param string $version a semantic version
     * @throws InvalidArgumentException when the version is not supported.
     */
    protected static function checkVersion(string $version)
    {
        if (!isset(static::SUPPORTED_VERSIONS[$version])) {
            throw QtiVersionException::unsupportedVersion(static::UNSUPPORTED_VERSION_MESSAGE, $version, static::SUPPORTED_VERSIONS);
        }
    }

    /**
     * Infer the QTI version of the document from its XML definition.
     *
     * @param DOMDocument $document
     * @return string a semantic QTI version inferred from the document.
     * @throws XmlStorageException when the version can not be inferred.
     */
    public static function infer(DOMDocument $document): string
    {
        $root = $document->documentElement;
        $version = '';

        if ($root !== null) {
            $rootNs = $root->namespaceURI;
            if ($rootNs !== null) {
                $version = static::findVersionInDocument($rootNs, $document);
            }
        }

        if ($version === '') {
            $msg = 'Cannot infer QTI version. Check namespaces and schema locations in XML file.';
            throw new XmlStorageException($msg);
        }

        return $version;
    }

    /**
     * Finds the version of the document given the namespace and Xsd location.
     *
     * @param string $rootNs
     * @param DOMDocument $document
     * @return string
     */
    public static function findVersionInDocument(string $rootNs, DOMDocument $document): string
    {
        $version = '';

        if ($rootNs === QtiVersion200::XMLNS) {
            $version = '2.0.0';
        } elseif ($rootNs === QtiVersion210::XMLNS) {
            $version = '2.1.0';

            $nsLocation = Utils::getXsdLocation($document, $rootNs);
            if ($nsLocation === QtiVersion211::XSD) {
                $version = '2.1.1';
            }
        } elseif ($rootNs === QtiVersion220::XMLNS) {
            $version = '2.2.0';

            $nsLocation = Utils::getXsdLocation($document, $rootNs);
            if ($nsLocation === QtiVersion221::XSD) {
                $version = '2.2.1';
            } elseif ($nsLocation === QtiVersion222::XSD) {
                $version = '2.2.2';
            }
        } elseif ($rootNs === QtiVersion300::XMLNS) {
            $version = '3.0.0';
        }
        
        return $version;
    }

    public function getLocalXsd(): string
    {
        return __DIR__ . '/../schemes/' . static::LOCAL_XSD;
    }

    public function getNamespace(): string
    {
        return static::XMLNS;
    }

    public function getXsdLocation(): string
    {
        return static::XSD;
    }

    public function getMarshallerFactoryClass(): string
    {
        return static::MARSHALLER_FACTORY;
    }
}
