

if (!window.fishpig) {
	var fishpig = {};
}

fishpig.SmartTabs = Class.create({
	initialize: function(wrapper, options) {
		this.wrapper = $(wrapper);

		this._initializeOptions(options || {});
		this._initializeTabs();
		
		if (this.options.isGreedy) {
			this._initializeGreedyLinks();
		}
		
		if (this.ready && window.location.hash.length > 1) {
			this.openTab(window.location.hash.substr(1), true);
		}
	},
	_initializeOptions: function(options) {
		// Setup options
		this.options = {
			'selector' : {
				'clicker': 'ul.tabs li a',
				'content': '.tab-content',
				'greedyClicker': 'a.tab-link'
			},
			'idSuffix': {
				'tab': 'tab-',
				'content': ''
			},
			'activeClass': 'active',
			'tabIdAttribute': 'tab_id',
			'isGreedy': false,
			'animation': 'slide',
			'activeClassOnAnchor': true
		};
		
		Object.extend(this.options, options || {});	
	},
	_initializeTabs: function() {
		this.tabs = new Array();
		
		this.wrapper.select(this.options.selector.clicker).each(function(elem, ind) {
			var tabId = '';
			
			if ((tabId = this._parseTabId(elem.readAttribute('href'))) !== '') {
				// Activate tab
				this.tabs.push(elem.writeAttribute(this.options.tabIdAttribute, tabId));

				// Setup active tab
				if (!this.activeTabId) {
					this.activeTabId = tabId;
					
					if (this.options.activeClassOnAnchor) {
						elem.addClassName(this.options.activeClass);
					}
					
					elem.up('li').addClassName(this.options.activeClass);
				}
			}
		}.bind(this));
		
		if (this.tabs.length > 1) {
			this.tabs.each(function(elem, ind) {
				elem.observe('click', this._tabClickEventListener.bindAsEventListener(this));	
			}.bind(this));
			
			this.ready = true;
		}
	},
	_initializeGreedyLinks: function() {
		$$(this.options.selector.greedyClicker).invoke('observe', 'click', this._tabClickEventListener.bindAsEventListener(this));
	},
	openTab: function(tabId) {
		if (!this.ready || this.activeTabId == tabId) {
			return this;
		}
		
		this.ready = false;

		var oldTab = $(this.options.idSuffix.tab + this.activeTabId);
		var oldTabContent = $(this.options.idSuffix.content + this.activeTabId);
		
		var newTab = $(this.options.idSuffix.tab + tabId);
		var newTabContent = $(this.options.idSuffix.content + tabId);

		if (!newTabContent) {
			this.ready = true;
			return this;	
		}

		if (this.options.animation === 'toggle') {
			// Swap content
			oldTabContent.hide();
			newTabContent.show();

			// Change active class
			oldTab.removeClassName(this.options.activeClass);
			newTab.addClassName(this.options.activeClass);
			
			if (this.options.activeClassOnAnchor) {
				oldTab.select('a').invoke('removeClassName', this.options.activeClass);
				newTab.select('a').invoke('addClassName', this.options.activeClass);
			}
			
			this.activeTabId = tabId;
			this.ready = true;
		}
		else {
			Effect.SlideUp(oldTabContent, {
				duration: 0.5,
				afterFinish: function() {
					setTimeout(function() {
						oldTab.removeClassName(this.options.activeClass);
						newTab.addClassName(this.options.activeClass);
					
						if (this.options.activeClassOnAnchor) {
							oldTab.select('a').invoke('removeClassName', this.options.activeClass);
							newTab.select('a').invoke('addClassName', this.options.activeClass);
						}
						
						Effect.SlideDown(newTabContent, {
							duration: 0.5,
							afterFinish: function() {
								this.activeTabId = tabId;
								this.ready = true;
	//							window.location.hash = '#' + tabId;
							}.bind(this)
						});
					}.bind(this), 100);
					
				}.bind(this)
			});
		}
	},
	_tabClickEventListener: function(event) {
		Event.stop(event);
		var elem = Event.findElement(event, 'a');
		
		if (!elem.readAttribute(this.options.tabIdAttribute)) {
			var tabId = this._parseTabId(elem.readAttribute('href'));
			this.openTab(tabId, true);
		}
		else {
			return this.openTab(elem.readAttribute(this.options.tabIdAttribute));
		}
	},
	_parseTabId: function(href) {
		var pos = 0;
		return (pos = href.indexOf('#')) > -1 ? href.substr(pos+1) : '';
	}
});

var FishpigSmartTabs = fishpig.SmartTabs;
