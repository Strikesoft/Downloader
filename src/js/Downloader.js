import $ from 'jquery';
import Utils from './Utils.js';
import ModalSecure from './ModalSecure.js';

class Downloader {

    constructor() {
        this.$input = $('#inputUrl');
        this.$formGroupUrl = $('#formGrpUrl');
        this.$btnDl = $('#btnDownload');
        this.$divResultUrl = $('#divResultUrl');
        this.$loader = $('.loader');
        this.$downloadLink = $('#downloadLink');
        this.$errorMsg = $('#errorMsg');
        this._modalSecure = new ModalSecure();
    }

    // private

    _isUrl(str) {
        return /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(str);
    }

    _reset() {
        this.$input.removeClass('form-control-danger');
        this.$formGroupUrl.removeClass('has-danger');
        this.$divResultUrl
            .removeClass('alert-success alert-danger')
            .addClass('hide');
        this.$downloadLink.addClass('hide');
        this.$errorMsg.addClass('hide');
    }

    _download() {
        if (this._modalSecure.isSecure() && !this._modalSecure.isLogged()) {
            // TODO : display modal
            return;
        }
        if (this._checkDownload()) {
            this._launchDownload();
        }
    }

    _checkDownload() {
        this._reset();
        const tmpUrl = this.$input.val();
        // URL must be under 2000 characters
        if (!this._isUrl(tmpUrl) || tmpUrl.length > 2000) {
            this.$input.addClass('form-control-danger');
            this.$formGroupUrl.addClass('has-danger');
            return false;
        }

        this.$btnDl.addClass('hide');
        this.$loader.removeClass('hide');
        return true;
    }

    _launchDownload() {
        Utils.ajax({
            method: 'POST',
            data: {
                url: tmpUrl
            },
            callbackSuccess: (data) => {
                if (typeof data.downloadLink !== 'undefined') {
                    this.$downloadLink
                        .attr('href', data.downloadLink)
                        .removeClass('hide');
                    $('#titleDownload').html(data.filename);
                    this.$divResultUrl
                        .addClass('alert-success')
                        .removeClass('hide');
                }
                else if (typeof data.error !== 'undefined') {
                    this.$divResultUrl
                        .addClass('alert-danger')
                        .removeClass('hide');
                    this.$errorMsg
                        .removeClass('hide')
                        .html(data.error);
                }
                this.$btnDl.removeClass('hide');
                this.$loader.addClass('hide');
            },
            callbackError: () => {
                this.$btnDl.removeClass('hide');
                this.$loader.addClass('hide');
            }
        });
    }

    // public

    initListeners() {
        this.$btnDl.on('click', () => { this._download(); });
        $(window).on(this._modalSecure.getCheckSecureEvent(), () => {
            this.$btnDl.removeClass('disabled');
        });
        this._modalSecure.checkSecure();
    }
}

$(function() {
    const downloader = new Downloader();
    downloader.initListeners();
});
