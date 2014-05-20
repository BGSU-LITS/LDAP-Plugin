<?php

/**
 * LDAP Plugin
 *
 * @author    Dave Widmer <dwidmer@bgsu.edu>
 */
class LdapPlugin extends Omeka_Plugin_AbstractPlugin
{
	/**
	 * @var array  All of the hooks used in the LDAP plugin
	 */
	protected $_hooks = array('install', 'config', 'config_form', 'uninstall',
		'define_routes');

	/**
	 * @var array  The filters used in this plugin.
	 */
	protected $_filters = array('login_adapter', 'admin_whitelist');

	/**
	 * @var array  Options that are used in the ldap plugin.
	 */
	protected $_options = array(
		'ldap_host' => 'localhost',
		'ldap_port' => 389,
		'ldap_baseDn' => "",
		'ldap_accountCanonicalForm' => 1,
		'ldap_accountFilterFormat' => "",
		'ldap_accountDomainName' => "",
		'ldap_accountDomainNameShort' => "",
		'ldap_bindRequiresDn' => 1,
		'ldap_username' => '',
		'ldap_password' => ''
	);

	/**
	 * Installation hook.
	 */
	public function hookInstall()
	{
		$this->_installOptions();
	}

	/**
	 * Uninstalls any options that have been set.
	 */
	public function hookUninstall()
	{
		$this->_uninstallOptions();
	}

	/**
	 * Set the options from the Config form.
	 */
	public function hookConfig()
	{
		foreach (array_keys($this->_options) as $key)
		{
			set_option($key, trim($_POST[$key]));
		}
	}

	/**
	 * Displays the configuration form.
	 */
	public function hookConfigForm()
	{
		require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'views'. DIRECTORY_SEPARATOR . 'config_form.php';
	}

	/**
	 * Add some routes to the flow to override the default user actions.
	 *
	 * @param  array $args  The route arguments
	 * @return [type] [description]
	 */
	public function hookDefineRoutes($args)
	{
		$router = $args['router'];

		$routes = array(
			array(
				'name' => 'ldap_forgot',
				'pattern' => 'users/forgot-password',
				'action' => 'forgot-password'
			),
			array(
				'name' => 'ldap_add',
				'pattern' => 'users/add',
				'action' => 'add'
			),
			array(
				'name' => 'ldap_edit',
				'pattern' => 'users/edit/:id',
				'action' => 'edit'
			)
		);

		foreach ($routes as $route)
		{
			$r = new Zend_Controller_Router_Route($route['pattern'], array(
				'module' => 'ldap',
				'controller' => 'users',
				'action' => $route['action']
			));

			$router->addRoute($route['name'], $r);
		}
	}

	/**
	 * Bypasses the normal login functionality and checks the LDAP server
	 * to authenticate the user.
	 *
	 * @param  Omeka_Auth_Adapter_UserTable  $adapter   The auth adapter for users
	 * @param  array                         $form      The login form
	 * @return LdapAuthAdapter
	 */
	public function filterLoginAdapter($adapter, $form)
	{
		$user = $form['login_form']->getValue('username');
		$password = $form['login_form']->getValue('password');

		$ldap = array();
		foreach (array_keys($this->_options) as $key)
		{
			$name = substr($key, 5);
			$ldap[$name] = get_option($key);
		}

		/**
		 * The auth adapter should be autoloaded, but I'm not that familiar with Zend...
		 */
		require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'LdapAuthAdapter.php';
		$ldap_adapter = new LdapAuthAdapter(array('ldap' => $ldap), $user, $password);
		
		return $ldap_adapter;
	}

	/**
	 * Adds the forgot password page to the admin whitelist.
	 *
	 * @param  array $whitelist The current whitelist
	 * @return array
	 */
	public function filterAdminWhitelist($whitelist)
	{
		$whitelist[] = array(
			'module' => 'ldap',
			'controller' => 'users',
			'action' => 'forgot-password'
		);

		return $whitelist;
	}

}
