 
GO.netspresso.NetspressoDialog = Ext.extend(GO.dialog.TabbedFormDialog , {

	customFieldType : "GO_Netspresso_Model_Netspresso",

	initComponent : function(){
		
		Ext.apply(this, {
			titleField:'lastname',
			goDialogId:'netspresso',
			title:GO.netspresso.lang.netspresso,
			width: 300,
			height: 280,
			formControllerUrl: 'netspresso/netspresso'
		});
		
		GO.netspresso.NetspressoDialog.superclass.initComponent.call(this);	
	},
	
	buildForm : function () {

		this.propertiesPanel = new Ext.Panel({
			title:GO.lang.strProperties,			
			cls:'go-form-panel',
			layout:'form',
			items:[{
				xtype: 'textfield',
				name: 'calendar_name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.netspresso.lang.calendar_name
			},
			{
				xtype: 'textfield',
				name: 'lastname',
				fieldLabel: GO.netspresso.lang.lastname,
				anchor: '100%'
			},
			{
				xtype:'combo',
				fieldLabel: GO.netspresso.lang.party_id,
				hiddenName:'party_id',
				anchor:'100%',
				emptyText:GO.lang.strPleaseSelect,
				store: new GO.data.JsonStore({
					url: GO.url('netspresso/party/store'),
					baseParams: {
						permissionLevel:GO.permissionLevels.write
					},	
					fields: ['id', 'name']	
				}),
				valueField:'id',
				displayField:'name',
				triggerAction: 'all',
				editable: true,
				forceSelection: true,
				allowBlank: false
			},
			{
				xtype: 'datefield',
				name: 'tookoffice',
				fieldLabel: GO.netspresso.lang.tookoffice,
				allowBlank: false,
				anchor: '100%'
			},
			{
				xtype: 'datefield',
				name: 'leftoffice',
				fieldLabel: GO.netspresso.lang.leftoffice,
				//format: GO.settings['date_format'],			    
				allowBlank: false,
				anchor: '100%'
			},
			{
				xtype: 'numberfield',
				name: 'income',
				value: GO.util.numberFormat(0),
				fieldLabel: GO.netspresso.lang.income,   
				allowBlank: false,
				anchor: '100%'
			},
			new GO.form.SelectLink({
				anchor:'100%'
			})
			]				
		});

		this.addPanel(this.propertiesPanel);
	}
	
});