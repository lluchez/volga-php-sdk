<?php
/** - - - - Initialization - - - - */
	if( ! Vars::defined(GET_VIEWING_MODE) )
		Common::error404();
	
	$superAdmin = Authentication::isSuperAdmin();
	$_rights = Array(
		'create' => $superAdmin || Authentication::getPrivileges('addTexts'),
		'delete' => $superAdmin || Authentication::getPrivileges('delTexts')
	);
	
	function add_quotes($item){ return "'".str_replace("'", "\'", $item)."'"; }
	function generate_array($collection) { return "\$A(Array(".implode(", ", array_map('add_quotes', $collection))."))"; }
	
	$tiny = Array();
	$validators = Array();
	$db_types = DB::select(SQL_QUERY_TYPES);
	foreach($db_types as $db_type)
	{
		$tiny[] = "'$db_type->key': $db_type->tiny";
		$validators[] = "'$db_type->key': '$db_type->validator'";
	}
	
	$langs = Array();
	foreach($allLangs as $lang)
		$langs[] = "'$lang': '".Lang::translate("lang_$lang")."'";
?>

/** - - - - - Global declaration - - - - - */

var Data = {
	Langs: {
		FO:  <?php echo generate_array($foLangs); ?>,
		BO:  <?php echo generate_array($boLangs); ?>,
		All: <?php echo generate_array($allLangs); ?>,
		Names: $H({<?php echo implode(", ", $langs); ?>}),
		getLanguages: function(value) {
			if( value == '<?php echo YES; ?>' ) return Data.Langs.BO;
			else if( value == '<?php echo ALL; ?>' ) return Data.Langs.All;
			else return Data.Langs.FO;
		}
	},
	fieldSizes: $H({<?php echo implode(", ", $tiny); ?>}),
	validators: $H({<?php echo implode(", ", $validators); ?>}),
	Forms: {
		Search: {
			byText: null,
			byID: null
		},
		Create: {
			form: null,
			langRows: null
		},
		Edit: {
			form: null,
			langRows: null,
			options: {},
			popup: null,
			resultRowTR: null
		}
	},
	editPopup: {
		waitingContent: "<center><img src='<?php echo Lang::IMAGE('popup/loading'); ?>' /></center>",
		title: "<?php echo Lang::translate('bo.texts.edit.popup.title') ?>"
	},
	atLeastOneError: null,
	ajaxCompleter: {
		element: null,
		getParams: function () {
			var params = '<?php echo GET_AJAX_TYPE; ?>=' + $F('<?php echo GET_IDSEARCH_TYPE; ?>');
			if( $('<?php echo GET_IDSEARCH_PART; ?>') )
				params += '&<?php echo GET_AJAX_PART; ?>=' + $F('<?php echo GET_IDSEARCH_PART; ?>');
			return params;
		},
		init: function() {
			var div = document.createElement('div');
			div.id = 'keys_list';
			div.className = 'autocomplete';
			document.body.appendChild(div);
		},
		exText: null
	}
};
<?php
if( Vars::defined(GET_EDIT_LANGUAGE) )
	echo "\tvar languageUsedForSearch = '".Vars::get(GET_EDIT_LANGUAGE)."';\n";
?>
	
/** - - - - - Initialization procedures - - - - - */
	
/* Add messages / translated texts */
Data.addMessages = function() {
<?php
		Lang::addMessages('Validate.isInteger|Validate.isFloat|Validate.isValidKey|Validate.isNotEmpty', "\t\t");
		Lang::addMessages('bo.lang.search.emptyFields', "\t\t");
		Lang::addMessages('bo.texts.error.([0-9])+', "\t\t");
?>
}


