
var sections   = "<?php echo implode(',',$API_sections); ?>".split(',');
var phpClasses = "<?php echo VARS::get(JS_PARAM_PHP_CLASSES); ?>".split(',');


function addHandlersToMenuLinks()
{
	$A(sections).each(function(section, i) {
		if( elt = $(section) ) {
			if( i == 0 ) { openSection(section); }
			elt.sectionName = section;
			Event.observe(elt, 'click', function(event){ openSection(event.element().sectionName); });
		}
	});
	$A(phpClasses).each(function(phpClass, i) {
		if( elt = $('<?php echo LINK_SUFFIX; ?>'+phpClass) ) {
			elt.VolgaClassName = phpClass;
			Event.observe(elt, 'click', function(event){ openPhpClass(event.element().VolgaClassName); });
		}
	});
}

function resetDisplay() {
	$$("td.desc div.section").each(Element.hide);
	$$("td.menu li a.opened").invoke('removeClassName', 'opened');
}

function openPhpClass(className) {
	resetDisplay();
	$('<?php echo DIV_SUFFIX; ?>'+className).show();
	$('<?php echo LINK_SUFFIX; ?>'+className).addClassName('opened');
}

function openSection(sectionName) {
	resetDisplay();
	$('<?php echo API_SUFFIX; ?>'+sectionName).show();
	$(sectionName).addClassName('opened');
}



function createPhpFunctionsList() {
	var list = $A();
	$$('div.function h3').each( function(elt) {
		if(res = elt.innerHTML.match(/^(([a-z]+)::)?([^\(]+)/i)) {
			var className = res[2], fctName = res[3];
			var itemName = className + '::' + fctName;
			if( ! className ) {
				className = elt.parentNode.parentNode.id.replace(/^Class/, '');
				itemName = className + '.' + fctName;
			}
		}
		list.push(itemName);
	});
	return list;
}

function scrollToFunction(fctName) {
	document.getElementsByName(fctName)[0].scrollTo();
}

function scrollToTop() {
	$('top').scrollTo();
}

// ----- Wait for DOM to be ready ------
Core.whenReady( function() {
	resetDisplay();
	addHandlersToMenuLinks();
	$('TopLink').observe('click', scrollToTop);
	
	var phpFcts = createPhpFunctionsList();
	
	var options = {
		afterUpdateElement: function(element, update) {
			var parts = element.value.match(/^([a-z]+)(::|\.)(.+)$/i);
			var className = parts[1], fonctionName = parts[3];
			openPhpClass(className);
			var elt = document.getElementsByName(element.value)[0];
			$(elt).scrollTo().focus();
			element.value = "";
		},
		fullSearch: true
	};
	new Autocompleter.Local('php_search', 'php_fcts_list', phpFcts, options);
});

