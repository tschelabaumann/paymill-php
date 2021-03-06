<?php

namespace Paymill\Services;

use Paymill\Models\Response as Models;
use Paymill\Models\Response\Checksum;
use Paymill\Models\Response\Error;

/**
 * ResponseHandler
 */
class ResponseHandler
{
    /**
     * Possible response codes
     *
     * @var array
     */
    private $_errorCodes = array(
        10001 => "General undefined response.",
        10002 => "Still waiting on something.",
        11000 => "Retry later",

        20000 => "General success response.",
        20100 => "Funds held by acquirer.",
        20101 => "Funds held by acquirer because merchant is new.",
        20200 => "Transaction reversed.",
        20201 => "Reversed due to chargeback.",
        20202 => "Reversed due to money-back guarantee.",
        20203 => "Reversed due to complaint by buyer.",
        20204 => "Payment has been refunded.",
        20300 => "Reversal has been canceled.",

        30000 => "Transaction still in progress.",
        30100 => "Transaction has been accepted.",
        31000 => "Transaction pending.",
        31100 => "Pending due to address.",
        31101 => "Pending due to uncleared eCheck.",
        31102 => "Pending due to risk review.",
        31103 => "Pending due regulatory review.",
        31104 => "Pending due to unregistered/unconfirmed receiver.",
        31200 => "Pending due to unverified account, verify acquirer account.",
        31201 => "Pending due to uncaptured funds, capture funds first.",
        31202 => "Pending due to international account, accept manually.",
        31203 => "Pending due to currency conflict, accept manually.",
        31204 => "Pending due to fraud filters.",

        40000 => "General problem with data.",
        40001 => "General problem with payment data.",
        40002 => "Invalid checksum.",
        40100 => "Problem with credit card data.",
        40101 => "Problem with cvv.",
        40102 => "Card expired or not yet valid.",
        40103 => "Limit exceeded.",
        40104 => "Card invalid.",
        40105 => "Expiry date not valid.",
        40106 => "Credit card brand required.",
        40200 => "Problem with bank account data.",
        40201 => "Bank account data combination mismatch.",
        40202 => "User authentication failed.",
        40300 => "Problem with 3d secure data.",
        40301 => "Currency / amount mismatch",
        40400 => "Problem with input data.",
        40401 => "Amount too low or zero.",
        40402 => "Usage field too long.",
        40403 => "Currency not allowed.",
        40410 => "Invalid shopping cart data.",
        40420 => "Invalid address data.",
        40500 => "Permission error.",
        40510 => "Rate limit.",

        50000 => "General problem with backend.",
        50001 => "Country blacklisted.",
        50002 => "IP-Address blacklisted",
        50003 => "Anonymous IP proxy used",
        50004 => "Live mode not allowed.",
        50005 => "Insufficient permissions (paymill accesskey).",
        50100 => "Technical error with credit card.",
        50101 => "Error limit exceeded.",
        50102 => "Card declined by authorization system.",
        50103 => "Manipulation or stolen card.",
        50104 => "Card restricted.",
        50105 => "Invalid card configuration data.",
        50200 => "Technical error with bank account.",
        50201 => "Card blacklisted.",
        50300 => "Technical error with 3D secure.",
        50400 => "Decline because of risk issues.",
        50401 => "Checksum invalid.",
        50402 => "Bank account number invalid (format check).",
        50403 => "Technical risk error.",
        50404 => "Unknown risk error.",
        50405 => "Invalid bank code.",
        50406 => "Open chargeback.",
        50407 => "Historic chargeback.",
        50408 => "Institution/Government bank account (NCA).",
        50409 => "Fraud case.",
        50410 => "Personal Account Protection (PAP).",
        50420 => "Rejected due to fraud settings.",
        50430 => "Rejected due to risk settings.",
        50440 => "Merchant account restriction.",
        50500 => "General timeout.",
        50501 => "Timeout on side of the acquirer.",
        50502 => "Risk management transaction timeout.",
        50600 => "Duplicate transaction.",
        50700 => "Transaction canceled by user.",
        50710 => "Failed due to funding source.",
        50711 => "Cannot pay with PayPal.",
        50720 => "Declined by acquirer.",
        50730 => "Transaction denied by merchant.",
        50800 => "capture preauthorization failed.",
        50810 => "Authorization has been voided.",
        50820 => "Authorization period expired."
    );

