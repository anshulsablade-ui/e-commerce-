function ajaxCall(url, method, data, successCallback, errorCallback) {
    $.ajax({
        url: url,
        type: method,
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function(xhr) {
            var token = $('meta[name="csrf-token"]').attr('content');
            if (token) {
                xhr.setRequestHeader("X-CSRF-TOKEN", token);
            }
        },
        success: successCallback,
        error: errorCallback
    });
}