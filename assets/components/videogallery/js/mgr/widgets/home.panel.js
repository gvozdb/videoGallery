videoGallery.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('videogallery') + '</h2>',
            cls: '',
            style: {margin: '15px 0'},
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('videogallery_tab_reparsing'),
                layout: 'anchor',
                items: [{
                    html: _('videogallery_reparsing_intro_msg'),
                    cls: 'panel-desc',
                    border: false,
                }, {
                    xtype: 'videogallery-form-reparsing',
                    cls: 'main-wrapper',
                }],
            }],
        }],
    });
    videoGallery.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(videoGallery.panel.Home, MODx.Panel);
Ext.reg('videogallery-panel-home', videoGallery.panel.Home);
