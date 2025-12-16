<?php

/**
 * @OA\Get(
 *      path="/users/count",
 *      tags={"users"},
 *      summary="Count all users",
 *      security={{"ApiKey": {}}},
 *      @OA\Response(response=200, description="Total number of users")
 * )
 */
Flight::route('GET /users/count', function(){
    Flight::json(Flight::userService()->count_all_users());
});

/**
 * @OA\Get(
 *      path="/users",
 *      tags={"users"},
 *      summary="Get all users. Admin access required.",
 *      security={{"ApiKey": {}}},
 *      @OA\Response(
 *          response=200,
 *          description="A list of all users."
 *      )
 * )
 */
Flight::route('GET /users', function(){
    $users = Flight::userService()->get_all_users();
    Flight::json($users);
});

/**
 * @OA\Get(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Get a user by ID. Admin access required.",
 *      security={{"ApiKey": {}}},
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
 *      summary="Add a new user (Register). This is a public route.",
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
 *      )
 * )
 */
Flight::route('POST /users', function(){
    $data = Flight::request()->data->getData();
    try {
        $new_user_id = Flight::userService()->add_user($data);
        Flight::json(['message' => 'User added successfully', 'user_id' => $new_user_id]);
    } catch (Exception $e) {
        Flight::halt($e->getCode() ?: 500, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Update an existing user. Admin access required.",
 *      security={{"ApiKey": {}}},
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
 *      summary="Delete a user. Admin access required.",
 *      security={{"ApiKey": {}}},
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