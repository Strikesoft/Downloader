'use strict';

var Downloader = (function () {
    function isUrl(str) {
        return /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(str);
    }

    function ajax(params) {
        $.ajax({
            method: 'POST',
            url: window.location.href,
            data: params.data,
            success: params.callbackSuccess,
            error: params.callbackError
        });
    }

    return {
        $input: null,
        $formGroupUrl: null,
        $btnDl: null,
        $loader: null,
        $divResultUrl: null,
        download: function () {
            this.$input.removeClass('form-control-danger');
            this.$formGroupUrl.removeClass('has-danger');
            this.$divResultUrl.addClass('hide');
            var tmpUrl = this.$input.val();
            // URL must be under 2000 characters
            if (!isUrl(tmpUrl) || tmpUrl.length > 2000) {
                this.$input.addClass('form-control-danger');
                this.$formGroupUrl.addClass('has-danger');
                return;
            }
            this.$btnDl.addClass('hide');
            this.$loader.removeClass('hide');
            var _Downloader = this;
            ajax({
                data: {
                    url: tmpUrl
                },
                callbackSuccess: function (data) {
                    if (typeof data.downloadLink !== 'undefined') {
                        $('#downloadLink').attr('href', data.downloadLink);
                        $('#titleDownload').html(data.filename);
                        _Downloader.$divResultUrl.removeClass('hide');
                    }
                    _Downloader.$btnDl.removeClass('hide');
                    _Downloader.$loader.addClass('hide');
                },
                callbackError: function () {
                    _Downloader.$btnDl.removeClass('hide');
                    _Downloader.$loader.addClass('hide');
                }
            });
        }
    }
})();

$(function() {
    Downloader.$input = $('#inputUrl');
    Downloader.$formGroupUrl = $('#formGrpUrl');
    Downloader.$btnDl = $('#btnDownload');
    Downloader.$divResultUrl = $('#divResultUrl');
    Downloader.$loader = $('.loader');
    Downloader.$btnDl.on('click', function () { Downloader.download(); });
});
