<?php

/**
 * @OA\Get(
 *      path="/enrollments/user/{user_id}",
 *      tags={"enrollments"},
 *      summary="Get all enrollments for a specific user",
 *      @OA\Parameter(
 *          name="user_id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="A list of enrollments for the user."
 *      )
 * )
 */
Flight::route('GET /enrollments/user/@user_id', function($user_id){
    Flight::json(Flight::enrollmentService()->get_enrollments_by_user_id($user_id));
});

/**
 * @OA\Post(
 *      path="/enrollments",
 *      tags={"enrollments"},
 *      summary="Enroll a user in a course",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"user_id", "course_id"},
 *              @OA\Property(property="user_id", type="integer", example=1),
 *              @OA\Property(property="course_id", type="integer", example=1)
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Enrollment successful."
 *      )
 * )
 */
Flight::route('POST /enrollments', function(){
    $data = Flight::request()->data->getData();
    try {
        $enrollment_id = Flight::enrollmentService()->add_enrollment($data);
        Flight::json(['message' => 'Enrollment successful', 'enrollment_id' => $enrollment_id]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});


/**
 * @OA\Get(
 *      path="/enrollments/course/{course_id}",
 *      tags={"enrollments"},
 *      summary="Get all enrollments for a specific course",
 *      @OA\Parameter(
 *          name="course_id",
 *          in="path",
 *          required=true,
 *          description="The ID of the course to fetch enrollments for.",
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="A list of enrollments for the specified course."
 *      )
 * )
 */
Flight::route('GET /enrollments/course/@course_id', function($course_id){
    $enrollments = Flight::enrollmentService()->get_enrollments_by_course_id($course_id);
    Flight::json($enrollments);
});


?>