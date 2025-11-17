<?php

/**
 * @OA\Get(
 *      path="/courses",
 *      tags={"courses"},
 *      summary="Get all courses",
 *      @OA\Response(
 *          response=200,
 *          description="A list of all courses."
 *      )
 * )
 */
Flight::route('GET /courses', function(){
    // First, let's update the main index.php to register the CourseService.
    // Go to backend/public/index.php and add:
    // require_once __DIR__ . '/../services/CourseService.php';
    // Flight::register('courseService', 'CourseService');
    
    $courses = Flight::courseService()->get_all_courses();
    Flight::json($courses);
});

/**
 * @OA\Get(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Get a course by ID",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Course data."
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Course not found."
 *      )
 * )
 */
Flight::route('GET /courses/@id', function($id){
    try {
        $course = Flight::courseService()->get_course_by_id($id);
        Flight::json($course);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

/**
 * @OA\Post(
 *      path="/courses",
 *      tags={"courses"},
 *      summary="Add a new course",
 *      @OA\RequestBody(
 *          description="Course object to be added",
 *          required=true,
 *          @OA\JsonContent(
 *              required={"title", "instructor_id", "price"},
 *              @OA\Property(property="title", type="string", example="New Awesome Course"),
 *              @OA\Property(property="description", type="string", example="Learn awesome things."),
 *              @OA\Property(property="instructor_id", type="integer", example=101),
 *              @OA\Property(property="price", type="number", format="float", example=49.99),
 *              @OA\Property(property="difficulty_level", type="string", enum={"Beginner", "Intermediate", "Advanced"}, example="Beginner")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The ID of the newly created course."
 *      )
 * )
 */
Flight::route('POST /courses', function(){
    $data = Flight::request()->data->getData();
    try {
        $new_course_id = Flight::courseService()->add_course($data);
        Flight::json(['message' => 'Course added successfully', 'course_id' => $new_course_id]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

/**
 * @OA\Put(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Update an existing course",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          description="Course object with updated data",
 *          required=true,
 *          @OA\JsonContent(
 *              @OA\Property(property="title", type="string"),
 *              @OA\Property(property="description", type="string"),
 *              @OA\Property(property="price", type="number", format="float"),
 *              @OA\Property(property="difficulty_level", type="string", enum={"Beginner", "Intermediate", "Advanced"})
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The updated course data."
 *      )
 * )
 */
Flight::route('PUT /courses/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        $updated_course = Flight::courseService()->update_course($id, $data);
        Flight::json($updated_course);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

/**
 * @OA\Delete(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Delete a course",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Course deleted successfully."
 *      )
 * )
 */
Flight::route('DELETE /courses/@id', function($id){
    try {
        Flight::courseService()->delete_course($id);
        Flight::json(['message' => 'Course deleted successfully']);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

?>