<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendOtpEmail;
use App\Models\OTP;
use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $authService;
    protected $otpModel;

    public function __construct(AuthService $authService, OTP $otp)
    {
        $this->authService = $authService;
        $this->otpModel = $otp;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request);
    }

    public function sendOTP(Request $request)
    {
        return $this->authService->sendOTP($request);
    }

    public function verifyOTP(Request $request)
    {
        return $this->authService->verifyOTP($request);
    }

    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    public function logout()
    {
        return $this->authService->logout();
    }
}
