<?php
$fields = array(
	array(
		'label' => 'Host (LDAP Server)',
		'name' => 'ldap_host',
		'required' => true
	),
	array(
		'label' => 'Port',
		'name' => 'ldap_port',
		'required' => false
	),
	array(
		'label' => 'Base DN',
		'name' => 'ldap_baseDn',
		'required' => false
	),
	array(
		'label' => 'Account Canonical Form',
		'name' => 'ldap_accountCanonicalForm',
		'required' => false
	),
	array(
		'label' => 'Account Filter Format',
		'name' => 'ldap_accountFilterFormat',
		'required' => false
	),
	array(
		'label' => 'Account Domain Name',
		'name' => 'ldap_accountDomainName',
		'required' => false
	),
	array(
		'label' => 'Account Domain Name Short',
		'name' => 'ldap_accountDomainNameShort',
		'required' => false
	)
);

foreach ($fields as $row): ?>

<div class="field">
	<div id="<?php echo $row['name']; ?>-label" class="six columns">
		<label for="<?php echo $row['name']; ?>" class="<?php if ($row['required'] === true){ echo "required"; } ?>"><?php echo $row['label']; ?></label>
	</div>
	<div class="inputs">
		<input type="text" name="<?php echo $row['name']; ?>" id="<?php echo $row['name']; ?>" value="<?php echo option($row['name']); ?>" />
	</div>
</div>

<?php endforeach; ?>


<?php
	$selects = array(
		'Bind Requires DN' => 'ldap_bindRequiresDn'
	);

foreach ($selects as $label => $name): ?>

<div class="field">
	<div id="<?php echo $name; ?>-label" class="six columns">
		<label for="<?php echo $name; ?>"><?php echo $label; ?></label>
	</div>
	<div class="inputs">
		<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php $value = (int)get_option($name); ?>
			<option value="1"<?php if ($value === 1){ echo ' selected'; } ?>>True</option>
			<option value="0"<?php if ($value === 0){ echo ' selected'; } ?>>False</option>
		</select>
	</div>
</div>

<?php endforeach; ?>