    /**
     * Converts a response to a model
     * @param array $response
     * @param string $serviceResource
     * @return Models\Base|Error
     */
    public function convertResponse($response, $serviceResource)
    {
        $resourceName = substr($serviceResource, 0, -2);
        return $this->_convertResponseToModel($response, $resourceName);
    }

    /**
     * Creates an object from a response array based on the call-context
     * @param array $response Response from any Request
     * @param string $resourceName
     * @return Models\Base
     */
    private function _convertResponseToModel($response, $resourceName)
    {
        if (!is_array($response) || empty($response)) {
            return $response;
        }

        $model = null;
        switch (strtolower($resourceName)) {
            case 'client':
                $model = $this->_createClient($response);
                break;
            case 'payment':
                $model = $this->_createPayment($response);
                break;
            case 'transaction':
                $model = $this->_createTransaction($response);
                break;
            case 'preauthorization':
                if (isset($response['preauthorization'])) {
                    $response = $response['preauthorization'];
                }
                $model = $this->_createPreauthorization($response);
                break;
            case 'refund':
                $model = $this->_createRefund($response);
                break;
            case 'offer':
                $model = $this->_createOffer($response);
                break;
            case 'subscription':
                $model = $this->_createSubscription($response);
                break;
            case 'webhook':
                $model = $this->_createWebhook($response);
                break;
            case 'fraud':
                $model = $this->_createFraud($response);
                break;
            case 'checksum':
                $model = $this->_createChecksum($response);
                break;
        }

        return $model;
    }

