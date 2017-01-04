
/**
		Prototype API: http://www.prototypejs.org/api/
		
		tips:
			- encodeURIComponent(chaîne) 
			- value = value || 10; vals = vals || {}/[]
			- !!string.length
			- location.protocol
			- document.domain
			- location.port
 */



var Prototype =
{
	Browser, BrowserFeatures, emptyFunction(), K(), ...
};

var Class = 
{
	create()
	{
		function klass() { this.initialize.apply(this, arguments); }
    Object.extend(klass, Class.Methods);
	}
};

Class.Methods =
{
  addMethods(source)
};

var Try = { these() {} }; // try each given function while it fails, returns the fct value of the 1st success

function $A(iterable) {}; // <Array/Enumerable>

function $w(string) {} // return array: exploded on \s

var $R = function(start, end, exclusive) { // <ObjectRange>
  return new ObjectRange(start, end, exclusive);
};

function $$() { // <Selector>
  return Selector.findChildElements(document, $A(arguments));
}

function $(element) {} { // <Element>
	/* [...] */ return Element.extend(element);
}

var $F = Form.Element.Methods.getValue; // <Form>




/*************************   OBJECT   *************************/

Object.extend = function(destination, source) { }
Object.extend(Object, { // add all this functions to Object
	inspect(object) {},
	toJSON(object) {},
	toQueryString(object) {},
	toHTML(object) {},
	keys(object) {},
	values(object) {},
	clone(object) {},
	isElement, isArray, isHash, isFunction, isString, isNumber, isUndefined(object) {}
});

Object.extend(Function.prototype, {
	argumentNames() {},
	bind() {},
	bindAsEventListener() {}, // [event || window.event]
	curry() {}, // 'bind' without the initial scope argument
	delay() {}, // timeout
	defer() {}, // timeout(0)
	wrap() {},
	methodize() {} // Takes a function and wraps it in another function that, at call time, pushes this to the original function as the first argument.
});




/*************************   PERIODICAL EXECUTER   *************************/

var PeriodicalExecuter = Class.create({
	initialize: function(callback, frequency) {},
	registerCallback: function() {},
	execute: function() {},
	stop: function() {},
	onTimerEvent: function() {}
});




/*************************   STRING   *************************/

