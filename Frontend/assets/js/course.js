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