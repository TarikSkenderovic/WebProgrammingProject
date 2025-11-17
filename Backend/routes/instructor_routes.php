<?php

/**
 * @OA\Get(
 *      path="/instructors",
 *      tags={"instructors"},
 *      summary="Get all instructors with their user details",
 *      @OA\Response(
 *          response=200,
 *          description="A list of all instructors."
 *      )
 * )
 */
Flight::route('GET /instructors', function(){
    Flight::json(Flight::instructorService()->get_all_instructors());
});

/**
 * @OA\Post(
 *      path="/instructors",
 *      tags={"instructors"},
 *      summary="Add a new instructor profile",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"user_id"},
 *              @OA\Property(property="user_id", type="integer", example=2),
 *              @OA\Property(property="bio", type="string", example="Experienced developer and teacher."),
 *              @OA\Property(property="expertise", type="string", example="PHP, JavaScript, Databases")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Instructor profile created successfully."
 *      )
 * )
 */
Flight::route('POST /instructors', function(){
    $data = Flight::request()->data->getData();
    try {
        $new_instructor_id = Flight::instructorService()->add_instructor($data);
        Flight::json(['message' => 'Instructor profile added successfully', 'instructor_id' => $new_instructor_id]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

?>