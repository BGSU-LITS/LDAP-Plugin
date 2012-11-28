<?php 
/**
 * Ldap_LdapController class
 * 
 * @version $Id$
 * @copyright 
 * @license 
 * @package 
 * @author 
 **/
 
/** Zend_Application */
require_once 'Zend/Application.php';  
require_once CONTROLLER_DIR.'/UsersController.php';

class Ldap_LdapController extends UsersController {

	
   public function addAction() {
        $user = new User();
        try {
            if ($user->saveForm($_POST)) {                
                $this->flashSuccess('The user "' . $user->username . '" was successfully added!');
                $this->_helper->redirector->gotoUrl('/users/browse');
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
    }
    

    /**
     * Similar to 'add' action, except this requires a pre-existing record.
     * 
     * The ID For this record must be passed via the 'id' parameter.
     *
     * @return void
     **/
    public function editAction() {        
        $user = $this->findById();        
        $changePasswordForm = new Omeka_Form_ChangePassword;
        $changePasswordForm->setUser($user);

        $currentUser = $this->getCurrentUser();

        // Super users don't need to know the current password.
        if ($currentUser && $currentUser->role == 'super') {
            $changePasswordForm->removeElement('current_password');
        }
        
        //$this->view->passwordForm = $changePasswordForm;
        $this->view->user = $user;        
        
        
        try {
            if ($user->saveForm($_POST)) {
                $this->flashSuccess('The user "' . $user->username . '" was successfully changed!');
                
                if ($user->id == $currentUser->id) {
                    $this->_helper->redirector->gotoUrl('/');
                } else {
                     $this->_helper->redirector->gotoUrl('/users/browse');
                }
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flashError($e->getMessage());
        }        
    }
}