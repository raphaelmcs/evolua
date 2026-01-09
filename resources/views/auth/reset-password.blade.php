<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir senha - EVOLUA</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700">
    <link rel="stylesheet" href="{{ asset('css/app/evolua-admin.css') }}">
</head>
<body class="fi-body">
    <main class="evolua-login">
        <div class="evolua-login-card" style="grid-template-columns: 1fr;">
            <div class="evolua-login-panel" style="max-width: 560px; margin: 0 auto; width: 100%;">
                <div class="evolua-login-brand">
                    <img src="{{ asset('images/logo-evolua.png') }}" alt="Evolua" class="evolua-login-logo">
                </div>

                <h1 class="evolua-login-title">Redefinir senha</h1>
                <p class="evolua-login-subtitle">
                    Use o token recebido por email ou telefone para criar uma nova senha.
                </p>

                @if ($errors->any())
                    <div class="evolua-alert evolua-alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="evolua-login-form">
                    @csrf

                    <label for="login" class="evolua-field-label">Email ou telefone</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        class="fi-input"
                        value="{{ old('login', $login) }}"
                        required
                        autocomplete="username"
                    >

                    <label for="token" class="evolua-field-label" style="margin-top: 12px;">Token</label>
                    <input
                        id="token"
                        name="token"
                        type="text"
                        class="fi-input"
                        value="{{ old('token', $token) }}"
                        required
                        autocomplete="one-time-code"
                    >

                    <label for="password" class="evolua-field-label" style="margin-top: 12px;">Nova senha</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="fi-input"
                        required
                        autocomplete="new-password"
                    >

                    <label for="password_confirmation" class="evolua-field-label" style="margin-top: 12px;">Confirmar senha</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="fi-input"
                        required
                        autocomplete="new-password"
                    >

                    <button type="submit" class="fi-btn" style="margin-top: 20px; width: 100%; padding: 12px 20px;">
                        Atualizar senha
                    </button>
                </form>

                <div class="evolua-login-footer">
                    <a href="{{ url('/admin/login') }}" class="evolua-link">Voltar para o login</a>
                </div>
            </div>
        </div>
    </main>
    <script>
        (() => {
            const input = document.getElementById('login');
            if (!input) {
                return;
            }

            const isEmail = (value) => /@/.test(value) || /[a-zA-Z]/.test(value);
            const formatPhone = (value) => {
                const digits = value.replace(/\D/g, '').slice(0, 11);
                if (digits.length <= 2) {
                    return digits;
                }

                const area = digits.slice(0, 2);
                const rest = digits.slice(2);

                if (digits.length <= 6) {
                    return `(${area}) ${rest}`;
                }

                if (digits.length <= 10) {
                    return `(${area}) ${rest.slice(0, 4)}-${rest.slice(4)}`;
                }

                return `(${area}) ${rest.slice(0, 5)}-${rest.slice(5)}`;
            };

            const update = () => {
                const value = input.value;
                const emailMode = isEmail(value);
                input.setAttribute('inputmode', emailMode ? 'email' : 'tel');

                if (!emailMode) {
                    input.value = formatPhone(value);
                }
            };

            input.addEventListener('input', update);
            update();
        })();
    </script>
</body>
</html>
