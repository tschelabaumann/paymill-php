<?php

namespace Paymill\Test\Unit\Models\Request;

use Paymill\Models\Request as Request;
use PHPUnit_Framework_TestCase;

/**
 * Paymill\Models\Request\Checksum test case.
 */
class ChecksumTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Request\Checksum
     */
    private $_model;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Request\Checksum();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_model = null;
        parent::tearDown();
    }

    /**
     * Tests the getters and setters of the model
     * @test
     */
    public function setGetTest()
    {
        $sample = array(
            'checksum_type' => 'creditcard',
            'amount'        => '200',
            'currency'      => 'CHF',
            'description'   => 'foo bar',
            'return_url'    => 'https://www.example.com',
            'cancel_url'    => 'https://www.example.com'
        );

        $this->_model
            ->setChecksumType($sample['checksum_type'])
            ->setAmount($sample['amount'])
            ->setCurrency($sample['currency'])
            ->setDescription($sample['description'])
            ->setReturnUrl($sample['return_url'])
            ->setCancelUrl($sample['cancel_url'])
        ;

        $this->assertEquals($this->_model->getChecksumType(), $sample['checksum_type']);
        $this->assertEquals($this->_model->getAmount(),       $sample['amount']);
        $this->assertEquals($this->_model->getCurrency(),     $sample['currency']);
        $this->assertEquals($this->_model->getDescription(),  $sample['description']);
        $this->assertEquals($this->_model->getReturnUrl(),    $sample['return_url']);
        $this->assertEquals($this->_model->getCancelUrl(),    $sample['cancel_url']);

        return $this->_model;
    }

    /**
     * Test the Parameterize function of the model
     * @test
     * @depends setGetTest
     */
    public function parameterizeTestGetOne(Request\Checksum $model)
    {
        $model->setId('chk_123');
        $parameterArray = [
            'count' => 1,
            'offset' => 0
        ];

        $creationArray = $model->parameterize("getOne");

        $this->assertEquals($creationArray, $parameterArray);
    }

    /**
     * Test the Parameterize function of the model
     * @test
     * @depends setGetTest
     */
    public function parameterizeTestCreate(Request\Checksum $model)
    {
        $parameterArray = array();
        $parameterArray['checksum_type'] = 'creditcard';
        $parameterArray['amount']        = '200';
        $parameterArray['currency']      = 'CHF';
        $parameterArray['description']   = 'foo bar';
        $parameterArray['return_url']    = 'https://www.example.com';
        $parameterArray['cancel_url']    = 'https://www.example.com';

        $creationArray = $model->parameterize("create");

        $this->assertEquals($creationArray, $parameterArray);
    }

}
