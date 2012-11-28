<?php

   //Define hooks
   add_plugin_hook('install', 'ldap_install');
   add_plugin_hook('initialize', 'ldap_initialize');
   add_plugin_hook('config', 'ldap_config');
   add_plugin_hook('config_form', 'ldap_config_form');

   ///Define filters
   add_filter('login_adapter', 'login');
   add_filter('admin_whitelist','addToWhitelist');


   //Pull values from form & create LDAP Auth Adapter Object:
   function login($authAdapter,$loginForm) {

		//retrieve username and password 
		$username = $loginForm->getValue('username');
		$pwd = $loginForm->getValue('password');
		
		//get plugin settings:
		$server1 = array();
		$server1["host"] = get_option('ldap_server');
		$server1["baseDn"] =  get_option('ldap_basedn');
		$server1["port"] = get_option('ldap_port');
		$server1["accountCanonicalForm"] = get_option('ldap_accountCanonicalForm') + 0;
		$server1["accountFilterFormat"] = get_option('ldap_accountFilterFormat');
		$server1["accountDomainName"] = get_option('ldap_accountDomainName');
		$server1["accountDomainNameShort"] = get_option('ldap_accountDomainNameShort');
		$server1["bindRequiresDn"] = get_option('ldap_bindRequiresDn');
		$options = array($server1);
		
		$authAdapter = new Omeka_Auth_Adapter_Ldap($options,$username,$pwd);
		$authAdapter->setIdentity($username)->setCredential($pwd);
		return $authAdapter;
		
   }

	/**
	 * Checks to see if the currently logged in user is a super-user.
	 *
	 * @return boolean  Do we have a super admin?
	 */
	function isSuper()
	{
		$user = current_user();
		return $user->role === 'super';
	}



	function addToWhitelist($adminWhiteList){
		
	   array_push($adminWhiteList,array('controller' => 'ldap', 'action' => 'forgot-password'));
	   return $adminWhiteList;
	   
	}


	function ldap_initialize(){
	
	   $front = Zend_Controller_Front::getInstance();
	   Zend_Controller_Front::getInstance()->registerPlugin(new LdapControllerPlugin);
	
	}

	function ldap_config() {
		
		set_option('ldap_server', trim($_POST['ldap_server']));
		set_option('ldap_port', trim($_POST['ldap_port']));
		set_option('ldap_basedn', trim($_POST['ldap_basedn']));
		//new in v .2
		set_option('ldap_accountCanonicalForm', trim($_POST['ldap_accountCanonicalForm']));
		set_option('ldap_accountFilterFormat',trim($_POST['ldap_accountFilterFormat']));
		set_option('ldap_accountDomainName',trim($_POST['ldap_accountDomainName']));
		set_option('ldap_accountDomainNameShort',trim($_POST['ldap_accountDomainNameShort']));
		set_option('ldap_bindRequiresDn',trim($_POST['ldap_bindRequiresDn']));
		
	}
	
	function ldap_config_form() {
		
		echo '<div id = "ldap_server">';
		echo '<label for = "top_domain">Host (LDAP Server):</label>';
		echo text (array('name'=>'ldap_server'), get_option('ldap_server'), null);
		echo '<br />';
		echo '<div id = "ldap_port">';
		echo '<label for = "ldap_port">Port: </label>';
		echo text (array('name'=>'ldap_port'), get_option('ldap_port'), null);
		echo '<br />';
		echo '<div id = "ldap_basedn">';
		echo '<label for = "ldap_basedn">Base DN: </label>';
		echo text (array('name'=>'ldap_basedn'), get_option('ldap_basedn'), null);
		echo '<br />';
		//new in v .2
		echo '<div id = "ldap_accountCanonicalForm">';
		echo '<label for = "ldap_accountCanonicalForm">Account Canonical Form: </label>';
		echo text (array('name'=>'ldap_accountCanonicalForm'), get_option('ldap_accountCanonicalForm'), null);
		echo '<br />';
		echo '<div id = "ldap_accountFilterFormat">';
		echo '<label for = "ldap_accountFilterFormat">Account Filter Format: </label>';
		echo text (array('name'=>'ldap_accountFilterFormat'), get_option('ldap_accountFilterFormat'), null);
		echo '<br />';
		echo '<div id = "ldap_accountDomainName">';
		echo '<label for = "ldap_accountDomainName">Account Domain Name: </label>';
		echo text (array('name'=>'ldap_accountDomainName'), get_option('ldap_accountDomainName'), null);
		echo '<br />';
		echo '<div id = "ldap_accountDomainNameShort">';
		echo '<label for = "ldap_accountDomainNameShort">Account Domain Name Short: </label>';
		echo text (array('name'=>'ldap_accountDomainNameShort'), get_option('ldap_accountDomainNameShort'), null);
		echo '<br />';
		echo '<div id = "ldap_bindRequiresDn">';
		echo '<label for = "ldap_bindRequiresDn">Bind Requires DN (true or false): </label>';
		echo text (array('name'=>'ldap_bindRequiresDn'), get_option('ldap_bindRequiresDn'), null);
		
}

 class Omeka_Auth_Adapter_Ldap extends Zend_Auth_Adapter_Ldap {
	
	private $omeka_userid;
	
	public function __construct($options,$username,$password) {
		parent::__construct($options,$username,$password);
		
		//The Zend_Auth_Result (returned from Zend_Auth_Adapter_Ldap)-- 'identity' attribute
		//does not hold the username needed for omeka user 'id' lookup so $omeka_userid will hold it
		$this->omeka_userid = $username;
	}
	
	public function authenticate() {
		$authResult = parent::authenticate();
		if (!$authResult->isValid()) {
			return $authResult;
		}
		// Omeka needs the user ID (not username)
		$omeka_user = get_db()->getTable('User')->findBySql("username = ?", array($this->omeka_userid), true);
		if ($omeka_user) {
			$id = $omeka_user->id;
			$correctResult = new Zend_Auth_Result($authResult->getCode(), $id , $authResult->getMessages());
			return $correctResult;	
		}
		//if we can't find the user name in Omeka - return an error:
		//(The Omeka Admin should set up the LDAP username to match the Omeka Username)
		//Another alternative here 'could be' -- if needed -- creating a new Omeka User 
		else {
			$messages = array();
			$messages[] = 'Login information incorrect. Please try again.';
			$authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->omeka_userid , $messages);
			return $authResult;
		}
	}
	
 }
	
	

 class LdapControllerPlugin extends Zend_Controller_Plugin_Abstract {
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {
		 $router = Omeka_Context::getInstance()->getFrontController()->getRouter();
		
		 $route = new Zend_Controller_Router_Route(
				'users/forgot-password',
					array(
					  'module'     => 'ldap', 
					  'controller' => 'ldap',
					  'action'     => 'forgot-password'
		 ));
 
		$router->addRoute('forgot', $route);
		
		$route = new Zend_Controller_Router_Route(
				 'users/add',
					array(
						'module'     => 'ldap', 
						'controller' => 'ldap',
						'action'     => 'add'
		 ));
 
		$router->addRoute('addLdapUser', $route);
		
		$route = new Zend_Controller_Router_Route(
				 'users/edit/:id',
					array(
						'module'       => 'ldap', 
						'controller' => 'ldap',
						'action'     => 'edit'
		 ));
 
		$router->addRoute('editLdapUser', $route);
	}
   
 }

