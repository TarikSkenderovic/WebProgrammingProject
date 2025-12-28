// This file contains all the CRUD Action objects for the Admin Dashboard.

// CRUD Actions for Users
const UserActions = {
    loadUsers: function() { UserService.getAll(UI.renderUsersTable, (err) => console.error("Failed to load users", err)); },
    openAddModal: function() { $("#userForm")[0].reset(); $("#userId").val(""); $("#userModalLabel").text("Add New User"); $("#password-field-container").show(); new bootstrap.Modal(document.getElementById('userModal')).show(); },
    openEditModal: function(userId) { UserService.getById(userId, (user) => { $("#userId").val(user.id); $("#username").val(user.username); $("#email").val(user.email); $("#firstName").val(user.first_name); $("#lastName").val(user.last_name); $("#role").val(user.role); $("#userModalLabel").text("Edit User"); $("#password-field-container").hide(); new bootstrap.Modal(document.getElementById('userModal')).show(); }, (error) => { alert("Failed to fetch user data."); }); },
    openDeleteModal: function(userId) { $("#confirmDeleteBtn").data("entityId", userId).data("entityType", "user"); new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show(); },
    handleFormSubmit: function(e) { e.preventDefault(); const userId = $("#userId").val(); const userData = { username: $("#username").val(), email: $("#email").val(), first_name: $("#firstName").val(), last_name: $("#lastName").val(), role: $("#role").val() }; if (userId) { UserService.updateUser(userId, userData, () => { bootstrap.Modal.getInstance(document.getElementById('userModal')).hide(); UserActions.loadUsers(); }, (error) => { alert("Update failed: " + (error.error || "Unknown error.")); }); } else { userData.password = $("#password").val(); if (!userData.password) { alert("Password is required for new users."); return; } UserService.addUser(userData, () => { bootstrap.Modal.getInstance(document.getElementById('userModal')).hide(); UserActions.loadUsers(); }, (error) => { alert("Add failed: " + (error.error || "Unknown error.")); }); } },
    handleDelete: function() {
        const userId = $("#confirmDeleteBtn").data("entityId");
        UserService.deleteUser(userId, () => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', 'auto');
            UserActions.loadUsers();
        }, (error) => {
            alert("Delete failed: " + (error.error || "Unknown error."));
        });
    }
};

// CRUD Actions for Courses
const CourseActions = {
    loadCourses: function() {
        CourseService.getAll(null, UI.renderCoursesTable, (err) => console.error("Failed to load courses", err));
    },
    openAddModal: function() { $("#courseForm")[0].reset(); $("#courseId").val(""); $("#courseModalLabel").text("Add New Course"); new bootstrap.Modal(document.getElementById('courseModal')).show(); },
    openEditModal: function(courseId) { CourseService.getById(courseId, (course) => { $("#courseId").val(course.id); $("#courseTitle").val(course.title); $("#courseDescription").val(course.description); $("#courseInstructorId").val(course.instructor_id); $("#coursePrice").val(course.price); $("#courseDifficulty").val(course.difficulty_level); $("#courseModalLabel").text("Edit Course"); new bootstrap.Modal(document.getElementById('courseModal')).show(); }, (error) => alert("Failed to fetch course data.")); },
    openDeleteModal: function(courseId) { $("#confirmDeleteBtn").data("entityId", courseId).data("entityType", "course"); new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show(); },
    handleFormSubmit: function(e) { e.preventDefault(); const courseId = $("#courseId").val(); const courseData = { title: $("#courseTitle").val(), description: $("#courseDescription").val(), instructor_id: $("#courseInstructorId").val(), price: $("#coursePrice").val(), difficulty_level: $("#courseDifficulty").val(), image_url: "" }; if (courseId) { CourseService.updateCourse(courseId, courseData, () => { bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide(); CourseActions.loadCourses(); }, (error) => alert("Update failed: " + (error.error || "Unknown error."))); } else { CourseService.addCourse(courseData, () => { bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide(); CourseActions.loadCourses(); }, (error) => alert("Add failed: " + (error.error || "Unknown error."))); } },
    handleDelete: function() {
        const courseId = $("#confirmDeleteBtn").data("entityId");
        CourseService.deleteCourse(courseId, () => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', 'auto');
            CourseActions.loadCourses();
        }, (error) => {
            alert("Delete failed: " + (error.error || "Unknown error."));
        });
    },
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
    handleDelete: function() {
        const instructorId = $("#confirmDeleteBtn").data("entityId");
        InstructorService.deleteInstructor(instructorId, () => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', 'auto');
            InstructorActions.loadInstructors(); 
        }, (error) => {
            alert("Delete failed: " + (error.error || "Unknown error."));
        });
    }
};


// CRUD Actions for Enrollments (Admin View)
const EnrollmentActions = {
    loadEnrollments: function() {
        EnrollmentService.getAll(UI.renderEnrollmentsTable, (err) => {
            console.error("Failed to load enrollments", err);
            $("#enrollments-table-body").html('<tr><td colspan="6" class="text-center text-danger">Could not load enrollments.</td></tr>');
        });
    },
    openDeleteModal: function(enrollmentId) {
        $("#confirmDeleteBtn").data("entityId", enrollmentId).data("entityType", "enrollment");
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    },
    handleDelete: function() {
        const enrollmentId = $("#confirmDeleteBtn").data("entityId");
        EnrollmentService.delete(enrollmentId, 
            () => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('overflow', 'auto');
                EnrollmentActions.loadEnrollments(); // Refresh the table
            },
            (error) => {
                alert("Delete failed: " + (error.error || "Unknown error."));
            }
        );
    }
};