<?php


/**
 * Description of AuthorizeController
 *
 * @author andou
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
    
    
    public function init() {
        //initialize the default visualization
        $this->_helper->viewRenderer('index');
        
        //inject the factory dependencies
        $factoryLocator = $this->_helper->FactoryLocator;
        $this->_code_factory = $factoryLocator->getAuthorizationCodeFactory();
        $this->_token_factory = $factoryLocator->getTokenFactory();
    }

    public function indexAction() {

        $this->validateRequest();
        
        
        $this->view->message = 'Did you authorize this application?';

        $this->view->form = $this->getForm();
    }

    public function processAction() {

        $request = $this->getRequest();

        $this->validateRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->message = 'process-authorize';
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        if ($form->getValue('yes')) {
            $this->processApprove($form->getValues());
        } else if ($form->getValue("no")) {
            $this->processDeny($form->getValues());
        } else {
            $this->view->message = 'process-authorize';
            $this->view->form = $form;
            return $this->render('index');
        }
    }

    /**
     * If the user chose to authorize the application, we build an authorization code
     * and then we send it in the redirect uri
     * 
     * @param type $data 
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
            $url = $urlHelper->authorizationCodeRedirect($data[REDIRECT_URI],$code,$state);            
        } else {
            $token = $this->_token_factory->create($resource_owner,$scopes);
            $url = $urlHelper->tokenRedirect($data[REDIRECT_URI],$token,$state);
        }
        //echo "goto: $url";//
        $this->_helper->redirector->gotoUrl($url);
    }

    protected function processDeny($data) {
        $state = isset($data[STATE]) ? $data[STATE] : NULL;
        $urlHelper = $this->_helper->RedirectUriFormatter;
        
        $url = $urlHelper->errorRedirect($data[REDIRECT_URI],$state);
        //echo "goto: $url";//
        $this->_helper->redirector->gotoUrl($url);
    }

    protected function getForm() {

        $disclaimer = "<strong>%s</strong> will: <ul>%s</ul>";
        
        $form = new Oauth_Form_ApproveForm(array(
                    'action' => '/v2/oauth/authorize/process',
                    'method' => 'post',
                ));
        
        $scope_desc = array();
        
        $scopes = explode(" ", $this->getRequest()->getParam(SCOPE));
        foreach($scopes as $scope){
            $s = $this->_helper->ModelLoader->LoadScope($scope);
            $scope_desc[]=  sprintf("<li>%s</li>",$s->getDescription());
        }
        
        $scope_desc = implode("",$scope_desc);
        
        $client_id = $this->getRequest()->getParam(CLIENT_ID);
        $client = $this->_helper->ModelLoader->LoadClient($client_id);
        $client_desc = $client->getName();
        
        
        $form->setDescription(sprintf($disclaimer,$client_desc,$scope_desc));

        $form->injectRequestValues($this->getRequest()->getParams());

        return $form;
    }

    /**
     * Index method to validate an obtaining grant request
     * 
     */
    protected function validateRequest() {
        $this->validateRequestRFC();
        $this->validateRequestServerSpecific();
    }

    /**
     * This validates the request against the RFC definition.
     * Does not check in the DB, only see presence/absence of values
     * 
     * @throws Exception 
     */
    protected function validateRequestRFC() {
        if (!$response_type = $this->getRequest()->getParam(RESPONSE_TYPE))
            throw new Exception('invalid_request', 401);

        switch ($response_type) {
            case RESPONSE_TYPE_CODE:
            case RESPONSE_TYPE_TOKEN:
                if (!$this->getRequest()->getParam(CLIENT_ID))
                    throw new Exception('invalid_request:noclientid', 401);
                break;
            default:
                throw new Exception('unsupported_response_type', 401);
        }
    }

    /**
     * This validates a request with server specific informations
     *
     * @throws Exception 
     */
    protected function validateRequestServerSpecific() {
        if (!$response_type = $this->getRequest()->getParam(RESPONSE_TYPE))
            throw new Exception('invalid_request', 401);

        //Database Agnostic controls
        switch ($response_type) {
            case RESPONSE_TYPE_CODE:
            case RESPONSE_TYPE_TOKEN:
                if (!$client_id = $this->getRequest()->getParam(CLIENT_ID))
                    throw new Exception('invalid_request:noclientid', 401);
                if (!$redirect_uri = $this->getRequest()->getParam(REDIRECT_URI))
                    throw new Exception('invalid_request:noredirecturi', 401);
                if (!$scope = $this->getRequest()->getParam(SCOPE))
                    throw new Exception('invalid_request:noscope', 401); //change to redirect!
                break;
            default:
                throw new Exception('unsupported_response_type', 401);
        }

        //Database Dependent Controls
        //checking that scopes exists
        foreach (explode(" ", trim($scope)) as $s) {
            if (!$this->_helper->ModelLoader->loadScope($s))
                throw new Exception('invalid_scope', 401); //change to redirect!
        }

        //checking client
        if (!$requesting_client = $this->_helper->ModelLoader->loadClient($client_id))
            throw new Exception('invalid_request:clientnotexists', 401);

        if (!$requesting_client->isAuthorized($response_type))
            throw new Exception('unauthorized_client', 401);

        if (!$requesting_client->checkRedirectUri($redirect_uri))
            throw new Exception('invalid_request:invalidredirecturi', 401);
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

?>
