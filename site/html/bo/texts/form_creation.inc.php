<?php
	
	
/** ======================= Forms creation (add buttons/fields) ======================= */
	
	/** ----------- Search by Text Form ----------- */
		$langs = Vars::get(GET_TXTSEARCH_PART) == YES ? $boLangs : $foLangs;
		$langSelect = Array();
		foreach($langs as $lang)
			$langSelect[$lang] = Lang::translate("lang_$lang");
		
		$searchTxtForm = new Form(Lang::translate('bo.texts.search.title'), '', Array(), $padding);
		$searchTxtForm->addButton(Lang::translate('bo.texts.search.submitBtn'));
		$searchTxtForm->setId('searchByTextForm');
		$field1 = $searchTxtForm->addFieldset(Lang::translate('bo.texts.search.fieldset'));
		$field1->addElement(new Element(new TextArea(Array('id' => GET_TXTSEARCH_TEXT)), Lang::translate('bo.texts.RscContent.title'), Lang::translate('bo.texts.RscContent.stitle'), true));
		$field1->addElement(new Element(new Select($langSelect, Array('id' => GET_TXTSEARCH_LANG, 'init' => Lang::getCurrentLanguage())), Lang::translate('bo.texts.search.RscLang.title'), Lang::translate('bo.texts.search.RscAdmin.stitle')));
		$field1->addElement(new Element(new Select($types, Array('id' => GET_TXTSEARCH_TYPE)), Lang::translate('bo.texts.RscType.title'), Lang::translate('bo.texts.RscType.stitle')));
		if( $superAdmin )
			$field1->addElement(new Element(new Select($yes_no_all, Array('id' => GET_TXTSEARCH_PART)), Lang::translate('bo.texts.search.RscAdmin.title'), Lang::translate('bo.texts.search.RscAdmin.stitle')));
		else
			$searchTxtForm->addHiddenField(Array('name' => GET_TXTSEARCH_PART, 'value' => NO));
			
	/** ----------- Search by ID Form ----------- */
		$searchIDForm = new Form(Lang::translate('bo.texts.search.title2'), '', Array(), $padding);
		$searchIDForm->addButton(Lang::translate('bo.texts.search.submitBtn'));
		$searchIDForm->setId('searchByIDForm');
		$field1 = $searchIDForm->addFieldset(Lang::translate('bo.texts.search.fieldset'));
		$field1->addElement(new Element(new Input(Array('id' => GET_IDSEARCH_KEY, 'maxlength' => MAX_KEY_LEN)), Lang::translate('bo.texts.RscKey.title'), Lang::translate('bo.texts.RscKey.stitle'), true));
		$field1->addElement(new Element(new Select($types, Array('id' => GET_IDSEARCH_TYPE)), Lang::translate('bo.texts.RscType.title'), Lang::translate('bo.texts.RscType.stitle')));
		if( $superAdmin )
			$field1->addElement(new Element(new Select($yes_no_all, Array('id' => GET_IDSEARCH_PART)), Lang::translate('bo.texts.search.RscAdmin.title'), Lang::translate('bo.texts.search.RscAdmin.stitle')));
		else
			$searchIDForm->addHiddenField(Array('name' => GET_IDSEARCH_PART, 'value' => NO));
		
	/** ----------- Create Resources Form ----------- */
		if( $superAdmin || Authentication::getPrivileges('addTexts') )
		{
			array_shift($types);
			$createForm = new Form(Lang::translate('bo.texts.create.title'), '++', Array(), $padding);
			$createForm->addButton(Lang::translate('bo.texts.create.submitBtn'));
			$createForm->setId('createRscForm');
			
			// Fieldset for information
			$field1 = $createForm->addFieldset(Lang::translate('bo.texts.create.main_fieldset'));
			$field1->addElement(new Element(new Input(Array('id' => GET_CREATE_KEY)), Lang::translate('bo.texts.RscKey.title'), Lang::translate('bo.texts.RscKey.stitle'), true));
			$field1->addElement(new Element(new Select($types, Array('id' => GET_CREATE_TYPE)), Lang::translate('bo.texts.RscType.title'), Lang::translate('bo.texts.RscType.stitle'), true));
			$field1->addElement(new Element(new Select($yes_no, Array('id' => GET_CREATE_TINY)), Lang::translate('bo.texts.RscTiny.title'), Lang::translate('bo.texts.RscTiny.stitle'), true));
			if( $superAdmin )
				$field1->addElement(new Element(new Select($yes_no, Array('id' => GET_CREATE_PART)), Lang::translate('bo.texts.create.RscAdmin.title'), Lang::translate('bo.texts.create.RscAdmin.stitle'), true));
			else
				$createForm->addHiddenField(Array('name' => GET_CREATE_PART, 'value' => NO));
				
			// Fieldset for texts
			$field2 = $createForm->addFieldset(Lang::translate('bo.texts.create.rsc_fieldset'), 'lang_fieldset');
			$subText = Lang::translate('bo.texts.create.langText.stitle');
			foreach($allLangs as $lang)
			{
				$txtLang = Lang::translate("lang_$lang");
				$textarea = new Element(new Textarea(Array('id' => GET_CREATE_PREFIX_TEXTAREA.$lang, 'rows' => '6')), $txtLang, $subText);
				$input = new Element(new Input(Array('id' => GET_CREATE_PREFIX_INPUT.$lang, 'autocomplete' => 'off')), $txtLang, $subText);
				
				if( ! in_array($lang, $foLangs) )
					$textarea->hide();
				$input->hide();
				
				$field2->addElement($textarea, 'lang_field');
				$field2->addElement($input, 'lang_field');
			}
		}
	
	/** ----------- Search Resources Result Form ----------- */
	$resultForm = null;
	if( $display_result_panel && is_array($searchResults) && $searchLanguage )
	{
		$language = new Lang($searchLanguage);
		$resultForm = "$padding<div class='message'><label class='success'>".Lang::translate('bo.texts.results.title')."</label></div>\n";
		$resultForm .= "$padding<table id='results' class='results'>\n";
		$resultForm .= "$padding\t<thead><tr><td class='keys'>Keys</td><td class='text'>Texts</td><td class='actions'>Actions</td></tr></thead>\n";
		$actions = "<img src='".Media::IMAGE('icons/edit')."' title=\"".Lang::translate('bo.texts.results.img.edit.title')."\" class='edit' /> ";
		if( $_rights['delete'] )
			$actions .= "<img src='".Media::IMAGE('icons/delete')."' title=\"".Lang::translate('bo.texts.results.img.drop.title')."\" class='drop' />";
		$resultForm .= "$padding\t<tbody>\n";
		foreach($searchResults as $row)
		{
			$resName = $row->key; $resIdx = $row->locID;
			$text = String::toText(DB::getField(LangDAL::FindTranslation($resIdx, $searchLanguage)));
			$trClass = $row->admin ? " class='bo'" : '';
			//$text = $language->translation($resName, null, false);
			$resultForm .= "$padding\t\t<tr$trClass><td class='keys'><label>$resName</label><div class='idx'>".MD5($resIdx)."</div></td><td class='text'><div>$text</div></td><td class='actions'>$actions</td></tr>\n";
		}
		$resultForm .= "$padding\t</tbody>\n$padding</table>\n";
	}
	

/** ======================= Store them into an array ======================= */
	$forms = Array
	(
		VIEWING_MODE_TEXT_SEARCH => $searchTxtForm,
		VIEWING_MODE_ID_SEARCH => $searchIDForm,
		VIEWING_MODE_CREATION => $createForm,
		VIEWING_MODE_VIEW_RESULTS => $resultForm
	);
?>