    /**
     * Creates and fills a client model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Client
     */
    private function _createClient($response)
    {
        $model = new Models\Client();
        $model->setId($response['id']);
        $model->setEmail($response['email']);
        $model->setDescription($response['description']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setSubscription($this->_handleRecursive($response['subscription'], 'subscription'));
        $model->setAppId($response['app_id']);
        $model->setPayment($this->_handleRecursive($response['payment'], 'payment'));
        return $model;
    }

    /**
     * Creates and fills a payment model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Payment
     */
    private function _createPayment($response)
    {
        $model = new Models\Payment();
        $model->setId($response['id']);
        $model->setType($response['type']);
        $model->setClient($this->_convertResponseToModel($response['client'], "client"));
        if ($response['type'] === "creditcard") {
            $model->setCardType($response['card_type']);
            $model->setCountry($response['country']);
            $model->setExpireMonth($response['expire_month']);
            $model->setExpireYear($response['expire_year']);
            $model->setCardHolder($response['card_holder']);
            $model->setLastFour($response['last4']);
        } else if ($response['type'] === "debit") {
            $model->setHolder($response['holder']);
            $model->setCode($response['code']);
            $model->setAccount($response['account']);
            $model->setBic($response['bic']);
            $model->setIban($response['iban']);
        }
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setAppId($response['app_id']);
        return $model;
    }

    /**
     * Creates and fills a transaction model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Transaction
     */
    private function _createTransaction($response)
    {
        $model = new Models\Transaction();
        $model->setId($response['id']);
        $model->setAmount($response['amount']);
        $model->setOriginAmount($response['origin_amount']);
        $model->setStatus($response['status']);
        $model->setDescription($response['description']);
        $model->setLivemode($response['livemode']);
        $model->setRefunds($this->_handleRecursive($response['refunds'], 'refund'));
        $model->setCurrency($response['currency']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setResponseCode($response['response_code']);
        $model->setShortId($response['short_id']);
        $model->setInvoices($response['invoices']);
        $model->setPayment($this->_convertResponseToModel($response['payment'], "payment"));
        $model->setClient($this->_convertResponseToModel($response['client'], "client"));
        $model->setPreauthorization($this->_convertResponseToModel($response['preauthorization'], "preauthorization"));
        $model->setFees($response['fees']);
        $model->setAppId($response['app_id']);
        return $model;
    }

    /**
     * Creates and fills a preauthorization model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Preauthorization
     */
    private function _createPreauthorization($response)
    {
        $model = new Models\Preauthorization();
        $model->setId($response['id']);
        $model->setAmount($response['amount']);
        $model->setCurrency($response['currency']);
        $model->setStatus($response['status']);
        $model->setLivemode($response['livemode']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setPayment($this->_convertResponseToModel($response['payment'], "payment"));
        $model->setClient($this->_convertResponseToModel($response['client'], "client"));
        $model->setTransaction(isset($response['transaction']) ? $this->_convertResponseToModel($response['transaction'], 'transaction') : null);
        $model->setAppId($response['app_id']);
        $model->setDescription($response['description']);
        return $model;
    }

    /**
     * Creates and fills a refund model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Refund
     */
    private function _createRefund($response)
    {
        $model = new Models\Refund();
        $model->setId($response['id']);
        $model->setAmount($response['amount']);
        $model->setStatus($response['status']);
        $model->setDescription($response['description']);
        $model->setLivemode($response['livemode']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setResponseCode($response['response_code']);
        //Refund doesn't have the array index 'transaction' when using getOne
        $model->setTransaction(isset($response['transaction']) ? $this->_convertResponseToModel($response['transaction'], 'transaction') : null);
        $model->setAppId($response['app_id']);
        return $model;
    }

    /**
     * Creates and fills a offer model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Offer
     */
    private function _createOffer($response)
    {
        $model = new Models\Offer();
        $model->setId($response['id']);
        $model->setName($response['name']);
        $model->setAmount($response['amount']);
        $model->setCurrency($response['currency']);
        $model->setInterval($response['interval']);
        $model->setTrialPeriodDays($response['trial_period_days']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setSubscriptionCount($response['subscription_count']['active'], $response['subscription_count']['inactive']);
        $model->setAppId($response['app_id']);
        return $model;
    }

    /**
     * Creates and fills a subscription model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Subscription
     */
    private function _createSubscription($response)
    {
        $model = new Models\Subscription();
        $model->setId($response['id']);
        $model->setOffer($this->_convertResponseToModel($response['offer'], 'offer'));
        $model->setLivemode($response['livemode']);
        $model->setTrialStart($response['trial_start']);
        $model->setTrialEnd($response['trial_end']);
        $model->setNextCaptureAt($response['next_capture_at']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setCanceledAt($response['canceled_at']);
        $model->setPayment($this->_convertResponseToModel($response['payment'], "payment"));
        $model->setClient($this->_convertResponseToModel($response['client'], "client"));
        $model->setAppId($response['app_id']);
        $model->setIsCanceled($response['is_canceled']);
        $model->setIsDeleted($response['is_deleted']);
        $model->setStatus($response['status']);
        $model->setAmount($response['amount']);
        $model->setTempAmount($response['temp_amount']);
        return $model;
    }

    /**
     * Creates and fills a webhook model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Webhook
     */
    private function _createWebhook($response)
    {
        $model = new Models\Webhook();
        $model->setId($response['id']);
        isset($response['url']) ? $model->setUrl($response['url']) : $model->setEmail($response['email']);
        $model->setLivemode($response['livemode']);
        $model->setEventTypes($response['event_types']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        $model->setAppId($response['app_id']);
        $model->setActive($response['active']);
        return $model;
    }

    /**
     * Creates and fills a fraud model
     *
     * @param array $response
     * @return \Paymill\Models\Response\Fraud
     */
    private function _createFraud($response)
    {
        $model = new Models\Fraud();
        $model->setId($response['id']);
        $model->setLivemode($response['livemode']);
        $model->setStatus($response['status']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);
        return $model;
    }

    /**
     * Creates and fills a checksum model
     *
     * @param array $response
     * @return Checksum
     */
    private function _createChecksum($response)
    {
        $model = new Checksum();
        $model->setId($response['id']);
        $model->setChecksum($response['checksum']);
        $model->setData($response['data']);
        $model->setType($response['type']);
        $model->setAction($response['action']);
        $model->setAppId($response['app_id']);
        $model->setCreatedAt($response['created_at']);
        $model->setUpdatedAt($response['updated_at']);

        return $model;
    }

    /**
     * Handles the multidimensional param arrays during model creation
     * @param array $response
     * @param string $resourceName
     * @return array|null|Models\Base
     */
    private function _handleRecursive($response, $resourceName)
    {
        $result = null;
        if (isset($response['id'])) {
            $result = $this->_convertResponseToModel($response, $resourceName);
        } else if (!is_null($response)) {
            $paymentArray = array();
            foreach ($response as $paymentData) {
                array_push($paymentArray, $this->_convertResponseToModel($paymentData, $resourceName));
            }
            $result = $paymentArray;
        }
        return $result;
    }

    /**
     * Generates an error model based on the provided response array
     * @param array $response
     * @param string $resourceName
     * @return Error
     */
    public function convertErrorToModel($response, $resourceName = null)
    {
        $errorModel = new Error();

        $httpStatusCode = isset($response['header']['status']) ? $response['header']['status'] : null;
        $errorModel->setHttpStatusCode($httpStatusCode);

        $responseCode = isset($response['body']['data']['response_code']) ? $response['body']['data']['response_code'] : null;
        $errorModel->setResponseCode($responseCode);

        $errorCode = 'Undefined Error. This should not happen!';
        if (isset($this->_errorCodes[$responseCode])) {
            $errorCode = $this->_errorCodes[$responseCode];
        }

        if (isset($resourceName) && isset($response['body']['data'])) {
            try {
                $errorModel->setRawObject($this->convertResponse($response['body']['data'], $resourceName));
            } catch (\Exception $e) { }
        }

        if (isset($response['body'])) {
            if (is_array($response['body'])) {
                if (isset($response['body']['error'])) {
                    if (is_array($response['body']['error'])) {
                        $errorCode = $this->getErrorMessageFromArray($response['body']['error']);
                    } elseif (is_string($response['body']['error'])) {
                        $errorCode = $response['body']['error'];
                    }
                }
            } elseif (is_string($response['body'])) {
                $json = json_decode($response['body']);
                $errorCode = $json->error;
            }
        }
        $errorModel->setErrorMessage($errorCode);
        return $errorModel;
    }

    /**
     * Validates the data responded by the API
     * Just checks the header status is successful.
     *
     * @param array $response
     *
     * @return boolean True if valid
     */
    public function validateResponse($response)
    {
        $returnValue = false;
        if (isset($response['header'])
            && isset($response['header']['status'])
            && $response['header']['status'] >= 200
            && $response['header']['status'] < 300
        ) {
            $returnValue = true;
        }

        return $returnValue;
    }

    private function getErrorMessageFromArray($errorArray)
    {
        $errorMessage = array_shift($errorArray);
        if (is_array($errorMessage)) {
            return $this->getErrorMessageFromArray($errorMessage);
        } else {
            return $errorMessage;
        }
    }

    /**
     * Converts an array into an object
     *
     * @param array $array
     * @return stdClass
     */
    public function arrayToObject($array)
    {
        return is_array($array) ? (object) array_map(array($this, 'arrayToObject'), $array) : $array;
    }

}
