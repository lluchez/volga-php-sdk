<?php
	$form = new Form(Lang::translate('bo.data.form.title'), '+', Array(), 1);
	$form->setLocation(Context::AJAX());
	$form->addButton(Lang::translate('bo.data.form.submitBtn'));
	$form->addHiddenField(KEY_FORM_HIDDEN);
	$form->setId('dataForm');
	foreach($datas as $data)
	{
		$title = Lang::translate('bo.data.'.$data->key.'.title');
		$desc  = Lang::translate('bo.data.'.$data->key.'.desc');
		$field = $form->addFieldset(Lang::translate('bo.data.form.fieldset').$title);
		$props = Array('id' => 'id_'.$data->key, 'value' => $data->value, 'autocomplete' => 'off');
		$input = $data->tiny ? new Input($props) : new Textarea($props);
		$field->addElement(new Element($input, $title, $desc));
	}
	
	echo $form;
	
?>