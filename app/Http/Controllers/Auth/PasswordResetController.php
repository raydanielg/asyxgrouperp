<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $email = $request->email;

        // Delete any existing codes for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Generate 6-digit activation code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        // Send email with the code
        Mail::send('auth.emails.password-reset', ['code' => $code, 'email' => $email], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Password Reset Activation Code - ASYX Group');
        });

        return redirect()->route('password.code', ['email' => base64_encode($email)])
            ->with('status', 'A 6-digit activation code has been sent to your email. The code expires in 15 minutes.');
    }

    public function showCodeForm(Request $request)
    {
        $emailEncoded = $request->query('email');
        $email = $emailEncoded ? base64_decode($emailEncoded) : null;

        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid request. Please try again.');
        }

        return view('auth.passwords.code', ['email' => $email]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $code = $request->code;

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('code', $code)
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Invalid activation code.'])->withInput();
        }

        // Check if code expired (15 minutes)
        $created = Carbon::parse($record->created_at);
        if ($created->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'The activation code has expired. Please request a new one.');
        }

        // Code is valid — redirect to password reset form with token
        return redirect()->route('password.reset', [
            'token' => $record->token,
            'email' => $email,
        ]);
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('auth.emails.password-reset', ['code' => $code, 'email' => $email], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Password Reset Activation Code - ASYX Group');
        });

        return back()->with('status', 'A new activation code has been sent to your email.');
    }

    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email') ?? old('email');

        if (!$email || !$token) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid reset link. Please request a new one.');
        }

        // Verify token still valid
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired reset link. Please request a new one.');
        }

        $created = Carbon::parse($record->created_at);
        if ($created->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'The reset link has expired. Please request a new one.');
        }

        return view('auth.passwords.reset', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->email;
        $token = $request->token;

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid reset token.'])->withInput();
        }

        $created = Carbon::parse($record->created_at);
        if ($created->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->with('error', 'The reset link has expired. Please request a new one.');
        }

        // Update password
        $user = \App\Models\User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. Please sign in with your new password.');
    }
}
