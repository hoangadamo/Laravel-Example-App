<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
        // $this->middleware('web');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = $this->userModel->storeUser($request);
        $this->sendOTP($request);
        return redirect()->route('verify')->with('success', 'Registration successful! Verify email now.');
    }

    public function verify()
    {
        return view('auth.verify-otp');
    }

    public function sendOTP(Request $request)
    {
        $otp = rand(1000, 9999);
        $email = $request->input('email');
        $expirationTime = Carbon::now()->addMinutes(10)->toDateTimeString();
        Mail::send('mail.template', ['otp' => $otp, 'expirationTime' => $expirationTime], function ($message) use ($email) {
            $message->to($email)
                ->subject('Your OTP Code');
        });

        // Save otp to session:
        session(['otp' => $otp, 'otp_expiration' => $expirationTime, 'email' => $email]);
        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOTP(Request $request)
    {
        $inputOtp = $request->input('otp');
        $storedOtp = session('otp');
        $otpExpiration = session('otp_expiration');
        $email = session('email');
        if ($storedOtp && $inputOtp == $storedOtp && Carbon::now()->lessThanOrEqualTo(Carbon::parse($otpExpiration))) {
            session()->forget(['otp', 'otp_expiration']);
            $user = $this->userModel->where('email', $email)->first();
            if ($user) {
                $user->email_verified_at = Carbon::now();
                $user->save();
            } else {
                return redirect()->back()->withErrors(['email' => 'User not found'])->withInput();
            }

            return redirect()->route('login')->with('success', 'OTP verified successfully. Please log in.');
        } else {
            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP'])->withInput();
        }
    }

    // Login
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            // check if email is verified
            if ($user->email_verified_at === null) {
                auth()->logout();
                return redirect()->back()->withErrors(['email' => 'Your email address is not verified.'])->withInput();
            }
            if ($user->isAdmin) {
                return redirect()->route('user.index')->with('success', 'Login successful!');
            } else {
                return redirect('/')->with('success', 'Login successful!');
            }
        }
        return redirect()->back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }

    // Reset password
    public function enterEmail()
    {
        return view('auth.enter-email');
    }

    public function showResetForm($token)
    {
        return view('auth.forgot-password', ['token' => $token]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');
        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email does not exist');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::send('mail.reset-link', ['token' => $token], function ($message) use ($email) {
            $message->to($email)
                ->subject('Reset Password Notification');
        });
        return redirect()->back()->with('success', 'Reset link sent successfully');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required'
        ]);

        $passwordReset = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (!$passwordReset || $passwordReset->email !== $request->email) {
            return redirect()->back()->with('error', 'Invalid token or email.');
        }

        $user = $this->userModel->where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'No user found with this email.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }
}
