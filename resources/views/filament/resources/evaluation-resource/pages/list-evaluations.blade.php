<x-filament-panels::page class="evolua-resource-page evolua-evaluations-list">
    <section class="evolua-resource-hero evolua-resource-hero--flat">
        <div class="evolua-resource-hero-content">
            <p class="evolua-resource-kicker">Avaliacoes</p>
            <h1 class="evolua-resource-title">Lista de avaliacoes</h1>
            <p class="evolua-resource-subtitle">
                Acompanhe historico, responsaveis e resultados das avaliacoes.
            </p>
        </div>
        <div class="evolua-resource-hero-actions">
            <x-filament-actions::actions :actions="$this->getCachedHeaderActions()" />
        </div>
    </section>

    <section class="evolua-resource-card">
        <x-filament-panels::resources.tabs />

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

        {{ $this->table }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
    </section>
</x-filament-panels::page>
