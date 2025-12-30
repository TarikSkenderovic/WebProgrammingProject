// This file handles all authentication logic (login, logout, token)
// and the forms for login and registration.

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

        // --- VALIDATION LOGIC ---
        if (!userData.username || !userData.email || !userData.password) {
            toastr.error("Please fill in all required fields: Username, Email, and Password.");
            return;
        }
        if (userData.email.indexOf('@') === -1) {
            toastr.error("Please enter a valid email address.");
            return;
        }
        if (userData.password.length < 8) {
            toastr.warning("Password must be at least 8 characters long.");
            return;
        }
        if (userData.password !== $("#confirmPassword").val()) {
            toastr.error("Passwords do not match.");
            return;
        }
        
        // --- API CALL ---
        UserService.register(userData,
            (response) => {
                toastr.success("Registration successful! Please log in.");
                window.location.hash = "#login";
            },
            (error) => {
                toastr.error("Registration failed: " + (error.error || "Unknown error."));
            }
        );
    },

    handleLogin: function(e) {
        e.preventDefault();
        const email = $("#loginEmail").val();
        const password = $("#loginPassword").val();

        if (!email || !password) {
            toastr.error("Please enter both email and password.");
            return;
        }

        AuthService.login(email, password,
            (response) => {
                toastr.success("Login successful!");
                window.location.hash = "#dashboard";
                UI.updateNavbar();
            },
            (error) => {
                toastr.error("Login failed: " + (error.error || "Invalid credentials."));
            }
        );
    }
};