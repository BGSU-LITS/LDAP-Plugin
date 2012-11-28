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
	protected $_hooks = array('install', 'config', 'config_form', 'uninstall');

	/**
	 * @var array  The filters used in this plugin.
	 */
	protected $_filters = array('login_adapter','admin_whitelist');

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
		'ldap_bindRequiresDn' => 1
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
		 * THe auth adapter should be autoloaded, but I'm not that familiar with Zend...
		 */
		require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'LdapAuthAdapter.php';
		$ldap_adapter = new LdapAuthAdapter(array('ldap' => $ldap), $user, $password);
		return $ldap_adapter;
	}

	/**
	 * Adds some pages to the administration whitelist
	 *
	 * @param  array $list  The current whitelist
	 * @return array        An updated whitelist
	 */
	public function filterAdminWhitelist($list)
	{
		$list[] = array(
			'controller' => 'ldap',
			'action' => 'forgot-password'
		);

		return $list;
	}

}
