<?php

/**
 * Description of ModelLoader
 *
 * @author andou
 */
class Oauth_Controller_Action_Helper_ModelLoader extends Zend_Controller_Action_Helper_Abstract {
    
    public function loadScope($scope_name) {
        $scopeMapper = new Oauth_Mapper_Scope();
        $scope = $scopeMapper->find($scope_name);
        
        return $scope;
    }

    public function loadClient($client_id) {
        $clientMapper = new Oauth_Mapper_Client();
        $client = $clientMapper->find($client_id);
        return $client;
    }
    
    public function loadResourceOwnerFromSession(){
        $resourceOwnerMapper = new Oauth_Mapper_ResourceOwner();
        $resource_owner = $resourceOwnerMapper->fromSession();
        return $resource_owner;
    }
    
    
    
    public function loadResourceOwner($resouce_owner_id){
        $resourceOwnerMapper = new Oauth_Mapper_ResourceOwner();
        $resource_owner = $resourceOwnerMapper->find($resouce_owner_id);
        return $resource_owner;
    }

}