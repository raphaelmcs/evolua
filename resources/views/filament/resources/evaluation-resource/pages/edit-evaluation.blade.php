<x-filament-panels::page class="evolua-resource-page evolua-evaluations-form">
    <section class="evolua-resource-hero">
        <div class="evolua-resource-hero-content">
            <p class="evolua-resource-kicker">Atualizacao</p>
            <h1 class="evolua-resource-title">Editar avaliacao</h1>
            <p class="evolua-resource-subtitle">
                Ajuste notas, observacoes e visibilidade quando necessario.
            </p>
        </div>
    </section>

    @capture($form)
        <div class="evolua-form-card">
            <x-filament-panels::form
                id="form"
                :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
                wire:submit="save"
            >
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
    @endphp

    <section class="evolua-form-layout">
        @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
            {{ $form() }}
        @endif

        @if (count($relationManagers))
            <x-filament-panels::resources.relation-managers
                :active-locale="isset($activeLocale) ? $activeLocale : null"
                :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
                :content-tab-label="$this->getContentTabLabel()"
                :content-tab-icon="$this->getContentTabIcon()"
                :content-tab-position="$this->getContentTabPosition()"
                :managers="$relationManagers"
                :owner-record="$record"
                :page-class="static::class"
            >
                @if ($hasCombinedRelationManagerTabsWithContent)
                    <x-slot name="content">
                        {{ $form() }}
                    </x-slot>
                @endif
            </x-filament-panels::resources.relation-managers>
        @endif

        <aside class="evolua-form-aside">
            <h3>Dica rapida</h3>
            <p>
                Mantenha a visibilidade correta antes de compartilhar o relatorio.
            </p>
        </aside>
    </section>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>
