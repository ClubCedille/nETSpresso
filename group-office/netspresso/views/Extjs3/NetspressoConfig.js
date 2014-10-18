 

GO.netspresso.NetspressoConfig = function(config){
	
	if(!config)
	{
		config={};
	}
	
//     var configForm = Ext.create('Ext.form.FormPanel', 
//     {
//         	renderTo: 'form-ct',
//         	frame: true,
//         	title:'XML Form',
// 			width: 340,
//         	bodyPadding: 5,
//         	waitMsgTarget: true,
// 
//         	fieldDefaults: {
//             	labelAlign: 'right',
//             	labelWidth: 85,
//             	msgTarget: 'side'
//         	},
//      });

        // configure how to read the XML data
//         reader : Ext.create('Ext.data.reader.Xml', {
//             model: 'example.contact',
//             record : 'contact',
//             successProperty: '@success'
//         }),

        // configure how to read the XML errors
//         errorReader: Ext.create('Ext.data.reader.Xml', {
//             model: 'example.fielderror',
//             record : 'field',
//             successProperty: '@success'
//         }),

//         items: 
//         [
//         	{
//             xtype: 'fieldset',
//             title: 'Contact Information',
//             defaultType: 'textfield',
//             defaults: {
//                 width: 280
//             	},
//             items: 
//             [
//             	{
//                     fieldLabel: 'First Name',
//                     emptyText: 'First Name',
//                     name: 'first'
//                 }, {
//                     fieldLabel: 'Last Name',
//                     emptyText: 'Last Name',
//                     name: 'last'
//                 }, {
//                     fieldLabel: 'Company',
//                     name: 'company'
//                 }, {
//                     fieldLabel: 'Email',
//                     name: 'email',
//                     vtype:'email'
//                 }, 
//                {
//                     xtype: 'combo',
//                     fieldLabel: 'State',
//                     name: 'state',
//  					store : new Ext.data.SimpleStore({
// 						fields : ['abbr', 'state'],
// 						data : [
// 							['NEEDS-ACTION', GO.calendar.lang.statuses["NEEDS-ACTION"]],
// 							['CONFIRMED', GO.calendar.lang.statuses["CONFIRMED"]],
// 							//['TENTATIVE',	GO.calendar.lang.statuses["TENTATIVE"]],
// 							['CANCELLED',	GO.calendar.lang.statuses["CANCELLED"]]
// 						]
//  					}),
//                     valueField: 'abbr',
//                     displayField: 'state',
//                     typeAhead: true,
//                     queryMode: 'local',
//                     emptyText: 'Select a state...'
//                 }, {
//                     xtype: 'datefield',
//                     fieldLabel: 'Date of Birth',
//                     name: 'dob',
//                     allowBlank: false
//                 }
//            ]
//        }
//        ],

//         buttons: 
//         [
//         	{
//             	text: 'Load',
//             	handler: function(){
//                 	formPanel.getForm().load({
//                     	url: 'http://netspresso.cedille.club/go/status.json?box=netspresso01',
//                     	waitMsg: 'Loading...'
//                 	});
//             	}
//         	}, 
//         	{
//             	text: 'Submit',
//             	disabled: true,
//             	formBind: true,
//             	handler: function(){
//                 	this.up('form').getForm().submit({
//                     	url: 'http://netspresso.cedille.club/go/status.json?box=netspresso01',
//                     	submitEmptyText: false,
//                     	waitMsg: 'Saving Data...'
//                 	});
//             	}
//         	}
//         ]
//     }
//     );

// 	this.photoPanel = new Ext.Panel({
// 		title : GO.addressbook.lang.photo,
// 		layout: 'form',
// 		border:false,
// 		cls : 'go-form-panel',		
// 		autoScroll:true,
// 		labelAlign:'top',
// 		items:[	{
// 				style:'margin-bottom:15px',
// 				xtype:'button',
// 				text:GO.addressbook.lang.searchForImages,
// 				scope:this,
// 				handler:function(){
// 					var f= this.formPanel.form;
// 					var mn = f.findField('middle_name').getValue();
// 					
// 					if(mn)
// 						mn = ' '+mn+' ';
// 					else
// 						mn = ' ';
// 					
// 					var name = f.findField('first_name').getValue()+mn+f.findField('last_name').getValue();
// 					var sUrl = 'http://www.google.com/search?tbm=isch&q="'+encodeURIComponent(name)+'"';
// 					window.open(sUrl);
// 				}
// 			},
// 			{
// 				
// 				xtype:'textfield',
// 				fieldLabel:GO.addressbook.lang.downloadPhotoUrl,
// 				name:'download_photo_url',
// 				anchor:'100%'
// 			},{
// 				style:'margin-top:15px;margin-bottom:10px;',
// 				html:GO.addressbook.lang.orBrowseComputer+':',
// 				xtype:'htmlcomponent'
// 			},
// 			this.uploadFile,
// 			{
// 				style:'margin-top:15px',
// 				html:GO.addressbook.lang.currentImage+':',
// 				xtype:'htmlcomponent'
// 			},
// 			this.contactPhoto,
// 			this.deleteImageCB,
// 			this.fullImageButton
// 		]
// 	});
// 
// 	var items = [
// 		this.photoPanel,
// 	];
// 	
// 	this.formPanel = new Ext.FormPanel({
// 		waitMsgTarget:true,
// 		baseParams: {},
// 		border: false,
// 		fileUpload : true,
// 		items: [
// 		this.tabPanel = new Ext.TabPanel({
// 			border: false,
// 			activeTab: 0,
// 			hideLabel: true,
// 			deferredRender: false,
// 			enableTabScroll:true,
// 			anchor:'100% 100%',
// 			items: items
// 		})
// 		]
// 	});
// 
// 	this.items= this.formPanel;
// 
// 	this.buttons= [
// 	{
// 		text: GO.lang['cmdOk'],
// 		handler:function(){
// 			this.saveContact(true);
// 		},
// 		scope: this
// 	},
// 	{
// 		text: GO.lang['cmdClose'],
// 		handler: function()
// 		{
// 			this.hide();
// 		},
// 		scope: this
// 	}
// 	];
	
// 	config.items = [			
// 		this.configForm
// 		];
// 
// 		config.items = [
// 		{
//             	xtype: 'fieldset',
//             	title: 'Configuration',
//             	defaultType: 'textfield',
//             	defaults: {
//                 	width: 280
//             	},
//             
//             items: [
//             	{
//                     fieldLabel: 'First Name',
//                     emptyText: 'First Name',
//                     name: 'first'
//                 }, 
//                 {
//                     fieldLabel: 'Last Name',
//                     emptyText: 'Last Name',
//                     name: 'last'
//                 }, 
//                 {
//                     fieldLabel: 'Company',
//                     name: 'company'
//                 }, 
//                 {
//                     fieldLabel: 'Email',
//                     name: 'email',
//                     vtype:'email'
//                 }, 
//                 {
//                     xtype: 'combo',
//                     fieldLabel: 'State',
//                     name: 'state',
//  					store : new Ext.data.SimpleStore({
// 						fields : ['abbr', 'state'],
// 						data : [
// 							['NEEDS-ACTION', GO.calendar.lang.statuses["NEEDS-ACTION"]],
// 							['CONFIRMED', GO.calendar.lang.statuses["CONFIRMED"]],
// 							//['TENTATIVE',	GO.calendar.lang.statuses["TENTATIVE"]],
// 							['CANCELLED',	GO.calendar.lang.statuses["CANCELLED"]]
// 						]
//  					}),
//  				    valueField: 'abbr',
//                     displayField: 'state',
//                     //typeAhead: true,
//                     //queryMode: 'local',
//                     mode: 'local',
//                     emptyText: 'Select a state...'
//                 }, 
//                 {
//                     xtype: 'datefield',
//                     fieldLabel: 'Date of Birth',
//                     name: 'dob',
//                     allowBlank: false
//                 }
//             ],
// 
// 
// 		
// 			buttons :[
// 			{
//         		text: 'Reset',
//         		handler: function() {
//             		this.getForm().reset();
//         		}
// 
//         	}
//         	],
// 			
// 		}
// 		];
//         config.buttons =  [{
//             text: 'Load',
//             handler: function(){
//                 formPanel.getForm().load({
//                     url: 'http://netspresso.cedille.club/go/status.json?box=netspresso01',
//                     waitMsg: 'Loading...'
//                 });
//             }
//         }, {
//             text: 'Submit',
//             disabled: true,
//             formBind: true,
//             handler: function(){
//                 this.up('form').getForm().submit({
//                     url: 'http://netspresso.cedille.club/go/status.json?box=netspresso01',
//                     submitEmptyText: false,
//                     waitMsg: 'Saving Data...'
//                 });
//             }
//         }];


// 	config.keys = [{
// 			key: Ext.EventObject.ENTER,
// 			fn: function(){
// 				this.pressButton('ok');
// 			},
// 			scope:this
// 		}];
//
	
	GO.netspresso.NetspressoConfig.superclass.constructor.call(this, config);
	
// 		this.addEvents({
// 		'save':true
// 	});

}

/*
 * Extend our NetspressoConfig from the ExtJS Panel
 */
//Ext.extend(GO.netspresso.NetspressoConfig, GO.Window, {
Ext.extend(GO.netspresso.NetspressoConfig, Ext.Panel,{

});





