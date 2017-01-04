<?php
	
	/** ======== Ajax CallBack for Autocompletion ======== */
	if( Vars::pDefined(GET_AJAX_KEY, GET_AJAX_TYPE) && ((!$superAdmin) || Vars::defined(GET_AJAX_PART)) )
	{
		$part = ($superAdmin ? Vars::get(GET_AJAX_PART) : NO);
		$name = Vars::get(GET_AJAX_KEY);
		$sql = LangDAL::SearchResourceID("%$name%", $part, Vars::get(GET_AJAX_TYPE));
		echo "<ul>\n";
		if( $rows = DB::select($sql) )
		{
			foreach($rows as $sqlRow)
			{
				$row = LangDAL::RscInfoToArray($sqlRow);
				$text = $row['key'];
				try {
					$text = preg_replace('/'.str_replace('.', "\\.", $name).'/i', '<high>${0}</high>', $text);
				} catch(Exception $e) {}
				echo "\t<li type='".$row['type']."'>$text</li>\n";
			}
		}
		else
			echo "\t<li class='not_found'>".Lang::translate('bo.texts.ajax.no_keys')."</li>\n";
		echo "</ul>\n";
		die();
	}
	/** ============ Delete Resource CallBack ============ */
	elseif( Vars::pDefined(GET_DEL_RESNAME, GET_DEL_RESIDX) && $_rights['delete'] )
	{
		if( $id = DB::getField(LangDAL::FindResourceToEdit(Vars::get(GET_DEL_RESNAME), Vars::get(GET_DEL_RESIDX))) )
		{
			$sqls = LangDAL::DeleteResource($id);
			if( DB::update($sqls[0]) && DB::update($sqls[1]) )
				echo 'OK';
			else
				echo ERROR_UNABLE_DEL_RES;
		}
		else
			echo ERROR_RES_NOT_FOUND;
		die();
	}
	/** ============ Edit Resource CallBack ============ */
	elseif( Vars::pDefined(GET_EDIT_KEY, GET_EDIT_TYPE, GET_EDIT_TINY, HIDDEN_EDIT_IDX, HIDDEN_EDIT_NAME, GET_EDIT_LANGUAGE) )
	{
		$errors = Array();
		
		$resIdxMD5 = Vars::get(HIDDEN_EDIT_IDX);
		if( $sqlRow = DB::getOneRow(LangDAL::FindResourceToEdit(Vars::get(HIDDEN_EDIT_NAME), $resIdxMD5, true)) )
		{
			// Retieve resource data
			$row = LangDAL::RscInfoToArray($sqlRow);
			$id = $row['id'];
			$resName = Vars::get(HIDDEN_EDIT_NAME);
			
			// Update Resource name
			if( Vars::get(HIDDEN_EDIT_NAME) != Vars::get(GET_EDIT_KEY) )
			{
				if( DB::update(LangDAL::ChangeResourceKey($id, Vars::get(GET_EDIT_KEY))) )
					$resName = Vars::get(GET_EDIT_KEY);
				else
					$errors[] = ERROR_EDIT_CHANGE_NAME;
			}
			
			// Update Resource properties
			if( ! DB::update(LangDAL::ChangeResourceProperties($id, Vars::get(GET_EDIT_TYPE), Vars::get(GET_EDIT_TINY))) )
				$errors[] = ERROR_EDIT_CHANGE_PROP;
			
			// No error, we can edit texts
			if( ! $errors )
			{
				if( $sqlRow = DB::getOneRow(LangDAL::FindResourceToEdit($resName, $resIdxMD5, true)) )
				{
					$row = LangDAL::RscInfoToArray($sqlRow);
					$texts = Vars::match(FIELD_REGEXP, 3);
					$adminRsc = $row['isBO'] ? YES : NO;
					$langs = ( $adminRsc == YES ) ? $boLangs : $foLangs;
					$keys = array_keys($texts);
					foreach($keys as $key)
						if( ! in_array($key, $langs) )
							unset($texts[$key]);
					
					// Update texts for each language
					foreach($texts as $lang => $text)
					{
						if( ! DB::update(LangDAL::ReplaceText($id, $lang, $text)) )
							$errors['key'] = ERROR_EDIT_SQL_ERROR; // report the error only once
					}
				}
			}
		}
		else
		{
			$errors[] = ERROR_EDIT_MATCHING_RSC;
		}
?>
<script type="text/javascript">
	Data.atLeastOneError = '<?php echo ($errors ? YES : NO); ?>';
<?php
	if( $errors )
	{
		Vars::set(POST_ERROR, $errors);
		Media::displayErrorMessages('bo.texts.error.', $error_messages);
	}
	else
	{
		$text = DB::getField(LangDAL::FindTranslation($id, Vars::get(GET_EDIT_LANGUAGE)));
		$text = String::toJS(String::toText($text, false));
		echo "\tupdateResourceData('$resName', '$text');\n";
	}
?>
</script>
<?php
		die();
	}
	/** ============ Edit Resource Form ============ */
	elseif( Vars::defined(GET_EDITFROM_KEY, GET_EDIT_LANGUAGE) && Common::isRightReferer())
	{
		$formId = 'form_'.str_replace('.', '_', Vars::get(GET_EDITFROM_KEY));
		$errorPanelID = 'errorPanelPopup';
		$editForm = new Form(Lang::translate('bo.texts.edit.title'), '+', Array('style' => 'margin: 0 auto;'));
		$editForm->addButton(Lang::translate('bo.texts.edit.submitBtn'));
		$editForm->setId($formId);
		$field1 = $editForm->addFieldset(Lang::translate('bo.texts.create.main_fieldset'));
		$field2 = $editForm->addFieldset(Lang::translate('bo.texts.create.rsc_fieldset'), 'langEdit_fieldset');
		
		$adminRsc = ($superAdmin ? ALL : NO);
		if( $res = DB::getOneRow(LangDAL::SearchResourceID(Vars::get(GET_EDITFROM_KEY), $adminRsc)) )
		{
			$infos = LangDAL::RscInfoToArray($res);
			array_shift($types);
			$editForm->setLocation(Context::AJAX());
			$editForm->addHiddenField(Array('name' => HIDDEN_EDIT_IDX,  'value' => md5($infos['id'])));
			$editForm->addHiddenField(Array('name' => HIDDEN_EDIT_NAME, 'value' => $infos['key']));
			$editForm->addHiddenField(GET_EDIT_LANGUAGE);
			$field1->addElement(new Element(new Input(Array('id' => GET_EDIT_KEY, 'value' => $infos['key'])), Lang::translate('bo.texts.RscKey.title'), Lang::translate('bo.texts.RscKey.stitle'), true));
			$field1->addElement(new Element(new Select($types, Array('id' => GET_EDIT_TYPE, 'value' => $infos['type'])), Lang::translate('bo.texts.RscType.title'), Lang::translate('bo.texts.RscType.stitle'), true));
			$field1->addElement(new Element(new Select($yes_no, Array('id' => GET_EDIT_TINY, 'value' => $infos['tiny'] ? YES : NO)), Lang::translate('bo.texts.RscTiny.title'), Lang::translate('bo.texts.RscTiny.stitle'), true));
			
			$langs = $infos['isBO'] ? $boLangs : $foLangs;
			foreach($langs as $lang)
			{
				$value = '';
				if( $row = DB::getOneRow(LangDAL::FindTranslation($infos['id'], $lang, 'text')) )
					$value = $row->text;
				
				$txtLang = Lang::translate("lang_$lang");
				$textarea = new Element(new Textarea(Array('id' => GET_EDIT_PREFIX_TEXTAREA.$lang, 'value' => $value, 'rows' => 5)), $txtLang, $subText);
				$input = new Element(new Input(Array('id' => GET_EDIT_PREFIX_INPUT.$lang, 'value' => $value, 'autocomplete' => 'off')), $txtLang, $subText);
				
				$obj = ($infos['tiny'] ? $textarea : $input); $obj->hide();
				$field2->addElement($textarea, 'lang_field');
				$field2->addElement($input, 'lang_field');
			}
			echo "<div id='$errorPanelID'></div>\n" . $editForm
					. "<div style='display: none; margin: 0 auto; width: 40px;' id='editFormWaiting'><img src='"
					. Media::IMAGE('popup/loading')."' /></div>\n";
		}
		else
		{
			die(ERROR_RES_NOT_FOUND);
		}
		
?>
<script type="text/javascript">
	Object.extend(Data.Forms.Edit.options, {
		visibility: '<?php echo $infos['isBO'] ? YES : NO; ?>',
		nbLanguages: <?php echo count($langs); ?>,
		ajaxCallback: Data.Forms.Edit.AjaxCallback,
		validators: Array({element:'<?php echo GET_EDIT_KEY; ?>', validator: 'isValidKey'})
	});
	
	var errorPanel = {
		element: '<?php echo $errorPanelID; ?>',
		options: {
			onDisplay: function() { this.form.options.resize(); },
			onHide: function() { this.form.options.resize(); }
		}
	};
	
	Data.Forms.Edit.form = new Core.Form('<?php echo $formId; ?>', errorPanel, Data.Forms.Edit.options);
	
	$('<?php echo GET_EDIT_TYPE; ?>').observe('change', function(event){ Data.Forms.Edit.updateLanguageFields(event); });
	$('<?php echo GET_EDIT_TINY; ?>').observe('change', function(event){ Data.Forms.Edit.updateLanguageFields(event); });
	Data.Forms.Edit.updateLanguageFields();
</script>

<?php
		die();
	}
	
	/** ============ Search Resource by Text CallBack ============ */
	elseif( Vars::pDefined(GET_TXTSEARCH_TEXT, GET_TXTSEARCH_LANG, GET_TXTSEARCH_TYPE) )
	{
		/* Init */
		$text = Vars::safeSQL(GET_TXTSEARCH_TEXT);
		$lang = Vars::get(GET_TXTSEARCH_LANG);
		$type = Vars::get(GET_TXTSEARCH_TYPE);
		$adminRsc = ($superAdmin ? Common::notNull(Vars::get(GET_TXTSEARCH_PART), NO) : NO);
		
		$errors = checkSearchByTextForm($lang, $type);
		if( ! $errors )
		{
			if( $searchResults = DB::select(LangDAL::SearchText($text, $lang, $adminRsc, $type, "`locID`,`key`,`admin`")) )
			{
				// Toogle on the Result panel
				$panel_to_display = VIEWING_MODE_VIEW_RESULTS;
				$display_result_panel = true;
				$searchLanguage = $lang;
			}
			else
			{
				$errors[] = ERROR_SEARCH_NO_MATCH;
				$panel_to_display = VIEWING_MODE_TEXT_SEARCH;
			}
		}
	}
	
	/** ============ Search Resource by ID CallBack ============ */
	elseif( Vars::pDefined(GET_IDSEARCH_KEY, GET_IDSEARCH_TYPE) )
	{
		/* Init */
		$rscKey = Vars::get(GET_IDSEARCH_KEY);
		$type = Vars::get(GET_IDSEARCH_TYPE);
		$adminRsc = ($superAdmin ? Common::notNull(Vars::get(GET_IDSEARCH_PART), NO) : NO);
		
		$errors = checkSearchByIDForm($type);
		if( count($errors) == 0 )
		{
			if( $searchResults = DB::select(LangDAL::SearchResourceID("%$rscKey%", $adminRsc, $type, "`locID`,`key`,`admin`")) )
			{
				// Toogle on the Result panel
				$panel_to_display = VIEWING_MODE_VIEW_RESULTS;
				$display_result_panel = true;
				$searchLanguage = Lang::getCurrentLanguage();
			}
			else
			{
				$errors[] = ERROR_SEARCH_NO_MATCH;
				$panel_to_display = VIEWING_MODE_ID_SEARCH;
			}
		}
	}
	
	
	/** ============ Creation of new resources | CallBack ============ */
	elseif( Vars::pDefined(GET_CREATE_KEY, GET_CREATE_TYPE, GET_CREATE_TINY) && $_rights['create'] )
	{
		/* Init */
		$type = Vars::get(GET_CREATE_TYPE);
		$tiny = (Vars::get(GET_CREATE_TINY) == YES) || $isTiny[$type];
		$rscKey = Vars::get(GET_CREATE_KEY);
		$panel_to_display = VIEWING_MODE_CREATION;
		
		/* Checks */
		$errors = checkFormCreation($rscKey, $type, $tiny);
		
		if( count($errors) == 0 )
		{
			/* Retrieve the texts to add and remove unexpected fields */
			$texts = Vars::match(FIELD_REGEXP, 3);
			$adminRsc = $superAdmin ? Common::notNull(Vars::get(GET_CREATE_PART), NO) : NO;
			$langs = ( $adminRsc == YES ) ? $boLangs : $foLangs;
			$keys = array_keys($texts);
			foreach($keys as $key)
				if( ! in_array($key, $langs) )
					unset($texts[$key]);
			
			if( DB::getOneRow(LangDAL::SearchResourceID($rscKey)) )
			{
				$errors[] = ERROR_NEW_DUPLICATE_KEY;
			}
			else
			{
				$sql = LangDAL::CreateResourceKey($rscKey, $type, $tiny, $adminRsc);
				if( DB::update($sql) )
				{
					$resID = DB::getLastInsertedId();
					foreach($texts as $lang => $text)
					{
						if( ! DB::update(LangDAL::ReplaceText($resID, $lang, $text)) )
						{
							DB::update(LangDAL::ReplaceText($resID, $lang, ""));
							$errors['key'] = ERROR_NEW_SQL_ERROR; // report the error only once
						}
					}
				}
				else
				{
					$errors['key'] = ERROR_NEW_SQL_ERROR; // report the error only once
				}
			}
			
			
			// Redirection
			if( ! $errors )
			{
				$params = Array(GET_RESOURCE_CREATED => true, GET_IDSEARCH_KEY => $rscKey, GET_CREATE_TYPE => null, GET_CREATE_TINY => null);
				if( Vars::defined(GET_CREATE_PART) )
					$params += Array(GET_CREATE_PART => null);
				if( $superAdmin )
					$params += Array(GET_RES_ID => $resID);
				Context::forward($params);
				die();
			}
			else
			{
				Vars::set(POST_ERROR, implode(',', $errors));
				Vars::set(GET_IDSEARCH_KEY, $rscKey);
			}
			
		}
	}
	
	
	/** ============ Creation of new resources, after a forward ============ */
	elseif( Vars::defined(GET_RESOURCE_CREATED) )
	{
		$panel_to_display = VIEWING_MODE_CREATION;
	}

?>