/* ----- Form creations ----- */
Data.Forms.Init = function() {
	
	// Search by Text Form
	Data.Forms.Search.byText = new Core.Form('searchByTextForm', undefined, {
		validators: Array({element: '<?php echo GET_TXTSEARCH_TEXT; ?>', validator: 'isNotEmpty', text: Core.Messages.get('Validate.isNotEmpty')})
	});
	Data.Forms.mySearchIDForm = new Core.Form('searchByIDForm', undefined, {
		validators: Array({element: '<?php echo GET_IDSEARCH_KEY; ?>', validator: 'isNotEmpty'})
	});
	
<?php
if( $_rights['create'] )
{
?>
	// Resource creation Form
	Data.Forms.Create.form = new Core.Form('createRscForm', undefined, {
		prepareSubmit: function() {
			$$('#<?php echo MESSAGE_PANEL_ID; ?>').invoke('hide');
		},
		check: function() {
			if( $F('<?php echo GET_CREATE_TINY; ?>') == '<?php echo NO; ?>' && Data.fieldSizes.get($F('<?php echo GET_CREATE_TYPE; ?>')) ) {
				this.reportError(Core.Messages.get('bo.texts.error.7'), '<?php echo GET_CREATE_TINY; ?>', true);
			}
		},
		validators: Array({element: '<?php echo GET_CREATE_KEY; ?>', validator: 'isValidKey'})
	});
<?php
}

if( Vars::defined(GET_EDIT_LANGUAGE) )
{
?>
	// Resource edit Form / Options
	Data.Forms.Edit.options = {
		check: function() {
			if( $F('<?php echo GET_EDIT_TINY; ?>') == '<?php echo NO; ?>' && Data.fieldSizes.get($F('<?php echo GET_EDIT_TYPE; ?>')) ) {
				this.element.reportError(Core.Messages.get('bo.texts.error.7'), '<?php echo GET_EDIT_TINY; ?>', true);
			}
		},
		waitForResponse: function() { this.resize(); },
		visibility: null,
		nbLanguages: 0,
		resize: function() {
			if( (that = this.element) && that.errorPanel) {
				if( that.formID.visible() ) {
					var inputSize = ($F('<?php echo GET_EDIT_TINY; ?>') == '<?php echo YES; ?>') ? <?php echo POPUP_SIZE_INPUT; ?> : <?php echo POPUP_SIZE_TEXTAREA; ?>;
					var h = <?php echo POPUP_SIZE_FORM; ?> + (this.nbLanguages * inputSize);
					if( that.errorPanel.errPanelObj.visible() )
						h += 72 + (that.errorPanel.countError()*12);
				} else {
					var h = <?php echo POPUP_SIZE_BORDERS; ?> + 64;
				}
				Data.Forms.Edit.popup.setStyles({height: h+'px'});
			}
		}
	};
<?php
}
?>
};
	
	
/* Create the event handlers */
Data.Forms.createHandlers = function () {
	// Menu/section
	createNavigationHandlers();
	
<?php
if( Vars::defined(GET_EDIT_LANGUAGE) )
{
?>
		// Results
		createResultsHandlers();
		
<?php
}

if( $_rights['create'] )
{
?>
	// Resource creation form: DropDowns
	$('<?php echo GET_CREATE_TYPE; ?>').observe('change', function(event){ Data.Forms.Create.updateLanguageFields(event); });
	$('<?php echo GET_CREATE_PART; ?>').observe('change', function(event){ Data.Forms.Create.updateLanguageFields(event); });
	$('<?php echo GET_CREATE_TINY; ?>').observe('change', function(event){ Data.Forms.Create.updateLanguageFields(event); });

<?php
}
?>
	$('<?php echo GET_TXTSEARCH_PART; ?>').observe('change', function(event){ changeAvailableLanguages(event); });
	
	// Help to choose the visibility when res key is standard
	if( visivbility = $('<?php echo GET_CREATE_PART; ?>') )
		$('<?php echo GET_CREATE_KEY; ?>').observe('blur', function(event){ 
			var val = event.element().value;
			if( val.match(/^((bo|options|Popup|Validate|rsc\.type)\.|lang_([a-z]{2,3})$)/) ) { visivbility.value = '<?php echo YES; ?>'; }
			else if( val.match(/^((fo|site)\.|meta_)/) ) { visivbility.value = '<?php echo NO; ?>'; }
		});
};
	
	
	
	
	