Object.extend(String, {
  interpret: function(value) {},
  specialChar: { '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '\\': '\\\\' }
});

Object.extend(String.prototype, {
	gsub(pattern, replacement) {},
	sub(pattern, replacement, count) {},
	scan(pattern, iterator) {},
	truncate(length, truncation) {}, // max size
	strip() {}, // = trim
	stripTags() {}, // remove HTML/XML tags
	stripScripts() {}, // remove scripts
	extractScripts() {}, // retrieve concat content of scripts
	evalScripts() {}, // eval()
	escapeHTML() {}, // Converts HTML special characters to their entity equivalents. 
	unescapeHTML() {}, // Strips tags and converts the entity forms of special HTML characters to their normal form.
	toQueryParams(separator) {},
	parseQuery // alias of toQueryParams()
	toArray() {},
	succ() {}, // Converts the last character of the string to the following character in the Unicode alphabet.
	times(count) {}, // Concatenates the string count times.
	camelize() {}, // converts a string separated by dashes into a camelCase equivalent
	capitalize() {},
	underscore() {}, // Converts a camelized string into a series of words separated by an underscore ("_").
	dasherize() {}, // Replaces every instance of the underscore character ("_") by a dash ("-").
	inspect(useDoubleQuotes) {}, // kind of Common::clean()
	toJSON() {}, // Strips comment delimiters around Ajax JSON or JavaScript responses.
	isJSON() {},
	evalJSON(sanitize) {},
	include(pattern) {}, // Check if the string contains a substring.
	startsWith(pattern) {} //
	endsWith(pattern) {}, //
	empty() {}, // == ''
	blank() {}, // contains only \s chars
	interpolate(object, pattern) {} // Treats the string as a Template and fills it with object’s properties.
});




/*************************   TEMPLATE   *************************/

var Template = Class.create({
	initialize(template, pattern) {}, // default pattern: /(^|.|\r|\n)(#\{(.*?)\})/ => '#{car}'
	evaluate(object) {}
});




/*************************   ENUMERABLE / ARRAY   *************************/

var Enumerable = {
	each(iterator, context) {}, // It lets you iterate over all the elements, then returns the Enumerable itself
	eachSlice(number, iterator, context) {}, // Groups items in chunks based on a given size
	all(iterator, context) {}, // Determines whether all the elements are boolean-equivalent to true
	any(iterator, context) {}, // Determines whether at least one element is boolean-equivalent to true
	collect(iterator, context) {}, // Returns the results of applying the iterator to each element (Aliased as map)
	detect(iterator, context) {}, // Aliased by the find method
	findAll(iterator, context) {}, // Aliased as select
	grep(filter, iterator, context) {}, // Returns all the elements that match the filter
	include(object) {}, // Aliased as member
	inGroupsOf(number, [fillWith = null]) {}, // Groups items in fixed-size chunks, fillWith = default filling value
	inject(memo, iterator, context) {}, // Incrementally builds a result value based on the successive results of the iterator
	invoke(method) {}, // Invokes the same method, with the same arguments, for all items in a collection
	max(iterator, context) {}, // Returns the maximum element (or element-based computation)
	min(iterator, context) {}, // Returns the minimum element (or element-based computation)
	partition(iterator, context) {}, // Partitions the elements in two groups: those regarded as true, and those considered false
	pluck(property) {}, // fetching the same property for all the elements. Returns the property values
	reject(iterator, context) {}, // Returns all the elements for which the iterator returned false
	sortBy(iterator, context) {}, // Provides a custom-sorted view of the elements based on the criteria computed
	toArray() {}, // toArray
	zip() {}, // Zips together (think of the zip on a pair of trousers) 2+ sequences, providing an array of tuples
	size() {}, // count 
	inspect() {} // ???
});

Object.extend(Enumerable, { // macros 
  map:     Enumerable.collect,
  find:    Enumerable.detect,
  select:  Enumerable.findAll,
  filter:  Enumerable.findAll,
  member:  Enumerable.include,
  entries: Enumerable.toArray,
  every:   Enumerable.all,
  some:    Enumerable.any
});


Object.extend(Array.prototype, {
  _each(iterator) {},
	clear() {},
	first() {},
	last() {},
	compact() {}, // Returns a new array, without any null/undefined values
	flatten() {},
	without() {}, // Produces a new version of the array that does not contain any of the specified values.
	reverse(inline) {},
	reduce() {}, // [a] => a, [a,b,c] => [a,b,c]
	uniq(sorted) {}, // [a,b,a] => [a,b]
	intersect(array) {}, // collision between the 2 arrays
	clone() {}, // duplicate
	size() {}, // length
	inspect() {}, // Returns the debug-oriented string representation of an array.
	toJSON() {},
	
	indexOf(item, i) {} // -1 if !member()
	lastIndexOf(item, i) {},
	toArray // alias of clone
});




/*************************   NUMBER   *************************/

Object.extend(Number.prototype, {
  toColorPart() {},
  succ() {},
  times(iterator, context) {},
  toPaddedString(length, radix) {},
  toJSON() {},
	abs(), round(), ceil(), floor()
});



/*************************   HASH / OBJECTRANGE   *************************/

var Hash = Class.create(Enumerable, (
	function() { return {
		initialize(object) {},
		set(key, value) {},
		get(key) {},
		unset(key) {},
		toObject() {},
		keys() {},
		values() {},
		index(value) {},
		merge(object) {},
		update(object) {},
		toQueryString() {},
		inspect() {},
		toJSON() {},
		clone() {},
		toTemplateReplacements // macro of toObject
  }}
));

