<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    protected function redirectTo(): string
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return '/admin/dashboard';
        }
        return '/dashboard';
    }

    protected function authenticated(Request $request, $user)
    {
        session(['current_company_id' => $user->company_id]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
