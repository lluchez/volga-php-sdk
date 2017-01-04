/**
 * @info MooTools needs to be included first
 */


Element.implement({
	getTagName:    function() { return this.tagName.toLowerCase();  },
	isDisabled:    function() { state = this.disabled; return (state == 'disabled' || state); },
	isEnabled:     function() { state = this.disabled; return (! state || state == 'enabled'); },
	isInputTag:    function() { return this.getTagName() == 'input'; },
	getInputType:  function() { return this.isInputTag() ? this.type : null ; },
	isOption:      function() { return ( type = this.getInputType() ) ? (type.match(/^(checkbox|radio)$/) ? true : false) : null; },
	isButton:      function() { return ( type = this.getInputType() ) ? (type.match(/^(submit|reset)$/) ? true : false) : null; }
});


Array.implement({
	keyVals: function(key){
		var vals = [];
		for (var i = 0, l = this.length; i < l; i++)
			vals.push(this[i][key]);
		return vals;
	}
});



/************************ Summary ***********************/

/*
var tracer;
var Tracer = new Class {
	initialize(panelID, properties),
	line(), traceLine(),
	trace(object[, property])
};


var CoreError = new Class {
	initialize(errorPanel, panelTitle, className),
	hasError(),
	add(error, sender[, errno]),
	getErrorList([separator]), // returns array if separator is null, a string with 'separator' imploded
	hide(), show(),	clear()
};


var CoreForm = new Class {
	initialize(id, errorPanel, errorPanelTitle, processingPanel, submitOnlyOnce, errorPanelClass),
	reportError(error, sender, code),
	getForm(),
	check(),
	prepareSubmit(),
	preFill()
};


var ToolTip = new Class(
{
	initialize(toolTipId, toolTipContent, options), // options = { posX,posY, followMouse, timeout }
	show(event, message),
	hide(event),
	click(event)
});
*/



/**********************************************************************************************
 *                                       Class Tracer                                         *
 **********************************************************************************************/

var tracer; // declare it as global
var TRACER_NEWLINE = '<#new_line#>';

var Tracer = new Class(
{
	/**
	 * @param panelID ID of the DIV containg outputs
	 * @param properties either properties (if countaining '-') or a class name
	 */
	initialize: function(panelID, properties)
	{
		this.tracerPanel = $(panelID).fade('hide').setStyle('white-space', 'pre');
		properties = properties || {};
		if( $type(properties) == 'string' )
			this.tracerPanel.addClass(properties);
		else
			this.tracerPanel.setStyles(properties);
	},
	
	
	/* Insert a line breaker in the tracer */
	line: function () { this.trace(TRACER_NEWLINE); },
	traceLine: function () { this.trace(TRACER_NEWLINE); },
	
	/* convert an object into a string */
	_objectToString: function(object)
	{
		if( !$defined(object) ) return "undefined";
		else if( object === null )  return "null";
		else if( object === false ) return "false";
		else if( object === true )  return "true";
		
		var msg = object;
		if( object )
		{
			//if( typeof object == "object" && !Object.isArray(object) )
			//	object = $H(object); // to UPDATE
			//if( object.inspect && !Object.isString(object) ) // try to use the 'inspect' function
			//	msg = object.inspect().replace(/^#<(.*)>$/, "$1"); // to UPDATE
		}
		return msg;
	},
	
	/* debug an object : string, hash, array, bool, etc... */
	trace: function (object, property)
	{
		property = ( $defined(property) && $type(property) == 'string' ) ? '<b>'+property+'</b>: '  : '';
		if( div = this.tracerPanel )
		{
			var msg = this._objectToString(object);
			msg = msg.replace(/\\(.{1})/, "$1").replace(/</, "&lt;").replace(/>/, "&gt;").replace("\n", "\\n");
			msg = ( object == TRACER_NEWLINE ) ? '<hr />' : property+msg+"\n";
			div.innerHTML += msg;
			div.fade('show');
		}
	}
});



/**********************************************************************************************
 *                                       Class CoreError                                      *
 **********************************************************************************************/


//function CoreError(container, className)
var CoreError = new Class(
{
	listErrors: null, // array of hash : [{code: xxx, msg: yyy, element: zzz}]
	
	/**
	 * @param errorPanel DIV panel where outputs will be displayed
	 * @param panelTitle title of this error panel
	 * @param className class added to this panel
	 */
	initialize: function(errorPanel, panelTitle, className)
	{
		this.errorPanel = $(errorPanel);
		this.panelTitle = panelTitle || 'Please review the following items';
		this.className = className || 'error-panel';
		this.clear();
	},
	
	clear: function()    { this.listErrors = Array(); this.hide(); },
	hasError: function() { return( !! this.listErrors.length ); },
	hide: function()     {  this.errorPanel.fade('hide'); },
	
	show: function()
	{
		var innerContent = '<span>'+this.panelTitle+'</span><ul class="' + this.className + '">';
		for(var i=0, count=this.listErrors.length; i<count; i++)
		{
			var error = this.listErrors[i];
			var label = '<label' + ( error.element ? ' for="' + error.element.id + '"' : '') + '>';
			innerContent += '<li>' + label + error.msg + '</label></li>';
			innerContent += '<!-- corresponding error code: ' + error.code + ' -->';
		}
		this.errorPanel.innerHTML = innerContent + '</ul>';
		if( this.hasError() )
			this.errorPanel.fade('show');
	},
	
	add: function(error, sender, errno)
	{
		this.listErrors.push({code: errno || 0, msg: error, element: $(sender)});
	},
	
	getErrorList: function(separator)
	{
		// var codes = $A(this.listErrors).pluck('code'); // to UPDATE
		//return separator ? codes.join(separator) : codes;
		// return $A(this.listErrors).pluck('code').join(separator || ';');
		codes = this.listErrors.keyVals('code');
		return codes.join(separator || ';');
	}
});




