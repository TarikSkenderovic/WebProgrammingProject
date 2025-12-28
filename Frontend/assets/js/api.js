const API_BASE_URL = "http://localhost/TarikSkenderovic/WebProgrammingProject/Backend";


window.selectedCourseId = null;

// ApiService: A central place for making all API calls.
const ApiService = {
    call: function(endpoint, method, data, successCallback, errorCallback) {
        const token = AuthService.getToken();
        $.ajax({
            url: API_BASE_URL + endpoint,
            type: method,
            contentType: "application/json",
            data: data ? JSON.stringify(data) : undefined,
            beforeSend: function(xhr) {
                if (token) {
                    xhr.setRequestHeader("Authorization", "Bearer " + token);
                }
            },
            
        }).done(function(response) {
            if (successCallback) successCallback(response);
        }).fail(function(jqXHR) {
            if (errorCallback) errorCallback(jqXHR.responseJSON || { error: "An unknown error occurred." });
        });
    }
};
