<?php

namespace Paymill\Models\Request;

/**
 * Transaction Model
 *
 * A transaction is the charging of a credit card or a direct debit.
 * In this case you need a new transaction object with either a valid token, payment, client + payment or
 * preauthorization. Every transaction has a unique identifier which will be generated by Paymill to identify every
 * transaction. You can issue/create, list and display transactions in detail. Refunds can be done in an extra entity.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-transactions
 */
class Transaction extends Base
{
    /**
     * Amount
     *
     * @var string
     */
    private $_amount;

    /**
     * Description
     *
     * @var string
     */
    private $_description;

    /**
     * Currency
     *
     * @var string
     */
    private $_currency;

    /**
     * Payment
     *
     * @var string
     */
    private $_payment;

    /**
     * Client
     *
     * @var string
     */
    private $_client = null;

    /**
     * Preauthorization
     *
     * @var string
     */
    private $_preauthorization;

    /**
     * Token
     *
     * @var string
     */
    private $_token;

    /**
     * Fee amount
     *
     * @var string
     */
    private $_feeAmount;

    /**
     * Fee payment
     *
     * @var string
     */
    private $_feePayment;

    /**
     * Fee currency
     *
     * @var string
     */
    private $_feeCurrency;

    /**
     * Source
     *
     * @var $_source
     */
    private $_source;

    /**
     * Shipping address
     *
     * @var array $_shippingAddress
     */
    private $_shippingAddress;

    /**
     * Billing address
     *
     * @var array $_billingAddress
     */
    private $_billingAddress;

    /**
     * Items
     *
     * @var array $_items
     */
    private $_items;

    /**
     * Shipping amount
     *
     * @var int $_shipping_amount
     */
    private $_shipping_amount;

    /**
     * Handling amount
     *
     * @var int $_handling_amount
     */
    private $_handling_amount;

    /**
     * @var string
     */
    private $_mandateReference;

    /**
     * Creates an instance of the transaction request model
     */
    function __construct()
    {
        $this->_serviceResource = 'Transactions/';
    }

    /**
     * Returns the 'real' amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Sets the 'real' amount for the transaction.
     * The number must be in the smallest currency unit and will be saved as a string
     *
     * @param string $amount Amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;

        return $this;
    }

    /**
     * Returns the transaction description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the transaction description
     *
     * @param string $description Description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Returns the currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Sets the currency
     *
     * @param string $currency Currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;

        return $this;
    }

    /**
     * Returns the identifier of the payment associated with the transaction
     *
     * @return string
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Sets the identifier of the Payment for the transaction
     *
     * @param string $payment Payment
     *
     * @return $this
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;

        return $this;
    }

    /**
     * Returns the identifier of the Client associated with the transaction. If no client is available null will be returned
     *
     * @return string|null
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Sets the identifier of the Client for the transaction
     *
     * @param string $client Client
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Returns the identifier of the Preauthorization associated with the transaction. If no preAuth is available null will be returned
     *
     * @return string|null
     */
    public function getPreauthorization()
    {
        return $this->_preauthorization;
    }

    /**
     * Sets the identifier of the Preauthorization for the transaction
     *
     * @param string $preauthorization Preauthorization
     *
     * @return $this
     */
    public function setPreauthorization($preauthorization)
    {
        $this->_preauthorization = $preauthorization;

        return $this;
    }

    /**
     * Returns the FeeAmount
     * Fee included in the transaction amount (set by a connected app). Mandatory if feePayment is set
     *
     * @return integer
     */
    public function getFeeAmount()
    {
        return $this->_feeAmount;
    }

    /**
     * Sets the Fee included in the transaction amount (set by a connected app).
     *
     * @param integer $feeAmount Fee amount
     *
     * @return $this
     */
    public function setFeeAmount($feeAmount)
    {
        $this->_feeAmount = $feeAmount;

        return $this;
    }

    /**
     * Returns the identifier of the payment from which the fee will be charged (creditcard-object or direct debit-object).
     *
     * @return string
     */
    public function getFeePayment()
    {
        return $this->_feePayment;
    }

    /**
     * Sets the identifier of the payment from which the fee will be charged (creditcard-object or direct debit-object).
     *
     * @param string $feePayment Fee payment
     *
     * @return $this
     */
    public function setFeePayment($feePayment)
    {
        $this->_feePayment = $feePayment;

        return $this;
    }

    /**
     * Set the currency which should be used for collecting the given fee
     *
     * @param string $feeCurrency (e.g. EUR, USD ...)
     *
     * @return $this
     */
    public function setFeeCurrency($feeCurrency)
    {
        $this->_feeCurrency = $feeCurrency;

        return $this;

    }

    /**
     * returns the set fee currency which is used for the fee collection
     *
     * @return string
     */
    public function getFeeCurrency()
    {
        return $this->_feeCurrency;
    }



