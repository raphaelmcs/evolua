<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar senha - EVOLUA</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700">
    <link rel="stylesheet" href="{{ asset('css/app/evolua-admin.css') }}">
</head>
<body class="fi-body">
    <main class="evolua-login">
        <div class="evolua-login-card" style="grid-template-columns: 1fr;">
            <div class="evolua-login-panel" style="max-width: 520px; margin: 0 auto; width: 100%;">
                <div class="evolua-login-brand">
                    <img src="{{ asset('images/logo-evolua.png') }}" alt="Evolua" class="evolua-login-logo">
                </div>

                <h1 class="evolua-login-title">Esqueceu a senha?</h1>
                <p class="evolua-login-subtitle">
                    Informe seu email ou telefone cadastrado. Vamos enviar um link ou token para redefinicao.
                </p>

                @if (session('status'))
                    <div class="evolua-alert evolua-alert--success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="evolua-alert evolua-alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="evolua-login-form">
                    @csrf

                    <label for="login" class="evolua-field-label">Email ou telefone</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        class="fi-input"
                        value="{{ old('login') }}"
                        required
                        autocomplete="username"
                        placeholder="email@exemplo.com ou (11) 90000-0000"
                    >

                    <button type="submit" class="fi-btn" style="margin-top: 16px; width: 100%; padding: 12px 20px;">
                        Enviar instrucoes
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
