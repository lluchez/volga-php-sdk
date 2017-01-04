/**
 *
 */

/**********************************************************************************************
 *                  Prototype Upgrade: Add some functions to Element objects                  *
 **********************************************************************************************/
// /**  @info Prototype needs to be included first */

// replace normal space by '&nbsp;' (unsecable space) when they are before a ponctuation mark
String.prototype.unsecableSpace = function() { // then punctuations are merged with the space mark.
	return this.replace(/ ([\.\,\!\?]{1})/g, "&nbsp;$1");
}


Element.addMethods({ // methods available for all HTMLObjects 
	getTagName:    function(element) { return element.tagName.toLowerCase();  },
	isDisabled:    function(element) { state = element.disabled; return (state == 'disabled' || state); },
	isEnabled:     function(element) { state = element.disabled; return (! state || state == 'enabled'); },
	isInputTag:    function(element) { return element.getTagName() == 'input'; },
	getInputType:  function(element) { return element.isInputTag() ? element.type : null ; },
	isOption:      function(element) { return ( type = element.getInputType() ) ? (type.match(/^(checkbox|radio)$/) ? true : false) : null; },
	isButton:      function(element) { return ( type = element.getInputType() ) ? (type.match(/^(submit|reset)$/) ? true : false) : null; },
	remove:        function(element) { element.parentNode.removeChild(element); },
	setVisible:    function(element, state) { if( state ) Element.show(element); else Element.hide(element); },
	_returnSizes:  function(w, h, r) { var res = [w, h]; res.width = w; res.height = h; if(!Object.isUndefined(r)){res.ratio=r;} return res; },
	addValidator:  function(element, validator) {
		if( element = $(element) ) {
			element.validator = validator;
			element.observe('blur', function(event) { event.element().validate(); });
		}
	},
	removeValidator: function(element) {
		if( (element = $(element)) && element.validator ) {
			element.stopObserving('blur');
			element.validator = null;
			this.validate(element);
		}
	},
	validate: function(element) {
		element = element || this;
		if( ! element.visible() )
			return;
		var parent = $('row_'+element.id);
		if( ! parent )
			throw('Error in Element.Valide(): unable to find the parent element !');
		if( element.validator ) {
			var res = eval('element.validator(element);');
			if( res )
				$(parent).removeClassName('error');
			else
				$(parent).addClassName('error');
			return res;
		} else {
			$(parent).removeClassName('error');
		}
	},
	setAbsolutePosition: function(element, x, y) {
		if( element = $(element) ) {
			var offset = ( typeof x == "object" && typeof x.top != "undefined" ) ? x : 
				offset = Element._returnOffset(parseInt(x,10), parseInt(y,10));
			var parent = element.parentNode;
			while( parent) {
				if( Object.isFunction(parent.getStyle) && parent.getStyle('position') == 'relative' ) {
					var parentOffset = parent.positionedOffset();
					offset = Element._returnOffset(offset.left-parentOffset.left, offset.top-parentOffset.top);
				}
				parent = parent.parentNode;
			}
			element.setStyle({top: offset.top+'px', left: offset.left+'px'});
		}
	}
});



/**********************************************************************************************
 *                              Volga Core really starts here !!!                             *
 **********************************************************************************************/

// Will contain every object linked to this core:
// Core.Validator, Core.Localization, Core.Messages, Core.Ajax, Core.ErrorPanel, Core.Form, Core.ToolTip, Core.Popup (.Error/Warning/Info)
var Core = {
	isInit: false,
	Options: {},
	init: function() {}, // overridden by 'init_all.js'. Will set Core.isInit to true
	_whenReady: function(cb) {
		if(Core.isInit){ try{cb();} catch(e){this.reportError(e);} }
		else{ setTimeout( function(){Core._whenReady(cb)}, 20 ); }
	},
	whenReady: function(init) { // IE FIX (IE can run another Script before Core is init, so we need to wait for it under IE)
		document.observe('dom:loaded', function(){ 
			if(Prototype.Browser.IE){ Core._whenReady(init); } 
			else{ try{init();} catch(e){Core.reportError(e);} }
		});
	},
	reportError: function(e) {
		if(!Core.isLocalContext) throw(e);
		if( typeof console != 'undefined' ) console.log(e);
		else { var txt = "Error raised\n----------------\n";
			$H(e).each(function(elt) { txt += elt.key+": "+elt.value+"\n"; });
			alert(txt);
		}
	},
	defErrorPanelName: 'errorPanel',
	raiseError: function(errorObj, className, parameter) {
		error = Object.extend({className: className || null}, errorObj);
		if( typeof parameter != 'undefined' )
			Object.extend(error, {parameter: parameter});
		throw(error);
	},
	_elementDoesNotExistError:  {name: 'ElementDoesNotExistError',  message: 'The specified DOM element does not exist, but is required to operate'},
	_functionDoesNotExistError: {name: 'FunctionDoesNotExistError', message: 'The specified function does not exist, but is required to operate'},
	_wrongParameterError:       {name: 'WrongParameterError', message: 'The specified parameter is not as expected'}
};


