<?php
$fields = array(
	array(
		'label' => 'Host (LDAP Server)',
		'description' => 'The hostname of LDAP server. Required.',
		'name' => 'ldap_host',
		'required' => true
	),
	array(
		'label' => 'Port',
		'description' => 'The port on which the LDAP server is listening.',
		'name' => 'ldap_port',
		'required' => false
	),
	array(
		'label' => 'Base DN',
		'description' => 'The DN under which all accounts being authenticated are located. A more precise location (e.g., OU=Sales,DC=foo,DC=net) will be more efficient.',
		'name' => 'ldap_baseDn',
		'required' => false
	),
	array(
		'label' => 'Account Canonical Form',
		'description' => 'The form to which account names should be canonicalized after successful authentication. 2 for traditional username style names (e.g., alice), 3 for backslash-style names (e.g., example\alice) or 4 for principal style usernames (e.g., alice@example.com).',
		'name' => 'ldap_accountCanonicalForm',
		'required' => false
	),
	array(
		'label' => 'Account Filter Format',
		'description' => 'A printf()-style expression indicating the LDAP search filter used to search for accounts.',
		'name' => 'ldap_accountFilterFormat',
		'required' => false
	),
	array(
		'label' => 'Account Domain Name',
		'description' => 'The fully qualified domain name for which the target LDAP server is an authority (e.g., example.com).',
		'name' => 'ldap_accountDomainName',
		'required' => false
	),
	array(
		'label' => 'Account Domain Name Short',
		'description' => 'The \'short\' domain for which the target LDAP server is an authority (e.g., example).',
		'name' => 'ldap_accountDomainNameShort',
		'required' => false
	),
	array(
		'label' => 'Bind DN',
		'description' => 'The DN of the account used to perform account DN lookups. Required if bind requires DN.',
		'name' => 'ldap_username',
		'required' => false
	),
	array(
		'label' => 'Bind Password',
		'description' => 'The password of the account used to perform account DN lookups. Required if bind requires DN.',
		'name' => 'ldap_password',
		'required' => false,
		'password' => true
	)
);

foreach ($fields as $row): ?>

<div class="field">
	<div id="<?php echo $row['name']; ?>-label" class="two columns alpha">
		<label for="<?php echo $row['name']; ?>" class="<?php if ($row['required'] === true){ echo "required"; } ?>"><?php echo $row['label']; ?></label>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation"><?php echo $row['description']; ?></p>
		<input type="<?php echo (isset($row['password']) && $row['password'] === true) ? 'password' : 'text'; ?>" name="<?php echo $row['name']; ?>" id="<?php echo $row['name']; ?>" value="<?php echo option($row['name']); ?>" />
	</div>
</div>

<?php endforeach; ?>


<?php
	$selects = array(
		'Bind Requires DN' => 'ldap_bindRequiresDn'
	);

foreach ($selects as $label => $name): ?>

<div class="field">
	<div id="<?php echo $name; ?>-label" class="two columns alpha">
		<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
	</div>
	<div class="inputs five columns omega">
		<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php $value = (int)get_option($name); ?>
			<option value="1"<?php if ($value === 1){ echo ' selected'; } ?>>True</option>
			<option value="0"<?php if ($value === 0){ echo ' selected'; } ?>>False</option>
		</select>
	</div>
</div>

<?php endforeach; ?>
