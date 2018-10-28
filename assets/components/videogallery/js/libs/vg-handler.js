(function () {
    function videoGalleryHandler(config) {
        if (typeof config['tv'] == 'undefined') {
            console.error('Error! Tv id not found!');
            return;
        } else if (typeof config['resource'] == 'undefined') {
            console.error('Error! Resource id not found!');
            return;
        }

        this.config = config;
        var self = this;

        function setImage(str) {
            if (typeof str == 'undefined' || !str) {
                str = '';
            }
            var element = document.querySelector(self.config.selectors['videoImageBlock']);
            if (element) {
                element.innerHTML = str
                    ? '<img width="140" height="105" src="' + str + '" />'
                    : '';
            }
        }

        function setVideo(str) {
            if (typeof str == 'undefined' || !str) {
                str = '';
            }
            var element = document.querySelector(self.config.selectors['videoEmbedBlock']);
            if (element) {
                element.innerHTML = str
                    ? '<iframe width="140" height="105" src="' + str + '" frameborder="0" allowfullscreen></iframe>'
                    : '';
            }
        }

        function setError(str) {
            if (typeof str == 'undefined' || !str) {
                str = '';
            }
            var element = document.querySelector(self.config.selectors['videoErrorBlock']);
            if (element) {
                element.textContent = str;
            }
        }

        function reset() {
            setError();
            setImage();
            setVideo();
            self['tv'].value = '';

            self.config.callbacks.reset();
        }

        this.initialize = function () {
            var tmp = {
                tv: self.config['tv'],
                tvid: self.config['tvid'],
                resource: self.config['resource'],
                actionUrl: '/assets/components/videogallery/getvideo.php',
                selectors: {
                    videoUrlInput: '#vgUrl_' + self.config['tv'],
                    videoEmbedBlock: '#vgVideo_' + self.config['tv'],
                    videoImageBlock: '#vgImage_' + self.config['tv'],
                    videoErrorBlock: '#vgError_' + self.config['tv'],
                    tvInput: '#vgTv_' + self.config['tv'],
                    // tvErrorBlock: '#vgTvError_' + self.config['tv'],
                },
                callbacks: {
                    success: function (response) {
                        // console.log('response', response);
                    },
                    reset: function (that) {
                        // console.log('reset', that);
                    },
                },
            };
            if (typeof self.config['actionUrl'] != 'undefined') {
                tmp['actionUrl'] = self.config['actionUrl'];
            }
            if (typeof self.config['callbacks'] != 'undefined') {
                for (var i in self.config['callbacks']) {
                    if (self.config.callbacks.hasOwnProperty(i)) {
                        tmp.callbacks[i] = self.config.callbacks[i];
                    }
                }
            }
            if (typeof self.config['selectors'] != 'undefined') {
                for (var i in self.config['selectors']) {
                    if (self.config.selectors.hasOwnProperty(i)) {
                        tmp.selectors[i] = self.config.selectors[i];
                    }
                }
            }
            self.config = tmp;

            self['tv'] = document.querySelector(self.config.selectors['tvInput']);
            self['video'] = document.querySelector(self.config.selectors['videoUrlInput']);
            if (self['tv'] && self['video']) {
                // Парсим уже имеющийся в поле видеоролик
                try {
                    var data = JSON.parse(self.tv['value']);
                    setImage(data['image']);
                    setVideo(data['video']);
                } catch (e) {
                    console.error('[videoGallery] Неудалось распарсить JSON строку. Может это не JSON?');
                }

                // При вводе в поле ссылки на видео
                self.video.addEventListener("input", function (e) {
                    // console.log('video input e', e);

                    // Сбрасываем форму
                    reset();

                    // Получаем ссылку на видео
                    var url = e.target.value;
                    if (!url) {
                        return;
                    }

                    // Формируем данные для отправки на сервер
                    var formData = new FormData();
                    formData.append('resource', self.config['resource']);
                    formData.append('tv', self.config['tvid']);
                    formData.append('video', url);

                    // Отсылаем
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", self.config['actionUrl']);
                    xhr.send(formData);

                    // Обработка ответа
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState != 4) {
                            return;
                        }

                        if (xhr.status == 200) {
                            try {
                                var data = JSON.parse(xhr.responseText);
                                // console.log('data', data);

                                if (data.success) {
                                    if (data.object.hasOwnProperty('json')) {
                                        self['tv'].value = data.object.json;
                                    }

                                    setImage(data.object['image']);
                                    setVideo(data.object['video']);

                                    self.config.callbacks.success(data);
                                } else {
                                    setError(data.message);
                                }
                            } catch (e) {
                                setError('Неудалось обработать ответ от сервера. Попробуйте вставить ссылку на видеоролик заново.');
                            }
                        } else {
                            setError(xhr.status + ': ' + xhr.statusText);
                        }
                    };
                });
            }
        };

        this.initialize();
    }

    window.videoGalleryHandler = videoGalleryHandler;
})();