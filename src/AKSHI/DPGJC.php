<?php

namespace AKSHI;

use GovTalk\GovTalk;

class DPGJC extends GovTalk {

    private $gatewayURL;

    private $gatewayUserId;

    private $gatewayUserPassword;

    private $service = "http://eDPGJC.org/";

    private $function = "GetPersonByNID";

    public function __construct($userID, $password, $url) {
        $this->gatewayUserId = $userID;
        $this->gatewayUserPassword = $password;
        $this->gatewayURL = $url;

    }

    public function setUpService() {
	    parent::__construct(
            $this->gatewayURL,
            $this->gatewayUserId,
            $this->gatewayUserPassword,
            null,
            '/home/gjergj/projects/gov-talk/test/'
        );
    }

    public function constructAndSendMessage() {
        $this->gtService = $this->setUpService();

        $this->setMessageAuthentication('clear');
        $this->addMessageKey('SpokeName', 'SVC_6_IND');
        $this->setMessageClass('TRA_GETPERSONALDATA');
        $this->setMessageQualifier('request');
        $this->setMessageFunction('submit');

        $package = new \XMLWriter();
        $package->openMemory();
        $package->setIndent(true);

        $package->startElement('soap12:Envelope');
        $package->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
	    $package->writeAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
	    $package->writeAttribute('xmlns:soap12', 'http://www.w3.org/2003/05/soap-envelope');
        $package->startElement('soap12:Body');
	    $package->startElement($this->function);
	    $package->writeAttribute('xmlns',$this->service);
	    $package->writeElement('V_NID', 'I10803137Q');
	    $package->endElement();
	    $package->endElement();
	    $package->endElement();
	    $this->setMessageBody( $package->outputMemory() );

	    $this->setSchemaValidation(false);

	    if ($this->sendMessage() && ($this->responseHasErrors() === false)) {
		    $returnable = $this->getResponseEndpoint();
		    $returnable['transactionId'] = $this->getTransactionId();
		    $returnable['correlationId'] = $this->getResponseCorrelationId();
		    $this->fullResponseObject->registerXPathNamespace('person',$this->service);
		    $person = $this->fullResponseObject->xpath('//person:Table');
		    foreach ($person as $node) {
			    $array = array();
			    foreach ($node->children() as $child) {
				    $array[$child->getName()] = (string) $child;
			    }
			    $returnable['statusRecords'] = $array;
		    }
	    } else {
		    $returnable = array('errors' => $this->getResponseErrors());
	    }

	    return $returnable;
    }

}