/** - - - - - Handlers - - - - - */
	
<?php
if( Vars::defined(GET_EDIT_LANGUAGE) )
{
?>
	Data.Forms.Edit.AjaxCallback = function(response){
		var that = Data.Forms.Edit;
		Data.atLeastOneError = null;
		response.responseText.evalScripts();
		
		if( Data.atLeastOneError == '<?php echo YES; ?>' ) {
			that.form.showForm();
			that.form.options.resize();
		} else if ( Data.atLeastOneError == '<?php echo NO; ?>' ) {
			that.popup.hide();
			that.popup = null;
			new Core.Popup.Info('<?php echo Lang::translate("bo.texts.edit.success"); ?>');
		} else {
			// Another error...
		}
	};

	Data.Forms.Edit.updateLanguageFields = function(event) {
		var that = this, type = $F('<?php echo GET_EDIT_TYPE; ?>'), tiny = $F('<?php echo GET_EDIT_TINY; ?>');
		var langs = Data.Langs.getLanguages(this.options.visibility);
		var shouldBeTiny = Data.fieldSizes.get(type);
		if( shouldBeTiny )
			$('<?php echo GET_EDIT_TINY; ?>').value = '<?php echo YES; ?>';
		var isTinyField = (tiny == '<?php echo YES; ?>') || shouldBeTiny;
		var refPrefix = isTinyField ? '<?php echo GET_EDIT_PREFIX_INPUT; ?>' : '<?php echo GET_EDIT_PREFIX_TEXTAREA; ?>';
		this.form.options.resize();
		
		if( ! this.langRows ) // compute it only once
			this.langRows = $('langEdit_fieldset').select('*.lang_field');
		this.langRows.each( function(row) {
			if( res = row.id.match(/^row_(edit_[a-z\_]+_)([a-z]{2,3})$/) ) {
				var prefix = res[1], lang = res[2];
				var element = $(prefix+lang);
				if( langs.member(lang) && prefix == refPrefix ) {
					row.show();
					if( validator = Data.validators.get(type) )
						that.form.addValidator(element, eval('Core.Validator.'+validator), Core.Messages.get('Validate.'+validator));
					else
						that.form.removeValidator(element);
					element.enable();
				} else {
					row.hide();
					element.disable();
				}
			}
		});
	};
	
<?php
if( $_rights['delete'] )
{
?>
	/* Create handlers for Results panel */
	function dropResource(res, tr) {
		if( confirm("<?php echo Lang::translate('bo.texts.results.drop.confirm'); ?>") ) {
			new Core.Ajax('<?php echo Context::AJAX(); ?>', $H({<?php echo GET_DEL_RESNAME; ?>: res.get('name'), <?php echo GET_DEL_RESIDX; ?>: res.get('id')}).toQueryString(), function(response) {
					if( response.responseText.match(/^<?php echo ERROR_RES_NOT_FOUND; ?>$/) ) {
						new Core.Popup.Warning("<?php echo Lang::translate('bo.texts.results.errorFindRsc'); ?>");
					} else if( response.responseText.match(/^<?php echo ERROR_UNABLE_DEL_RES; ?>$/) ) {
						new Core.Popup.Error("<?php echo Lang::translate('bo.texts.results.drop.unableDropRes'); ?>");
					} else {
						var tbody = tr.parentNode;
						tbody.removeChild(tr);
						if( tbody.getElementsByTagName('tr').length == 0 ) {
							$('frame_results').hide();
							$('bar_results').hide();
						}
					}
				}
			);
		}
	}
<?php
}
?>

	
	function editResource(res, tr) {
		Data.Forms.Edit.resultRowTR = $(tr);
		var options = {
			title: Data.editPopup.title,
			buttonText: "<?php echo Lang::translate('btn.cancel'); ?>",
			sizes: {width: 564}
		};
		Data.Forms.Edit.popup = new Core.Popup(Data.editPopup.waitingContent, options);
		var onSuccess = function(response) {
			if( response.responseText.match(/^<?php echo ERROR_RES_NOT_FOUND; ?>$/) ) {
				Data.Forms.Edit.popup.hide();
				new Core.Popup.Error("<?php echo Lang::translate('bo.texts.results.errorFindRsc'); ?>");
			} else {
				Data.Forms.Edit.popup.setText(response.responseText);
				response.responseText.evalScripts();
			}
		};
		params = $H({<?php echo GET_EDITFROM_KEY; ?>: res.get('name'), <?php echo GET_EDIT_LANGUAGE.": '".Vars::get(GET_EDIT_LANGUAGE)."'"; ?>});
		new Core.Ajax('<?php echo Context::HTML(); ?>', params, onSuccess);
	}
	
	function updateResourceData(resName, resContent) {
		if( Data.Forms.Edit.resultRowTR && Data.Forms.Edit.resultRowTR.getTagName() == 'tr' ) {
			Data.Forms.Edit.resultRowTR.select('td.keys label')[0].innerHTML = resName;
			Data.Forms.Edit.resultRowTR.select('td.text div')[0].innerHTML = resContent;
			createResultsHandlerForTR(Data.Forms.Edit.resultRowTR, true);
			Data.Forms.Edit.resultRowTR = null;
		}
	}
	
	
	function createResultsHandlerForTR(tr, justImg) {
		var resContent = tr.select('td')[0];
		var resInfos = $H({'name': resContent.select('label')[0].innerHTML, 'id': resContent.select('div.idx')[0].innerHTML});
		var imgEdit = tr.select("img.edit")[0], imgDrop = tr.select("img.drop")[0];
		imgEdit.stopObserving('click');
		imgEdit.observe('click', function(event) { editResource(resInfos, tr); } );
<?php
if( $_rights['delete'] )
{
?>
		if( imgDrop ) {
			imgDrop.stopObserving('click');
			imgDrop.observe('click', function(event) { dropResource(resInfos, tr); } );
		}
<?php
}


if( ! Client::isCompliantWithCSS3() ) /* For old browsers... */
{
?>
		if( ! justImg ) {
			tr.observe('mouseover', function(event) { tr.addClassName('over'); });
			tr.observe('mouseout', function(event) { tr.removeClassName('over'); });
		}
<?php
}
?>
	}
	
	function createResultsHandlers() {
		if( table = $('results') ) {
			table.select("tbody tr").each( createResultsHandlerForTR );
		}
	}

<?php
}


