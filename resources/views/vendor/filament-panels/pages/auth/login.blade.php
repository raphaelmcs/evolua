@php
    $loginImage = asset('images/login-hero.jpg');
    $logoImage = asset('images/logo-evolua.png');
@endphp

<div class="evolua-login">
    <div class="evolua-login-card">
        <div class="evolua-login-media" style="background-image: url('{{ $loginImage }}');">
            <div class="evolua-login-ribbon"></div>
        </div>

        <div class="evolua-login-panel">
            <div class="evolua-login-brand">
                <img src="{{ $logoImage }}" alt="Evolua" class="evolua-login-logo">
            </div>

            <p class="evolua-login-subtitle">
                Avalie o desempenho, acompanhe a evolucao e gere relatorios em minutos.
            </p>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

            <x-filament-panels::form id="form" wire:submit="authenticate">
                {{ $this->form }}

                <div class="evolua-login-links">
                    <a href="{{ route('password.request') }}" class="evolua-link">Esqueceu a senha?</a>
                </div>

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>

            <div class="evolua-login-footer">
                Nao tem uma conta?
                <a href="{{ route('auth.register') }}" class="evolua-link">Cadastre-se</a>
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
        </div>
    </div>
</div>
