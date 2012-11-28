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
	protected $_filters = array(
		//'login_adapter',
		//'admin_whitelist'
	);

	/**
	 * @var array  Options that are used in the ldap plugin.
	 */
	protected $_options = array(
		'ldap_server' => 'localhost',
		'ldap_port' => 389,
		'ldap_basedn' => "",
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
	 * Helps filter out the login.
	 */
	public function filterLoginAdapter()
	{

	}

	/**
	 * Administration Whitelist.
	 */
	public function filterAdminWhitelist()
	{

	}

}
