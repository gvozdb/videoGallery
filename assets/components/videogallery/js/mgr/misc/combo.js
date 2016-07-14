Ext.namespace('videoGallery.combo');

// videoGallery.combo.Fields = function (config) {
//     config = config || {};
//     config.name = config.name || 'fields';
//     config.hiddenName = config.hiddenName || config.name;
//
//     Ext.applyIf(config, {
//         id: 'videogallery-combo-fields',
//         name: config.name,
//         hiddenName: config.hiddenName,
//         fieldLabel: _('videogallery_combo_fields'),
//         emptyText: _('videogallery_combo_fields_select'),
//         listEmptyText: '<div style="padding:10px">' + _('sental_combo_empty') + '</div>',
//         anchor: '100%',
//         store: new Ext.data.JsonStore({
//             id: 0,
//             root: 'results',
//             totalProperty: 'total',
//             autoLoad: true,
//             fields: ['value', 'display'],
//             url: videoGallery.config['connector_url'],
//             baseParams: {
//                 action: 'sental/processor',
//                 // filter: config.filter || 0,
//                 // exclude: config.exclude || '[]',
//             },
//         }),
//         valueField: 'value',
//         displayField: 'display',
//         editable: true,
//         listeners: {},
//     });
//     videoGallery.combo.Fields.superclass.constructor.call(this, config);
//
//     var that = this;
//     this.on('expand', function () {
//         that.getStore().load();
//     }, this);
// };
// Ext.extend(videoGallery.combo.Fields, MODx.combo.ComboBox);
// Ext.reg('videogallery-combo-fields', videoGallery.combo.Fields);


videoGallery.sbs.Fields = function (config) {
    config = config || {};
    config.name = config.name || 'fields';
    config.hiddenName = config.hiddenName || config.name;

    Ext.applyIf(config, {
        xtype: 'superboxselect',
        name: config.name,
        hiddenName: config.hiddenName,
        originalName: config.name,

        store: new Ext.data.JsonStore({
            id: 'videogallery-superboxselect-fields',
            root: 'results',
            autoLoad: true,
            autoSave: false,
            totalProperty: 'total',
            fields: ['value', 'display', 'field'],
            url: videoGallery.config['connector_url'],
            baseParams: {
                action: 'mgr/combo/getfields',
            }
        }),

        mode: 'remote',
        valueField: 'value',
        displayField: 'display',

        allowBlank: true,
        allowAddNewData: false,
        addNewDataOnBlur: false,
        forceSameValueQuery: true,
        editable: false,
        msgTarget: 'under',
        resizable: true,
        forceFormValue: false,

        anchor: '100%',
        minChars: 2,
        pageSize: 20,
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls: 'x-form-trigger',
        clearBtnCls: 'x-form-trigger',

        tpl: new Ext.XTemplate(''
            + '<tpl for="."><div class="x-combo-list-item">'
            + '<span>'
            + '<b>{display}</b>'
            + '<tpl if="field">'
            + '<span class="field">'
            + '<small> / {field}</small>'
            + '</span>'
            + '</tpl>'
            + '</span>'
            + '</div></tpl>'
            + '', {
                compiled: true,
            }
        ),

        listeners: {},
    });
    videoGallery.sbs.Fields.superclass.constructor.call(this, config);
};
Ext.extend(videoGallery.sbs.Fields, Ext.ux.form.SuperBoxSelect);
Ext.reg('videogallery-superboxselect-fields', videoGallery.sbs.Fields);