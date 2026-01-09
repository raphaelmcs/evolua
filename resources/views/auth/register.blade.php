<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro - EVOLUA</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700">
    <link rel="stylesheet" href="{{ asset('css/app/evolua-admin.css') }}">
</head>
<body class="fi-body">
    <main class="evolua-login">
        <div class="evolua-login-card" style="grid-template-columns: 1fr;">
            <div class="evolua-login-panel" style="max-width: 620px; margin: 0 auto; width: 100%;">
                <div class="evolua-login-brand">
                    <img src="{{ asset('images/logo-evolua.png') }}" alt="Evolua" class="evolua-login-logo">
                </div>

                <h1 class="evolua-login-title">Crie sua conta</h1>
                <p class="evolua-login-subtitle">
                    Cadastre sua organizacao e comece a avaliar atletas em poucos minutos.
                </p>

                @if ($errors->any())
                    <div class="evolua-alert evolua-alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.register.store') }}" class="evolua-login-form">
                    @csrf

                    <label for="organization_name" class="evolua-field-label">Nome da organizacao</label>
                    <input
                        id="organization_name"
                        name="organization_name"
                        type="text"
                        class="fi-input"
                        value="{{ old('organization_name') }}"
                        required
                        autocomplete="organization"
                    >

                    <label for="name" class="evolua-field-label">Responsavel</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="fi-input"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name"
                    >

                    <label for="email" class="evolua-field-label">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="fi-input"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >

                    <label for="phone" class="evolua-field-label">Telefone (opcional)</label>
                    <input
                        id="phone"
                        name="phone"
                        type="text"
                        class="fi-input"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        inputmode="tel"
                        placeholder="(11) 90000-0000"
                    >

                    <label for="password" class="evolua-field-label">Senha</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="fi-input"
                        required
                        autocomplete="new-password"
                    >

                    <label for="password_confirmation" class="evolua-field-label">Confirmar senha</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="fi-input"
                        required
                        autocomplete="new-password"
                    >

                    <button type="submit" class="fi-btn" style="margin-top: 12px; width: 100%; padding: 12px 20px;">
                        Criar conta
                    </button>
                </form>

                <div class="evolua-login-footer">
                    Ja possui conta?
                    <a href="{{ url('/admin/login') }}" class="evolua-link">Entrar</a>
                </div>
            </div>
        </div>
    </main>
    <script>
        (() => {
            const input = document.getElementById('phone');
            if (!input) {
                return;
            }

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
                input.value = formatPhone(input.value);
            };

            input.addEventListener('input', update);
            update();
        })();
    </script>
</body>
</html>
