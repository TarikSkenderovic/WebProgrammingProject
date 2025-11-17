<?php


/**
 * @OA\Get(
 *      path="/users",
 *      tags={"users"},
 *      summary="Get all users",
 *      @OA\Response(
 *          response=200,
 *          description="A list of all users."
 *      )
 * )
 */
Flight::route('GET /users', function(){
    // Use the registered userService to call its method
    $users = Flight::userService()->get_all_users();
    Flight::json($users);
});

/**
 * @OA\Get(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Get a user by ID",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The user data, or false if not found."
 *      )
 * )
 */
Flight::route('GET /users/@id', function($id){
    $user = Flight::userService()->get_user_by_id($id);
    Flight::json($user);
});

/**
 * @OA\Post(
 *      path="/users",
 *      tags={"users"},
 *      summary="Add a new user",
 *      @OA\RequestBody(
 *          description="User object that needs to be added",
 *          required=true,
 *          @OA\JsonContent(
 *              required={"username", "email", "password"},
 *              @OA\Property(property="username", type="string", example="newuser"),
 *              @OA\Property(property="email", type="string", example="new.user@example.com"),
 *              @OA\Property(property="password", type="string", example="strongpassword123"),
 *              @OA\Property(property="first_name", type="string", example="New"),
 *              @OA\Property(property="last_name", type="string", example="User")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The ID of the newly created user."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request - Missing required fields."
 *      ),
 *       @OA\Response(
 *          response=409,
 *          description="Conflict - Username or email already exists."
 *      )
 * )
 */
Flight::route('POST /users', function(){
    // Get the request body
    $data = Flight::request()->data->getData();
    
    try {
        $new_user_id = Flight::userService()->add_user($data);
        Flight::json(['message' => 'User added successfully', 'user_id' => $new_user_id]);
    } catch (Exception $e) {
        // Handle exceptions from the service layer (like validation errors)
        Flight::json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : 500);
    }
});

/**
 * @OA\Put(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Update an existing user",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\RequestBody(
 *          description="User object with updated data",
 *          required=true,
 *          @OA\JsonContent(
 *              @OA\Property(property="username", type="string"),
 *              @OA\Property(property="email", type="string"),
 *              @OA\Property(property="first_name", type="string"),
 *              @OA\Property(property="last_name", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The updated user data."
 *      )
 * )
 */
Flight::route('PUT /users/@id', function($id){
    $data = Flight::request()->data->getData();
    $updated_user = Flight::userService()->update_user($id, $data);
    Flight::json($updated_user);
});

/**
 * @OA\Delete(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Delete a user",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="User deleted successfully."
 *      )
 * )
 */
Flight::route('DELETE /users/@id', function($id){
    Flight::userService()->delete_user($id);
    Flight::json(['message' => 'User deleted successfully']);
});

?>