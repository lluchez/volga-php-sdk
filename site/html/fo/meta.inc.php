<?php
	$meta = SETTINGS_MULTI_LINGUAGES ? Lang::translate('meta_description') : '';
	Head::addMetaData('DESCRIPTION', $meta);
	Head::addMetaData('ABSTRACT', $meta);
	Head::addMetaData('KEYWORDS', SETTINGS_MULTI_LINGUAGES ? Lang::translate('meta_keywords') : '');
	Head::addMetaData('SUBJECT', SETTINGS_MULTI_LINGUAGES ? Lang::translate('meta_subject') : '');
	Head::addMetaData('RATING', 'general');
	Head::setFavIcon(Media::IMAGE('favicon'));
	Head::setTitle(Context::getPageName());
	Head::toString();

?>