<?php

/**
 * @OA\Get(
 *      path="/courses",
 *      tags={"courses"},
 *      summary="Get all courses with optional filtering",
 *      security={{"ApiKey": {}}},
 *      @OA\Parameter(
 *          name="search",
 *          in="query",
 *          description="Search term to filter courses by title",
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Parameter(
 *          name="level",
 *          in="query",
 *          description="Difficulty level to filter by (e.g., 'Beginner', 'Intermediate')",
 *          @OA\Schema(type="string")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="A list of courses."
 *      )
 * )
 */
Flight::route('GET /courses', function(){
    // Read the optional 'search' and 'level' query parameters from the URL
    $search = Flight::request()->query['search'];
    $level = Flight::request()->query['level'];

    // Call the service with the retrieved parameters (they will be null if not provided)
    $courses = Flight::courseService()->get_all_courses($search, $level);
    
    Flight::json($courses);
});

/**
 * @OA\Get(
 *      path="/courses/count",
 *      tags={"courses"},
 *      summary="Count all courses",
 *      security={{"ApiKey": {}}},
 *      @OA\Response(response=200, description="Total number of courses")
 * )
 */
Flight::route('GET /courses/count', function(){
    Flight::json(Flight::courseService()->count_all_courses());
});

/**
 * @OA\Get(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Get a course by ID",
 *      security={{"ApiKey": {}}},
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
        Flight::halt($e->getCode() ?: 404, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *      path="/courses",
 *      tags={"courses"},
 *      summary="Add a new course",
 *      security={{"ApiKey": {}}},
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
        $code = $e->getCode();
        if ($code < 100 || $code > 599) {
            $code = 500; 
        }
        Flight::halt($code, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Update an existing course",
 *      security={{"ApiKey": {}}},
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
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *      path="/courses/{id}",
 *      tags={"courses"},
 *      summary="Delete a course",
 *      security={{"ApiKey": {}}},
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
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

?>