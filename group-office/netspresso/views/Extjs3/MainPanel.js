// Creates namespaces to be used for scoping variables and classes so that they are not global.
Ext.namespace('GO.netspresso');

/*
 * This is the constructor of our MainPanel
 */
GO.netspresso.MainPanel = function(config){

	if(!config)
	{
		config = {};
	} 

	//config.html='Hello World';
	config.layout='border';
	
	this.centerPanel = new GO.netspresso.NetspressoGrid({
		region:'center',
		id:'pm-center-panel',
		border:true
	});
	
// 	this.centerPanel.on("delayedrowselect",function(grid, rowIndex, r){
// 		this.eastPanel.load(r.data.id);		
// 	}, this);

// 	this.centerPanel.store.on('load', function(){
// 			this.eastPanel.reset();
// 	}, this);
	
 	this.eastPanel = new GO.netspresso.NetspressoPanel({	
//	this.eastPanel = new GO.netspresso.NetspressoConfig({
		region:'east',
		id:'pm-east-panel',
		width:440,
		border:true
	});
	
	//Setup a toolbar for the grid panel
// 	config.tbar = new Ext.Toolbar({		
// 			cls:'go-head-tb',
// 			items: [
// 				{
// 					iconCls: 'btn-add',							
// 					text: GO.lang['cmdAdd'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){					
// 						this.centerPanel.showEditDialog();
// 					},
// 					scope: this
// 				},
// 				{
// 					iconCls: 'btn-delete',
// 					text: GO.lang['cmdDelete'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){
// 						this.centerPanel.deleteSelected();
// 					},
// 					scope: this
// 				},
// 				{
// 					iconCls: 'btn-refresh',							
// 					text: GO.lang['cmdRefresh'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){					
// 						//this.init();
// 						this.centerPanel.reload();
// 						//this.centerPanel.init();
// 						//this.centerPanel.showEditDialog();					
// 					},
// 					scope: this					
// 				},
// 				GO.lang.strSearch + ':',
// 				new GO.form.SearchField({
// 					store: config.store,
// 					width:320
// 				}),
// 			]
// 	});
	
	config.items=[
	this.centerPanel,
	this.eastPanel
	];
	
	/*
	 * Explicitly call the superclass constructor
	 */
 	GO.netspresso.MainPanel.superclass.constructor.call(this, config);

}

/*
 * Extend our MainPanel from the ExtJS Panel
 */
Ext.extend(GO.netspresso.MainPanel, Ext.Panel,{

});

/*
 * This will add the module to the main tab-panel filled with all the modules
 */
GO.moduleManager.addModule(
	'netspresso', //Module alias
	GO.netspresso.MainPanel, //The main panel for this module
	{
		title : 'nÉTSpresso', //Module name in start-menu
		iconCls : 'go-module-icon-netspresso' //The css class with icon for start-menu
	}
);

