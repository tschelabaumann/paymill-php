<?php

namespace Paymill\Models\Response;

/**
 * Checksum Model
 *
 * A checksum validation is a simple method to nearly ensure the integrity of transferred data.
 * Basically we generate a hash out of the over given parameters and your private key.
 * If you send us a request with transaction data and the generated checksum, we can easily validate the data
 * because we know your private key and the used hash algorithm.
 * To make the checksum computation as easy as possible we provide this endpoint for you.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-checksum
 */
class Checksum extends Base
{
    /**
     * Checksum
     *
     * @var string
     */
    private $_checksum;

    /**
     * Type
     *
     * @var string
     */
    private $_type;

    /**
     * Data
     *
     * @var string
     */
    private $_data;

    /**
     * Returns the checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->_checksum;
    }

    /**
     * Sets the checksum
     *
     * @param string $val
     * @return $this
     */
    public function setChecksum($val)
    {
        $this->_checksum = $val;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set type
     *
     * @param string $type type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->_type = $type;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set data
     *
     * @param string $data data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;

        return $this;
    }
}
