<?php

/**
 * @OA\Post(
 *      path="/login",
 *      tags={"login"},
 *      summary="Login to the system",
 *      @OA\RequestBody(
 *          description="User login object",
 *          required=true,
 *          @OA\JsonContent(
 *              required={"email", "password"},
 *              @OA\Property(property="email", type="string", example="test@example.com", description="User's email"),
 *              @OA\Property(property="password", type="string", example="password123", description="User's password")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Login successful, returns JWT token."
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized - Invalid email or password."
 *      )
 * )
 */
Flight::route('POST /login', function(){
    // Get the data from the request body
    $data = Flight::request()->data->getData();
    
    try {
        // Call the login function from our new AuthService
        $response = Flight::authService()->login_user($data);
        Flight::json($response);
    } catch (Exception $e) {
        // Handle any exceptions (e.g., invalid credentials)
        Flight::halt($e->getCode() ?: 401, $e->getMessage());
    }
});

?>