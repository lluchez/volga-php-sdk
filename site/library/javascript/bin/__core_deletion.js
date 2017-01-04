	/*
	// create a new ID based on the pattern: replace the inner string 'mask' by an int value incremented until the ID is not already used
	// generateNewID('elmt_#', '#') will try to generate: 'elmt_1', elmt_2', ...
	_generateNewID: function(pattern, mask) // mask default value: '#'
	{
		mask = mask || '#';
		mask = '('+mask+')';
		var reg = new RegExp(mask, 'g');
		var index = 1;
		while( $(id = pattern.replace(reg, index++)) ) {}
		return id;
	},
	
	// add the missing field ID/name for each input/textarea/select
	_setIdsAndNames: function(elements)
	{
		for(var i=0; i<elements.length; i++)
		{
			var element = elements[i];
			if( ! element.isButton() )
			{
				if( element.isInputTag() && ! element.hasAttribute('type') )
					element.setAttribute('type', 'text');
				if( element.hasAttribute('id') && !element.hasAttribute('name') ) // add 'name' attribute ?
					element.setAttribute('name', element.isOption() ? element.id.replace(/_[0-9]+$/, '') : element.id);
				else if( !element.hasAttribute('id') && element.hasAttribute('name') ) // add 'id' attribute ?
					element.setAttribute('id', element.isOption() ? this._generateNewID(element.name+'_#') : element.name);
			}
		}
	},
	*/