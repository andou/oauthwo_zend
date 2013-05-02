<?php

/**
 * 
 * AuthorizeController.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Implements the Authorization endpoint of the OAuth 2.0 framework
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_AuthorizeController extends Zend_Controller_Action {

    /**
     *
     * @var Oauth_Factory_TokenAbstractFactory
     */
    protected $_token_factory;

    /**
     *
     * @var Oauth_Factory_AuthorizationCodeAbstractFactory
     */
    protected $_code_factory;

    /**
     * The request validator
     *
     * @var Oauth_Request_Validator
     */
    protected $_request_validator;

    public function init() {

        $this->_request_validator = new Oauth_Request_Validator();

        //initialize the default visualization
        $this->_helper->viewRenderer('index');
        $this->_helper->_layout->setLayout('layout');

        //inject the factory dependencies
        $factoryLocator = $this->_helper->FactoryLocator;
        $this->_code_factory = $factoryLocator->getAuthorizationCodeFactory();
        $this->_token_factory = $factoryLocator->getTokenFactory();
    }

    /**
     * The index action of this controller.
     * Validates the incoming request and, if valid, prompt the user with
     * an authorize form.
     * 
     */
    public function indexAction() {
        if (!$this->validateRequest()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
            return;
        }
        $this->view->form = $this->getForm();
    }

    /**
     * Receives data from the authorization form.
     * Validates the incoming form request and, if valid, checks the resource
     * owner choose. Then route the request to two helper functions, namely:
     * 
     * - processApprove()
     * - processDeny()
     * 
     * in order to handle the positive and negative response respectively.
     *
     *
     */
    public function processAction() {

        if (!$this->validateRequest()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout()->disableLayout();
            return;
        }

        $request = $this->getRequest();

        // Check if we have a POST request, otherwise, redirect
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }
        //Retrieving the form
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {// Invalid entries
            $this->view->message = 'process-authorize';
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        if ($form->getValue('yes')) {//Resource Owner says yes
            $this->processApprove($form->getValues());
        } else if ($form->getValue("no")) {//Resource Owner says no
            $this->processDeny($form->getValues());
        } else {//unreckognized value
            $this->view->message = 'process-authorize';
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }
    }

    /**
     * If the user chose to authorize the application, we build an authorization code
     * and then we send it in the redirect uri
     * 
     * @param array $data 
     */
    protected function processApprove($data) {

        //retrieving data to build the authorization code / token
        $requesting_client = $this->_helper->ModelLoader->loadClient($data[CLIENT_ID]);
        $scopes = $data[SCOPE];
        $resource_owner = $this->_helper->ModelLoader->loadResourceOwnerFromSession();

        $state = isset($data[STATE]) ? $data[STATE] : NULL;
        $urlHelper = $this->_helper->RedirectUriFormatter;

        if ($data[RESPONSE_TYPE] === RESPONSE_TYPE_CODE) {
            $code = $this->_code_factory->create($requesting_client, $scopes, $resource_owner);
            $url = $urlHelper->authorizationCodeRedirect($data[REDIRECT_URI], $code, $state);
        } else {
            $token = $this->_token_factory->create($resource_owner, $scopes);
            $url = $urlHelper->tokenRedirect($data[REDIRECT_URI], $token, $state);
        }


        $this->_helper->redirector->gotoUrl($url);
    }

    /**
     * Actions to perform when a user deny grant access
     *
     * @param array $data 
     */
    protected function processDeny($data) {
        $state = isset($data[STATE]) ? $data[STATE] : NULL;
        $urlHelper = $this->_helper->RedirectUriFormatter;

        $url = $urlHelper->errorRedirect($data[REDIRECT_URI], $state);

        $this->_helper->redirector->gotoUrl($url);
    }

    /**
     * Builds the Authorization Form
     *
     * @return Oauth_Form_ApproveForm 
     */
    protected function getForm() {
    	//avoid hard code the links... if the application moves to 
    	//another path.. then the link will be invalid
	$action = $this->view->url(array('module' => 'oauth',
					'controller' => 'authorize',
					'action'     => 'process'), 'Oauth_module_route');
        //create a new OAuth Approve Form
        $form = new Oauth_Form_ApproveForm(array(
                    'action' => $action/*'/v2/oauth/authorize/process'*/,
                    'method' => 'post',
                ));

        //retrieve our bloody scopes
        $scope = explode(" ", $this->getRequest()->getParam(SCOPE));
        $scopes = array();
        foreach ($scope as $s) {
            $scopes[] = $this->_helper->ModelLoader->LoadScope($s);
        }

        //retrieve the client
        $client_id = $this->getRequest()->getParam(CLIENT_ID);
        $client = $this->_helper->ModelLoader->LoadClient($client_id);

        return $form->buildDisclaimer($scopes, $client)
                        ->injectRequestValues($this->getRequest()->getParams());
    }

    /**
     * This method calls the Validator to check if the request is valid or not
     * 
     * @return boolean TRUE if the request is valid, FALSE otherwise
     */
    protected function validateRequest() {

        $request = new Oauth_Model_Request($this->getRequest());

        if (!$this->_request_validator->isValid($request)) {
            $response = Array();

            $messages = $this->_request_validator->getMessages();
            $last_msg = explode(":", array_pop($messages));
            $response['error'] = $last_msg[0];
            $response['error_description'] = isset($last_msg[1]) ? $last_msg[1] : "";

            $this->getResponse()->setHttpResponseCode(401);
            $this->getResponse()->setBody(json_encode($response));
            $this->getResponse()->setHeader('Content-Type', 'application/json;charset=UTF-8');

            return FALSE;
        }

        return TRUE;
    }
    
    /**
     * Ensures the user is logged in using Zend_Auth, if not, prompt the login
     *  
     */
    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            $session = new Zend_Session_Namespace('lastRequest');
            $session->lastRequestUri = $requestUri;
            $this->_helper->redirector('index', 'index', 'login');
        }
    }

}
