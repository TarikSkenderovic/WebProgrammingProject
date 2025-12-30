const EnrollmentService = {
    create: (data, success, error) => ApiService.call("/enrollments", "POST", data, success, error),
    delete: (id, success, error) => ApiService.call(`/enrollments/${id}`, "DELETE", null, success, error),
    getByUserId: (userId, success, error) => ApiService.call(`/enrollments/user/${userId}`, "GET", null, success, error),
    check: (userId, courseId, success, error) => ApiService.call(`/enrollments/check?user_id=${userId}&course_id=${courseId}`, "GET", null, success, error),
    count: (success, error) => ApiService.call("/enrollments/count", "GET", null, success, error),
    getAll: (success, error) => ApiService.call("/enrollments", "GET", null, success, error)
};