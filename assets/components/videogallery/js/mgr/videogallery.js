var videoGallery = function (config) {
    config = config || {};
    videoGallery.superclass.constructor.call(this, config);
};
Ext.extend(videoGallery, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    form: {},
    panel: {},
    config: {},
    view: {},
    combo: {},
    sbs: {},
    utils: {},
    ux: {},
});
Ext.reg('videogallery', videoGallery);

videoGallery = new videoGallery();