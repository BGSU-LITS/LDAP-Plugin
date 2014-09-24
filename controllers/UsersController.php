<?php 

/** Not autoloading is lame... */
require_once CONTROLLER_DIR.'/UsersController.php';

/**
 * A class to override the default user actions with the LDAP system.
 *
 * @package   LDAP
 * @author    Dave Widmer <dwidmer@bgsu.edu>
 */

class Ldap_UsersController extends UsersController
{
	/**
	 * Since we are using a LDAP system the password can't be changed through Omeka
	 */
	public function forgotPasswordAction()
	{
		return;
	}

	/**
	 * Adds an LDAP user.
	 */
	public function addAction()
	{
		$user = new User();

		$form = $this->_getUserForm($user);
		$this->view->form = $form;
		$this->view->user = $user;

		if ($this->getRequest()->isPost() && $form->isValid($_POST))
		{
			$data = $_POST;
			$data['active'] = 1; // Let's automatically activate the users...
			$user->setPostData($data);

			if ($user->save())
			{
				$message = 'The user "%s" was successfully added!';
				$this->_helper->flashMessenger(__($message, $user->username), 'success');
				$this->_helper->redirector('browse');
			}
			else
			{
				$this->_helper->flashMessenger($user->getErrors());
			}
		}
	}

	/**
     * Edits a user.
     * 
     * The ID For this record must be passed via the 'id' parameter.
     *
     * @return void
     */
    public function editAction()
    {
		$user = $this->_helper->db->findById();
		$this->view->user = $user;

		$form = $this->_getUserForm($user);

		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$user->setPostData($form->getValues());
				if ($user->save(false))
				{
					$message = 'The user %s was successfully changed!';
					$this->_helper->flashMessenger(__($message, $user->username), 'success');

					if ($user->id === $this->getCurrentUser()->id)
					{
						$this->_helper->redirector->gotoUrl('/');
					}
					else
					{
						$this->_helper->redirector('browse');
					}
				}
				else
				{
					$this->_helper->flashMessenger($user->getErrors());
				}
			}
			else
			{
				$this->_helper->flashMessenger(__('There was an invalid entry on the form. Please try again.'), 'error');
			}
		}
		else
		{
			$form->setDefaults(array(
				'username' => $user->username,
				'name' => $user->name,
				'email' => $user->email,
				'role' => $user->role,
				'active' => $user->active
			));
		}

		$this->view->form = $form;
    }

}
