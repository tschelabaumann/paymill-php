<?php

namespace Paymill\Test\Unit\Models\Response;

use Paymill\Models\Response as Response;
use PHPUnit_Framework_TestCase;

/**
 * Paymill\Models\Response\Checksum test case.
 */
class ChecksumTest
        extends PHPUnit_Framework_TestCase
{

    /**
     * @var \Paymill\Models\Response\Checksum
     */
    private $_model;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Response\Checksum();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_model = null;
        parent::tearDown();
    }

    //Testmethods
    /**
     * Tests the getters and setters of the model
     * @test
     */
    public function setGetTest()
    {
        $this->_model->setData('test=foo');
        $this->_model->setType('creditcard');
        $this->_model->setChecksum('foo-checksum');
        $this->_model->setAppId('app_123');
        $this->_model->setId('chk_123');
        $this->_model->setCreatedAt(23423142314);
        $this->_model->setUpdatedAt(23423142314);

        $this->assertEquals($this->_model->getData(), 'test=foo');
        $this->assertEquals($this->_model->getType(), 'creditcard');
        $this->assertEquals($this->_model->getChecksum(), 'foo-checksum');
        $this->assertEquals($this->_model->getAppId(), 'app_123');
        $this->assertEquals($this->_model->getId(), 'chk_123');
        $this->assertEquals($this->_model->getCreatedAt(), 23423142314);
        $this->assertEquals($this->_model->getUpdatedAt(), 23423142314);
    }

}
