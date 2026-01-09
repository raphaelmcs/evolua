<x-filament-panels::page class="evolua-resource-page evolua-athletes-form">
    <section class="evolua-resource-hero">
        <div class="evolua-resource-hero-content">
            <p class="evolua-resource-kicker">Cadastro</p>
            <h1 class="evolua-resource-title">Novo atleta</h1>
            <p class="evolua-resource-subtitle">
                Registre dados essenciais para acompanhar performance e evolucao.
            </p>
        </div>
    </section>

    <section class="evolua-form-layout">
        <div class="evolua-form-card">
            <x-filament-panels::form
                id="form"
                :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
                wire:submit="create"
            >
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>

        <aside class="evolua-form-aside">
            <h3>Dica rapida</h3>
            <p>
                Inclua a categoria e posicao para melhorar filtros e relatorios.
            </p>
        </aside>
    </section>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
