<x-filament::page class="filament-dashboard-page">

    <x-filament::widgets :widgets="$this->getWidgets()" />
	@livewire('random-games-polling')
    <x-filament::button type="submit" wire:click="$emit('refreshComponent')">
        Refresh Random
    </x-filament::button>

</x-filament::page>


