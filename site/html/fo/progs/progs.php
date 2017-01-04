<?php
	
	$cats = Lang::getAssocArray("SELECT * FROM `prog_category`", 'cID', 'cName');
	$catsAll = Array(Lang::translate('prog.cat.all'))+$cats;
	
	$langs = Lang::getAvailableLanguages(false);
	$langSelect = Array(Lang::translate("lang_all"));
	foreach($langs as $lang)
		$langSelect[$lang] = Lang::translate("lang_$lang");
	
	$sortsTxt = Lang::translateKeys("^progs\.search\.sortby\.");
	$sorts = Array(
		SORT_DL   => $sortsTxt['progs.search.sortby.dl'],
		SORT_NAME => $sortsTxt['progs.search.sortby.name'],
		SORT_CAT  => $sortsTxt['progs.search.sortby.category']
		//SORT_DATE => $sortsTxt['progs.search.sortby.date']
	);

	$searchForm = new Form(Lang::translate('progs.search.title'), '-');
	$searchForm->setId('search_form');
	$searchForm->addButton(Lang::translate('progs.search.submit'));
	$fieldForm = $searchForm->addFieldset(Lang::translate('progs.search.title'));
	$fieldForm->addElement(new Element(new Select($catsAll, POST_CAT_ID), Lang::translate('progs.search.category')));
	$fieldForm->addElement(new Element(new Select($langSelect, POST_LANG_ID), Lang::translate('progs.search.language')));
	$fieldForm->addElement(new Element(new Select($sorts, Array('id'=>POST_SORT_ID,'value'=>SORT_DEF)), Lang::translate('progs.search.sortby')));
	echo $searchForm;
?>


<div id="processing" style="display: none;"><img src="<?php echo Media::IMAGE('popup/loading2'); ?>" title="<?php echo Lang::translate('progs.search.category'); ?>" /></div>
<div id="programs">
<?php
	generateProgramPanelContent();
?>
</div>
<div style="clear: both"></div>

