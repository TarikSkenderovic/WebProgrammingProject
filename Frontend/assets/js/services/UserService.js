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