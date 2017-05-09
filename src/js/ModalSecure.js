import $ from 'jquery';
import 'bootstrap';
import Utils from './Utils.js';

export default class ModalSecure {

    constructor() {
        this._secure = false;
        this._logged = false;
        this._rememberPass = false;
        this._checkSecureDone = false;
        this._events = {
            CHECKSECURE : 'dl.checksecure'
        }
    }

    // public

    isSecure() {
        return this._secure;
    }

    isLogged() {
        return this._logged;
    }

    getCheckSecureEvent() {
        return this._events.CHECKSECURE;
    }

    checkSecure() {
        Utils.ajax({
            url: '/checkSecure',
            callbackSuccess: (data) => {
                this._checkSecureDone = true;
                $(window).trigger($.Event(this._events.CHECKSECURE));
                if (typeof data.isSecure !== "undefined") {
                    this._secure = data.isSecure;
                }
            }
        });
    }
}
