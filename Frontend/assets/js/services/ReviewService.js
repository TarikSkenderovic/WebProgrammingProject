const ReviewService = {
    getByCourseId: (courseId, success, error) => ApiService.call(`/reviews/course/${courseId}`, "GET", null, success, error),
    addReview: (data, success, error) => ApiService.call("/reviews", "POST", data, success, error)
};