/**********************************************************************************************
 *                                       Class CoreForm                                       *
 **********************************************************************************************/

var CoreForm = new Class(
{
	
	initialize: function(id, errorPanel, errorPanelTitle, processingPanel, submitOnlyOnce, errorPanelClass)
	{
		// form ID
		this.formID = $(id);
		// errors
		this.errors = new CoreError(errorPanel, errorPanelTitle, errorPanelClass);
		// allow to submit only once the form ?
		this.submitOnlyOnce = $defined(submitOnlyOnce) ? submitOnlyOnce : true;
		// the form has not been submited yet
		this.isAlreadySubmited = false;
		// display the "form submition in process" image
		this.processingPanel = $(processingPanel);
		
		// init the fields
		this._init();
	},
	
	// add an error
	reportError: function(error, sender, code) { this.errors.add(error, sender, code); },
	// get form Object
	getForm: function() { return this.formID; },
	// actions on the form, to be overriden
	check: function() {},
	prepareSubmit: function() {},
	preFill: function() {},
	
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
	
	// if the form contains input<file>, set the method to 'post' and set the enctype mode
	_setMethodAndEnctype: function(inputs)
	{
		inputs = inputs || this.getForm().getElementsByTagName('input');
		for(var i=0, length = inputs.length, containsFile = false; i<length && !containsFile; i++  )
			containsFile = ( inputs[i].type == 'file' )
		if( containsFile )
		{
			this.formID.method = "post"; // check in the book for lower/upper case...
			this.formID.enctype = "multipart/form-data";
		}
	},
	
	// add the missing field ID/name for each field and add a submit-observer on the form
	_init: function()
	{
		var inputs = this.getForm().getElementsByTagName('input');
		this._setIdsAndNames(inputs);
		this._setIdsAndNames(this.getForm().getElementsByTagName('textarea'));
		this._setIdsAndNames(this.getForm().getElementsByTagName('select'));
		this._setMethodAndEnctype(inputs);
		var me = this;
		this.getForm().addEvent('submit', function(event){ me._submit(event); });
	},
	
	_cancelSubmit: function (event) { event.stop(); event.stopPropagation(); return false; },

	// process "form submittion": check if not submited, check for error and then submit
	_submit: function (event)
	{
		if ( this.submitOnlyOnce && this.isAlreadySubmited )
			return this._cancelSubmit(event);
		this.errors.clear();
		this.check();
		if( this.errors.hasError() )
		{
			this.errors.show();
			return this._cancelSubmit(event);
		}
		else
		{
			this.prepareSubmit();
			this.isAlreadySubmited = true;
			if( this.processingPanel ) { this.processingPanel.show(); this.getForm().fade('hide'); }
		}
		return true;
	}
	
});


/**********************************************************************************************
 *                                       Class CoreForm                                       *
 **********************************************************************************************/

var ToolTip = new Class(
{
	initialize: function(toolTipId, toolTipContent, options)
	{
		this.panelID = $(toolTipId);
		this.contentID = $(toolTipContent) || $(toolTipId);
		this.options = {
			posX: 20,
			posY: 20,
			followMouse: false,
			timeout: 500
		};
    $extend(this.options, options || { });
		this.isClicked = false;
		this.timeouts = Array();
	},
	
	// adjust the tooltip position and set the tooltip visible
	_display: function(event)
	{
		if ( this.options.followMouse || ! $(this.panelID).visible() )
		{
			var mouse = event.page;
			this.panelID.setStyles({top: this.options.posY+mouse.y, left: this.options.posX+mouse.x});
		}
		this.panelID.fade('show');
	},
	
	// clear all timeouts
	_clearTimeOut: function()
	{
		while( timeoutId = this.timeouts.pop() )
			clearTimeout(timeoutId);
	},
	
	// display the tooltip
	show: function(event, message)
	{
		this._clearTimeOut();
		this._display(event);
		if( message )
			$(this.contentID).innerHTML = message;
	},
	
	// hide it
	hide: function(event)
	{
		if( ! this.isClicked )
		{
			var self = this;
			this.timeouts.push(setTimeout(function() { self.panelID.fade('hide'); }, this.options.timeout));
		}
	},
	
	// cliking toogle the state: odd clics: locked-visible, even clics: basic tooltip behaviour
	click: function(event)
	{
		if( this.isClicked = ! this.isClicked )
		{
			this._clearTimeOut();
			this._display(event);
		}
		else
		{
			this.panelID.fade('hide');
		}
	}
	
});

