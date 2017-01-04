
function addMessages()
{
<?php
	Lang::addMessages('Popup.Title.Info|Popup.Title.Error', "\t");
	Lang::addMessages('Validate.isEmail|Validate.isPassword', "\t");
	Lang::addMessages('bo.login_form.fetch_pwd.emailMissing|bo.login_form.fetch_pwd.emailSent|bo.login_form.fetch_pwd.emailError|bo.login_form.errorAuth', "\t");
?>
}


function createForm()
{
	var myForm = new Core.Form('loginForm', 'errorPanel', {
		validators: Array(
			{element: '<?php echo POST_EMAIL; ?>', validator: 'isEmail'},
			{element: '<?php echo POST_PASSWORD; ?>', validator: 'isPassword'}
		)
	});
<?php
	if( Vars::defined(POST_AUTHENTICATION_ERROR) )
	{
?>
	myForm.reportError(Core.Messages.get('bo.login_form.errorAuth'), null);
	myForm.showErrorPanel();
<?php
	}
?>
}

function createForgetPasswordHandler()
{
	Event.observe($('linkForgetPassword'), 'click', function(event){ 
		var element = event.element();
		if( Core.Validator.isEmail('<?php echo POST_EMAIL; ?>') )
		{
			new Core.Ajax('<?php echo Context::AJAX(); ?>', {<?php echo POST_FORGET_PASSWORD.': $F(\''.POST_EMAIL.'\')'; ?>}, function(response) {
				if (response.responseText.match(/^ok$/))
					new Core.Popup.Info(Core.Messages.get('bo.login_form.fetch_pwd.emailSent'), {sizes: {height: 120}});
				else
					new Core.Popup.Error(Core.Messages.get('bo.login_form.fetch_pwd.emailError'), {sizes: {height: 120}});
			});
		}
		else
		{
			new Core.Popup.Error(Core.Messages.get('bo.login_form.fetch_pwd.emailMissing'));
		}
	});
}


Core.whenReady( function() {
	addMessages();
	createForm();
	createForgetPasswordHandler();
});

