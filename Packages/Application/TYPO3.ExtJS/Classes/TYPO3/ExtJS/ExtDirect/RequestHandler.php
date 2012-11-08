<?php
namespace TYPO3\ExtJS\ExtDirect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.ExtJS".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Configuration\ConfigurationManager;

/**
 * The ExtDirect request handler
 *
 * @Flow\Scope("singleton")
 */
class RequestHandler extends \TYPO3\Flow\Http\RequestHandler {

	/**
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @var \TYPO3\Flow\Mvc\Routing\RouterInterface
	 */
	protected $router;

	/**
	 * @var \TYPO3\Flow\Mvc\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Whether to expose exception information in an ExtDirect response
	 * @var boolean
	 */
	protected $exposeExceptionInformation = FALSE;

	/**
	 * Checks if the request handler can handle the current request.
	 *
	 * @return boolean TRUE if it can handle the request, otherwise FALSE
	 */
	public function canHandleRequest() {
		return isset($_GET['TYPO3_ExtJS_ExtDirectRequest']);
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request.
	 *
	 * @return integer The priority of the request handler
	 */
	public function getPriority() {
		return 200;
	}

	/**
	 * Handles a raw ExtDirect request and sends the response.
	 *
	 * @return void
	 */
	public function handleRequest() {
			// Create the request very early so the Resource Management has a chance to grab it:
		$this->request = \TYPO3\Flow\Http\Request::createFromEnvironment();

		$this->boot();
		$this->resolveDependencies();

		$this->router->setRoutesConfiguration($this->routesConfiguration);
		$extDirectRequest = $this->buildJsonRequest($this->request);

		$results = array();
		foreach ($extDirectRequest->getTransactions() as $transaction) {
			$requestOfCurrentTransaction = $transaction->buildRequest($extDirectRequest);
			$this->requestOfCurrentTransaction = $requestOfCurrentTransaction;
			$responseOfCurrentTransaction = $transaction->buildResponse();

			try {
				$this->securityContext->setRequest($requestOfCurrentTransaction);
				$this->securityContext->initialize();

				$this->dispatcher->dispatch($requestOfCurrentTransaction, $responseOfCurrentTransaction);
				$results[] = array(
					'type' => 'rpc',
					'tid' => $transaction->getTid(),
					'action' => $transaction->getAction(),
					'method' => $transaction->getMethod(),
					'result' => $responseOfCurrentTransaction->getResult()
				);
			} catch (\Exception $exception) {
				$this->systemLogger->logException($exception);

					// As an exception happened, we now need to check whether detailed exception reporting was enabled.
				$exposeExceptionInformation = ($this->settings['ExtDirect']['exposeExceptionInformation'] === TRUE);

				$exceptionWhere = ($exception instanceof \TYPO3\Flow\Exception) ? ' (ref ' . $exception->getReferenceCode() . ')' : '';
				$exceptionMessage = $exposeExceptionInformation ? 'Uncaught exception #' . $exception->getCode() . $exceptionWhere . ' - ' . $exception->getMessage() : 'An internal error occured';
				$results[] = array(
					'type' => 'exception',
					'tid' => $transaction->getTid(),
					'message' => $exceptionMessage,
					'where' => $exceptionWhere
				);
			}
		}

		$this->sendResponse($results, $extDirectRequest);
		$this->bootstrap->shutdown('Runtime');
		$this->exit->__invoke();
	}

	/**
	 * Builds a Json ExtDirect request by reading the transaction data from
	 * the HTTP request body.
	 *
	 * @param \TYPO3\Flow\Http\Request $httpRequest The HTTP request
	 * @return \TYPO3\ExtJS\ExtDirect\Request The Ext Direct request object
	 * @throws \TYPO3\ExtJS\ExtDirect\Exception\InvalidExtDirectRequestException
	 */
	protected function buildJsonRequest(\TYPO3\Flow\Http\Request $httpRequest) {
		$transactionDatas = json_decode($httpRequest->getContent());
		if ($transactionDatas === NULL) {
			throw new \TYPO3\ExtJS\ExtDirect\Exception\InvalidExtDirectRequestException('The request is not a valid Ext Direct request', 1268490738);
		}

		if (!is_array($transactionDatas)) {
			$transactionDatas = array($transactionDatas);
		}

		$extDirectRequest = new Request($httpRequest);
		foreach ($transactionDatas as $transactionData) {
			$extDirectRequest->createAndAddTransaction(
				$transactionData->action,
				$transactionData->method,
				is_array($transactionData->data) ? $transactionData->data : array(),
				$transactionData->tid
			);
		}
		return $extDirectRequest;
	}

	/**
	 * Resolves a few dependencies of this request handler which can't be resolved
	 * automatically due to the early stage of the boot process this request handler
	 * is invoked at.
	 *
	 * @return void
	 */
	protected function resolveDependencies() {
		parent::resolveDependencies();

		$objectManager = $this->bootstrap->getObjectManager();
		$this->systemLogger = $objectManager->get('TYPO3\Flow\Log\SystemLoggerInterface');

		$configurationManager = $objectManager->get('TYPO3\Flow\Configuration\ConfigurationManager');
		$this->settings = $configurationManager->getConfiguration(\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.ExtJS');
	}

	/**
	 * Sends the response
	 *
	 * @param array $results The collected results from the transaction requests
	 * @param \TYPO3\ExtJS\ExtDirect\Request $extDirectRequest
	 * @return void
	 */
	protected function sendResponse(array $results, \TYPO3\ExtJS\ExtDirect\Request $extDirectRequest) {
		$response = json_encode(count($results) === 1 ? $results[0] : $results);
		if ($extDirectRequest->isFormPost() && $extDirectRequest->isFileUpload()) {
			header('Content-Type: text/html');
			echo '<html><body><textarea>' . $response . '</textarea></body></html>';
		} else {
			header('Content-Type: text/javascript');
			echo $response;
		}
	}
}
?>