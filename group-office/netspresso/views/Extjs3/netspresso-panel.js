var Ext = require('../ext');
var GO = require('../GO');

module.exports = Ext.extend(GO.DisplayPanel,{
    model_name : "GO_Netspresso_Model_Netspresso",
    stateId : 'pm-netspresso-panel',
    noFileBrowser : true,

    initComponent : function(){ 
        
        this.loadUrl=('netspresso/netspresso/store');
        
        this.template = 
                '<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
                    '<tr>'+
                        '<td colspan="2" class="display-panel-heading">{calendar_name}</td>'+
                    '</tr>'+
                    '<tr>'+
                        '<td>'+GO.netspresso.lang.event_id+':</td>'+
                        '<td>{temperature}</td>'+
                    '</tr>'+
                    '<tr>'+
                        '<td>'+GO.netspresso.lang.start_time+':</td>'+
                        '<td>{date}</td>'+
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
        return GO.DisplayPanel.prototype.initComponent.apply(this, arguments);
    }
});