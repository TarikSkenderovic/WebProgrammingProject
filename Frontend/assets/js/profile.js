const ProfileActions = {
    
    loadProfile: function() {
        const user = AuthService.getUser(); 
        if (!user) {
            alert("You must be logged in to view your profile.");
            window.location.hash = "#login";
            return;
        }

        UserService.getById(user.id, 
            (fullUserData) => {
                $("#profileFirstName").val(fullUserData.first_name);
                $("#profileLastName").val(fullUserData.last_name);
                $("#profileUsername").val(fullUserData.username);
                $("#profileEmail").val(fullUserData.email);
                
                
                $("#profile-name-display").text(fullUserData.first_name + ' ' + fullUserData.last_name);
                $("#profile-email-display").text(fullUserData.email);
            },
            (error) => {
                console.error("Failed to fetch profile data:", error);
                alert("Could not load your profile data.");
            }
        );
    },

    
    handleProfileUpdate: function(e) {
        e.preventDefault(); 
        const user = AuthService.getUser();
        if (!user) return; 

        // Get the updated data from the form fields
        const updatedData = {
            first_name: $("#profileFirstName").val(),
            last_name: $("#profileLastName").val(),
            username: $("#profileUsername").val(),
            email: $("#profileEmail").val(), 
            role: user.role
        };

        // Call the API to update the user
        UserService.updateUser(user.id, updatedData,
            (response) => {
                alert("Profile updated successfully!");
                $("#profile-name-display").text(response.first_name + ' ' + response.last_name);
            },
            (error) => {
                alert("Profile update failed: " + (error.error || "Unknown error."));
            }
        );
    },

    // This function handles the password change form
    handleChangePassword: function(e) {
        e.preventDefault();
        const user = AuthService.getUser();
        if (!user) return;

        const currentPassword = $("#currentPassword").val();
        const newPassword = $("#newPassword").val();
        const confirmNewPassword = $("#confirmNewPassword").val();

        if (newPassword !== confirmNewPassword) {
            alert("New passwords do not match.");
            return;
        }

        const data = {
            user_id: user.id,
            current_password: currentPassword,
            new_password: newPassword
        };

        UserService.changePassword(data,
            (response) => {
                alert("Password changed successfully! Please log in again for security.");
                bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                AuthService.logout(); 
            },
            (error) => {
                alert("Failed to change password: " + (error.error || "Unknown error."));
            }
        );
    },

    
    openDeleteAccountModal: function() {
        const user = AuthService.getUser();
        if (!user) return;
        $("#confirmDeleteBtn").data("entityId", user.id).data("entityType", "self-delete");
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    },
    
    handleDeleteAccount: function() {
        const userId = $("#confirmDeleteBtn").data("entityId");
        UserService.deleteUser(userId, () => {
            alert("Your account has been permanently deleted.");
            AuthService.logout(); 
        }, (error) => { /* ... */ });
    }
};