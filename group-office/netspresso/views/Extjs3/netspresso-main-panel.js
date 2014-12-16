var Ext = require('../ext');
var GO = require('../GO');
var _ = require('lodash');
var Grid = require('./netspresso-grid');
var Panel = require('./netspresso-panel');
 
var MainPanelFactory = function(config){
    config = config || {};
    
    // Defaults for the Config
    _.defaults(config, {
        layout: 'border'
    });

    // Setup the Panels
    var centerPanel = new Grid({
        region:'center',
        id:'pm-center-panel',
        border:true
    });

    var eastPanel = new Panel({
        region:'east',
        id:'pm-east-panel',
        width:440,
        border:true
    });

    // Set the items
    config.items = [centerPanel, eastPanel];

    // And return the new panel
    return new Ext.Panel(config);
};

module.exports = MainPanelFactory;