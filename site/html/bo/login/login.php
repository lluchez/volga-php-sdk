<div id="authentication">
	<div id="logo">
		<img src="<?php echo Media::IMAGE('logo_admin'); ?>" />
	</div>
	<div id='errorPanel'></div>
<?php
	Form::useGetVales(false);
	$form = new Form(Lang::translate('bo.login_form.title'), '-', Array(), 1);
	$form->addButton(Lang::translate('bo.login_form.btn.submit'));
	$form->setId('loginForm');
	$field1 = $form->addFieldset(Lang::translate('bo.login_form.fieldset'));
	$field1->addElement(new Element(new Input(Array('id' => POST_EMAIL)), Lang::translate('bo.login_form.field.email'), Lang::translate('bo.login_form.sfield.email'), true));
	$field1->addElement(new Element(new Input(Array('id' => POST_PASSWORD, 'type' => 'password')), Lang::translate('bo.login_form.field.pass'), Lang::translate('bo.login_form.sfield.pass'), true));
	if( Vars::defined(POST_REDIRECT_TO) )
		$form->addHiddenField(POST_REDIRECT_TO);
	echo $form;
?>
	<center>
		<a id="linkForgetPassword" href="javascript: void(null)"><?php echo Lang::translate('bo.login_form.forgetpassword'); ?></a>
	</center>
</div>
