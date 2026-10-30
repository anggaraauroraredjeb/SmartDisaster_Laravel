@extends('layouts.admin')

@section('title', ($modul->exists ? 'Edit' : 'Tambah') . ' Modul Edukasi - Admin')

@section('admin')
    @php
        $kategoriOpts = collect(\App\Models\EducationModule::KATEGORI)->mapWithKeys(fn ($v) => [$v => ucfirst($v)])->all();
    @endphp

    <x-page-header :title="$modul->exists ? 'Edit Modul Edukasi' : 'Tambah Modul Edukasi'" subtitle="Tulis materi yang mudah dipahami pengguna." />

    <x-card class="max-w-3xl">
        <form method="POST" enctype="multipart/form-data" action="{{ $modul->exists ? route('admin.edukasi.update', $modul) : route('admin.edukasi.store') }}">
            @csrf
            @if ($modul->exists) @method('PUT') @endif

            <x-input name="judul" label="Judul" :value="$modul->judul" required />
            <x-select name="kategori" label="Kategori" :options="$kategoriOpts" :value="$modul->kategori" required />
            <x-textarea name="konten" label="Konten" rows="8" :value="$modul->konten" required />
            <x-input name="file" label="Gambar atau dokumen" type="file" :required="!$modul->exists" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp,.pdf,.doc,.docx" />
            <p class="-mt-3 mb-4 text-xs text-gray-500">Format JPG, PNG, GIF, WEBP, BMP, PDF, DOC, atau DOCX. Maksimal 5 MB.</p>

            @if ($modul->file_path)
                <div class="mb-4 text-sm">
                    <span class="text-gray-600">Berkas saat ini:</span>
                    <a href="{{ Storage::disk('public')->url($modul->file_path) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:text-blue-800">Lihat berkas</a>
                </div>
            @endif

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-[#cc0000] hover:bg-red-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors">Simpan</button>
                <a href="{{ route('admin.edukasi.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 border border-gray-200 hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </x-card>
@endsection
