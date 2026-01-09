<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $phone = $this->normalizePhone((string) $request->input('phone'));
        $request->merge([
            'phone' => $phone !== '' ? $phone : null,
        ]);

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($validated): void {
            $organization = Organization::create([
                'name' => $validated['organization_name'],
                'slug' => $this->generateUniqueSlug($validated['organization_name']),
                'primary_color' => '#16a34a',
            ]);

            User::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_OWNER,
            ]);

            Subscription::create([
                'organization_id' => $organization->id,
                'plan' => 'starter',
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
                'current_period_ends_at' => now()->addDays(14),
                'max_active_athletes' => 20,
            ]);
        });

        return redirect('/admin/login')->with('status', 'Conta criada com sucesso. Voce ja pode entrar.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'organizacao';
        }

        $slug = $base;
        $counter = 1;

        while (Organization::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function normalizePhone(string $value): string
    {
        return (string) preg_replace('/\D+/', '', $value);
    }
}