    /**
     * Returns the  token generated through our JavaScript-Bridge.
     * When this parameter is used, none of the following should be used: payment, preauthorization.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the token generated through our JavaScript-Bridge.
     * When this parameter is used, none of the following should be used: payment, preauthorization.
     *
     * @param string $token Token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->_token = $token;

        return $this;
    }

    /**
     * Sets the name of origin of the call creating the transaction.
     *
     * @param string $source Source
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->_source = $source;

        return $this;
    }

    /**
     * Gets the name of origin of the call creating the transaction.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Get shipping address
     *
     * @return array
     */
    public function getShippingAddress()
    {
        return $this->_shippingAddress;
    }

    /**
     * Set shipping address
     *
     * @param array $shippingAddress Shipping address
     *
     * @return $this
     */
    public function setShippingAddress(array $shippingAddress)
    {
        $this->_shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get billing address
     *
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->_billingAddress;
    }

    /**
     * Set billing address
     *
     * @param array $billingAddress Billing address
     *
     * @return $this
     */
    public function setBillingAddress(array $billingAddress)
    {
        $this->_billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Set items
     *
     * @param array $items Items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->_items = $items;

        return $this;
    }

    /**
     * Get shipping amount
     *
     * @return int
     */
    public function getShippingAmount()
    {
        return $this->_shipping_amount;
    }

    /**
     * Set shipping_amount
     *
     * @param int $shipping_amount Shipping amount
     *
     * @return $this
     */
    public function setShippingAmount($shipping_amount)
    {
        $this->_shipping_amount = $shipping_amount;

        return $this;
    }

    /**
     * Get handling amount
     *
     * @return int
     */
    public function getHandlingAmount()
    {
        return $this->_handling_amount;
    }

    /**
     * Set handling amount
     *
     * @param int $handling_amount Handling amount
     *
     * @return $this
     */
    public function setHandlingAmount($handling_amount)
    {
        $this->_handling_amount = $handling_amount;

        return $this;
    }

    /**
     * Returns mandate reference
     * @return string
     */
    public function getMandateReference()
    {
        return $this->_mandateReference;
    }

    /**
     * Set mandate reference
     *
     * @param string $mandateReference
     *
     * @return $this
     */
    public function setMandateReference($mandateReference)
    {
        $this->_mandateReference = $mandateReference;

        return $this;
    }

    /**
     * Returns an array of parameters customized for the given method name.
     *
     * @param string $method Method
     *
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = [];
        switch ($method) {
            case 'create':
                if (!is_null($this->getPreauthorization())) {
                    $parameterArray['preauthorization'] = $this->getPreauthorization();
                } elseif (!is_null($this->getPayment())) {
                    $parameterArray['payment'] = $this->getPayment();
                } else {
                    $parameterArray['token'] = $this->getToken();
                }
                $parameterArray['amount'] = $this->getAmount();
                $parameterArray['currency'] = $this->getCurrency();
                $parameterArray['description'] = $this->getDescription();
                $parameterArray['client'] = $this->getClient();
                if (!is_null($this->getFeeAmount())) {
                    $parameterArray['fee_amount'] = $this->getFeeAmount();
                }
                if (!is_null($this->getFeePayment())) {
                    $parameterArray['fee_payment'] = $this->getFeePayment();
                }
                if (!is_null($this->getFeeCurrency())) {
                    $parameterArray['fee_currency'] = $this->getFeeCurrency();
                }
                if(!is_null($this->getSource())) {
                    $parameterArray['source'] = $this->getSource();
                }
                if(!is_null($this->getShippingAddress())) {
                    $parameterArray['shipping_address'] = $this->getShippingAddress();
                }
                if(!is_null($this->getBillingAddress())) {
                    $parameterArray['billing_address'] = $this->getBillingAddress();
                }
                if(!is_null($this->getItems())) {
                    $parameterArray['items'] = $this->getItems();
                }
                if(!is_null($this->getShippingAmount())) {
                    $parameterArray['shipping_amount'] = $this->getShippingAmount();
                }
                if(!is_null($this->getHandlingAmount())) {
                    $parameterArray['handling_amount'] = $this->getHandlingAmount();
                }
                if (!is_null($this->getMandateReference())) {
                    $parameterArray['mandate_reference'] = $this->getMandateReference();
                }
                if(!is_null($this->getShippingAddress())) {
                    $parameterArray['shipping_address'] = $this->getShippingAddress();
                }
                if(!is_null($this->getBillingAddress())) {
                    $parameterArray['billing_address'] = $this->getBillingAddress();
                }
                break;
            case 'update':
                $parameterArray['description'] = $this->getDescription();
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'delete':
                break;
        }

        return $parameterArray;
    }
}
