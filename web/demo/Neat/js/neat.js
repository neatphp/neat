var neat;
if (!neat) neat = {};
else if (typeof neat != 'object') {
	throw new Error('Namespace "neat" is invalid.');
}

neat.addEvent = function (obj, type, fn) {
  if ( obj.attachEvent ) {
    obj['e'+type+fn] = fn;
    obj[type+fn] = function(){obj['e'+type+fn]( window.event );};
    obj.attachEvent( 'on'+type, obj[type+fn] );
  } else
    obj.addEventListener( type, fn, false );
};

neat.removeEvent = function ( obj, type, fn ) {
  if ( obj.detachEvent ) {
    obj.detachEvent( 'on'+type, obj[type+fn] );
    obj[type+fn] = null;
  } else
    obj.removeEventListener( type, fn, false );
};

neat.getElementsByClassName = function (element, tagName, className) {
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

neat.addClassName = function (element, className) {
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

neat.removeClassName = function(element, className) {
	if (element && element.className) {
		var list = element.className.split(/\s+/);
		var classNameUpper = className.toUpperCase();
		
		for (var i = 0; i < list.length; i++) {
			if (list[i].toUpperCase() == classNameUpper) delete list[i];
		}
		
		element.className = list.join(' ');	
   }
};

neat.over = function(element, className) {
	if (!className) className = 'over';
	neat.addClassName(element, className);
};

neat.out = function(element, className) {
	if (!className) className = 'over';
	neat.removeClassName(element, className);
};

neat.toggle = function () {
	for (var i = 0; i < arguments.length; i++) {
		var element = document.getElementById(arguments[i]);
		element.style.display = (element.style.display == 'none') ? 'block' : 'none';
	}
};

neat.checkAll = function(name) {
	var elements = document.getElementsByTagName('input');
	for (var i = 0; i<elements.length; i++) {
		if ((elements[i].type == 'checkbox') && (elements[i].name == name)) {
			elements[i].checked = true;
		}
	}
};

neat.clearAll = function (name) {
	var elements = document.getElementsByTagName('input');
	for (var i = 0; i<elements.length; i++) {
		if ((elements[i].type == 'checkbox') && (elements[i].name == name)) {
			elements[i].checked = false;
		}
	}
};

neat.accordion = {};
neat.accordion.toggle = function(element) {
	var ul = element.parentNode.getElementsByTagName('ul');
	for (var i = 0; i < ul.length; i++) {
		if (ul[i].parentNode == element.parentNode) {
			ul[i].style.display = (ul[i].style.display == 'none') ? 'block' : 'none';
		}
	}

	if (ul[0]) {
		if (ul[0].style.display == 'block') {
			neat.addClassName(element, 'opened');
		} else {
			neat.removeClassName(element, 'opened');
		}
	}
};

neat.overlay = {};
neat.overlay.adjust = function (id) {
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

neat.tabs = {
	links: new Array(),
	contents: new Array()
};
neat.tabs.init = function (tabsId, activeTabId) {
	this.links[tabsId] = new Array();
	this.contents[tabsId] = new Array();
	
	var list = neat.getElementsByClassName(document.getElementById(tabsId), 'LI', '*');
	for (var i = 0; i < list.length; i++) {
    	var link = neat.getElementsByClassName(list[i], 'A', '*')[0];
    	if (!link) continue;
    	
    	var href = link.getAttribute('href');
    	var id = href.substring(href.lastIndexOf('#') + 1);
    	var content = document.getElementById(id);
    	var ref = this;
    	
    	if (id == 'close') {
    		link.onclick = function (tabsId) {
        		return function () {
        			neat.toggle(tabsId);
        			neat.tabs.activate(tabsId);
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
	    		neat.addClassName(link, 'active');
	    		neat.addClassName(content, 'active');
	    	}
	    	
	        this.links[tabsId][id] = link;
	        this.contents[tabsId][id] = content;
    	}
    }
};
neat.tabs.activate = function (tabsId, activeTabId) {
	for (var id in this.links[tabsId]) {
		var link = this.links[tabsId][id];
		var content = this.contents[tabsId][id];
		
		if (id == activeTabId) {
			neat.addClassName(link, 'active');
			neat.addClassName(content, 'active');
		} else {
			neat.removeClassName(link, 'active');
			neat.removeClassName(content, 'active');
		}
	}
	
	return false;
};