/**********************************************************************************************
 *                                    Namespace Validator                                     *
 **********************************************************************************************/

var Validator =
{
	_email:     /^[A-Za-z0-9\-_\.]+@[A-Za-z0-9\-_\.]+\.[a-z]{2,5}$/,
	_password:  /^([a-z0-9\xC0-\xFF\@\!_\-\&\.\,\?\#\ ]{8,20})$/i,
	check:      function(text, ereg) { return ereg.test(text); },
	isEmpty:    function(text) { return text.blank(); },
	isNotEmpty: function(text) { return ! Validator.isEmpty(text); },
	isEmail:    function(text) { return Validator.check(text, Validator._email); },
	isPassword: function(text) { return Validator.check(text, Validator._password); },
	isValidKey: function(text) { return Validator.check(text, /^([a-z0-9\xC0-\xFF\@_\-\.]{4,50})$/i); },
	isInteger:  function(text) { return Validator.check(text, /^(\d+)$/); },
	isFloat:    function(text) { return Validator.check(text, /^(\d+)(,(\d+))?$/); },
	isDateUS:   function(text) { return Validator.check(text, /^(0\d{1}|10|11|12)(?:\/|-)((?:(?:0|1|2)\d{1})|30|31)(?:\/|-)(\d{4})$/); },
	//isDateUS:   function(text) { return Validator.check(text, /^(\d{4})(?:\/|-)(0\d{1}|10|11|12)(?:\/|-)((?:(?:0|1|2)\d{1})|30|31)$/); },
	isDateEU:   function(text) { return Validator.check(text, /^((?:(?:0|1|2)\d{1})|30|31)(?:\/|-|\.)(0\d{1}|10|11|12)(?:\/|-|\.)(\d{4})$/); },
	//isDateJP:   function(text) { return Validator.check(text, /^(\d{4})-(0\d{1}|10|11|12)-((?:(?:0|1|2)\d{1})|30|31)$/); },
	isRegExp:   function(text) { try{ new RegExp(text); return true; } catch(err){ return false; } }
};


Core.Validator = {
	check:      function(field, validator) { if($(field)){return validator($F(field));} else{Core.raiseError(Core._elementDoesNotExistError, 'Core.Validator');} },
	isEmpty:    function(field) { return Core.Validator.check(field, Validator.isEmpty); },
	isNotEmpty: function(field) { return Core.Validator.check(field, Validator.isNotEmpty); },
	isEmail:    function(field) { return Core.Validator.check(field, Validator.isEmail); },
	isPassword: function(field) { return Core.Validator.check(field, Validator.isPassword); },
	isValidKey: function(field) { return Core.Validator.check(field, Validator.isValidKey); },
	isInteger:  function(field) { return Core.Validator.check(field, Validator.isInteger); },
	isFloat:    function(field) { return Core.Validator.check(field, Validator.isFloat); },
	isDateUS:   function(field) { return Core.Validator.check(field, Validator.isDateUS); },
	isDateFR:   function(field) { return Core.Validator.check(field, Validator.isDateFR); },
	//isDateJP:   function(field) { return Core.Validator.check(field, Validator.isDateJP); },
	isRegExp:   function(field) { return Core.Validator.check(field, Validator.isRegExp); }
};


/**********************************************************************************************
 *                       Class Core.Localization / Object Core.Messages                       *
 **********************************************************************************************/

Core.Localization = Class.create({
	listMessages: new $H({}),
	initialize: function(){},
	add: function(key, value) {
		this.listMessages.set(key, value);
	},
	get: function(key) {
		if( this.listMessages.keys().indexOf(key) != -1 )
			return this.listMessages.get(key);
		else // Debug: // str = ""; lst = this.listMessages.keys(); for(k in lst){ if( (k+"").match(/^(\d+)$/) ) str += lst[k]+"\n";} alert(str);
			return "%Key "+key+" not found%";
	}
});
Core.Messages = new Core.Localization();



/**********************************************************************************************
 *                                       Class Core.Ajax                                      *
 **********************************************************************************************/

Core.Ajax = Class.create({
	initialize: function(href, params, options) {
		if( typeof params == 'object' ) {
			if( params.serialize )
				params = params.serialize(true);
			//if( ! params.toQueryString ) params = $H(params); params = params.toQueryString(); // Useless: done automatically ...
		}
		
		this.options = {
			method: 'post',
			encoding: Core.Options.AjaxEncoding,
			parameters: params
		};
		
		// handle: onSuccess, onFailure, onComplete, onCreate
		if( typeof options == 'function' )
			options = {onSuccess: options};
		Object.extend(this.options, options || {});
		new Ajax.Request(href, this.options);
	}
});

Core.AjaxForm = Class.create({
	initialize: function(form, options) {
		form = $(form);
		if( (! form) || (! form.getTagName) || (form.getTagName() != 'form') )
			Core.raiseError(Core._elementDoesNotExistError, 'Core.AjaxForm');
		var params = form.serialize(true);
		new Core.Ajax(form.action, params, options);
	}
});

/**********************************************************************************************
 *                                   Class Core.ErrorPanel                                    *
 **********************************************************************************************/

Core.ErrorPanel = Class.create({
	listErrors: null, // array of hash : [{msg: yyy, element: zzz}]
	errPanelObj: null,
	
	initialize: function(errorPanel, options, form) {
		this.errPanelObj = $(errorPanel || Core.defErrorPanelName);
		if(! this.errPanelObj )
			Core.raiseError(Core._elementDoesNotExistError, 'Core.ErrorPanel');
		
		this.options = {
			panelTitle: Core.Messages.get('Validate.ErrorPanel.Title'),
			closeBtnId: 'close_'+this.errPanelObj.id,
			className:  'error-panel',
			onDisplay:  function() {},
			onHide:     function() {},
			element:    this
		};
		Object.extend(this.options, options || {});
		if( form )
			this.options.form = form;
		
		this.errPanelObj.addClassName(this.options.className);
		this.clear();
	},
	
	clear: function()    { this.listErrors = Array(); this.hide(); },
	countError: function() { return( this.listErrors.length ); },
	hasError: function() { return( !! this.listErrors.length ); },
	
	hide: function() {
		$(this.errPanelObj).hide();
		if( closeBtn = $(this.options.closeBtnId) )
			closeBtn.stopObserving();
		this.errPanelObj.innerHTML = '';
		this.options.onHide();
	},
	
	show: function() {
		if( this.hasError() ) {
			this.errPanelObj.innerHTML = '<span>'+this.options.panelTitle+'</span><div class="close" id="'+this.options.closeBtnId+'"></div><ul></ul>';
			var ul = this.errPanelObj.getElementsByTagName('ul')[0];
			$A(this.listErrors).each( function(error) {
				li = document.createElement('li');
				label = document.createElement('label');
				label.innerHTML = error.msg;
				if( error.element ) { // create the <label /> tag and add the event listener
					label.setAttribute('for', error.element.id);
					label.setAttribute('title', Core.Messages.get('Validate.ErrorPanel.ErrorAlt'));
					label.className = 'info';
					$(label).observe('click', function(event) { $(error.element).scrollTo(); });
				}
				li.appendChild(label);
				ul.appendChild(li);
			});
			
			var that = this;
			$(this.options.closeBtnId).observe('click', function(event) { that.hide(); });
			this.errPanelObj.show().scrollTo();
			this.options.onDisplay();
		}
	},
	
	add: function(error, sender) {
		this.listErrors.push({msg: error, element: $(sender)});
	},
	
	getErrorList: function(separator) {
		var codes = $A(this.listErrors).pluck('code');
		return separator ? codes.join(separator) : codes;
	},
	
	hideError: function(element) {
		var keepPanel = false;
		$$('#'+this.errPanelObj.id+' li label').each(function(labelTag) {
			li = labelTag.parentNode;
			if( labelTag.getAttribute('for') == element.id )
				/*li.parentNode.removeChild(li);*/ Element.remove(li);
			else
				keepPanel = true;
		});
		if(! keepPanel)
			this.hide();
		else
			this.options.onHide();
	}
});



/**********************************************************************************************
 *                                      Class Core.Form                                       *
 **********************************************************************************************/

Core.Form = Class.create({
	initialize: function(id, errorPanel, options) {
		var that = this;
		
		// form ID
		if( ! (this.formID = $(id)) )
			Core.raiseError(Core._elementDoesNotExistError, 'Core.Form');
		
		// options
		this.options = {
			submitOnlyOnce: true,
			processingPanel: null,
			ajaxCallback: null,
			validators: Array(),
			// functions that can be ovveridden
			check: Prototype.emptyFunction,
			prepareSubmit: Prototype.emptyFunction,
			preFill: Prototype.emptyFunction,
			waitForResponse: Prototype.emptyFunction, // action between processing panel and the AjaxCall
			preventSubmit: function(){ return false; },
			element: this
		};
		Object.extend(this.options, options || {});
		
		// errorPanel
		errPanel = {};
		if( Object.isUndefined(errorPanel) )
			errorPanel = Core.defErrorPanelName;
		if( errorPanel ) {
			if( typeof errorPanel == "string" )
				errPanel = {element: errorPanel, options: {}};
			else if( typeof errorPanel == "object" )
				errPanel = errorPanel;
			
			if( errPanel.element ) {
				this.errorPanel = new Core.ErrorPanel(errPanel.element, errPanel.options, this);
			}
		}
		
		this.options.processingPanel = $(this.options.processingPanel || null);
		
		// the form has not been submited yet
		this.isAlreadySubmited = false;
		
		// list of elements who needs to be validated
		this.elementsUnderControl = $A();
		if( this.errorPanel ) {
			this.options.validators.each( function(v) {
				if( typeof v == "object" ) {
					var element = (v.element || null), fct = (v.validator || v.fct), text = (v.text || v.msg);
					if( element && (typeof fct == 'function' || typeof fct == 'string') )
						that.addValidator(element, fct, text);
					else
						Core.raiseError(Core._functionDoesNotExistError, 'Core.Form', 'this.options.validators');
				}
			});
		}
		
		// init the fields
		Event.observe(this.getForm(), 'submit', function(event){ that._submit(event); } );
		this.options.preFill();
	},
	
	showErrorPanel: function() { this.errorPanel.show(); },
	showForm: function() {
		if(this.options.processingPanel)
			this.options.processingPanel.hide();
		this.getForm().show();
	},
	// get form Object
	getForm: function() { return this.formID; },
	
	// form validation will be an ajax call
	setAjaxProperties: function(ajaxCallback) { this.options.ajaxCallback = ajaxCallback; return this; },
	
	// add an error
	reportError: function(error, sender, highlight) {
		if( this.errorPanel ) {
			this.errorPanel.add(error, sender);
			if( highlight && $(sender)) {
				if( row = $('row_'+$(sender).id) ) {
					if( ! row.addClassName('error') )
						row.addClassName('error');
				}
			}
		}
	},

	// Now done by PHP classes
	// _setMethodAndEnctype: function() { if( Element.select(this.formID, 'input[type="file"]').size() ) { Object.extend(this.formID, { method: 'post', enctype: 'multipart/form-data'}); } },
	
	_cancelSubmit: function (event) { Event.stop(event); return false; },

	// process "form submittion": check if not submited, check for error and then submit
	_submit: function (event) {
		// check if the form is not already submitted
		if ( this.options.submitOnlyOnce && this.isAlreadySubmited )
			return this._cancelSubmit(event);
		if( this.errorPanel )
			this.errorPanel.clear();
		
		// check validators and manual/other errors
		this._validatorsCheck();
		this.options.check();
		
		if( this.errorPanel && (this.errorPanel.hasError() || this.options.preventSubmit()) ) {
			this.showErrorPanel();
			return this._cancelSubmit(event);
		} else {
			this.options.prepareSubmit();
			this.isAlreadySubmited = true;
			if( this.options.processingPanel ) { this.getForm().hide(); this.options.processingPanel.show(); }
			this.options.waitForResponse();
			// Form sending Ajax Call
			if( this.options.ajaxCallback ) {
				new Core.AjaxForm(this.formID, this.options.ajaxCallback);
				return this._cancelSubmit(event);
			}
		}
		return true;
	},
	
	_validatorsCheck: function() {
		var that = this;
		this.elementsUnderControl.each(function(item) {
			if( ! item.validate() )
				that.reportError(item.errorMessage, item);
		});
	},
	
	addValidator: function(element, validator, text) {
		if( this.errorPanel && (element = $(element)) ) {
			this.removeValidator(element);
			if( ! text && (typeof validator == 'string') )
				text = Core.Messages.get('Validate.'+validator);
			if( typeof validator == 'string' )
				validator = eval('Core.Validator.'+validator);
			element.addValidator(validator);
			element.errorMessage = text;
			this.elementsUnderControl.push(element);
		}
		return this; // to chain methods
	},
	
	removeValidator: function(element) {
		if( this.errorPanel && (element = $(element)) ) {
			if( this.elementsUnderControl.member(element) ) {
				this.elementsUnderControl = this.elementsUnderControl.without(element);
				Element.removeValidator(element);
				this.errorPanel.hideError(element);
			}
		}
		return this; // to chain methods
	}
});



/*********************************************************************************************
 *                                     Class Core.ToolTip                                    *
 *********************************************************************************************/

Core.ToolTip = Class.create( {
	initialize: function(toolTipId, options) {
		var that = this;
		if( ! (this.layoutID = $(toolTipId)) )
			Core.raiseError(Core._elementDoesNotExistError, 'Core.ToolTip');
		this.options = {
			posX: 20,
			posY: 20,
			followMouse: false,
			timeout: 500,
			contentID: this.layoutID,
			lockable: false, // if click loack the tooltip
			show: function() {},
			hide: function() {},
			innerText: '', // default text to display
			element: that  // internal
		};
    Object.extend(this.options, options || { });
		if( !(this.options.contentID = $(this.options.contentID)) )
			Core.raiseError(Core._elementDoesNotExistError, 'Core.ToolTip');
		
		this.isClicked = false;
		this.isOverShowingElement = false;
		
		if( !this.layoutID.timeouts ) // maybe this tooltip is already linked to another object
			this.layoutID.timeouts = Array(); // associate callbacks to the tooltip itself
		
		this.layoutID.observe('mouseover', this._clearTimeOut.bindAsEventListener(this));
		this.layoutID.observe('mouseout', this.hide.bindAsEventListener(this));
		this.documentEvent = this._display.bindAsEventListener(this);
		document.observe('mousemove', this.documentEvent);
	},
	
	// adjust the tooltip position and set the tooltip visible
	_display: function(event) {
		if( this.isOverShowingElement ) {
			if ( this.options.followMouse || ! this.layoutID.visible() ) {
				var mouse = Event.pointer(event);
				//this.layoutID.setStyle({top: (this.options.posY+mouse.y)+'px', left: (this.options.posX+mouse.x)+'px'});
				this.layoutID.setAbsolutePosition(this.options.posX+mouse.x, this.options.posY+mouse.y);
			}
			this.layoutID.show();
		}
	},
	
	// clear all timeouts
	_clearTimeOut: function() {
		while( timeoutId = this.layoutID.timeouts.pop() )
			clearTimeout(timeoutId);
	},
	
	// display the tooltip
	show: function(event, message) {
		this.isOverShowingElement = true;
		this._clearTimeOut();
		this._display(event);
		if( (message || (message = this.options.innerText)) )
			$(this.options.contentID).innerHTML = message;
		this.options.show();
	},
	
	// hide it
	hide: function(event) {
		this.isOverShowingElement = false;
		if( ! this.isClicked )
			this.layoutID.timeouts.push(setTimeout(this._hide.bind(this), this.options.timeout));
	},
	
	_hide: function() {
		this.layoutID.hide();
		this.options.hide();
	},
	
	// cliking toogle the state: odd clics: locked-visible, even clics: basic tooltip behaviour
	click: function(event) {
		if( this.options.lockable ) {
			if( (this.isClicked = !this.isClicked) ) { // toggle state
				this._clearTimeOut();
				this._display(event);
			} else {
				this.layoutID.hide();
			}
		}
	},
	
	destroy: function() {
		if( this.documentEvent && this.layoutID ) {
			document.stopObserving('mousemouve', this.documentEvent);
			this.documentEvent = null;
			this._clearTimeOut();
			this.layoutID.stopObserving();
			this.layoutID.hide();
			this.layoutID = null;
		}
	}
	
});


Core.ToolTipObject = Class.create(Core.ToolTip, {
	initialize: function($super, toolTipId, objectID, options) {
		this.options = options || {};
		this.observedObject = $(objectID);
		$super(toolTipId, this.options);
		
		this.observedObject.observe('mouseover', this.show.bindAsEventListener(this));
		this.observedObject.observe('mouseout',  this.hide.bindAsEventListener(this));
		if( this.options.lockable )
			this.observedObject.observe('click',  this.click.bindAsEventListener(this));
	}
});

/**********************************************************************************************
 *                                      Class Core.Popup                                      *
 **********************************************************************************************/

Core.Popup = Class.create({
	whenDone: Prototype.emptyFunction,
	
	initialize: function(content, options) {
		var that = this;
		this.content = content.unsecableSpace();
		this.options = {
			title: Core.Messages.get('Popup.Title.Default'),
			btnOkText: Core.Messages.get('Popup.DefaultBtnText'),
			btnCloseText: Core.Messages.get('Popup.CloseTitle'),
			isDraggable: (typeof Draggable != "undefined" ), // Scriptaculous && Draggable;
			hasBeenDragged: false,
			popupClass: '',
			type: null,
			divOverlayID: 'popupOverlay',
			divLayoutID: 'popup',
			divActionsID: 'pop-bottom',
			btnCloseID: 'popupClose',
			btnOkID: 'popupBtn',
			setPopupInnerHTML: function(content) {
				return '<div class="title"><div class="bl"></div><div class="dragger"><h1>'+this.title
		     +'</h1></div><div class="br">'+(this.btnCloseID ? ('<div id="'+this.btnCloseID
				 +'"></div>') : '')+'</div></div><div class="content">'+''+this.setContent(content)
				 +'</div><div id="'+this.divActionsID+'">'+this.setActionsContent()+'</div>';
			},
			setContent: function(content) { return content; },
			setActionsContent: function() { return this.btnOkID ? ('<input id="'+this.btnOkID+'" type="button" />') : ''; },
			hide: Prototype.emptyFunction,
			show: Prototype.emptyFunction,
			openOnCreation: true,
			sizes: {width: 300, height: 108},
			keyUpEvent: function(event) {},
			element: that // internal
		};
		
		options = options || {};
		if( typeof options == 'string' )
			options = {type: options};
		if( ! options.popupClass )
			options.popupClass = '';
		
		// skinned and predefined popup
		var className = null;
		if( options.type ) {
			if( options.type.match(/err(or)?/i) )
				className = 'error';
			else if ( options.type.match(/(warning|alert)/i) )
				className = 'warning';
			else if ( options.type.match(/info(rmation)?/i) )
				className = 'information';
			if( className ) {
				var skins = {title: Core.Messages.get('Popup.Title.'+className.capitalize())};
				options.popupClass += ' '+className + ' hasIcon';
				Object.extend(skins, options);
				options = skins;
			}
		}
		var sizes = Object.extend(this.options.sizes, options.sizes || {});
		Object.extend(this.options, options);
		this.options.sizes = sizes;
		if( this.options.isDraggable )
			this.options.popupClass += ' drag';
		
		if( this.options.openOnCreation )
			this.show();
	},
	
	show: function() {
		// create the background overlay
		this.divOverlay = document.createElement("div");
		this.divOverlay.id = this.options.divOverlayID;
		$(this.divOverlay).setOpacity(0.6);
		document.body.appendChild(this.divOverlay);
		
		// set the content of the popup
		this.divLayout = document.createElement("div");
		this.divLayout.id = this.options.divLayoutID;
		$(this.divLayout).addClassName(this.options.popupClass);
		this.divLayout.innerHTML = this.options.setPopupInnerHTML(this.content);
		document.body.appendChild(this.divLayout);
		
		// Ok button (top-right)
		if( this.options.btnOkID ) {
			var btnOk = $(this.options.btnOkID);
			btnOk.value = this.options.btnOkText;
			this.addEvents(btnOk);
		} else { if(btnOk) btnOk.remove(); }
		
		// Close button (bottom)
		if( this.options.btnCloseID ) {
			var btnClose = $(this.options.btnCloseID);
			btnClose.title = this.options.btnCloseText;
			this.addEvents(btnClose);
		}
		
		// Set the Popup draggable
		if( this.options.isDraggable ) {
			var that = this, clone = null, ghost_opacity = 0.4;
			this.draggable = new Draggable(this.divLayout, {
				handle: this.divLayout.down('div.title div h1'),
				zindex: 10002,
				ghosting: true,
				starteffect: function(element) {
					that.options.hasBeenDragged = true;
					shadow = Element.previous(element, 0); shadow.setOpacity(ghost_opacity);
					clone = shadow.cloneNode(true); element.parentNode.insertBefore(clone, element); shadow.hide();
				},
				endeffect: function(element) {
					new Effect.Opacity(clone, {duration:0.4, from:ghost_opacity, to:0});
					setTimeout(function(){ Element.remove(clone); }, 500);
				}
			});
		}
		
		this.options.show();
		
		// Add event: Escape key close the popup
		this.evtKeyUp = this.onKeyUp.bindAsEventListener(this)
		document.observe('keyup', this.evtKeyUp);
		
		// Update popup styles(including sizes) and move viewport to the created popup
		this.setStyles();
	},
	
	onKeyUp: function(event) {
		if( event.keyCode == Event.KEY_ESC ) { this.hide(); Event.stop(event); return; }
		this.options.keyUpEvent(event);
	},
	
	hide: function() {
		// terminate
		this.whenDone(); 
		
		// remove event handlers
		if( this.draggable )
			this.draggable.destroy();
		if( this.options.btnOkID )    { Element.stopObserving(this.options.btnOkID); }
		if( this.options.btnCloseID ) { Element.stopObserving(this.options.btnCloseID); }
		document.stopObserving('keyup', this.evtKeyUp);
		
		// additional hide() function (merge it with whenDone() ?)/* ******** TO DO ********** */
		this.options.hide();
		
		// remove/delete Objects
		this.evtKeyUp = null;
		document.body.removeChild(this.divOverlay);
		document.body.removeChild(this.divLayout);
		this.divOverlay = null;
		this.divLayout = null;
	},
	
	setText: function(text) {
		this.content = text.unsecableSpace();
		if( this.divLayout )
			$(this.divLayout).select("div.content")[0].innerHTML = this.content;
	},
	
	setStyles: function(sizes) {
		Object.extend(this.options.sizes, sizes || {});
		if( this.divLayout ) {
			if( typeof this.options.sizes.width == 'number' )
				this.options.sizes.width = this.options.sizes.width+'px';
			if( typeof this.options.sizes.height == 'number' )
				this.options.sizes.height = this.options.sizes.height+'px';
			if( ! this.options.hasBeenDragged ) {
				if( sizes = this._allSizesInPx() ) {
					var vp = document.viewport, vpScroll = vp.getScrollOffsets(), vpDim = vp.getDimensions();
					this.options.sizes.top = (vpScroll.top+(vpDim.height-sizes.height)/2)+'px';
					this.options.sizes.left = (vpScroll.left+(vpDim.width-sizes.width)/2)+'px';
					this.options.sizes.marginLeft = null;
					this.options.sizes.marginTop = null;
				} else {
					this.options.sizes.marginLeft = '-'+this._diviseSize(this.options.sizes.width);
					this.options.sizes.marginTop = '-'+this._diviseSize(this.options.sizes.height);
					this.options.sizes.top = '50%';
					this.options.sizes.left = '50%';
					this.scrollToPopup();
				}
				$(this.divLayout).setStyle(this.options.sizes);
			} else {
				Object.extend($(this.divLayout).style, {width: this.options.sizes.width, height: this.options.sizes.height});
			}
			
		}
	},
	
	addEvents: function(obj, action) {
		if( obj = $(obj) ) {
			action = action || this.hide;
			obj.observe('click', action.bind(this));
			obj.observe('mouseover', function(event) { event.element().addClassName('over'); });
			obj.observe('mouseout', function(event) { event.element().removeClassName('over'); });
		}
	},
	
	_matchDimension: function(property) {
		return (res = property.match(/(\d+(?:\.\d+)?)\s*([a-z]*)$/)) ? res : Array('0px', '0', 'px');
	},
	
	_diviseSize: function(cssSize) {
		var res = this._matchDimension(cssSize);
		return (parseInt(res[1], 10) / 2) + '' + (res[2] ? res[2] : 'px');
	},
	
	// if the height or the width is given, automatically compute the margin-left/top
	_allSizesInPx: function() {
		var w = this._matchDimension(this.options.sizes.width);
		var h = this._matchDimension(this.options.sizes.height);
		var res = (w[2] == 'px' || !w[2]) && (h[2] == 'px' || !h[2])
		return res ? Element._returnSizes(parseInt(w[1],10), parseInt(h[1],10)) : null;
	},
	
	scrollToPopup: function() {
		if( this.divLayout ) {
			var docOff = document.viewport.getScrollOffsets();
			if( docOff.top ) { // only when the viewport has been scrolled down
				window.scrollTo(0, 0);
			}
		}
	}
});


// --- Easy Popups ---

Core.Popup.Error   = Class.create(Core.Popup, { initialize: function($super, content, options) { 
	options = options || {}; Object.extend(options, {type: 'error'}); $super(content, options) } });
Core.Popup.Warning = Class.create(Core.Popup, { initialize: function($super, content, options) { 
	options = options || {}; Object.extend(options, {type: 'warning'}); $super(content, options) } });
Core.Popup.Info    = Class.create(Core.Popup, { initialize: function($super, content, options) { 
	options = options || {}; Object.extend(options, {type: 'information'}); $super(content, options) } });


Object.extend(Core, {
	Image: function(url) { var img = new Image(); img.src = url; return img; },
	isImageLoaded: function(image) {
		if( typeof image == 'string' )
			image = Core.Image(image);
		return image.complete;
	},
	preloadImage_CB: function(image, callback) {
		var that = this;
		if( image.complete ) {
			if( typeof callback == 'function' )
				callback(image);
			else if( typeof callback == 'string' )
				eval(callback);
		} else {
			setTimeout(function(){that.preloadImage_CB.bind(that, image, callback);}, 20);
		}
	},
	preloadImage: function(url, callback) {
		if( typeof url != 'string' ) return;
		image = Core.Image(url);
		Core.preloadImage_CB(image, callback);
	}
});
	

/*********************************************************************************************
 *                                   Class Core.ThumbViewer                                  *
 *********************************************************************************************/

Core.Thumbnails = Class.create(Core.Popup, {
	initialize: function($super, thumbnails, options) {
		var that = this;
		if( (! Object.isArray(thumbnails)) || (thumbnails.length == 0) )
			Core.raiseError(Core._wrongParameterError, 'Core.Thumbnails', 'thumbnails');
		this.images = Array();
		thumbnails.each( function(elt) {
			that.images.push(Object.isString(elt) ? {url: elt, title: elt} : elt);
		});
		
		this.options = {
			element: that, // internal
			index: 0, // internal
			nbImages: this.images.length, // internal
			btnOkID: false, // internal
			smoothResize: (typeof Effect != 'undefined'),
			preload: false,
			title: Core.Messages.get('Thumbnails.title'),
			prevText: Core.Messages.get('Thumbnails.previous'),
			nextText: Core.Messages.get('Thumbnails.next'),
			btnCloseText: Core.Messages.get('Thumbnails.close'),
			popupClass: 'normal',
			loadingID: 'thumb_loading',
			imageID: 'thumb_image',
			nextImgID: 'thumb_next',
			prevImgID: 'thumb_previous',
			labelID: 'thumb_label',
			loadingWidth: 280,
			loadingHeight: 170,
			horizontalMargins: 20,
			verticalMargins: 70,
			sideMargins: 50,
			growingTime: 0.5,
			setActionsContent: function() {
				return "<div id='"+this.prevImgID+"' title=\""+this.prevText+"\" style='display: none;'>"
					+this.prevText+"</div><div id='"+this.nextImgID+"' title=\""+this.prevText+ "\">"
					+this.nextText+"</div><label id='"+this.labelID+"'>1 / "+this.nbImages+"</label>";
			},
			setContent: function(content) {
				return "<div id='"+this.loadingID+"'></div><div id='"+this.imageID+"' style='display:none;'></div>";
			},
			show: function() {
				var that = this.element;
				that.addEvents(this.prevImgID, that.showPrev);
				that.addEvents(this.nextImgID, that.showNext);
			},
			hide: function() {
				Element.stopObserving(this.prevImgID);
				Element.stopObserving(this.nextImgID);
			},
			keyUpEvent: function(event) {
				var that = this.element, key_code = event.keyCode;
				if( key_code == Event.KEY_LEFT  ) { that.showPrev(); Event.stop(event); return; }
				if( key_code == Event.KEY_RIGHT ) { that.showNext(); Event.stop(event); return; }
			}
		}
		this.options.loadingStyle =  {height: this.options.loadingHeight+'px', width: this.options.loadingWidth+'px', top: null, left: null};
		this.options.sizes = Object.clone(this.options.loadingStyle);
		Object.extend(this.options, options || {});
		
		if( this.options.preload )
			setTimeout(this.preloadImages.bind(this), 100);
		
		// Call the parent constructor and display 
		$super('', this.options);
		if( this.isLast() )
			Element.hide(this.options.nextImgID);
		this._display(this.options.index);
	},
	
	getCurrent: function() { return this.images[this.options.index]; },
	isFirst: function() { return (this.options.index == 0) },
	isLast: function()  { return (this.options.index == this.options.nbImages-1) },
	
	preloadImages: function() {
		$A(this.images).each( function(elt) {
			Core.preloadImage(elt.url);
		});
	},
	
	showPrev: function() { if(!this.isFirst()) {this.options.index--; this.display();} },
	showNext: function() { if(!this.isLast())  {this.options.index++; this.display();} },
	
	display: function() {
		var i = this.options.index;
		this.options.hasBeenDragged = false;
		// clear if an image is loading
		this._updateTitle();
		if( this.growingEffect )
			this.growingEffect.cancel(); this.growingEffect = null;
		if( this.timeout )
			clearTimeout(this.timeout); this.timeout = null;
		// update visible canvas
		if( Element.visible(this.options.imageID) ) {
			Element.hide(this.options.imageID);
			Element.show(this.options.loadingID);
			this.setStyles(this.options.loadingStyle);
		}
		// update next/previous buttons and counter
		[this.options.prevImgID].each(this.isFirst() ? Element.hide : Element.show);
		[this.options.nextImgID].each(this.isLast()  ? Element.hide : Element.show);
		$(this.options.labelID).innerHTML = (i+1)+" / "+this.options.nbImages;
		// call display to display the new imge
		this._display(this.images[i]);
	},
	
	_display: function(image) {
		image = image || this.getCurrent();
		this.timeout = null;
		if( Core.isImageLoaded(image.url) )
			this.displayImage();
		else
			this.timeout = setTimeout(this._display.bind(this, image), 20);
	},
	
	displayImage: function() {
		var that = this, i = this.options.index, elt = this.images[i], image = Core.Image(elt.url);
		// update visible canvas
		if( ! Element.visible(this.options.imageID) ) {
			Element.hide(this.options.loadingID);
			Element.show(this.options.imageID);
		}
		// update popup size 	nd display the image
		var imgH=image.height, imgW=image.width;
		var vp = document.viewport, vpScroll = vp.getScrollOffsets(), vpSizes = vp.getDimensions();
		var mY=this.options.verticalMargins, mX=this.options.horizontalMargins, margins=this.options.sideMargins;
		var imgSizes=this._getMaxSizes(image, vpSizes.width-mX-margins, vpSizes.height-mY-margins);
		var imgObj=$(this.options.imageID);
		var initSizes = this._getMaxSizes(image, 260, 100);
		var fSizes = (imgSizes.ratio<100) ? imgSizes : Element._returnSizes(imgW,imgH,100);
		var pSizes = Element._returnSizes(fSizes.width+mX, fSizes.height+mY);
		if( this.options.smoothResize ) {
			var delay = this.options.growingTime, popup = $(this.options.divLayoutID);
			
			//this.options.sizes.top = (vpScroll.top+(vpSizes.height-sizes.height)/2)+'px';
			//this.options.sizes.left = (vpScroll.left+(vpSizes.width-sizes.width)/2)+'px';
			var fTop  = vpScroll.top+(vpSizes.height-pSizes.height)/2;
			var fLeft = vpScroll.left+(vpSizes.width-pSizes.width)/2;
			this._createImageObject(imgObj, elt, initSizes);
			this.growingEffect = new Effect.Morph(popup, {duration: delay, style: 'height: '+pSizes.height+'px; width: '+pSizes.width+'px; top: '+fTop+'px; left:'+fLeft+'px;'});
			new Effect.Morph(imgObj.select('img')[0], {duration: delay, style: 'height: '+fSizes.height+'px; width: '+fSizes.width+'px;'});
			setTimeout(function(){that.growingEffect = null; that._updateTitle(fSizes.ratio); }, delay*1000);
		} else {
			this.setStyles({height: (pSizes.height)+'px', width: (pSizes.width)+'px'});
			this._createImageObject(imgObj, elt, fSizes);
			this._updateTitle(fSizes.ratio);
		}
	},
	
	_getMaxSizes: function(image, max_w, max_h) {
		var w = image.width, h = image.height, h2 = h*(max_w/w);
		if( h2 > max_h ) { r = max_h/h; w = Math.round(w*(max_h/h)); h = max_h; }
		else { r = max_w/w; w = max_w; h = Math.round(h2); }
		return Element._returnSizes(w, h, Math.floor(r*100));
	},
	
	_createImageObject: function(container, item, sizes) {
		container.innerHTML = "<a href='"+item.url+"' target=_blank title='"+item.title+"'><img src='"+item.url
				+ "' title='"+item.title+"' style='width:"+sizes.width+"px; height:"+sizes.height+"px;' /></a>";
	},
	
	_updateTitle: function(ratio) {
		var title = this.options.title;
		if(! Object.isUndefined(ratio) && ratio <100 )
			title += " ("+ratio+"%)";
		$(this.options.divLayoutID).select('h1')[0].innerHTML = title;
	}
	
});
