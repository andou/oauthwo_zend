<?php

class Oauth_Form_ApproveForm extends Zend_Form {
    
        
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
    
    public function injectRequestValues(array $request_values){              
        unset($request_values['module']);
        unset($request_values['action']);
        unset($request_values['controller']);
        
        foreach($request_values as $k=>$v){
            $this->addElement('hidden', $k, array('value' => $v));
        }        
    }

}