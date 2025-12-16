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
                    xhr.setRequestHeader("Authentication", "Bearer " + token);
                }
            }
        }).done(function(response) {
            if (successCallback) successCallback(response);
        }).fail(function(jqXHR) {
            if (errorCallback) errorCallback(jqXHR.responseJSON || { error: "An unknown error occurred." });
        });
    }
};

// AuthService: Manages login, logout, token, and user data.
const AuthService = {
    login: function(email, password, successCallback, errorCallback) { ApiService.call("/login", "POST", { email, password }, (response) => { AuthService.saveToken(response.token); if (successCallback) successCallback(response); }, errorCallback); },
    logout: function() { localStorage.removeItem("jwt_token"); window.location.hash = "#login"; UI.updateNavbar(); },
    saveToken: function(token) { localStorage.setItem("jwt_token", token); },
    getToken: function() { return localStorage.getItem("jwt_token"); },
    getUser: function() { const token = AuthService.getToken(); if (!token) return null; try { const payload = JSON.parse(atob(token.split('.')[1])); return payload.user; } catch (e) { console.error("Error decoding token:", e); AuthService.logout(); return null; } }
};

// Frontend Services for each entity
const UserService = {
    register: (data, success, error) => ApiService.call("/users", "POST", data, success, error),
    getAll: (success, error) => ApiService.call("/users", "GET", null, success, error),
    getById: (id, success, error) => ApiService.call(`/users/${id}`, "GET", null, success, error),
    addUser: (data, success, error) => ApiService.call("/users", "POST", data, success, error),
    updateUser: (id, data, success, error) => ApiService.call(`/users/${id}`, "PUT", data, success, error),
    deleteUser: (id, success, error) => ApiService.call(`/users/${id}`, "DELETE", null, success, error),
    count: (success, error) => ApiService.call("/users/count", "GET", null, success, error)
};
const CourseService = {
    getAll: (success, error) => ApiService.call("/courses", "GET", null, success, error),
    getById: (id, success, error) => ApiService.call(`/courses/${id}`, "GET", null, success, error),
    addCourse: (data, success, error) => ApiService.call("/courses", "POST", data, success, error),
    updateCourse: (id, data, success, error) => ApiService.call(`/courses/${id}`, "PUT", data, success, error),
    deleteCourse: (id, success, error) => ApiService.call(`/courses/${id}`, "DELETE", null, success, error),
    count: (success, error) => ApiService.call("/courses/count", "GET", null, success, error)
};
const EnrollmentService = {
    create: (data, success, error) => ApiService.call("/enrollments", "POST", data, success, error),
    delete: (id, success, error) => ApiService.call(`/enrollments/${id}`, "DELETE", null, success, error),
    getByUserId: (userId, success, error) => ApiService.call(`/enrollments/user/${userId}`, "GET", null, success, error),
    check: (userId, courseId, success, error) => ApiService.call(`/enrollments/check?user_id=${userId}&course_id=${courseId}`, "GET", null, success, error),
    count: (success, error) => ApiService.call("/enrollments/count", "GET", null, success, error)
};
const InstructorService = {
    getAll: (success, error) => ApiService.call("/instructors", "GET", null, success, error),
    addInstructor: (data, success, error) => ApiService.call("/instructors", "POST", data, success, error),
    updateInstructor: (id, data, success, error) => ApiService.call(`/instructors/${id}`, "PUT", data, success, error),
    deleteInstructor: (id, success, error) => ApiService.call(`/instructors/${id}`, "DELETE", null, success, error)
};
const ReviewService = {
    getByCourseId: (courseId, success, error) => ApiService.call(`/reviews/course/${courseId}`, "GET", null, success, error),
    addReview: (data, success, error) => ApiService.call("/reviews", "POST", data, success, error)
};

