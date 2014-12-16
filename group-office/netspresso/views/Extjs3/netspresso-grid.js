var Ext = require('../ext');
var GO = require('../GO');
var _ = require('lodash');
var Dialog = require('./netspresso-dialog');

var GridFactory = function(config){
    config = config || {};
    
    // Defaults for the Config
    _.defaults(config, {
        title: GO.netspresso.lang.grid_title,
        layout: 'fit',
        autoScroll: true,
        split: true,
        autoLoadStore: true,
        paging: true,
        loadMask: true,
        editDialogClass: Dialog
    });

    // And the forced extension. Initialized objects should be here
    _.extend(config, {
        sm: new Ext.grid.RowSelectionModel(),
        cm: new Ext.grid.ColumnModel({
            defaults: {
                sortable: true
            },
            columns : [
                {
                hereader: '#',
                readOnly: true,
                dataIndex: 'id',
                renderer: function(value, cell){ 
                    cell.css = "readonlycell";
                    return value;
                },
                width: 40,
                align: "right"
                }, {
                    header: GO.netspresso.lang.calendar_name,
                    dataIndex: 'calendar_name',
                    width: 200
                }, {
                    header: GO.netspresso.lang.start_time,
                    dataIndex: 'start_time',
                    width: 140
                }, {
                    header: GO.netspresso.lang.end_time,
                    dataIndex: 'end_time',
                    width: 140
                }, {
                    header: GO.netspresso.lang.status,
                    dataIndex: 'status',
                    label: GO.calendar.lang.statuses["NEEDS-ACTION"],
                    width: 140,
                    align: "right"
                }
            ],
            store: new GO.data.JsonStore({
                url: GO.url('netspresso/netspresso/store'),
                fields: ['id','event_id','calendar_name','start_time','end_time','status', 'is_organizer'],
                remoteSort: true,
                model: 'GO_Netspresso_Model_Netspresso'
            }),
            view: new Ext.grid.GridView({
                emptyText: GO.lang.strNoItemse,
                
                autoFill: true,
                forceFit: true
            }),
            tbar: new Ext.Toolbar({
                items: [
                    GO.lang.strSearch + ':',
                    new GO.form.SearchField({
                        store: config.store,
                        width:320
                    })
                ]
            })
    }),
    
        //Defining the data store for the grid
        store: new GO.data.JsonStore({
            url: GO.url('netspresso/netspresso/store'),
            fields: ['id','event_id','calendar_name','start_time','end_time','status', 'is_organizer'],
            remoteSort: true,
            model: 'GO_Netspresso_Model_Netspresso'
        })
    });
    
    // And return the new Grid
    return new GO.grid.GridPanel(config);

};

module.exports = GridFactory;