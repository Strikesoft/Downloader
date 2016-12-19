'use strict';

var Downloader = {
  $input : null,
  $formGroupUrl : null,
  isUrl : function () {
      return /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(this.$input);
  },
  download : function () {
    this.$input.removeClass('form-control-danger');
    this.$formGroupUrl.removeClass('has-danger');
    if (!this.isUrl()) {
      this.$input.addClass('form-control-danger');
      this.$formGroupUrl.addClass('has-danger');
      return;
    }
  }
};

$(function() {
  Downloader.$input = $('#inputUrl');
  Downloader.$formGroupUrl = $('#formGrpUrl');
  $('#btnDownload').on('click', function () { Downloader.download(); });
});
