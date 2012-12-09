
/**
 * Quickjump4ward
 * A Contao-Extension to quickly access cartain backend-modules
 * through typing
 *
 * @copyright  4ward.media 2012 <http://www.4wardmedia.de>
 * @author     Christoph Wiechert <christoph.wiechert@4wardmedia.de>
 * @package    quickjump4ward
 * @license    LGPL
 * @filesource
 */

var Quickjump4ward = new Class({
	
	Implements: Options,
	
	options: {
		'searchText':'Quickjump'
	},

	/**
	 * Init the class
	 */
	initialize: function(options){
		this.setOptions(options);

		// run some actions
		if(this.executeDo()) return;

		// do nothing if we are in an popup window
		if($(document.body).hasClass('popup')) return;		
		
		// Init the form
        if(Contao.theme != 'smart_backend_theme')
        {
            this.container = new Element("div", {
          			id: "quickjump4ward",
          			styles: {
          				'text-align':'left',
          				'background-color':'#F3F3F3',
          				'border-top':'1px solid #BBBBBB'
          			}
          		}).inject($('header'),'bottom');
        }
        else
        {
            this.container = new Element("div", {
          			id: "quickjump4ward",
          			styles: {
          				'text-align':'left',
                        'width':'205px',
                        'position':'fixed',
                        'margin-top':'5px'
          			}
          		}).inject($('container'),'top');
        }

        this.container.adopt(
			this.form = new Element("form",{
				'events':{ 'submit':this.onSubmit.bind(this) }
			}).adopt(this.input = new Element("input", {
					'styles': {
						'color': "#606060",
                        'width': ((Contao.theme == 'smart_backend_theme') ? '180px' : '314px')
					},
					'class': 'tl_text',
					'value': this.options.searchText,
					'events': {
						'focus':function(e){
							if(this.input.get('value') == this.options.searchText){
								this.input.set('value','');
								this.input.setStyle('color','#000000');
							}
						}.bind(this),
						'blur':function(e){
							if(this.input.get('value') == ''){
								this.input.set('value',this.options.searchText);
								this.input.setStyle('color','#606060');
							}
						}.bind(this)
					}
				}),
				new Element('a',{'href': 'contao/help.php?table=quickjump4ward&field=Quickjump',
					'events':{'click':function(e){Backend.openWindow(this, 600, 500); return false;}}
				}).adopt(
					new Element('img',{'src':'system/themes/default/images/about.gif','styles':{
						'padding-left':'3px',
						'vertical-align':'middle'
					}})
				)
			)
		);
		
		// Init autocompleter
	    this.autocompleter = new Autocompleter.Request.JSON(this.input, 'system/modules/quickjump4ward/ajax.php', {
	        'postVar': 's',
	        'injectChoice':this.generateChoice,
	        'autoSubmit':true,
            'width':'314px',
	        'onSelection':function(inp,el,sel,val,x){
	    		if(val.substr(-1) == ':') return;
	    		this.currentHref = el.retrieve('url');
	    	}.bind(this),
			'postData': {'REQUEST_TOKEN':Contao.request_token},
	    	'onRequest': this.generateRequest.bind(this)
	    });

	    // Add Tab or right-arrow completion
		this.input.addEvent('keydown',function(e){
			// on right or tab take selected or first choice
			if((e.code == 39 || e.code == 9)){
				if(this.autocompleter.choices.getStyle('visibility') == 'visible'){
					choices = this.autocompleter.choices.getElements('li');
					if (choices.length > 0) {
						for(i=0;i<choices.length;i++){
							if(choices[i].inputValue.test(this.input.value,'i')){
								this.input.set('value',choices[i].inputValue);
								this.currentHref=choices[i].retrieve('url');
								if(this.input.value.substr(-1) != ':')this.input.value += ':';
								break;
							}
						}
					}
				}
				this.autocompleter.prefetch();
				return false;
			}
			return true;
		}.bind(this));
	    
	    
	    // Init hotkey
		if(Browser.Engine.trident){
			// Stupid IE....
			$(document.body).addEvent('keydown',this.keypress.bind(this));
		} else {
			$(window).addEvent('keypress',this.keypress.bind(this));
		}	    
	},


	/**
	 * Run some actions like form submits
	 */
	executeDo: function()
	{
		var url = new URI(document.location.href);
		if(!url.getData('quickjump4ward')) return false;
		switch(url.getData('quickjump4ward'))
		{
			case 'doClearCache':
				var frm = document.getElement('input[value=tl_purge]')
				if(!frm) return false;
				frm = frm.getParent('form');
				frm.getElements('input[value=images], input[value=scripts], input[value=pages], input[value=internal], input[value=temp], input[value=xml]').each(function(el){
					el.checked = true;
				});
				frm.set('action',frm.get('action').replace("&quickjump4ward=doClearCache",''));
				frm.submit();
				return true;
			break;
		}
		return false;
	},


	/**
	 * Handle the form-submit that Autocompleter triggers
	 */
	onSubmit: function(e){
		new Event(e).stop();
		if(this.currentHref == null){
			// try to lunch first option
			if(first = this.autocompleter.choices.getElement('li')){
				this.currentHref = this.autocompleter.choices.getElement('li').retrieve('url');
			} else {
				return false;
			}
		}
		document.location.href = this.currentHref;
		return false;
	},
	
	/**
	 * Hotkey to jump into the autocompleter field
	 */
	keypress: function(e){
		if((e.code == 106 || e.code == 74 || e.code == 17) && e.control) {
			this.input.focus();
			return false;
		}
		return true;
	},
	
	/**
	 * Split the input for requesting a particular section
	 * gets called from Autocompleter.Request
	 */
	generateRequest: function(inputEl,request,data,val) {
		data.get = 'all'
		if(val.indexOf(':') > 0){
			var sect = val.slice(0,val.indexOf(':'));
			data.s = val.slice(val.indexOf(':')+1).trim();

			if(typeof Quickjump4ward.sections[sect] != 'undefined')
			{
				data.get = Quickjump4ward.sections[sect]
			}
			else
			{
				data.get = sect;
			}
		}
	},
	
	/**
	 * Generate each choice
	 * this represents the Autocompleter object
	 */
	generateChoice: function(val){
	    var el = new Element('li').set('html',val.image+' '+val.name).store('url',val.url+'&rt='+Contao.request_token);
	    el.inputValue = val.name;
	    el.addEvents({
	    	'mouseover': this.choiceOver.bind(this, el),
	    	'click':function(e){
	    		document.location.href = el.retrieve('url');
	    	}
	    });
	    el.inject(this.choices);
	}

});

if(typeof Quickjump4ward.sections != 'object') Quickjump4ward.sections = new Object();
Quickjump4ward.sections['p']		= 'page';
Quickjump4ward.sections['a']		= 'article';
Quickjump4ward.sections['m']		= 'module';
Quickjump4ward.sections['pl']		= 'pagelayout';
Quickjump4ward.sections['layout']	= 'pagelayout';
Quickjump4ward.sections['css']		= 'stylesheet';
Quickjump4ward.sections['f']		= 'function';


window.addEvent('domready',function(){
	var quickjump4ward = new Quickjump4ward();
});
