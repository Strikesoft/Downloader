import $ from 'jquery';

export default class Utils {
    static ajax(params) {
        if (typeof params.method === "undefined") {
            params.method = 'GET';
        }

        if (typeof params.url === "undefined") {
            params.url = window.location.href;
        }

        $.ajax({
            method: params.method,
            url: params.url,
            data: params.data,
            success: params.callbackSuccess,
            error: params.callbackError
        });
    }
}
