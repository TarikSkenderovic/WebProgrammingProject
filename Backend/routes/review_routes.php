<?php

/**
 * @OA\Get(
 *      path="/reviews/course/{course_id}",
 *      tags={"reviews"},
 *      summary="Get all reviews for a specific course",
 *      @OA\Parameter(
 *          name="course_id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="A list of reviews for the course."
 *      )
 * )
 */
Flight::route('GET /reviews/course/@course_id', function($course_id){
    Flight::json(Flight::reviewService()->get_reviews_by_course_id($course_id));
});

/**
 * @OA\Post(
 *      path="/reviews",
 *      tags={"reviews"},
 *      summary="Add a review to a course",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"user_id", "course_id", "rating"},
 *              @OA\Property(property="user_id", type="integer", example=1),
 *              @OA\Property(property="course_id", type="integer", example=1),
 *              @OA\Property(property="rating", type="integer", example=5, minimum=1, maximum=5),
 *              @OA\Property(property="comment", type="string", example="This course was amazing!")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Review added successfully."
 *      )
 * )
 */
Flight::route('POST /reviews', function(){
    $data = Flight::request()->data->getData();
    try {
        $review_id = Flight::reviewService()->add_review($data);
        Flight::json(['message' => 'Review added successfully', 'review_id' => $review_id]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});
/**
 * @OA\Put(
 *      path="/reviews/{id}",
 *      tags={"reviews"},
 *      summary="Update an existing review",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          description="Review object with updated data",
 *          required=true,
 *          @OA\JsonContent(
 *              @OA\Property(property="rating", type="integer", example=4, minimum=1, maximum=5),
 *              @OA\Property(property="comment", type="string", example="An updated thought on this course.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The updated review data."
 *      )
 * )
 */

Flight::route('PUT /reviews/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        $updated_review = Flight::reviewService()->update_review($id, $data);
        Flight::json($updated_review);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

/**
 * @OA\Delete(
 *      path="/reviews/{id}",
 *      tags={"reviews"},
 *      summary="Delete a review",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Review deleted successfully."
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Review not found."
 *      )
 * )
 */
Flight::route('DELETE /reviews/@id', function($id){
    try {
        Flight::reviewService()->delete_review($id);
        Flight::json(['message' => 'Review deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

?>