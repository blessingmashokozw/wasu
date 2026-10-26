<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone' => 'required|string|regex:/^\+?[0-9]{9,15}$/|unique:users,phone',
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/', Rules\Password::defaults()],
            'role' => 'required|in:Buyer,Seller',
            'business_name' => 'required_if:role,Seller|string|max:255',
            'business_address' => 'required_if:role,Seller|string|max:500',
            'business_reg_no' => 'nullable|string|max:50|required_if:role,Seller',
            'delivery_address' => 'required_if:role,Buyer|string|max:500',
            'city' => 'required_if:role,Buyer|string|max:100',
        ]);

        $user = DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'account_status' => $validated['role'] === 'Seller' ? 'Pending' : 'Active',
                'failed_attempts' => 0,
                'locked_until' => null,
            ]);

            if ($validated['role'] === 'Seller') {
                Seller::create([
                    'user_id' => $user->user_id,
                    'business_name' => $validated['business_name'],
                    'business_reg_no' => $validated['business_reg_no'] ?? null,
                    'business_address' => $validated['business_address'],
                    'verification_status' => 'Pending',
                ]);
            } else {
                Buyer::create([
                    'user_id' => $user->user_id,
                    'delivery_address' => $validated['delivery_address'],
                    'city' => $validated['city'],
                ]);
            }

            AuditLogger::logRegistration($user->user_id);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
