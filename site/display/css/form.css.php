
form.withCss3, form.noCss3 { position: relative; margin: 1em auto; border: 1px solid #000; background-color: #f8f8f8; text-align: left; }

/*  form size */
form.small { width: 28em; }
form.medium { width: 35em; }
form.large { width: 42em; }
form.xlarge { width: 50em; }

form div.header, form div.footer { text-align: left; background: black; color: white; font-weight: bold; }

form .changed { color: red; }

div.message { text-align: center; margin: 1em 0;}
div label.success { color: green; font-weight: bold; }
div.message label.error { color: red; font-weight: bold; }
div.message label.warning { color: orange; font-weight: bold; }

form div.error label { color: black; }

<?php
	
	if( Client::isCompliantWithCSS3() )
	{
		include DIR_CSS . "form_css3.css";
		
		echo "\n\n/** Specific browser fixes */\n\n";
		switch( Client::getBrowserName() )
		{
			case 'Opera':
?>
form.withCss3 fieldset>div>label>span { margin-left: -1em; }
form.withCss3>div.footer input { font-size: 0.9em; }
form.withCss3 input[type=text], form input[type=password] { font-size: 0.9em; }
form.withCss3.small fieldset>div>input[type=text] { width: 16em; }
form.withCss3.medium fieldset>div>input[type=text] { width: 23.5em; }
form.withCss3.large fieldset>div>input[type=text] { width: 32em; }
form.withCss3.xlarge fieldset>div>input[type=text] { width: 40em; }
form.withCss3 fieldset>div>div>p>input+label { font-size: 0.8em; }
<?php
				break;
			
			case 'MSIE':
?>
form.withCss3 fieldset>div>div>p>input+label { padding-top: 0.25em; }
<?php
				break;
		}
	}
	else
	{
		include DIR_CSS . "form_oldBrowser.css";
	}
	
	include DIR_CSS . "form_errPanel.css.php";
	

?>