import $ from 'jquery';
import Utils from './Utils.js';
import ModalSecure from './ModalSecure.js';
import 'bootstrap';

class Downloader {

    constructor() {
        this._$input = $('#inputUrl');
        this._$formGroupUrl = $('#formGrpUrl');
        this._$btnDl = $('#btnDownload');
        this._$btnDl.tooltip({
            title: 'Checking security parameters...',
            trigger: 'manual'
        });
        this._$divResultUrl = $('#divResultUrl');
        this._$loader = $('.loader');
        this._$downloadLink = $('#downloadLink');
        this._$errorMsg = $('#errorMsg');
        this._modalSecure = new ModalSecure();
    }

    // private

    _reset() {
        this._$input.removeClass('form-control-danger');
        this._$formGroupUrl.removeClass('has-danger');
        this._$divResultUrl
            .removeClass('alert-success alert-danger')
            .addClass('hide');
        this._$downloadLink.addClass('hide');
        this._$errorMsg.addClass('hide');
    }

    _download() {
        if (this._$btnDl.hasClass('disabled')) {
            this._$btnDl.tooltip('show');
            const $button = this._$btnDl;
            setTimeout(() => {
                this._$btnDl.tooltip('hide');
            }, 800);
            return;
        }
		
		this._$btnDl.tooltip('dispose');
        if (this._modalSecure.isSecure() && !this._modalSecure.isLogged()) {
            this._modalSecure.showModal();
            return;
        }
        if (this._checkDownload()) {
            this._launchDownload();
        }
    }

    _checkDownload() {
        this._reset();
        const tmpUrl = this._$input.val();
        // URL must be under 2000 characters
        if (!Utils.isUrl(tmpUrl) || tmpUrl.length > 2000) {
            this._$input.addClass('form-control-danger');
            this._$formGroupUrl.addClass('has-danger');
            return false;
        }

        this._$btnDl.addClass('hide');
        this._$loader.removeClass('hide');
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
                    this._$downloadLink
                        .attr('href', data.downloadLink)
                        .removeClass('hide');
                    $('#titleDownload').html(data.filename);
                    this._$divResultUrl
                        .addClass('alert-success')
                        .removeClass('hide');
                }
                else if (typeof data.error !== 'undefined') {
                    this._$divResultUrl
                        .addClass('alert-danger')
                        .removeClass('hide');
                    this._$errorMsg
                        .removeClass('hide')
                        .html(data.error);
                }
                this._$btnDl.removeClass('hide');
                this._$loader.addClass('hide');
            },
            callbackError: () => {
                this._$btnDl.removeClass('hide');
                this._$loader.addClass('hide');
            }
        });
    }

    // public

    initListeners() {
        this._$btnDl.on('click', () => { this._download(); });
        $(window).on(this._modalSecure.getCheckSecureEvent(), () => {
            this._$btnDl.removeClass('disabled');
        });
        this._modalSecure.checkSecure();
    }
}

$(() => {
    const downloader = new Downloader();
    downloader.initListeners();
});
