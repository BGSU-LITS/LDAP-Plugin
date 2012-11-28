<?php

/**
 * The Authorization Adapter for the LDAP plugin.
 *
 * @package LDAP
 * @author  Dave Widmer <dwidmer@bgsu.edu>
 */
class LdapAuthAdapter extends Zend_Auth_Adapter_Ldap
{
	/**
	 * Attempts to authenticate the user.
	 *
	 * @return Zend_Auth_Result   The authorization result
	 */
	public function authenticate()
	{
		//var_dump($this->getUsername()); die;
		$result = parent::authenticate();

		/**
		 * If the login was correct we need to grab the actual user from Omeka.
		 * This has to be done by grabbing the user id instead of whatever username was used.
		 */
		if ($result->isValid())
		{
			$user = get_db()->getTable('User')->findBySql("username = ?", array($this->getUsername()), true);

			if ($user === null)
			{
				$result = $this->hookUserNotFound();
			}
			else
			{
				$result = new Zend_Auth_Result($result->getCode(), $user->id, $result->getMessages());
			}
		}

		return $result;
	}

	/**
	 * A hook that handles when a user is not found in the omeka database.
	 *
	 * This function just gives back an invalid login message. You have to match
	 * the user accounts from you LDAP manually.
	 * 
	 * Override this function if you want to create a new Omeka user if they are
	 * authenticated with the LDAP.
	 *
	 * @return Zend_Auth_Result
	 */
	protected function hookUserNotFound()
	{
		$messages = array();
		$message[] = 'Login information incorrect. Please try again.';
		return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->getUsername(), $message);
	}

}