// UI Service: Manages updates to the user interface.
const UI = {
    updateNavbar: function() { const user = AuthService.getUser(); if (user) { $("#nav-login-btn, #nav-register-btn").hide(); $("#nav-logout-btn, #nav-profile-link").show(); if (user.role === 'admin') { $("#nav-admin-link").show(); } else { $("#nav-admin-link").hide(); } } else { $("#nav-login-btn, #nav-register-btn").show(); $("#nav-logout-btn, #nav-profile-link, #nav-admin-link").hide(); } },
    renderUsersTable: function(users) { const tableBody = $("#users-table-body"); tableBody.empty(); if (users && users.length > 0) { users.forEach(user => { const row = `<tr><td>${user.id}</td><td>${user.username}</td><td>${user.email}</td><td>${user.role}</td><td><button class="btn btn-sm btn-warning" onclick="UserActions.openEditModal(${user.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="UserActions.openDeleteModal(${user.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No users found.</td></tr>'); } },
    renderCoursesTable: function(courses) { const tableBody = $("#courses-table-body"); tableBody.empty(); if (courses && courses.length > 0) { courses.forEach(course => { const row = `<tr><td>${course.id}</td><td>${course.title}</td><td>${course.instructor_name || course.instructor_id}</td><td>$${course.price}</td><td><button class="btn btn-sm btn-warning" onclick="CourseActions.openEditModal(${course.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="CourseActions.openDeleteModal(${course.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No courses found.</td></tr>'); } },
    renderInstructorsTable: function(instructors) { const tableBody = $("#instructors-table-body"); tableBody.empty(); if (instructors && instructors.length > 0) { instructors.forEach(instructor => { const row = `<tr><td>${instructor.id}</td><td>${instructor.user_id}</td><td>${instructor.first_name} ${instructor.last_name}</td><td>${instructor.expertise}</td><td><button class="btn btn-sm btn-warning" onclick="InstructorActions.openEditModal(${instructor.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="InstructorActions.openDeleteModal(${instructor.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No instructors found.</td></tr>'); } },
    renderCourseCards: function(courses) {
        const container = $("#course-list-container");
        container.empty();
        if (courses && courses.length > 0) {
            courses.forEach(course => {
                const imageUrl = course.image_url && course.image_url.length > 0 ? `assets/static/images/${course.image_url}` : 'https://via.placeholder.com/400x200';
                const cardHtml = `<div class="col-lg-4 col-md-6 mb-4"><div class="card h-100"><img src="${imageUrl}" class="card-img-top" alt="${course.title}" style="height: 200px; object-fit: cover;"><div class="card-body d-flex flex-column"><h5 class="card-title">${course.title}</h5><p class="card-text">${course.description ? course.description.substring(0, 100) : ''}...</p><p class="card-text small text-muted mt-auto">Instructor: ${course.instructor_name || 'N/A'}</p><p class="card-text"><strong>$${course.price}</strong></p><a href="#course-detail" class="btn btn-primary mt-2 view-details-btn" data-course-id="${course.id}">View Details</a></div></div></div>`;
                container.append(cardHtml);
            });
        } else {
            container.append('<div class="col-12"><p class="text-center">No courses found.</p></div>');
        }
    },
    renderCourseDetail: function(course) {
        if (course) {
            window.selectedCourseId = course.id;
            const imageUrl = course.image_url && course.image_url.length > 0 ? `assets/static/images/${course.image_url}` : 'https://via.placeholder.com/800x400';
            $("#course-detail-title").text(course.title);
            $("#course-detail-description").text(course.description);
            $("#course-detail-image").attr("src", imageUrl);
            $("#course-detail-instructor-name").text(course.instructor_name || 'N/A');
            $("#course-detail-price").text(`$${course.price}`);
            $("#enroll-now-btn").text("Enroll Now").prop("disabled", false).off('click');
            CourseActions.checkEnrollmentStatus();
        } else {
            $("#course-detail-content").html("<h2>Course Not Found</h2><p>The course you are looking for does not exist.</p>");
        }
    },
    renderEnrolledCourses: function(enrollments) {
        const container = $("#enrolled-courses-container");
        container.empty();
        if (enrollments && enrollments.length > 0) {
            enrollments.forEach(enrollment => {
                const imageUrl = 'https://via.placeholder.com/400x200';
                const cardHtml = `<div class="col-md-4 mb-4"><div class="card h-100"><img src="${imageUrl}" class="card-img-top" alt="Course Image" style="height: 200px; object-fit: cover;"><div class="card-body d-flex flex-column"><h5 class="card-title">${enrollment.course_title}</h5><div class="progress mt-auto mb-2" style="height: 10px;"><div class="progress-bar" role="progressbar" style="width: ${enrollment.progress}%;" aria-valuenow="${enrollment.progress}" aria-valuemin="0" aria-valuemax="100"></div></div><p class="card-text small text-muted">${enrollment.progress}% Complete</p><a href="#course-detail" class="btn btn-primary mt-2 view-details-btn" data-course-id="${enrollment.course_id}">Continue Learning</a></div></div></div>`;
                container.append(cardHtml);
            });
        } else {
            container.append('<div class="col-12"><p>You are not yet enrolled in any courses. <a href="#courses">Explore courses</a> to get started!</p></div>');
        }
    },
    renderReviews: function(reviews) {
        const container = $("#reviews-container");
        container.empty();
        if (reviews && reviews.length > 0) {
            reviews.forEach(review => {
                let stars = '';
                for (let i = 0; i < 5; i++) { stars += `<span class="text-warning">${i < review.rating ? '&#9733;' : '&#9734;'}</span>`; }
                const reviewHtml = `<div class="card mb-3"><div class="card-body"><h5 class="card-title">${review.username || 'Anonymous'}</h5><p class="card-text">${stars}</p><p class="card-text">${review.comment}</p><footer class="blockquote-footer">${new Date(review.review_date).toLocaleDateString()}</footer></div></div>`;
                container.append(reviewHtml);
            });
        } else {
            container.append('<p>No reviews yet. Be the first to leave one!</p>');
        }
    }
};

// CRUD Actions for Users
const UserActions = {
    loadUsers: function() { UserService.getAll(UI.renderUsersTable, (err) => console.error("Failed to load users", err)); },
    openAddModal: function() { $("#userForm")[0].reset(); $("#userId").val(""); $("#userModalLabel").text("Add New User"); $("#password-field-container").show(); new bootstrap.Modal(document.getElementById('userModal')).show(); },
    openEditModal: function(userId) { UserService.getById(userId, (user) => { $("#userId").val(user.id); $("#username").val(user.username); $("#email").val(user.email); $("#firstName").val(user.first_name); $("#lastName").val(user.last_name); $("#role").val(user.role); $("#userModalLabel").text("Edit User"); $("#password-field-container").hide(); new bootstrap.Modal(document.getElementById('userModal')).show(); }, (error) => { alert("Failed to fetch user data."); }); },
    openDeleteModal: function(userId) { $("#confirmDeleteBtn").data("entityId", userId).data("entityType", "user"); new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show(); },
    handleFormSubmit: function(e) { e.preventDefault(); const userId = $("#userId").val(); const userData = { username: $("#username").val(), email: $("#email").val(), first_name: $("#firstName").val(), last_name: $("#lastName").val(), role: $("#role").val() }; if (userId) { UserService.updateUser(userId, userData, () => { bootstrap.Modal.getInstance(document.getElementById('userModal')).hide(); UserActions.loadUsers(); }, (error) => { alert("Update failed: " + (error.error || "Unknown error.")); }); } else { userData.password = $("#password").val(); if (!userData.password) { alert("Password is required for new users."); return; } UserService.addUser(userData, () => { bootstrap.Modal.getInstance(document.getElementById('userModal')).hide(); UserActions.loadUsers(); }, (error) => { alert("Add failed: " + (error.error || "Unknown error.")); }); } },
    handleDelete: function() { const userId = $("#confirmDeleteBtn").data("entityId"); UserService.deleteUser(userId, () => { bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide(); UserActions.loadUsers(); }, (error) => { alert("Delete failed: " + (error.error || "Unknown error.")); }); }
};

// CRUD Actions for Courses
const CourseActions = {
    loadCourses: function() { CourseService.getAll(UI.renderCoursesTable, (err) => console.error("Failed to load courses", err)); },
    openAddModal: function() { $("#courseForm")[0].reset(); $("#courseId").val(""); $("#courseModalLabel").text("Add New Course"); new bootstrap.Modal(document.getElementById('courseModal')).show(); },
    openEditModal: function(courseId) { CourseService.getById(courseId, (course) => { $("#courseId").val(course.id); $("#courseTitle").val(course.title); $("#courseDescription").val(course.description); $("#courseInstructorId").val(course.instructor_id); $("#coursePrice").val(course.price); $("#courseDifficulty").val(course.difficulty_level); $("#courseModalLabel").text("Edit Course"); new bootstrap.Modal(document.getElementById('courseModal')).show(); }, (error) => alert("Failed to fetch course data.")); },
    openDeleteModal: function(courseId) { $("#confirmDeleteBtn").data("entityId", courseId).data("entityType", "course"); new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show(); },
    handleFormSubmit: function(e) { e.preventDefault(); const courseId = $("#courseId").val(); const courseData = { title: $("#courseTitle").val(), description: $("#courseDescription").val(), instructor_id: $("#courseInstructorId").val(), price: $("#coursePrice").val(), difficulty_level: $("#courseDifficulty").val(), image_url: "" }; if (courseId) { CourseService.updateCourse(courseId, courseData, () => { bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide(); CourseActions.loadCourses(); }, (error) => alert("Update failed: " + (error.error || "Unknown error."))); } else { CourseService.addCourse(courseData, () => { bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide(); CourseActions.loadCourses(); }, (error) => alert("Add failed: " + (error.error || "Unknown error."))); } },
    handleDelete: function() { const courseId = $("#confirmDeleteBtn").data("entityId"); CourseService.deleteCourse(courseId, () => { bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide(); CourseActions.loadCourses(); }, (error) => alert("Delete failed: " + (error.error || "Unknown error."))); },
    enrollInCourse: function() { const user = AuthService.getUser(); if (!user) { alert("Please log in to enroll in a course."); window.location.hash = "#login"; return; } const courseId = window.selectedCourseId; if (!courseId) { alert("Could not determine the course. Please try again."); return; } const enrollmentData = { user_id: user.id, course_id: courseId }; EnrollmentService.create(enrollmentData, (response) => { alert("Congratulations! You have successfully enrolled in this course."); CourseActions.checkEnrollmentStatus(); }, (error) => { alert("Enrollment failed: " + (error.error || "An unknown error occurred.")); }); },
    unenrollFromCourse: function(enrollmentId) { if (confirm("Are you sure you want to unenroll from this course?")) { EnrollmentService.delete(enrollmentId, () => { alert("You have been unenrolled."); CourseActions.checkEnrollmentStatus(); }, (err) => alert("Failed to unenroll.")); } },
    checkEnrollmentStatus: function() {
        const user = AuthService.getUser();
        const courseId = window.selectedCourseId;
        const enrollButton = $("#enroll-now-btn");
        if (user && courseId) {
            EnrollmentService.check(user.id, courseId, (enrollment) => { enrollButton.text("Unenroll").removeClass("btn-primary").addClass("btn-secondary").off('click').on('click', () => CourseActions.unenrollFromCourse(enrollment.id)); }, (error) => { enrollButton.text("Enroll Now").removeClass("btn-secondary").addClass("btn-primary").off('click').on('click', CourseActions.enrollInCourse); });
        } else {
            enrollButton.text("Enroll Now").removeClass("btn-secondary").addClass("btn-primary").off('click').on('click', CourseActions.enrollInCourse);
        }
    }
};

// CRUD Actions for Instructors
const InstructorActions = {
    loadInstructors: function() { InstructorService.getAll(UI.renderInstructorsTable, (err) => console.error("Failed to load instructors", err)); },
    openAddModal: function() { $("#instructorForm")[0].reset(); $("#instructorId").val(""); $("#instructorModalLabel").text("Add New Instructor"); $("#instructorUserId").prop('readonly', false); new bootstrap.Modal(document.getElementById('instructorModal')).show(); },
    openEditModal: function(instructorId) { InstructorService.getAll((instructors) => { const instructor = instructors.find(inst => inst.id == instructorId); if (instructor) { $("#instructorId").val(instructor.id); $("#instructorUserId").val(instructor.user_id).prop('readonly', true); $("#instructorBio").val(instructor.bio); $("#instructorExpertise").val(instructor.expertise); $("#instructorModalLabel").text("Edit Instructor Profile"); new bootstrap.Modal(document.getElementById('instructorModal')).show(); } else { alert("Could not find instructor data to edit."); } }, (error) => { alert("Error fetching instructor list for edit."); }); },
    openDeleteModal: function(instructorId) { $("#confirmDeleteBtn").data("entityId", instructorId).data("entityType", "instructor"); new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show(); },
    handleFormSubmit: function(e) { e.preventDefault(); const instructorId = $("#instructorId").val(); const instructorData = { user_id: $("#instructorUserId").val(), bio: $("#instructorBio").val(), expertise: $("#instructorExpertise").val() }; if (instructorId) { InstructorService.updateInstructor(instructorId, instructorData, () => { bootstrap.Modal.getInstance(document.getElementById('instructorModal')).hide(); InstructorActions.loadInstructors(); }, (error) => alert("Update failed: " + (error.error || "Unknown error."))); } else { InstructorService.addInstructor(instructorData, () => { bootstrap.Modal.getInstance(document.getElementById('instructorModal')).hide(); InstructorActions.loadInstructors(); }, (error) => alert("Add failed: " + (error.error || "Unknown error."))); } },
    handleDelete: function() { const instructorId = $("#confirmDeleteBtn").data("entityId"); InstructorService.deleteInstructor(instructorId, () => { bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide(); InstructorActions.loadInstructors(); }, (error) => alert("Delete failed: " + (error.error || "Unknown error."))); }
};

// Form Handlers for login/register.
const FormHandler = {
    handleRegistration: function(e) {
        e.preventDefault();
        const userData = { username: $("#registerUsername").val(), email: $("#registerEmail").val(), password: $("#registerPassword").val(), first_name: $("#registerFirstName").val(), last_name: $("#registerLastName").val() };
        if (userData.password !== $("#confirmPassword").val()) { alert("Passwords do not match."); return; }
        UserService.register(userData, (response) => { alert("Registration successful! Please log in."); window.location.hash = "#login"; }, (error) => { alert("Registration failed: " + (error.error || "Unknown error.")); });
    },
    handleLogin: function(e) {
        e.preventDefault();
        const email = $("#loginEmail").val();
        const password = $("#loginPassword").val();
        AuthService.login(email, password, (response) => { window.location.hash = "#dashboard"; UI.updateNavbar(); }, (error) => { alert("Login failed: " + (error.error || "Invalid credentials.")); });
    }
};

// CRUD Actions for Reviews
const ReviewActions = {
    loadReviews: function(courseId) {
        ReviewService.getByCourseId(courseId, UI.renderReviews, (err) => {
            console.error("Failed to load reviews", err);
            $("#reviews-container").html('<p class="text-danger">Could not load reviews.</p>');
        });
    },
    handleFormSubmit: function(e) {
        e.preventDefault();
        const user = AuthService.getUser();
        const courseId = window.selectedCourseId;
        if (!user || !courseId) { alert("Cannot submit review. Missing user or course information."); return; }
        const reviewData = { user_id: user.id, course_id: courseId, rating: $("#rating").val(), comment: $("#comment").val() };
        if (!reviewData.rating) { alert("Please select a star rating."); return; }
        ReviewService.addReview(reviewData, () => {
            alert("Thank you for your review!");
            $("#reviewForm")[0].reset();
            ReviewActions.loadReviews(courseId);
        }, (error) => {
            alert("Failed to submit review: " + (error.error || "Unknown error."));
        });
    }
};

// Main application setup
$(document).ready(function() {
    $("main#spapp > section").height($(document).height() - 60);
    var app = $.spapp({ defaultView: "#dashboard", templateDir: "./views/", pageNotFound: "error_404" });


    $(document).on('click', '.view-details-btn', function(e) {
        const courseId = $(this).data('course-id');
        window.selectedCourseId = courseId;
    });

    // Define routes
    app.route({
        view: 'dashboard',
        load: 'dashboard.html',
        onReady: () => {
            const user = AuthService.getUser();
            if (user) {
                $("#enrolled-courses-section").show();
                EnrollmentService.getByUserId(user.id, UI.renderEnrolledCourses, (error) => {
                    $("#enrolled-courses-container").html("<p>Could not load your courses at this time.</p>");
                });
            }
        }
    });
    app.route({
        view: 'courses',
        load: 'courses.html',
        onReady: () => {
            CourseService.getAll(UI.renderCourseCards, (err) => {
                $("#course-list-container").html('<p class="text-center text-danger">Could not load courses.</p>');
            });
        }
    });
    app.route({
        view: 'course-detail',
        load: 'course-detail.html',
        onReady: () => {
            const hash = window.location.hash;
            const parts = hash.split('/');
            if (parts.length > 1 && parts[0] === '#course-detail') {
                const courseId = parts[1];
                window.selectedCourseId = courseId; 
                CourseService.getById(courseId, UI.renderCourseDetail);
                ReviewActions.loadReviews(courseId);
                if (AuthService.getToken()) {
                    $("#add-review-section").show();
                    $("#reviewForm").off("submit").on("submit", ReviewActions.handleFormSubmit);
                } else {
                    $("#add-review-section").hide();
                }
            } else if (window.selectedCourseId) { 
                CourseService.getById(window.selectedCourseId, UI.renderCourseDetail);
                ReviewActions.loadReviews(window.selectedCourseId);
                 if (AuthService.getToken()) {
                    $("#add-review-section").show();
                    $("#reviewForm").off("submit").on("submit", ReviewActions.handleFormSubmit);
                } else {
                    $("#add-review-section").hide();
                }
            }
             else {
                 $("#course-detail-content").html("<h2>No Course Selected</h2><p>Please go back to the courses page and select a course.</p>");
            }
        }
    });
    app.route({ view: 'profile', load: 'profile.html' });
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
                UserService.count((data) => { $("#total-users-count").text(data.count); }, (err) => console.error(err));
                CourseService.count((data) => { $("#total-courses-count").text(data.count); }, (err) => console.error(err));
                EnrollmentService.count((data) => { $("#total-enrollments-count").text(data.count); }, (err) => console.error(err));
                $("#userForm").on("submit", UserActions.handleFormSubmit);
                $("#courseForm").on("submit", CourseActions.handleFormSubmit);
                $("#instructorForm").on("submit", InstructorActions.handleFormSubmit);
                $("#confirmDeleteBtn").on("click", function() {
                    const entityType = $(this).data("entityType");
                    if (entityType === 'user') { UserActions.handleDelete(); } 
                    else if (entityType === 'course') { CourseActions.handleDelete(); }
                    else if (entityType === 'instructor') { InstructorActions.handleDelete(); }
                });
            } else {
                alert("Access denied. Admin privileges required.");
                window.location.hash = "#dashboard";
            }
        }
    });

    // Logout handler
    $("#nav-logout-btn").on("click", (e) => { e.preventDefault(); AuthService.logout(); });

    
    UI.updateNavbar();
    app.run();
});