var Ext = require('./ext');
var GO = require('./GO');
var MainPanel = require('./Extjs3/netspresso-main-panel');

// Creates namespaces to be used for scoping variables and classes so that they are not global.
Ext.namespace('GO.netspresso');

/*
 * This will add the module to the main tab-panel filled with all the modules
 */

GO.moduleManager.addModule(
	'netspresso', //Module alias
	MainPanel, //The main panel for this module
	{
		title : 'nÉTSpresso', //Module name in start-menu
		iconCls : 'go-module-icon-netspresso' //The css class with icon for start-menu
	}
);