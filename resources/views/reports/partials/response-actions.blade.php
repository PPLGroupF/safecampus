{{-- Tombol ubah dan hapus tanggapan milik pelapor. --}}
<div class="flex gap-1" x-show="! editing">
    <x-btn type="button" variant="ghost" size="sm" icon="pencil" x-on:click="editing = true" dusk="edit-response-{{ $response->id }}">Ubah</x-btn>
    <form method="POST" action="{{ route('tanggapan.destroy', $response) }}" onsubmit="return confirm('Hapus tanggapan ini?')">
        @csrf
        @method('DELETE')
        <x-btn variant="ghost" size="sm" icon="trash" dusk="delete-response-{{ $response->id }}">Hapus</x-btn>
    </form>
</div>
