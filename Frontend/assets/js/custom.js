// Main application setup and routing
$(document).ready(function() {
    $("main#spapp > section").height($(document).height() - 60);

    var app = $.spapp({ defaultView: "#dashboard", templateDir: "./frontend/views/", pageNotFound: "error_404" });
    
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // --- GLOBAL EVENT HANDLERS ---
    $(document).on('click', '.view-details-btn', function(e) { window.selectedCourseId = $(this).data('course-id'); });
    $("#confirmDeleteBtn").on("click", function() {
        const entityType = $(this).data("entityType");
        if (entityType === 'user') { UserActions.handleDelete(); } 
        else if (entityType === 'course') { CourseActions.handleDelete(); } 
        else if (entityType === 'instructor') { InstructorActions.handleDelete(); } 
        else if (entityType === 'enrollment') { EnrollmentActions.handleDelete(); } 
        else if (entityType === 'self-delete') { ProfileActions.handleDeleteAccount(); }
    });
    $("#nav-logout-btn").on("click", (e) => { e.preventDefault(); AuthService.logout(); });

    // --- DEFINE ROUTES ---
    app.route({
        view: 'dashboard',
        load: 'dashboard.html',
        onReady: () => {
            const user = AuthService.getUser();
            if (user) {
                $("#enrolled-courses-section").show();
                EnrollmentService.getByUserId(user.id, UI.renderEnrolledCourses);
            }
        }
    });
    app.route({
        view: 'courses',
        load: 'courses.html',
        onReady: () => {
            const loadFilteredCourses = () => { const filters = { search: $("#search-input").val(), level: $("#level-select").val() }; $("#course-list-container").html('<div class="text-center"><div class="spinner-border text-primary"></div></div>'); CourseService.getAll(filters, UI.renderCourseCards, (err) => { $("#course-list-container").html('<p class="text-center text-danger">Could not load courses.</p>'); }); };
            loadFilteredCourses();
            $("#search-btn").on('click', loadFilteredCourses);
            $("#level-select").on('change', loadFilteredCourses);
        }
    });
    app.route({
        view: 'course-detail',
        load: 'course-detail.html',
        onReady: () => {
            if (window.selectedCourseId) {
                CourseService.getById(window.selectedCourseId, UI.renderCourseDetail);
                ReviewActions.loadReviews(window.selectedCourseId);
                if (AuthService.getToken()) {
                    $("#add-review-section").show();
                    $("#reviewForm").off("submit").on("submit", ReviewActions.handleFormSubmit);
                } else {
                    $("#add-review-section").hide();
                }
            } else {
                 $("#course-detail-content").html("<h2>No Course Selected</h2><p>Please go back to the courses page and select a course.</p>");
            }
        }
    });
    app.route({
        view: 'profile',
        load: 'profile.html',
        onReady: () => {
            ProfileActions.loadProfile();
            $("#profileForm").on("submit", ProfileActions.handleProfileUpdate);
            $("#changePasswordForm").on("submit", ProfileActions.handleChangePassword);
            $("#delete-account-btn").on("click", ProfileActions.openDeleteAccountModal);
        }
    });
    app.route({ view: 'login', load: 'login.html', onReady: () => { $("#loginForm").on("submit", FormHandler.handleLogin); }});
    app.route({ view: 'register', load: 'register.html', onReady: () => { $("#registerForm").on("submit", FormHandler.handleRegistration); }});
    app.route({
        view: 'admin',
        load: 'admin.html',
        onReady: () => {
            const user = AuthService.getUser();
            if (user && user.role === 'admin') {
                UserActions.loadUsers();
                CourseActions.loadCourses();
                InstructorActions.loadInstructors();
                EnrollmentActions.loadEnrollments(); // THIS LINE IS NOW CORRECT
                UserService.count((data) => { $("#total-users-count").text(data.count); });
                CourseService.count((data) => { $("#total-courses-count").text(data.count); });
                EnrollmentService.count((data) => { $("#total-enrollments-count").text(data.count); });
                $("#userForm").on("submit", UserActions.handleFormSubmit);
                $("#courseForm").on("submit", CourseActions.handleFormSubmit);
                $("#instructorForm").on("submit", InstructorActions.handleFormSubmit);
            } else {
                alert("Access denied. Admin privileges required.");
                window.location.hash = "#dashboard";
            }
        }
    });

    // --- INITIALIZATION ---
    UI.updateNavbar();
    app.run();
});