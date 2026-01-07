$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function ajaxCall(url, method, data, successCallback, errorCallback) {
    $.ajax({
        url: url,
        type: method,
        data: data,
        processData: false,
        contentType: false,
        success: successCallback,
        error: errorCallback
    });
}