var ObjectRange = Class.create(Enumerable, {
  initialize(start, end, exclusive) {},
	_each(iterator) {},
	include(value) {}
});




/*************************   AJAX   *************************/

var Ajax = {
	getTransport() {},
	Responders { // extends Enumerable
		_each(iterator) {},
		register(responder) {},
		unregister(responder) {},
		dispatch(callback, request, transport, json) {},
		register()
	},
	Base = Class.create({
		initialize(options) {}
	}),
	Request = Class.create(Ajax.Base, {
		initialize($super, url, options) {},
		request(url) {}, // perform the query
		onStateChange() {},
		setRequestHeaders() {},
		success() {},
		getStatus() {},
		respondToReadyState(readyState) {},
		isSameOrigin() {}, // AJAX query on the same domain
		getHeader(name) {}, // assoc the key header-value
		evalResponse() {}, // eval(JS)
		dispatchException(exception) {},
		Events = ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete']
	}),
	Response = Class.create({
		initialize(request){},
		getStatus: Ajax.Request.prototype.getStatus,
		getStatusText() {},
		getHeader: Ajax.Request.prototype.getHeader,
		getAllHeaders() {},
		getResponseHeader(name) {}, // getAllResponseHeaders() {},
		_getHeaderJSON() {},
		_getResponseJSON() {},
		// but data seems to be in member: 
		this.status,
		this.statusText,
		this.responseText,
		this.headerJSON,
		this.readyState
	}),
	Updater = Class.create(Ajax.Request, {
		initialize($super, container, url, options) {},
		updateContent(responseText) {},
	}),
	PeriodicalUpdater = Class.create(Ajax.Base, {
		initialize($super, container, url, options) {},
		start() {},
		stop() {},
		updateComplete(response) {},
		onTimerEvent() {}
	})
};




/*************************   ELEMENT   *************************/

Element.Methods = {
  visible(element) {},
	toggle(element) {},
	hide(element) {},
	show(element) {},
	remove(element) {},
	update(element, content) {},
	replace(element, content) {},
	insert(element, insertions) {},
	wrap(element, wrapper, attributes) {}, // Wraps an element inside another, then returns the wrapper.
	inspect(element) {}, // Returns the debug-oriented string representation of element.
	recursivelyCollect(element, property) {},
	ancestors(element) {},
	descendants(element) {},
	firstDescendant(element), immediateDescendants(element), previousSiblings(element) //
	nextSiblings(element), siblings(element), //
	match(element, selector) {}, // Checks if element matches the given CSS selector.
	up(element, expression, index), down(element, expression, index), //
	previous(element, expression, index), next(element, expression, index), //
	select() {}, // Takes CSS selectors and returns an array of descendants of element that match any of them.
	adjacent() {}, // Finds all siblings of the current element that match the given selector(s).
	readAttribute(element, name) {},
	writeAttribute(element, name, value) {},
	classNames(element), hasClassName(element, className), addClassName(element, className), //
	removeClassName(element, className), toggleClassName(element, className), //
	cleanWhitespace(element), empty(element)
	descendantOf(element, ancestor),
	scrollTo(element),
	getStyle(element, style) // Returns the CSS property value. property in either CSS or camelized form.
	getOpacity(element), setOpacity(element, value),
	setStyle(element, styles)
	getHeight(element), getWidth(element), getDimensions(element),
	makePositioned(element), undoPositioned(element),
	makeClipping(element), undoClipping(element),
	cumulativeOffset(element), cumulativeScrollOffset(element), positionedOffset(element), 
	getOffsetParent(element), viewportOffset(forElement), 
	absolutize(element), relativize(element), clonePosition(element, source)
};

Element.Methods = {
	Simulated = { hasAttribute(element, attribute); },
	ByTag = { }
};

Object.extend(Element, Element.Methods);

