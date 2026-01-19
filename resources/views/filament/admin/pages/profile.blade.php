<x-filament::page>
    {{ $this->form }}

    <x-filament::button
        wire:click="save"
        class="mt-4"
        color="primary"
    >
        Simpan Perubahan
    </x-filament::button>
</x-filament::page>
