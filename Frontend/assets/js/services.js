// Frontend Services for each entity
const UserService = {
    register: (data, success, error) => ApiService.call("/users", "POST", data, success, error),
    getAll: (success, error) => ApiService.call("/users", "GET", null, success, error),
    getById: (id, success, error) => ApiService.call(`/users/${id}`, "GET", null, success, error),
    addUser: (data, success, error) => ApiService.call("/users", "POST", data, success, error),
    updateUser: (id, data, success, error) => ApiService.call(`/users/${id}`, "PUT", data, success, error),
    deleteUser: (id, success, error) => ApiService.call(`/users/${id}`, "DELETE", null, success, error),
    count: (success, error) => ApiService.call("/users/count", "GET", null, success, error),
    changePassword: (data, success, error) => ApiService.call("/users/change-password", "POST", data, success, error)
};
const CourseService = {
    getAll: function(filters, success, error) {
        let endpoint = "/courses";
        if (filters && (filters.search || filters.level)) {
            const queryParams = new URLSearchParams();
            if (filters.search) queryParams.append('search', filters.search);
            if (filters.level) queryParams.append('level', filters.level);
            endpoint += `?${queryParams.toString()}`;
        }
        ApiService.call(endpoint, "GET", null, success, error);
    },
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
    count: (success, error) => ApiService.call("/enrollments/count", "GET", null, success, error),
    getAll: (success, error) => ApiService.call("/enrollments", "GET", null, success, error)
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