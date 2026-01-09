<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordSmsNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
        ]);

        $login = trim((string) $request->input('login'));
        $user = $this->findUserByLogin($login);

        if ($user) {
            if ($this->isEmail($login)) {
                Password::sendResetLink(['email' => $user->email]);
            } else {
                $token = Password::broker()->createToken($user);
                $user->notify(new ResetPasswordSmsNotification($token));
            }
        }

        return back()->with('status', 'Se existir uma conta com esse email ou telefone, enviaremos o link de redefinicao.');
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
