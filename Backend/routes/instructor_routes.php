<?php

/**
 * @OA\Get(path="/instructors", tags={"instructors"}, summary="Get all instructors", security={{"ApiKey": {}}}, @OA\Response(response=200, description="List of instructors"))
 */
Flight::route('GET /instructors', function(){
    Flight::json(Flight::instructorService()->get_all_instructors());
});

/**
 * @OA\Post(path="/instructors", tags={"instructors"}, summary="Add an instructor profile", security={{"ApiKey": {}}},
 *     @OA\RequestBody(description="Instructor data", required=true, @OA\JsonContent(
 *         required={"user_id"}, @OA\Property(property="user_id", type="integer"), @OA\Property(property="bio", type="string"), @OA\Property(property="expertise", type="string")
 *     )),
 *     @OA\Response(response=200, description="Instructor profile created")
 * )
 */
Flight::route('POST /instructors', function(){
    $data = Flight::request()->data->getData();
    try {
        $instructor_id = Flight::instructorService()->add_instructor($data);
        Flight::json(['message' => 'Instructor profile added successfully', 'instructor_id' => $instructor_id]);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

/**
 * @OA\Put(path="/instructors/{id}", tags={"instructors"}, summary="Update an instructor profile", security={{"ApiKey": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(description="Instructor data to update", required=true, @OA\JsonContent(
 *         @OA\Property(property="bio", type="string"), @OA\Property(property="expertise", type="string")
 *     )),
 *     @OA\Response(response=200, description="Instructor profile updated")
 * )
 */
Flight::route('PUT /instructors/@id', function($id){
    $data = Flight::request()->data->getData();
    try {
        Flight::instructorService()->update_instructor($id, $data);
        Flight::json(['message' => 'Instructor profile updated successfully']);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

/**
 * @OA\Delete(path="/instructors/{id}", tags={"instructors"}, summary="Delete an instructor profile", security={{"ApiKey": {}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Instructor profile deleted")
 * )
 */
Flight::route('DELETE /instructors/@id', function($id){
    try {
        Flight::instructorService()->delete_instructor($id);
        Flight::json(['message' => 'Instructor profile deleted successfully']);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

?>