Element.extend = (function() {})();
Element.hasAttribute = function(element, attribute),
Element.addMethods = function(methods) {} // extends Form <- Form.Methods, Form.Element <- Form.Element.Methods
	// Element.Methods.ByTag <- {"FORM": clone(F.M), "INPUT": clone(F.E.M), "SELECT": clone(F.E.M), "TEXTAREA": clone(F.E.M) }
	// Element.Methods extended




/*************************   FORM   *************************/

var Form = {
  reset: function(form) {},
	serializeElements: function(elements, options) {},
};

Form.Methods = {
	serialize: function(form, options) {
    return Form.serializeElements(Form.getElements(form), options);
  },
  getElements: function(form) {},
	getInputs: function(form, typeName, name) {},
	disable: function(form) {},
	enable: function(form) {},
	findFirstElement: function(form) {},
	focusFirstElement: function(form) {},
	request: function(form, options) {} // return new Ajax.Request(action, options);
}

Form.Element = { focus: function(element) {}, select: function(element) {} };

Form.Element.Methods = {
  serialize: function(element) {},
	getValue: function(element) {},
	setValue: function(element, value) {},
	clear: function(element) {},
	present: function(element) {},
	activate: function(element) {},
	disable: function(element) {},
	enable: function(element) {}
};

var Field = Form.Element;


Form.Element.Serializers = {
  input: function(element, value) {},
	textarea: function(element, value) {},
	select: function(element, value) {},
		inputSelector: function(element, value) {}, // for radio and checkbox
		selectOne: function(element) {},
		selectMany: function(element) {},
		optionValue: function(opt) {}
};





/*************************   VIEWPORT   *************************/
	
document.viewport = {
	getDimensions: function() {},
	getWidth: function() {},
	getHeight: function() {},
	getScrollOffsets: function() {},
};




/*************************   SELECTOR   *************************/

var Selector = Class.create({
	initialize: function(expression) {}, // shouldUseXPath(), shouldUseSelectorsAPI(), compileMatcher(), compileXPathMatcher()
	findElements: function(root) {},
	match: function(element) {},
	toString: function() {},
	inspect: function() {}
};

Object.extend(Selector, {
	_cache: { },
	xpath: {},
	criteria: {},
	patterns: {},
	assertions: {},
	handlers: {},
	pseudos: {},
	operators: {},
	split: function(expression) {},
	matchElements: function(elements, expression) {},
	findElement: function(elements, expression, index) {},
	findChildElements: function(element, expressions) {}
};



/*************************   EVENT   *************************/

Abstract.TimedObserver = Class.create(PeriodicalExecuter, {
  initialize: function($super, element, frequency, callback) {},
  execute: function() {}
};

Abstract.EventObserver = Class.create({
	initialize: function(element, callback) {},
	onElementEvent: function() {},
	registerFormCallbacks: function() {},
	registerCallback: function(element) {}
};

Event.Methods = (function() {
  var isButton = function(event, code) {},
	return {
		isLeftClick(event), isMiddleClick(event), isRightClick(event), 
		element(event) { return Element.extend(node); }, findElement(event, expression),
		pointer(event) { return {x: xxx, y: xxx}; }, pointerX(event), pointerY(event),
		stop(event)
	}
});

Object.extend(Event, (function() {
	function getEventID(element) {},
	function getDOMEventName(eventName) {},
	function getCacheForID(id) {},
	function getWrappersForEventName(id, eventName) {},
	function createWrapper(element, eventName, handler) {},
	function findWrapper(id, eventName, handler) {},
	function destroyWrapper(id, eventName, handler) {},
	function destroyCache() {},
	return {
		observe: function(element, eventName, handler) {},
		stopObserving: function(element, eventName, handler) {},
		fire: function(element, eventName, memo) {}
	}
})() );

Element.addMethods({
  fire:          Event.fire,
  observe:       Event.observe,
  stopObserving: Event.stopObserving
});

Object.extend(document, {
  fire:          Element.Methods.fire.methodize(),
  observe:       Element.Methods.observe.methodize(),
  stopObserving: Element.Methods.stopObserving.methodize(),
  loaded:        false
});

