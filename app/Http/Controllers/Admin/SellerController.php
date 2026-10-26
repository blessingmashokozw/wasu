<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SellerController extends Controller
{
    /**
     * Display a listing of sellers with optional filtering.
     */
    public function index(Request $request): Response
    {
        $status = $request->query('status', 'Pending');

        $sellers = Seller::with('user')
            ->when($status === 'All', function ($query) {
                return $query;
            }, function ($query) use ($status) {
                return $query->where('verification_status', $status);
            })
            ->get();

        return Inertia::render('Admin/Sellers/Index', [
            'sellers' => $sellers,
            'filter' => $status,
        ]);
    }

    /**
     * Show the form for creating a new seller.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Sellers/Create');
    }

    /**
     * Store a newly created seller.
     */
    public function store(Request $request)
    {
        Gate::authorize('verifySeller', User::class);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|max:255',
            'business_description' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => bcrypt($validated['password']),
                'role' => 'Seller',
                'account_status' => 'Active',
                'email_verified_at' => now(),
            ]);

            Seller::create([
                'user_id' => $user->user_id,
                'business_name' => $validated['business_name'],
                'business_description' => $validated['business_description'],
                'verification_status' => 'Verified',
                'verified_by' => auth()->user()->user_id,
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('admin.sellers.index')->with('success', 'Seller created successfully.');
    }

    /**
     * Verify a seller.
     */
    public function verify(Request $request, Seller $seller)
    {
        Gate::authorize('verifySeller', User::class);

        DB::transaction(function () use ($seller, $request) {
            $seller->update([
                'verification_status' => 'Verified',
                'verified_by' => $request->user()->user_id,
                'verified_at' => now(),
            ]);

            $seller->user->update([
                'account_status' => 'Active',
            ]);

            AuditLogger::logSellerVerification($request->user()->user_id, 'verified');
        });

        return back()->with('success', 'Seller verified successfully.');
    }

    /**
     * Reject a seller.
     */
    public function reject(Request $request, Seller $seller)
    {
        Gate::authorize('verifySeller', User::class);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($seller, $request) {
            $seller->update([
                'verification_status' => 'Rejected',
                'verified_by' => $request->user()->user_id,
                'verified_at' => now(),
            ]);

            $seller->user->update([
                'account_status' => 'Suspended',
            ]);

            AuditLogger::logSellerVerification($request->user()->user_id, 'rejected');
        });

        return back()->with('success', 'Seller rejected successfully.');
    }
}
