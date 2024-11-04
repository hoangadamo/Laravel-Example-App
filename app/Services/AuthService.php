<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Jobs\SendOtpEmail;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function App\Helpers\uploadFile;

class AuthService
{
    protected $userRepository;
    protected $otpRepository;

    public function __construct(UserRepository $userRepository, OtpRepository $otpRepository)
    {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
    }

    public function register($request)
    {
        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'age' => $request->age,
                'password' => $request->password
            ];
            $data['password'] = Hash::make($data['password']);
            if ($request->hasFile('image')) {
                $IMAGE_URL = uploadFile($request->file('image'));
                $data['imageUrl'] = $IMAGE_URL;
            }
            $user = $this->userRepository->create($data);
            $userResource = new UserResource($user);
            return response()->json(['message' => 'User registered successfully', 'user' => $userResource], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function sendOTP($request)
    {
        try {
            $otp = rand(1000, 9999);
            $email = $request->email;
            $expirationTime = Carbon::now()->addMinutes(10)->toDateTimeString();

            // Job
            SendOtpEmail::dispatch($email, $otp, $expirationTime);

            $this->otpRepository->create([
                'otp' => $otp,
                'otp_expiration' => $expirationTime,
                'email' => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Send OTP failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyOTP($request)
    {
        try {
            $inputOtp = $request->input('otp');
            $otp = $this->otpRepository->getByOtp($inputOtp);

            if ($otp && Carbon::now()->lessThanOrEqualTo(Carbon::parse($otp->otp_expiration))) {
                $otp->delete();
                $user = $this->userRepository->getByEmail($otp->email);
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

    public function login($request)
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
