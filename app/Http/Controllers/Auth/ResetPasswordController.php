<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'login' => $request->query('email', $request->query('login', '')),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'login' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $user = $this->findUserByLogin($validated['login']);

        if (! $user) {
            return back()
                ->withErrors(['login' => 'Nao encontramos uma conta com esse email ou telefone.'])
                ->withInput();
        }

        $status = Password::reset(
            [
                'email' => $user->email,
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
            ],
            function (User $user) use ($validated): void {
                $user->forceFill([
                    'password' => Hash::make($validated['password']),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/admin/login')->with('status', 'Senha atualizada. Voce ja pode entrar.');
        }

        return back()
            ->withErrors(['login' => __($status)])
            ->withInput();
    }

    private function findUserByLogin(string $login): ?User
    {
        if ($this->isEmail($login)) {
            return User::query()->where('email', $login)->first();
        }

        $normalized = $this->normalizePhone($login);

        return User::query()
            ->where('phone', $login)
            ->orWhere('phone', $normalized)
            ->first();
    }

    private function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function normalizePhone(string $value): string
    {
        return (string) preg_replace('/\D+/', '', $value);
    }
}