if( $_rights['create'] )
{
?>
	
	/* Updating the 'Create Resource' form */
	Data.Forms.Create.updateLanguageFields = function (event) {
		var that = this, type = $F('<?php echo GET_CREATE_TYPE; ?>'), part = $F('<?php echo GET_CREATE_PART; ?>'), tiny = $F('<?php echo GET_CREATE_TINY; ?>');
		var langs = Data.Langs.getLanguages(part);
		var shownElts = $A();
		var shouldBeTiny = Data.fieldSizes.get(type);
		if( shouldBeTiny )
			$('<?php echo GET_CREATE_TINY; ?>').value = '<?php echo YES; ?>';
		var isTinyField = (tiny == '<?php echo YES; ?>') || shouldBeTiny;
		var refPrefix = isTinyField ? '<?php echo GET_CREATE_PREFIX_INPUT; ?>' : '<?php echo GET_CREATE_PREFIX_TEXTAREA; ?>';
		var otherPrefix = isTinyField ? '<?php echo GET_CREATE_PREFIX_TEXTAREA; ?>' : '<?php echo GET_CREATE_PREFIX_INPUT; ?>';
		
		if( ! this.langRows ) // compute it only once
			this.langRows = $('lang_fieldset').select('*.lang_field');
		this.langRows.each( function(row) {
			if( res = row.id.match(/^row_([a-z\_]+_)([a-z]{2,3})$/) ) {
				var prefix = res[1], lang = res[2];
				var element = $(prefix+lang);
				if( langs.member(lang) && prefix == refPrefix ) {
					if( ! row.visible() ) {
						shownElts.push(element);
						row.show();
					}
					if( validator = Data.validators.get(type) )
						that.form.addValidator(element, eval('Core.Validator.'+validator), Core.Messages.get('Validate.'+validator));
					else
						that.form.removeValidator(element);
					element.enable();
				} else {
					row.hide();
					element.disable();
				}
			}
		});
		
		if( (! event) || (event.element().id != '<?php echo GET_CREATE_PART; ?>') ) {
			shownElts.each( function(element) {
				if( Core.Validator.isEmpty(element) ) {
					var id = element.id.replace(refPrefix, otherPrefix);
					var oldFieldValue = $F(id);
					element.setValue(oldFieldValue);
					$(id).setValue('');
				}
			});
		}
	};
<?php
}
?>

	
	// Update a DropDown: change the <option /> tags
	function setChoices(listHTML, keys, values) {
		if( listHTML = $(listHTML) ) {
			// Empty it
			while (listHTML.options.length > 0)
				listHTML.remove(0);
			// add elements/options
			keys.each( function(key) {
				var opt = new Element('option');
				opt.value = key; opt.text = values.get(key);
				try { listHTML.add(opt, null); }
				catch(ex) { listHTML.add(opt); }
			});
		}
	}
	
	
	/* Search by text form: Visibility (BO/FO) have been changed */
	function changeAvailableLanguages(event) {
		var value = $F('<?php echo GET_TXTSEARCH_LANG; ?>');
		var langs = Data.Langs.getLanguages($F('<?php echo GET_TXTSEARCH_PART; ?>'));
		setChoices('<?php echo GET_TXTSEARCH_LANG; ?>', langs, Data.Langs.Names);
		if( value )
			$('<?php echo GET_TXTSEARCH_LANG; ?>').value = value;
	}
	
	
	// Updating the bar/sections
	function toogleFrame(elt) {
		elt.toggleClassName('unfolded');
		new Effect.toggle(elt.linkedTo, 'blind', {duration: 0.3});
	}
	
	
	/* Create handlers for the vertical menu/sections */
	function createNavigationHandlers() {
		var panelToDisplay = '<?php echo Vars::get(GET_VIEWING_MODE); ?>', elts = <?php echo generate_array($panels); ?>;
		elts.each( function(elt) {
			if( bar = $('label_'+elt) ) {
				var frame = $('frame_'+elt);
				bar.linkedTo = frame;
				if( panelToDisplay == elt )
					bar.toggleClassName('unfolded');
				else
					frame.setStyle({display: 'none'});
				bar.observe('click', function(event) { toogleFrame(event.element()); });
			}
		});
	}
	

	

// ------ Init, when DOM's ready ------
Core.whenReady( function init() {
	Data.addMessages();
	Data.Forms.Init();
	Data.Forms.createHandlers();
	Data.ajaxCompleter.init();
	Data.ajaxCompleter.element = new Ajax.Autocompleter("<?php echo GET_IDSEARCH_KEY; ?>", "keys_list", "<?php echo Context::HTML() ?>", {
		paramName: '<?php echo GET_AJAX_KEY; ?>', 
		method: 'post',
		encoding: Core.Options.AjaxEncoding,
		minChars: 2,
		afterUpdateElement: function (element, li) { 
			if( $(li).hasClassName('not_found') )
				$(element).value = Data.ajaxCompleter.exText; 
		},
		callback: function(element, entry) { Data.ajaxCompleter.exText = $F(element); return entry + '&' + Data.ajaxCompleter.getParams(); }
	});

<?php
	if( $_rights['create'] )
		echo "Data.Forms.Create.updateLanguageFields();\t\n";
	Media::displayErrorMessages('bo.texts.error.', $error_messages);
?>
});
