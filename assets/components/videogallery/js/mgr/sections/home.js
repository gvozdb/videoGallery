videoGallery.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'videogallery-panel-home', renderTo: 'videogallery-panel-home-div'
        }]
    });
    videoGallery.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(videoGallery.page.Home, MODx.Component);
Ext.reg('videogallery-page-home', videoGallery.page.Home);