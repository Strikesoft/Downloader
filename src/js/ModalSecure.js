import $ from 'jquery';
import 'bootstrap';
import Utils from './Utils.js';

export default class ModalSecure {

  constructor() {
    this._secure = false;
    this._logged = false;
    this._checkSecureDone = false;
    this._events = {
        CHECKSECURE  : 'dl.checksecure'
    }
    this._$modal = $('#secureModal');
    this._$btnSubmit = $('#btnSubmitSecureModal');
    this._$formGroup = $('#formGrpPassword');
    this._$input = $('#inputPassword');
    this._$loader = $('#loaderSecureModal');
    this._initListeners();
  }

  // private

  _initListeners() {
    this._$btnSubmit.on('click', () => {
        this._reset(false);
        if (this._checkInput()) {
            this._checkPassword();
        }
    });

    this._$modal.on('hidden.bs.modal', () => {
        this._reset(true);
    });
  }

  _checkInput() {
    const inputVal = this._$input.val();
    if (inputVal.length === 0) {
        this._$input.addClass('form-control-danger');
        this._$formGroup.addClass('has-danger');
        return false;
    }
    return true;
  }

  _reset(input) {
    if (input) {
        this._$input.val('');
    }
    this._$input.removeClass('form-control-danger');
    this._$formGroup.removeClass('has-danger');
  }

  _checkPassword() {
        this._reset(false);
        this._$loader.removeClass('hide');
        Utils.ajax({
            method: 'POST',
            url: '/checkPassword',
            data: {
                password: this._$input.val()
            },
            callbackSuccess: (data) => {
                this._$loader.addClass('hide');
                if (data.auth !== undefined && data.auth) {
                    this._logged = true;
                    this._$modal.modal('hide');
                }
                else {
                    this._$input.addClass('form-control-danger');
                    this._$formGroup.addClass('has-danger');
                }
            },
            callbackError: () => {
                this._$input.addClass('form-control-danger');
                this._$formGroup.addClass('has-danger');
                this._$loader.addClass('hide');
            }
        });
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

  showModal() {
    this._$modal.modal('show');
  }
}
