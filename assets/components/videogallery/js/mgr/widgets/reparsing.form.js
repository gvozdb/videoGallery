videoGallery.form.Reparsing = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'videogallery-form-reparsing';
    }

    Ext.applyIf(config, {
        url: videoGallery.config['connector_url'],
        params: {
            action: 'mgr/reparsing/run',
        },
        cls: 'container',
        buttonAlign: 'center',
        anchor: '100%',

        items: this.getFields(config),
        listeners: this.getListeners(config),
        buttons: this.getButtons(config),
        keys: this.getKeys(config),
    });
    videoGallery.form.Reparsing.superclass.constructor.call(this, config);

    this.on('afterrender', function () {
        this.loadMask = new Ext.LoadMask(this.form.el, {
            msg: _('videogallery_msg_please_wait'),
        });
    }, this);
};
Ext.extend(videoGallery.form.Reparsing, MODx.FormPanel, {

    getFields: function (config) {
        return [{
            layout: 'form',
            labelWidth: 250,
            width: '100%',
            autoHeight: true,
            border: true,
            buttonAlign: 'center',
            items: [{
                xtype: 'panel',
                defaults: {msgTarget: 'under', border: false},
                cls: 'main-wrapper',
                layout: 'form',
                items: [{
                    xtype: 'videogallery-superboxselect-fields',
                    id: config.id + '-fields',
                    name: 'fields',
                    fieldLabel: _('videogallery_reparsing_sbs_fields'),
                    emptyText: _('videogallery_reparsing_sbs_fields_empty'),
                    anchor: '100%',
                }]
            }]
        }];
    },

    getListeners: function (config) {
        return [];
    },

    getButtons: function (config) {
        return [{
            xtype: 'tbtext',
            id: config.id + '-loader',
            html: '&nbsp;',
            cls: 'videogallery-preloader',
            hidden: true,
        }, {
            xtype: 'button',
            id: config.id + '-btn-run',
            stop: false,
            text: _('videogallery_buttons_run'),
            handler: function (btn) {
                this.submit(btn.stop);
            },
            scope: this,
            cls: 'primary-button',
        }, {
            xtype: 'button',
            id: config.id + '-btn-reset',
            text: _('videogallery_buttons_reset'),
            handler: this.reset,
            scope: this,
        }];
    },

    getKeys: function () {
        return [{
            key: Ext.EventObject.ENTER,
            fn: function () {
                this.submit();
            },
            scope: this
        }];
    },

    stopping: function (done) {
        // console.log('stopping', this);

        if (typeof done == 'undefined') {
            var done = false;
        }

        this.buttons[1].setText(_('videogallery_buttons_continue'));
        this.buttons[1].stop = false;
        this.buttons[0].hide();
        this.running = false;
        this.loadMask.hide();

        if (done) {
            this.buttons[1].setText(_('videogallery_buttons_run'));
            MODx.msg.alert(_('done'), _('videogallery_msg_reparsing_done'));

            this.params.done = false;
            this.params.offset = 0;
            this.params.time = 0;
        }
    },

    updateLogStats: function (data) {
        // console.log('updateLogStats data', data);

        if (typeof data != 'object') {
            return;
        } else {
        }
    },

    submit: function (stop, auto) {
        // console.log('submit this', this);

        if (typeof stop == 'undefined') {
            var stop = false;
        }
        if (typeof auto == 'undefined') {
            var auto = false;
        }
        var form = this.getForm();
        var params = this.params;
        var values = form.getFieldValues();
        for (var i in values) {
            if (i != undefined && values.hasOwnProperty(i)) {
                params[i] = values[i];
            }
        }

        // Ставим на кнопку текст "Остановить" и меняем статус запуска на true
        if (!stop && !auto) {
            this.buttons[1].setText(_('videogallery_buttons_pause'));
            this.buttons[1].stop = true;
            this.buttons[0].show();
            this.running = true;
        }
        // Меняем статус запуска на false
        if (stop) {
            this.buttons[1].stop = false;
            this.running = false;
        }
        this.loadMask.show();

        // Запускаем/Продолжаем выгрузку
        if (this.running) {
            MODx.Ajax.request({
                url: this.url,
                params: params,
                listeners: {
                    success: {
                        fn: function (resp) {
                            // console.log('success resp', resp);
                            // console.log('success this.running', this.running);

                            var res = resp.object;
                            params['fields'] = res['fields'];
                            params['offset'] = res['offset'];
                            params['time'] = res['time'];
                            console.log('res', res);
                            // console.log('res.offset', res['offset']);

                            this.updateLogStats(res.log);

                            if (res.done || !this.running) {
                                this.stopping(res.done);
                            } else {
                                this.submit(stop, true);
                            }
                        },
                        scope: this
                    },
                    failure: {
                        fn: function (resp) {
                            console.log('failure', resp);

                            this.stopping();
                        },
                        scope: this
                    },
                }
            });
        }
    },

    reset: function () {
        var form = this.getForm();

        form.items.each(function (f) {
            if (f.name == 'status') {
                f.clearValue();
            }
            else {
                f.reset();
            }
        });
    },
});
Ext.reg('videogallery-form-reparsing', videoGallery.form.Reparsing);