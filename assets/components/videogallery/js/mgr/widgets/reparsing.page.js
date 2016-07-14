videoGallery.page.Reparsing = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        formpanel: 'videogallery-form-reparsing',
        buttons: [{
            process: 'mgr/reparsing/run',
            text: _('videogallery_buttons_run'),
            id: 'videogallery-btn-run',
            cls: 'primary-button',
            method: 'remote',
        }, {
            text: _('reset'),
            id: 'videogallery-btn-reset',
        }],
        components: [{
            xtype: 'videogallery-form-reparsing',
        }]
    });
    videoGallery.page.Reparsing.superclass.constructor.call(this, config);
};
Ext.extend(videoGallery.page.Reparsing, MODx.Component);
Ext.reg('videogallery-page-reparsing', videoGallery.page.Reparsing);