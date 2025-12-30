const InstructorService = {
    getAll: (success, error) => ApiService.call("/instructors", "GET", null, success, error),
    addInstructor: (data, success, error) => ApiService.call("/instructors", "POST", data, success, error),
    updateInstructor: (id, data, success, error) => ApiService.call(`/instructors/${id}`, "PUT", data, success, error),
    deleteInstructor: (id, success, error) => ApiService.call(`/instructors/${id}`, "DELETE", null, success, error)
};