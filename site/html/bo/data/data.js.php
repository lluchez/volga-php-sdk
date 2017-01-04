var values = new $H({});

function addMessages()
{
<?php
	Lang::addMessages('Validate.isEmail|Validate.isPassword|Validate.isRegExp|Validate.isInteger|Validate.isFloat', "\t");
	Lang::addMessages('bo.data.edit.error.callBack|bo.data.edit.error.update|bo.data.edit.success', "\t");
?>
}


function createForm()
{
	var myForm = new Core.Form('dataForm', 'errorPanel', {
		preventSubmit: function() {
			values.each(function(id){ if( $(id.key) ) { if( ! Element.hasClassName(id.key, 'changed') ) { $(id.key).disabled = 'disabled'; } } });
			if( $$("#dataForm *.changed").size() ) {
				var onSuccess = function(response) {
					if( res = response.responseText.match(/OK\:([^\|]*)\|KO\:([^\n]*)/i) ) {
						var errors = $A(res[2].split(';'));
						values.each(function(id) {
							if( $(id.key) ) {
								if( ! errors.member($(id.key).sname) ) {
									$(id.key).removeClassName('changed');
									values.set(id.key, $F(id.key));
								}
							}
						});
						if( ! res[2].empty() )
							new Core.Popup.Warning(Core.Messages.get('bo.data.edit.error.update'), {sizes: {height: 120}});
						else
							new Core.Popup.Info(Core.Messages.get('bo.data.edit.success'), {sizes: {height: 108}});
					} else {
						new Core.Popup.Error(Core.Messages.get('bo.data.edit.error.callBack'), {sizes: {height: 120}});
					}
				};
				new Core.AjaxForm('dataForm', onSuccess);
			}
			values.each(function(id){ if( $(id.key) ) { $(id.key).disabled = ''; } });
			return true;
		}
	});
<?php
foreach($datas as $data)
{
	$validator = $data->validator;
	if( $data->type == 'int' )
		$validator = 'isInteger';
	elseif( $data->type == 'float' )
		$validator = 'isFloat';
	if( $validator )
	{
?>
	myForm.addValidator('id_<?php echo $data->key; ?>', Core.Validator.<?php echo $validator; ?>, Core.Messages.get('Validate.<?php echo $validator; ?>'));
<?php
	}
}
?>
}



function saveOldValues()
{
<?php
foreach($datas as $data)
{
?>
	values.set('id_<?php echo $data->key; ?>', $F('id_<?php echo $data->key; ?>'));
<?php
}
?>
}


function createHandlers()
{
<?php
foreach($datas as $data)
{
?>
	$('id_<?php echo $data->key; ?>').sname = '<?php echo $data->key; ?>';
	$('id_<?php echo $data->key; ?>').observe('blur', function(event){
		var element = event.element();
		if( $F(element) == values.get(element.id) )
			element.removeClassName('changed');
		else
			element.addClassName('changed');
	});
<?php
}
?>
}


Core.whenReady( function() {
	addMessages();
	createForm();
	saveOldValues();
	createHandlers();
});

