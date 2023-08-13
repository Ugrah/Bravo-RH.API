<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Token;
use Exception;
use JwtApi;
use App\Http\Requests\LoginRequest;
use App\Traits\DataReturn;

class AuthController extends Controller
{
    use DataReturn;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
        $this->middleware('jwt.xauth', ['except' => ['login', 'register', 'refresh']]);
        $this->middleware('jwt.xrefresh', ['only' => ['refresh']]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Register",
     * description="Register by email, password",
     * operationId="authRegister",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(property="name", type="string", format="name", example="user1"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad request",
     *    @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again"),
     *        )
     *     )
     * )
     */
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'status'    => 'error',
                'success'   => false,
                'message'   => 'Bad request. Please try again',
                'error'     => $validator->errors()->toArray()
            ], Response::HTTP_BAD_REQUEST);
        }

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            // 'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Sign in",
     * description="Login by username, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"username","password"},
     *       @OA\Property(property="username", type="string", format="username", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * ),
     * @OAS\SecurityScheme(
     * securityScheme="bearerAuth",
     * type="http",
     * scheme="bearer"
     * )
     */
    public function login(LoginRequest $request)
    {

        $credentials = $request->getCredentials();

        $rules = [];
        foreach($credentials as $key => $value) {
            $rules[$key] = 'required';
        }
        $validator = Validator::make($credentials, $rules);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }

        // dd($credentials); exit;
        $access_token = auth()->claims(['xtype' => 'auth'])->attempt($credentials);

        if (!$access_token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //Create token
        $token = auth()->claims(['xtype' => 'auth'])->attempt($credentials);

        $data = [ 'token' => $this->getAccessTokenFormatted($token)];

        //Token created, return with success response and jwt token
        return $this->responseSuccess($data, 'SignIn Successfully!');
    }

    /**
     * Get user infos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $user = JWTAuth::authenticate($token);
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function tokenIssue(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required|boolean',
            'name' => 'required|boolean',
            'email' => 'required|boolean'
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => 'Error! Bad input.'], 400);
        }

        $resource_token = auth()->claims([
            'xtype' => 'resource'
        ])->setTTL(60 * 24 * 365)->tokenById(auth()->user()->id); //expire in 1 year

        $resource_token_obj = Token::create([
            'user_id' => auth()->user()->id,
            'value' => $resource_token,
            'jti' => auth()->setToken($resource_token)->payload()->get('jti'),
            'type' => auth()->setToken($resource_token)->payload()->get('xtype'),
            'pair' => null,
            'payload' => auth()->setToken($resource_token)->payload()->toArray(),
            'grants' => [
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ],
            'ip' => null,
            'device' => null
        ]);
        return response()->json(['token' => $resource_token]);
    }

    /**
     * Logout user close auth.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $refresh_token_obj = Token::findPairByValue(auth()->getToken()->get());
        auth()->logout();
        auth()->setToken($refresh_token_obj->value)->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function logoutAll(Request $request)
    {
        foreach (auth()->user()->authTokens() as $token_obj) {
            try {
                auth()->setToken($token_obj->value)->invalidate(true);
            } catch (Exception $e) {
                //do nothing, it's already bad token for various reasons
            }
        }

        return response()->json(['message' => 'Successfully logged out from all devices']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $access_token = auth()->claims(['xtype' => 'auth'])->refresh(true, true);
        auth()->setToken($access_token);

        return response()->json($this->getAccessTokenFormatted($access_token));
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getAccessTokenFormatted($access_token)
    {
        $response_array = [
            'access_token' => $access_token,
            'token_type' => 'bearer',
            'access_expires_in' => auth()->factory()->getTTL() * 60,
        ];

        $access_token_obj = Token::create([
            'user_id' => auth()->user()->id,
            'value' => $access_token, //or auth()->getToken()->get();
            'jti' => auth()->payload()->get('jti'),
            'type' => auth()->payload()->get('xtype'),
            'payload' => auth()->payload()->toArray(),
            'ip' => JwtApi::getIp(),
            'device' => JwtApi::getUserAgent()
        ]);

        $refresh_token = auth()->claims([
            'xtype' => 'refresh',
            'xpair' => auth()->payload()->get('jti')
        ])->setTTL(auth()->factory()->getTTL() * 3)->tokenById(auth()->user()->id);

        $response_array += [
            'refresh_token' => $refresh_token,
            'refresh_expires_in' => auth()->factory()->getTTL() * 60
        ];

        $refresh_token_obj = Token::create([
            'user_id' => auth()->user()->id,
            'value' => $refresh_token,
            'jti' => auth()->setToken($refresh_token)->payload()->get('jti'),
            'type' => auth()->setToken($refresh_token)->payload()->get('xtype'),
            'pair' => $access_token_obj->id,
            'payload' => auth()->setToken($refresh_token)->payload()->toArray(),
            'ip' => JwtApi::getIp(),
            'device' => JwtApi::getUserAgent()
        ]);

        $access_token_obj->pair = $refresh_token_obj->id;
        $access_token_obj->save();

        return $response_array;
    }
}
