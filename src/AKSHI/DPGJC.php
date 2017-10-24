<?php
/**
 * GovTalk API Client -- Builds, validates, sends, receives and validates
 * GovTalk messages for use with the UK government's GovTalk messaging system
 * (http://www.govtalk.gov.uk/). A generic wrapper designed to be extended for
 * use with the more specific interfaces provided by various government
 * departments. Generates valid GovTalk envelopes for agreed version 2.0.
 *
 * @author    Gjergj Sheldija
 * @copyright 2017 - , Gjergj Sheldija
 * Refactored for php &.x for inclusion in gjergjsheldija/albania-govtalk package.
 */

namespace GovTalk\AKSHI;

use GovTalk;

class DPGJC extends GovTalk\GovTalk
{
	/**
	 * GovTalk service.
	 *
	 * @var string
	 */
	const SERVICE = "http://eDPGJC.org/";

	/**
	 * GovTalk function.
	 *
	 * @var string
	 */
	const FUNCTION = "GetPersonByNID";

	/* Public methods. */


	/**
	 * Instance constructor.
	 *
	 * @param string $govTalkSenderId    GovTalk sender ID.
	 * @param string $govTalkPassword    GovTalk password.
	 * @param string $govTalkServer      GovTalk server URL.
	 * @param string $messageLogLocation Message log location (default null = no logging)
	 */
	public function __construct($userID, $password, $url, $messageLogLocation)
	{
		parent::__construct(
			$url,
			$userID,
			$password,
			null,
			$messageLogLocation
		);
	}



	/**
	 * Constructes the message based on AKSHI requirements for the eDPGJC
	 * specification.
	 * Constructed the SOAP body manualy to avoid consuming resources by
	 * calling \SOAPClient
	 *
	 * @param $NID string the The persons NID for which the query is being made
	 */
	public function constructMessage($NID)
	{
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
		$package->startElement(self::FUNCTION);
		$package->writeAttribute('xmlns', self::SERVICE);
		$package->writeElement('V_NID', $NID);
		$package->endElement();
		$package->endElement();
		$package->endElement();
		$this->setMessageBody($package->outputMemory());
	}

	/**
	 * @param bool $schemaValidation Enable or disable the schema validation. Default diabled
	 * because the http://www.govtalk.gov.uk is not online anymore
	 * @return array Return array with given data or error message
	 */
	public function sendQuery($schemaValidation = false)
	{

		if ($schemaValidation == false) {
			$this->setSchemaValidation(false);
		}

		if ($this->sendMessage() && ($this->responseHasErrors() === false)) {
			$returnable = $this->getResponseEndpoint();
			$returnable['transactionId'] = $this->getTransactionId();
			$returnable['correlationId'] = $this->getResponseCorrelationId();
			$this->fullResponseObject->registerXPathNamespace('person', self::SERVICE);
			$person = $this->fullResponseObject->xpath('//person:Table');
			foreach ($person as $node) {
				$array = array();
				foreach ($node->children() as $child) {
					$array[$child->getName()] = (string)$child;
				}
				$returnable['statusRecords'] = $array;
			}
		} else {
			$returnable = array('errors' => $this->getResponseErrors());
		}

		return $returnable;
	}
}
