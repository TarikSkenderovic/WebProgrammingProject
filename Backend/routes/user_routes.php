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
 *      summary="Get a user by ID. Requires Admin OR ownership.",
 *      security={{"ApiKey": {}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          @OA\Schema(type="integer")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="The user data."
 *      )
 * )
 */
Flight::route('GET /users/@id', function($id){
    $logged_in_user = Flight::get('user');
    if ($logged_in_user->role != 'admin' && $logged_in_user->id != $id) {
        Flight::halt(403, "Forbidden: You are not authorized to view this profile.");
        return;
    }
    $user = Flight::userService()->get_user_by_id($id);
    if ($user) {
        Flight::json($user);
    } else {
        Flight::halt(404, "User not found.");
    }
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
        $code = $e->getCode();
        if ($code < 100 || $code > 599) { $code = 500; }
        Flight::halt($code, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Update an existing user. Admin or owner access required.",
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
 *      summary="Delete a user. Requires Admin OR ownership.",
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
    // Get the user who is making the request from the JWT token
    $logged_in_user = Flight::get('user');

    // AUTHORIZATION LOGIC:
    // Allow the delete only if the user is an admin OR is deleting their own account.
    if ($logged_in_user->role != 'admin' && $logged_in_user->id != $id) {
        Flight::halt(403, "Forbidden: You are not authorized to delete this user.");
        return;
    }

    Flight::userService()->delete_user($id);
    Flight::json(['message' => 'User deleted successfully']);
});

/**
 * @OA\Post(
 *      path="/users/change-password",
 *      tags={"users"},
 *      summary="Change the password for the logged-in user.",
 *      security={{"ApiKey": {}}},
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"user_id", "current_password", "new_password"},
 *              @OA\Property(property="user_id", type="integer"),
 *              @OA\Property(property="current_password", type="string"),
 *              @OA\Property(property="new_password", type="string")
 *          )
 *      ),
 *      @OA\Response(response=200, description="Password changed successfully."),
 *      @OA\Response(response=401, description="Unauthorized - Current password was incorrect.")
 * )
 */
Flight::route('POST /users/change-password', function(){
    $data = Flight::request()->data->getData();
    try {
        $logged_in_user = Flight::get('user');
        
        // YOUR CORRECT FIX: Add this null check
        if (!$logged_in_user) {
            Flight::halt(401, "User not authenticated. Please log in again.");
            return;
        }
        
        if ($logged_in_user->id != $data['user_id']) {
            Flight::halt(403, "Forbidden: You can only change your own password.");
        }
        
        $response = Flight::userService()->change_password($data);
        Flight::json($response);
    } catch (Exception $e) {
        $code = $e->getCode();
        if ($code < 100 || $code > 599) { $code = 500; }
        Flight::halt($code, $e->getMessage());
    }
});

?>