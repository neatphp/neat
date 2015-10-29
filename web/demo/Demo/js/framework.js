var framework;
if (!framework) framework = {};
else if (typeof framework != 'object') {
	throw new Error('Namespace "framework" is invalid.');
}

framework.addEvent = function ( obj, type, fn ) {
  if ( obj.attachEvent ) {
    obj['e'+type+fn] = fn;
    obj[type+fn] = function(){obj['e'+type+fn]( window.event );};
    obj.attachEvent( 'on'+type, obj[type+fn] );
  } else
    obj.addEventListener( type, fn, false );
};

framework.removeEvent = function ( obj, type, fn ) {
  if ( obj.detachEvent ) {
    obj.detachEvent( 'on'+type, obj[type+fn] );
    obj[type+fn] = null;
  } else
    obj.removeEventListener( type, fn, false );
};

framework.getElementsByClassName = function (element, tagName, className) {
	var all = (tagName == '*' && element.all)? element.all : element.getElementsByTagName(tagName);
	if (className == '*') return all;
	
	var elements = new Array();
	className = className.replace(/\-/g, '\\-');
	var regExp = new RegExp('(^|\\s)' + className + '(\\s|$)');	
	for(var i = 0; i < all.length; i++){
		if(regExp.test(all[i].className)) elements.push(all[i]);
	}
	
	return elements;
};

framework.addClassName = function (element, className) {
	if (element) {
		if (element.className) {
			var list = element.className.split(/\s+/);
			var classNameUpper = className.toUpperCase();
			var hasClassName = false;
			
			for (var i = 0; i < list.length; i++) {
				if (list[i].toUpperCase() == classNameUpper) {
					hasClassName = true;
					break;
				}
			}
			
			if (!hasClassName) list.push(className);
			element.className = list.join(' ');
		} else {
			element.className = className;
		}
	}
};

framework.removeClassName = function (element, className) {
	if (element && element.className) {
		var list = element.className.split(/\s+/);
		var classNameUpper = className.toUpperCase();
		
		for (var i = 0; i < list.length; i++) {
			if (list[i].toUpperCase() == classNameUpper) delete list[i];
		}
		
		element.className = list.join(' ');	
   }
};

framework.over = function(element, className) {
	if (!className) className = 'over';
	framework.addClassName(element, className);
};

framework.out = function(element, className) {
	if (!className) className = 'over';
	framework.removeClassName(element, className);
};

framework.toggle = function () {
	for (var i = 0; i < arguments.length; i++) {
		var element = document.getElementById(arguments[i]);
		element.style.display = (element.style.display == 'none') ? 'block' : 'none';
	}
};

framework.checkAll = function (name) {
	var elements = document.getElementsByTagName('input');
	for (var i = 0; i<elements.length; i++) {
		if ((elements[i].type == 'checkbox') && (elements[i].name == name)) {
			elements[i].checked = true;
		}
	}
};

framework.clearAll = function (name) {
	var elements = document.getElementsByTagName('input');
	for (var i = 0; i<elements.length; i++) {
		if ((elements[i].type == 'checkbox') && (elements[i].name == name)) {
			elements[i].checked = false;
		}
	}
};

framework.overlay = {};
framework.overlay.adjust = function (id) {
	var width;
	var scrollWidth = Math.max(document.body.scrollWidth, document.documentElement.scrollWidth);
	var offsetWidth = Math.max(document.body.offsetWidth, document.documentElement.offsetWidth);
	var clientWidth = Math.max(document.body.clientWidth, document.documentElement.clientWidth);
	
	width = clientWidth;
	if (scrollWidth > offsetWidth) width = scrollWidth;
	document.getElementById(id).style.width = width + 'px';
	
	var height;
	var scrollHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
	var offsetHeight = Math.max(document.body.offsetHeight, document.documentElement.offsetHeight);
	var clientHeight = Math.max(document.body.clientHeight, document.documentElement.clientHeight);
	
	height = clientHeight;
	if (scrollHeight > offsetHeight) height = scrollHeight;
	document.getElementById(id).style.height = height + 'px';
};

framework.tabs = {
	links: new Array(),
	contents: new Array()
};
framework.tabs.init = function (tabsId, activeTabId) {
	this.links[tabsId] = new Array();
	this.contents[tabsId] = new Array();
	
	var list = framework.getElementsByClassName(document.getElementById(tabsId), 'LI', '*');
	for (var i = 0; i < list.length; i++) {
    	var link = framework.getElementsByClassName(list[i], 'A', '*')[0];
    	if (!link) continue;
    	
    	var href = link.getAttribute('href');
    	var id = href.substring(href.lastIndexOf('#') + 1);
    	var content = document.getElementById(id);
    	var ref = this;
    	
    	if (id == 'close') {
    		link.onclick = function (tabsId) {
        		return function () {
        			framework.toggle(tabsId);
        			framework.tabs.activate(tabsId);
        			return false;
        		};
        	}(tabsId);
    	} else {
	    	link.onclick = function (tabsId, id) {
	    		return function () {
	    			ref.activate(tabsId, id);
	    			return false;
	    		};
	    	}(tabsId, id);
	    	
	    	if (activeTabId && id == activeTabId) {
	    		framework.addClassName(link, 'active');
	    		framework.addClassName(content, 'active');
	    	}
	    	
	        this.links[tabsId][id] = link;
	        this.contents[tabsId][id] = content;
    	}
    }
};
framework.tabs.activate = function (tabsId, activeTabId) {
	for (var id in this.links[tabsId]) {
		var link = this.links[tabsId][id];
		var content = this.contents[tabsId][id];
		
		if (id == activeTabId) {
			framework.addClassName(link, 'active');
			framework.addClassName(content, 'active');
		} else {
			framework.removeClassName(link, 'active');
			framework.removeClassName(content, 'active');
		}
	}
	
	return false;
};

framework.accordion = {};
framework.accordion.toggle = function(element) {
	var ul = element.parentNode.getElementsByTagName('ul');
	for (var i = 0; i < ul.length; i++) {
		if (ul[i].parentNode == element.parentNode) {
			ul[i].style.display = (ul[i].style.display == 'none') ? 'block' : 'none';
		}
	}
	
	if (ul[0]) {
		if (ul[0].style.display == 'block') {
			framework.addClassName(element, 'framework-accordion-opened');
		} else {
			framework.removeClassName(element, 'framework-accordion-opened');
		}
	}
};