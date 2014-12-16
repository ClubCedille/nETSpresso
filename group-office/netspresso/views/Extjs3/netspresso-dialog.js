var Ext = require('../ext');
var GO = require('../GO');
var _ = require('lodash');

// Not much to do here other than compose the Dialog
module.exports = Ext.extend(GO.dialog.TabbedFormDialog , {

    customFieldType : "GO_Netspresso_Model_Netspresso",

    initComponent : function(){
        
        Ext.apply(this, {
            goDialogId:'netspresso',
            title:GO.netspresso.lang.dialog_title,
            width: 300,
            height: 160,
            formControllerUrl: 'netspresso/netspresso'
        });
        
        return GO.dialog.TabbedFormDialog.prototype.initComponent.apply(this, arguments);
    },
    
    buildForm : function () {
        this.propertiesPanel = new Ext.Panel({
            title:GO.lang.strProperties,
            cls:'go-form-panel',
            layout:'form',
            items:[
                {
                    xtype: 'textfield',
                    name: 'calendar_name',
                    //width:300,
                    anchor: '100%',
                    maxLength: 100,
                    //allowBlank:false,
                    //editable: false,
                    readOnly: true,
                    fieldLabel: GO.netspresso.lang.calendar_name
                },
                {
                    xtype:'combo',
                    fieldLabel: GO.netspresso.lang.status,
                    hiddenName:'status',
                    anchor:'100%',
                    triggerAction : 'all',
                    editable : false,
                    selectOnFocus : true,
                    //width : 148,
                    forceSelection : true,
                    mode : 'local',
                    //value : 'CONFIRMED',
                    value : 'status',
                    valueField : 'value',
                    displayField : 'text',
                    store : new Ext.data.SimpleStore({
                        fields : ['value', 'text'],
                        data : [
                        ['NEEDS-ACTION', GO.calendar.lang.statuses["NEEDS-ACTION"]],
                        ['CONFIRMED', GO.calendar.lang.statuses.CONFIRMED],
                        ['CANCELLED',   GO.calendar.lang.statuses.CANCELLED]
                        ]
                    })
                }
            ]              
        });

        this.addPanel(this.propertiesPanel);
    }
    
});