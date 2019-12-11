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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 */
namespace qtism\common\storage;

use \DateTime;
use Exception;

/**
 * The BinaryStreamAccess aims at providing the needed methods to
 * easily read the data contained by BinaryStream objects.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BinaryStreamAccess extends AbstractStreamAccess
{
    /**
     * Set the IStream object to be read.
     *
     * @param \qtism\common\storage\IStream $stream An IStream object.
     * @throws \qtism\common\storage\StreamAccessException If the $stream is not open yet.
     */
    protected function setStream(IStream $stream)
    {
        if ($stream->isOpen() === false) {
            $msg = "A BinaryStreamAccess do not accept closed streams to be read.";
            throw new BinaryStreamAccessException($msg, $this, StreamAccessException::NOT_OPEN);
        }

       parent::setStream($stream);
    }

    /**
     * Read a single byte unsigned integer from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return integer
     */
    public function readTinyInt()
    {
        try {
            $bin = $this->getStream()->read(1);

            return ord($bin);
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::TINYINT);
        }
    }

    /**
     * Write a single byte unsigned integer in the current binary stream.
     *
     * @param integer $tinyInt
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeTinyInt($tinyInt)
    {
        try {
            $this->getStream()->write(chr($tinyInt));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::TINYINT, false);
        }
    }

    /**
     * Read a 2 bytes unsigned integer from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return integer
     */
    public function readShort()
    {
        try {
            $bin = $this->getStream()->read(2);

            return current(unpack('S', $bin));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::SHORT);
        }
    }

    /**
     * Write a 2 bytes unsigned integer in the current binary stream.
     *
     * @param integer $short
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeShort($short)
    {
        try {
            $this->getStream()->write(pack('S', $short));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::SHORT, false);
        }
    }

    /**
     * Read a 8 bytes signed integer from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return integer
     */
    public function readInteger()
    {
        try {
            $bin = $this->getStream()->read(4);

            return current(unpack('l', $bin));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::INT);
        }
    }

    /**
     * Write a 8 bytes signed integer in the current binary stream.
     *
     * @param integer $int
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeInteger($int)
    {
        try {
            $this->getStream()->write(pack('l', $int));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::INT, false);
        }
    }

    /**
     * Read a double precision float from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return integer
     */
    public function readFloat()
    {
        try {
            $bin = $this->getStream()->read(8);

            return current(unpack('d', $bin));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::FLOAT);
        }
    }

    /**
     * Write a double precision float in the current binary stream.
     *
     * @param float $float
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeFloat($float)
    {
        try {
            $this->getStream()->write(pack('d', $float));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::FLOAT, false);
        }
    }

    /**
     * Read a boolean value from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return boolean
     */
    public function readBoolean()
    {
        try {
            $int = ord($this->getStream()->read(1));

            return ($int === 0) ? false : true;
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::BOOLEAN);
        }
    }

    /**
     * Write a boolean value from the current binary stream.
     *
     * @param boolean $boolean
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeBoolean($boolean)
    {
        try {
            $val = ($boolean === true) ? 1 : 0;
            $this->getStream()->write(chr($val));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::FLOAT, false);
        }
    }

    /**
     * Read a string value from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return string
     */
    public function readString()
    {
        try {
            $binLength = $this->getStream()->read(2);
            $length = current(unpack('S', $binLength));

            return $this->getStream()->read($length);
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::STRING);
        }
    }

    /**
     * Write a string value from in the current binary string.
     *
     * @param string $string
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeString($string)
    {
        // $maxLen = 2^16 -1 = max val of unsigned short integer.
        $maxLen = 65535;
        $len = strlen($string);

        if ($len > $maxLen) {
            $len = $maxLen;
            $string = substr($string, 0, $maxLen);
        }

        try {
            $this->getStream()->write(pack('S', $len) . $string);
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::STRING, false);
        }
    }

    /**
     * Read binary data from the current binary stream.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     * @return string A binary string.
     */
    public function readBinary()
    {
        return $this->readString();
    }

    /**
     * Write binary data in the current binary stream.
     *
     * @param string $binary
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeBinary($binary)
    {
        $this->writeString($binary);
    }

    /**
     * Read a DateTime from the current binary stream.
     *
     * @return \DateTime A DateTime object.
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function readDateTime()
    {
        try {
            $timeStamp = current(unpack('l', $this->getStream()->read(4)));
            return new DateTime('@' . $timeStamp, new \DateTimeZone('UTC'));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::DATETIME);
        } catch (Exception $e) {
            throw new BinaryStreamAccessException($e->getMessage(), $this, BinaryStreamAccessException::DATETIME);
        }
    }

    /**
     * Write a DateTime to the current binary stream.
     *
     * @param \DateTime $dateTime A DateTime object.
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeDateTime(DateTime $dateTime)
    {
        try {
            $this->getStream()->write(pack('l', $dateTime->getTimestamp()));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::DATETIME, false);
        }
    }

    /**
     * Read a DateTime with micro seconds from the current binary stream.
     *
     * @return \DateTime A DateTime object.
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function readDateTimeWithMicroSeconds()
    {
        try {
            // Boolean flag: 0 = null, 1 = DateTime.
            if (ord($this->getStream()->read(1)) === 0) {
                return null;
            }

            $timeStamp = current(unpack('l', $this->getStream()->read(4)));
            $microSeconds = current(unpack('L', $this->getStream()->read(4)));
            return DateTime::createFromFormat('U.u', $timeStamp . '.' . $microSeconds, new \DateTimeZone('UTC'));
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::DATETIME_MICROSECONDS);
        }
    }

    /**
     * Writes a DateTime with micro seconds to the current binary stream, prepended with a boolean flag:
     *      0: null value.
     *      1: non-null value.
     *
     * @param \DateTime $dateTime A DateTime object.
     *
     * @throws \qtism\common\storage\BinaryStreamAccessException
     */
    public function writeDateTimeWithMicroSeconds(DateTime $dateTime = null)
    {
        try {
            // Boolean flag: 0 = null, 1 = DateTime.
            $val = $dateTime !== null ? 1 : 0;
            $this->getStream()->write(chr($val));

            // Actual DateTime value.
            if ($dateTime !== null) {
                $this->getStream()->write(pack('l', $dateTime->getTimestamp()));
                $this->getStream()->write(pack('L', (int)$dateTime->format('u')));
            }
        } catch (StreamException $e) {
            $this->handleStreamException($e, BinaryStreamAccessException::DATETIME_MICROSECONDS, false);
        }
    }

    /**
     * Handle a StreamException in order to throw the relevant BinaryStreamAccessException.
     *
     * @param \qtism\common\storage\StreamException $e The StreamException object to deal with.
     * @param integer $typeError The BinaryStreamAccess exception code to be thrown in case of error.
     * @param boolean $read Wheter or not the error occured in a reading/writing context.
     * @throws \qtism\common\storage\BinaryStreamAccessException The resulting BinaryStreamAccessException.
     */
    protected function handleStreamException(StreamException $e, $typeError, $read = true)
    {
        $strType = 'unknown datatype';

        switch ($typeError) {
            case BinaryStreamAccessException::BOOLEAN:
                $strType = 'boolean';
            break;

            case BinaryStreamAccessException::BINARY:
                $strType = 'binary data';
            break;

            case BinaryStreamAccessException::FLOAT:
                $strType = 'double precision float';
            break;

            case BinaryStreamAccessException::INT:
                $strType = 'integer';
            break;

            case BinaryStreamAccessException::SHORT:
                $strType = 'short integer';
            break;

            case BinaryStreamAccessException::STRING:
                $strType = 'string';
            break;

            case BinaryStreamAccessException::TINYINT:
                $strType = 'tiny integer';
            break;

            case BinaryStreamAccessException::DATETIME:
                $strType = 'datetime';
            break;
        }

        $strAction = ($read === true) ? 'reading' : 'writing';

        switch ($e->getCode()) {
            case StreamException::NOT_OPEN:
                $strAction = ucfirst($strAction);
                $msg = "${strAction} a ${strType} from a closed binary stream is not permitted.";
                throw new BinaryStreamAccessException($msg, $this, BinaryStreamAccessException::NOT_OPEN, $e);
                break;

            case StreamException::READ:
                $msg = "An error occurred while ${strAction} a ${strType}.";
                throw new BinaryStreamAccessException($msg, $this, $typeError, $e);
                break;

            default:
                $msg = "An unknown error occurred while ${strAction} a ${strType}.";
                throw new BinaryStreamAccessException($msg, $this, BinaryStreamAccessException::UNKNOWN, $e);
                break;
        }
    }
}
