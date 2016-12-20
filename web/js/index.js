'use strict';

var Downloader = (function () {
  function isUrl(str) {
    return /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(str);
  }

  function ajax(callbacks) {
    $.ajax({
      method: 'POST',
      url: window.location.href,
      success: callbacks.success,
      error: callbacks.error
    });
  }

  return {
    $input: null,
    $formGroupUrl: null,
    $btnDl: null,
    $loader: null,
    download: function () {
      this.$input.removeClass('form-control-danger');
      this.$formGroupUrl.removeClass('has-danger');
      var tmpUrl = this.$input.val();
      if (!isUrl(tmpUrl)) {
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
        success: function () {
          _Downloader.$btnDl.removeClass('hide');
          _Downloader.$loader.addClass('hide');
        },
        error: function () {
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
  Downloader.$loader = $('.loader');
  Downloader.$btnDl.on('click', function () { Downloader.download(); });
});
