// UI Service: Manages updates to the user interface.
const UI = {
    updateNavbar: function() { const user = AuthService.getUser(); if (user) { $("#nav-login-btn, #nav-register-btn").hide(); $("#nav-logout-btn, #nav-profile-link").show(); if (user.role === 'admin') { $("#nav-admin-link").show(); } else { $("#nav-admin-link").hide(); } } else { $("#nav-login-btn, #nav-register-btn").show(); $("#nav-logout-btn, #nav-profile-link, #nav-admin-link").hide(); } },
    renderUsersTable: function(users) { const tableBody = $("#users-table-body"); tableBody.empty(); if (users && users.length > 0) { users.forEach(user => { const row = `<tr><td>${user.id}</td><td>${user.username}</td><td>${user.email}</td><td>${user.role}</td><td><button class="btn btn-sm btn-warning" onclick="UserActions.openEditModal(${user.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="UserActions.openDeleteModal(${user.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No users found.</td></tr>'); } },
    renderCoursesTable: function(courses) { const tableBody = $("#courses-table-body"); tableBody.empty(); if (courses && courses.length > 0) { courses.forEach(course => { const row = `<tr><td>${course.id}</td><td>${course.title}</td><td>${course.instructor_name || course.instructor_id}</td><td>$${course.price}</td><td><button class="btn btn-sm btn-warning" onclick="CourseActions.openEditModal(${course.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="CourseActions.openDeleteModal(${course.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No courses found.</td></tr>'); } },
    renderInstructorsTable: function(instructors) { const tableBody = $("#instructors-table-body"); tableBody.empty(); if (instructors && instructors.length > 0) { instructors.forEach(instructor => { const row = `<tr><td>${instructor.id}</td><td>${instructor.user_id}</td><td>${instructor.first_name} ${instructor.last_name}</td><td>${instructor.expertise}</td><td><button class="btn btn-sm btn-warning" onclick="InstructorActions.openEditModal(${instructor.id})">Edit</button> <button class="btn btn-sm btn-danger" onclick="InstructorActions.openDeleteModal(${instructor.id})">Delete</button></td></tr>`; tableBody.append(row); }); } else { tableBody.append('<tr><td colspan="5" class="text-center">No instructors found.</td></tr>'); } },
    renderEnrollmentsTable: function(enrollments) {
        const tableBody = $("#enrollments-table-body");
        tableBody.empty();
        if (enrollments && enrollments.length > 0) {
            enrollments.forEach(enrollment => {
                const row = `
                    <tr>
                        <td>${enrollment.id}</td>
                        <td>${enrollment.user_id} (${enrollment.student_name})</td>
                        <td>${enrollment.course_id} (${enrollment.course_title})</td>
                        <td>${new Date(enrollment.enrollment_date).toLocaleDateString()}</td>
                        <td>${enrollment.progress}%</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="EnrollmentActions.openDeleteModal(${enrollment.id})">Remove</button>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        } else {
            tableBody.append('<tr><td colspan="6" class="text-center">No enrollments found.</td></tr>');
        }
    },
    renderCourseCards: function(courses) {
        const container = $("#course-list-container");
        container.empty();
        if (courses && courses.length > 0) {
            courses.forEach(course => {
                const imageUrl = course.image_url && course.image_url.length > 0 ? `frontend/assets/static/images/${course.image_url}` : 'https://via.placeholder.com/400x200';
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
            const imageUrl = course.image_url && course.image_url.length > 0 ? `frontend/assets/static/images/${course.image_url}` : 'https://via.placeholder.com/800x400';
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
                const imageUrl = enrollment.image_url && enrollment.image_url.length > 0 ? `frontend/assets/static/images/${enrollment.image_url}` : 'https://via.placeholder.com/400x200';
                const cardHtml = `<div class="col-md-4 mb-4"><div class="card h-100"><img src="${imageUrl}" class="card-img-top" alt="Course Image" style="height: 200px; object-fit: cover;"><div class="card-body d-flex flex-column"><h5 class="card-title">${enrollment.course_title}</h5><div class="progress mt-auto mb-2" style="height: 10px;"><div class="progress-bar" role="progressbar" style="width: ${enrollment.progress}%;" aria-valuenow="${enrollment.progress}"></div></div><p class="card-text small text-muted">${enrollment.progress}% Complete</p><a href="#course-detail" class="btn btn-primary mt-2 view-details-btn" data-course-id="${enrollment.course_id}">Continue Learning</a></div></div></div>`;
                container.append(cardHtml);
            });
        } else {
            container.append('<div class="col-12"><p>You are not yet enrolled in any courses. <a href="#courses">Explore courses</a></p></div>');
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