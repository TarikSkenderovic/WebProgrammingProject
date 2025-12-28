// AuthService: Manages login, logout, token, and user data.
const AuthService = {
    login: function(email, password, successCallback, errorCallback) {
        ApiService.call("/login", "POST", { email, password }, (response) => {
            AuthService.saveToken(response.token);
            if (successCallback) successCallback(response);
        }, errorCallback);
    },
    logout: function() {
        localStorage.removeItem("jwt_token");
        window.location.hash = "#login";
        UI.updateNavbar();
    },
    saveToken: function(token) {
        localStorage.setItem("jwt_token", token);
    },
    getToken: function() {
        return localStorage.getItem("jwt_token");
    },
    getUser: function() {
        const token = AuthService.getToken();
        if (!token) return null;
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.user;
        } catch (e) {
            console.error("Error decoding token:", e);
            AuthService.logout();
            return null;
        }
    }
};

// Form Handlers for login/register.
const FormHandler = {
    handleRegistration: function(e) {
        e.preventDefault();
        const userData = {
            username: $("#registerUsername").val(),
            email: $("#registerEmail").val(),
            password: $("#registerPassword").val(),
            first_name: $("#registerFirstName").val(),
            last_name: $("#registerLastName").val()
        };
        if (userData.password !== $("#confirmPassword").val()) {
            alert("Passwords do not match.");
            return;
        }
        UserService.register(userData,
            (response) => {
                alert("Registration successful! Please log in.");
                window.location.hash = "#login";
            },
            (error) => {
                alert("Registration failed: " + (error.error || "Unknown error."));
            }
        );
    },
    handleLogin: function(e) {
        e.preventDefault();
        const email = $("#loginEmail").val();
        const password = $("#loginPassword").val();
        AuthService.login(email, password,
            (response) => {
                window.location.hash = "#dashboard";
                UI.updateNavbar();
            },
            (error) => {
                alert("Login failed: " + (error.error || "Invalid credentials."));
            }
        );
    }
};