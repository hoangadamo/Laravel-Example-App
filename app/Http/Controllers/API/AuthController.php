<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendOtpEmail;
use App\Models\OTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $userModel;
    protected $otpModel;

    public function __construct(User $user, OTP $otp)
    {
        $this->userModel = $user;
        $this->otpModel = $otp;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->userModel->storeUser($request);
            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function sendOTP(Request $request)
    {
        try {
            $otp = rand(1000, 9999);
            $email = $request->input('email');
            $expirationTime = Carbon::now()->addMinutes(10)->toDateTimeString();

            // Job
            SendOtpEmail::dispatch($email, $otp, $expirationTime);

            $this->otpModel->create([
                'otp' => $otp,
                'otp_expiration' => $expirationTime,
                'email' => $email,
            ]);
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Send OTP failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $inputOtp = $request->input('otp');
            $otp = $this->otpModel->where('otp', $inputOtp)->first();

            if ($otp && $inputOtp == $otp->otp && Carbon::now()->lessThanOrEqualTo(Carbon::parse($otp->otp_expiration))) {
                $otp->delete();
                $user = $this->userModel->where('email', $otp->email)->first();
                if ($user) {
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                    return response()->json(['message' => 'OTP verified successfully. Please log in.'], 200);
                } else {
                    return response()->json(['error' => 'User not found'], 404);
                }
            } else {
                return response()->json(['error' => 'Invalid or expired OTP'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Verify OTP failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['error' => 'Wrong email/password'], 401);
            }
            /** @var \App\Models\User $user **/
            $user = Auth::user();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = now()->addDays(10); // 10 days
            $token->save();

            return response()->json([
                'token' => $tokenResult->accessToken,
                'expires_at' => $token->expires_at,
                'user' => new UserResource($user)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        try {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $user->token()->revoke();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed', 'message' => $e->getMessage()], 500);
        }
    }
}
