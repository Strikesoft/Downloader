import $ from 'jquery';

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

    function reset(instance) {
        instance.$input.removeClass('form-control-danger');
        instance.$formGroupUrl.removeClass('has-danger');
        instance.$divResultUrl
            .removeClass('alert-success alert-danger')
            .addClass('hide');
        instance.$downloadLink.addClass('hide');
        instance.$errorMsg.addClass('hide');
    }

    return {
        $input: null,
        $formGroupUrl: null,
        $btnDl: null,
        $loader: null,
        $divResultUrl: null,
        download: function () {
            reset(this);
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
                        _Downloader.$downloadLink
                            .attr('href', data.downloadLink)
                            .removeClass('hide');
                        $('#titleDownload').html(data.filename);
                        _Downloader.$divResultUrl
                            .addClass('alert-success')
                            .removeClass('hide');
                    }
                    else if (typeof data.error !== 'undefined') {
                        _Downloader.$divResultUrl
                            .addClass('alert-danger')
                            .removeClass('hide');
                        _Downloader.$errorMsg
                                    .removeClass('hide')
                                    .html(data.error);
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
    Downloader.$downloadLink = $('#downloadLink');
    Downloader.$errorMsg = $('#errorMsg');
});
