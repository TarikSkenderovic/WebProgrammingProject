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