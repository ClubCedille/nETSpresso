 
GO.netspresso.NetspressoGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.netspresso.lang.grid_title; //Title of this panel
	config.layout='fit'; //How to lay out the panel
	config.autoScroll=true;
	config.split=true;
	config.autoLoadStore=true; //Load the datastore when grid render for the first time
	config.paging=true; //Use pagination for the grid
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true; //Mask the grid when it is loading data
	
	//This dialog will be opened when double clicking a row or calling showEditDialog()
	//config.editDialogClass = GO.netspresso.NetspressoDialog;
	//config.editDialogClass = GO.calendar.EventDialog;
// 	config.editDialogClass = function(){
// 		if(!GO.calendar.showEventDialog)
// 			GO.calendar.showEventDialog = new GO.calendar.showEventDialog();
// 		GO.calendar.showEventDialog({event_id: this.link_id});
// 	},
	
	//Configuring the column model
	config.cm =  new Ext.grid.ColumnModel({
		defaults:{
			sortable: true
		},
		columns : [{
			header: '#',
			readOnly: true,
			dataIndex: 'id',
			renderer: function(value, cell){ 
				cell.css = "readonlycell";
				return value;
			},
			width: 40,
			align: "right"
// 		},{
// 			header: GO.netspresso.lang.resource_id,
// 			dataIndex: 'resource_id',
// 			//width: 40
		},{
			header: GO.netspresso.lang.calendar_name,
			dataIndex: 'calendar_name',
			width: 260
		},{
			header: GO.netspresso.lang.start_time,
			dataIndex: 'start_time',
			width: 120
		},{
			header: GO.netspresso.lang.end_time,
			dataIndex: 'end_time',
			width: 120
		},{
			header: GO.netspresso.lang.status,
			dataIndex: 'status',
			// GO.calendar.lang.statuses["NEEDS-ACTION"]
			width: 140,
			align: "right"
// 		},{
// 			header: GO.netspresso.lang.is_organizer,
// 			dataIndex: 'is_organizer',
// 			renderer: function(value, metaData, record){
// 				if(record.data.is_organizer >= 1){
// 					metaData.attr = 'style="color:#336600;"';
// 					value = 'Yes';
// 				} else {
// 					metaData.attr = 'style="color:#CC0000;"';
// 					value = 'No';
// 				}
// 				return value;
// 			},
// 			align: "right"
		}]
	});

	
	//Defining the data store for the grid
	config.store = new GO.data.JsonStore({
		url: GO.url('netspresso/netspresso/store'),
		fields: ['id','event_id','calendar_name','start_time','end_time','status', 'is_organizer'],
		remoteSort: true,
		model: 'GO_Netspresso_Model_Netspresso'
	});
	
	//Adding the gridview to the grid panel
	config.view=new Ext.grid.GridView({
		emptyText: GO.lang.strNoItemse,
		
		autoFill: true,
		forceFit: true,
		
	});
	
	//Setup a toolbar for the grid panel
	config.tbar = new Ext.Toolbar({
			items: [
				GO.lang.strSearch + ':',
				new GO.form.SearchField({
					store: config.store,
					width:320
				}),
// 				{
// 					iconCls: 'btn-add',							
// 					text: GO.lang['cmdAdd'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){					
// 						this.showEditDialog();
// 					},
// 					scope: this
// 				},
// 				{
// 					iconCls: 'btn-delete',
// 					text: GO.lang['cmdDelete'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){
// 						this.deleteSelected();
// 					},
// 					scope: this
// 				},				
// 				{
// 					iconCls: 'btn-refresh',							
// 					text: GO.lang['cmdRefresh'],
// 					cls: 'x-btn-text-icon',
// 					handler: function(){					
// 						//this.init();
// 						this.reload();
// 						//this.centerPanel.init();
// 						//this.centerPanel.showEditDialog();					
// 					},
// 					scope: this					
// 				}
			]
	});

	//Construct the Gridpanel with the above configuration
	GO.netspresso.NetspressoGrid.superclass.constructor.call(this, config);

};

//Extend the NetspressoGrid from GridPanel
Ext.extend(GO.netspresso.NetspressoGrid, GO.grid.GridPanel,{
	
});