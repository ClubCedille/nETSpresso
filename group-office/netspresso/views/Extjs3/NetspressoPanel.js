
 
GO.netspresso.NetspressoPanel = Ext.extend(GO.DisplayPanel,{
	model_name : "GO_Netspresso_Model_Netspresso",
	stateId : 'pm-netspresso-panel',
	noFileBrowser : true,
	
// 	editHandler : function(){
// 		if(!GO.netspresso.netspressoDialog)
// 			GO.netspresso.netspressoDialog = new GO.netspresso.NetspressoDialog();
// 		GO.netspresso.netspressoDialog.show(this.link_id);
// 	},

// 	editHandler : function(){		
// 		GO.calendar.showEventDialog({event_id: this.link_id});
// 	},

	editHandler : function(){
		if(!GO.calendar.showEventDialog)
			GO.calendar.showEventDialog = new GO.calendar.showEventDialog();
		GO.calendar.showEventDialog({event_id: this.link_id});
	},
		
	initComponent : function(){	
		
		this.loadUrl=('netspresso/netspresso/display');
		
		this.template = 
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">{calendar_name}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.netspresso.lang.event_id+':</td>'+
						'<td>{event_id}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.netspresso.lang.start_time+':</td>'+
						'<td>{start_time}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.netspresso.lang.end_time+':</td>'+
						'<td>{end_time}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.netspresso.lang.is_organizer+':</td>'+
						'<td>{is_organizer}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.netspresso.lang.status+':</td>'+
						'<td>{status}</td>'+
					'</tr>'+
				'</table>';																		
				
		//if(GO.customfields)
		//	this.template += GO.customfields.displayPanelTemplate;
		
		//if(GO.tasks)
		//	this.template += GO.tasks.TaskTemplate;

		//if(GO.calendar)
		//	this.template += GO.calendar.EventTemplate;
		
		//if(GO.workflow)
		//	this.template +=GO.workflow.WorkflowTemplate;

		//this.template += GO.linksTemplate;	
				
		//if(GO.files)
		//{
		//	Ext.apply(this.templateConfig, GO.files.filesTemplateConfig);
		//	this.template += GO.files.filesTemplate;
		//}
		//Ext.apply(this.templateConfig, GO.linksTemplateConfig);
		
		//if(GO.comments)
		//	this.template += GO.comments.displayPanelTemplate;

		GO.netspresso.NetspressoPanel.superclass.initComponent.call(this);
	}
});