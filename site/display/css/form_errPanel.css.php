
/********************************
 *          Error Panel         *
 ********************************/


div.error-panel {
	background-color: beige;
	border: 1px dashed red;
	margin: 1em; padding: 0.5em;
}

div.error-panel span {
	margin: 0; padding: 0;
	font-weight: bold;
}

div.error-panel div.close {
	float: right;
	width: 22px; height: 22px;
	background: url('<?php echo Media::Image('icons/close_error'); ?>');
	cursor: pointer;
	margin: -0.25em -0.25em 0 0;
}

div.error-panel ul {
	margin: 0.25em; margin-left: 1em;
}
div.error-panel ul li {
	list-style: none;
	background: url('<?php echo Media::Image('icons/exclamation-mark'); ?>') no-repeat 0 2px;
	padding-left: 1.5em;
}


div.error-panel ul li label.info {
	cursor: help;
}
