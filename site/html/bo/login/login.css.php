
#authentication{
	background-color: #F8F8F8;
	width: 50em;
	margin: 0.5em auto;
	padding: 2em 0;
	border: 1px solid #333;
	border-bottom: 2px solid #000;
	border-right: 2px solid #000;
}

#logo{
	text-align: center;
}

#logo img{
	background-color: white;
	padding: 1em;
	border: 1px dashed #333;
}

#authentication form{
	margin-top: 3em;
}

a { color: #5F9BE7;}
a:hover { color:#fa9035; }


body.login div.error-panel {
	background-color: white;
	border: 1px solid red;
	border-right: 2px solid red;
	border-bottom: 2px solid red;
	margin: 1em 2em 0;
}

form fieldset {
	background: url('<?php echo Media::IMAGE('password_admin'); ?>') no-repeat 0.25em <?php echo Client::isMSIE() ? '20px' : '0.75em'; ?> !important;
}

form>fieldset>div.error {
	background: transparent !important;
}
