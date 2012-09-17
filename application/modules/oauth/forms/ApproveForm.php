<?php
/**
 * 
 * ApproveForm.php, 
 * 
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 * @version 0.1
 * 
 */

/**
 *  Extends a Zend_Form and realize the Approve/Deny OAuth form 
 *
 * @author Antonio Pastorino <antonio.pastorino@gmail.com>
 */
class Oauth_Form_ApproveForm extends Zend_Form {
    
    /**
     * Initialize the Approvation form
     * Uses a salt to verify session validity
     * 
     */    
    public function init() {
        
        $approve = $this->addElement('submit', 'yes', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Approve',
                ));

        $deny = $this->addElement('submit', 'no', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Deny',
                ));
        
        $token =  $this->addElement('hash', 'hihacker', array('salt' => 'thesal'));
        
        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend','escape'=>false)),
            'Form'
        ));
    }
    
    /**
     * Builds the disclaimer to tell resource owner what he is granting
     *
     * @param array $scopes
     * @param Oauth_Model_Client $client 
     */
    public function buildDisclaimer($scopes, Oauth_Model_Client $client){
        
        $disclaimer = "The application named:<strong> %s</strong><br/> ".
                "Requests to:<br/>".
                " <ul>%s</ul>";
        
        $scope_desc = array();
        
        foreach ($scopes as $scope) {
            $scope_desc[] = sprintf("<li>%s</li>", $scope->getDescription());
        }
        
        $scope_desc = implode("", $scope_desc);
        $description = sprintf($disclaimer, $client->getName(), $scope_desc);
        
        $this->setDescription($description);
        
        return $this;
        
    }
    
    /**
     * Inject some values from the HTTP request into the form
     * 
     * @param array $request_values 
     */
    public function injectRequestValues(array $request_values){              
        unset($request_values['module']);
        unset($request_values['action']);
        unset($request_values['controller']);
        
        foreach($request_values as $k=>$v){
            $this->addElement('hidden', $k, array('value' => $v));
        }        
        
        return $this;
    }

}