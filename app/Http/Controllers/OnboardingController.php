<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->company_id && $user->company) {
            return redirect()->route('onboarding.pending');
        }
        $plans = Plan::where('status', true)->get();
        return view('onboarding.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->company_id) {
            return redirect()->route('onboarding.pending');
        }

        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'vrn' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);
        $price = $data['billing_cycle'] === 'yearly' ? $plan->package_price_yearly : $plan->package_price_monthly;

        $company = Company::create([
            'name' => $data['company_name'],
            'legal_name' => $data['legal_name'] ?: $data['company_name'],
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'country' => $data['country'] ?? 'Tanzania',
            'tax_id' => $data['tax_id'],
            'vrn' => $data['vrn'],
            'website' => $data['website'],
            'is_group' => false,
            'is_active' => false,
            'created_by' => $user->id,
            'subscription_plan' => $plan->name,
            'subscription_status' => 'pending',
            'billing_cycle' => $data['billing_cycle'],
            'mrr' => $data['billing_cycle'] === 'yearly' ? round($plan->package_price_yearly / 12) : $price,
            'max_users' => $plan->number_of_users,
        ]);

        $user->update(['company_id' => $company->id]);

        Order::create([
            'order_id' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'name' => $company->name,
            'email' => $user->email,
            'plan_name' => $plan->name,
            'plan_id' => $plan->id,
            'price' => $price,
            'currency' => 'TZS',
            'payment_status' => 'pending',
            'payment_type' => 'quote_request',
            'created_by' => $user->id,
            'company_id' => $company->id,
        ]);

        return redirect()->route('onboarding.pending');
    }

    public function pending()
    {
        $user = auth()->user();
        $company = $user->company;
        if (!$company) {
            return redirect()->route('onboarding.index');
        }
        $order = Order::where('company_id', $company->id)->latest()->first();
        return view('onboarding.pending', compact('company', 'order'));
    }
}
