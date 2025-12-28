<?php

/**
 * @OA\Get(
 *      path="/enrollments",
 *      tags={"enrollments"},
 *      summary="Get all enrollments in the system (Admin only)",
 *      security={{"ApiKey": {}}},
 *      @OA\Response(
 *          response=200,
 *          description="A list of all enrollments."
 *      )
 * )
 */
Flight::route('GET /enrollments', function(){
    Flight::json(Flight::enrollmentService()->get_all_enrollments());
});

/**
 * @OA\Get(
 *      path="/enrollments/count",
 *      tags={"enrollments"},
 *      summary="Count all enrollments in the system (Admin only)",
 *      security={{"ApiKey": {}}},
 *      @OA\Response(
 *          response=200,
 *          description="Total number of enrollments."
 *      )
 * )
 */
Flight::route('GET /enrollments/count', function(){
    // This route is protected by the middleware, which checks for admin role.
    Flight::json(Flight::enrollmentService()->count_all_enrollments());
});

/**
 * @OA\Post(path="/enrollments", tags={"enrollments"}, summary="Enroll a user in a course", security={{"ApiKey": {}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(required={"user_id", "course_id"}, @OA\Property(property="user_id", type="integer"), @OA\Property(property="course_id", type="integer"))),
 *     @OA\Response(response=200, description="Enrollment successful.")
 * )
 */
Flight::route('POST /enrollments', function(){
    $data = Flight::request()->data->getData();
    try {
        $enrollment_id = Flight::enrollmentService()->add_enrollment($data);
        Flight::json(['message' => 'Enrollment successful', 'enrollment_id' => $enrollment_id]);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

/**
 * @OA\Get(path="/enrollments/user/{user_id}", tags={"enrollments"}, summary="Get enrollments for a user", security={{"ApiKey": {}}},
 *     @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List of enrollments.")
 * )
 */
Flight::route('GET /enrollments/user/@user_id', function($user_id){
    Flight::json(Flight::enrollmentService()->get_enrollments_by_user_id($user_id));
});

/**
 * @OA\Get(path="/enrollments/check", tags={"enrollments"}, summary="Check if a user is enrolled in a course", security={{"ApiKey": {}}},
 *     @OA\Parameter(name="user_id", in="query", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="course_id", in="query", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Enrollment details if enrolled."),
 *     @OA\Response(response=404, description="Enrollment not found.")
 * )
 */
Flight::route('GET /enrollments/check', function(){
    $user_id = Flight::request()->query['user_id'];
    $course_id = Flight::request()->query['course_id'];
    $enrollment = Flight::enrollmentService()->get_enrollment_by_user_and_course($user_id, $course_id);
    if ($enrollment) {
        Flight::json($enrollment);
    } else {
        Flight::halt(404, "Enrollment not found.");
    }
});

/**
 * @OA\Delete(path="/enrollments/{id}", tags={"enrollments"}, summary="Unenroll a user from a course", security={{"ApiKey": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Unenrolled successfully.")
 * )
 */
Flight::route('DELETE /enrollments/@id', function($id){
    try {
        Flight::enrollmentService()->delete_enrollment($id);
        Flight::json(['message' => 'Unenrolled successfully']);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});


?>