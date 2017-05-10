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
        CHECKSECURE  : 'dl.checksecure',
        SECUREPASSED : 'dl.securepassed'
    }
    this._$modal = $('#secureModal');
    this._$btnSubmit = $('#btnSubmitSecureModal');
    this._$formGroup = $('#formGrpPassword');
    this._$input = $('#inputPassword');
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
    if (inputVal === 0) {
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
