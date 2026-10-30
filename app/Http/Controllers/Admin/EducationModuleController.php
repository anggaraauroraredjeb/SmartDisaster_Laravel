<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EducationModule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class EducationModuleController extends Controller
{
    public function index(): View
    {
        return view('admin.edukasi.index', [
            'modules' => EducationModule::orderByDesc('created_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.edukasi.form', ['modul' => new EducationModule(['kategori' => 'umum'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $uploadedFile = $data['file'];
        unset($data['file']);
        $filePath = $uploadedFile->storePublicly('edukasi', 'public');

        if ($filePath === false) {
            throw new RuntimeException('Gagal menyimpan berkas modul edukasi.');
        }

        $data['file_path'] = $filePath;
        $data['created_by'] = User::admin()?->id;

        $modul = EducationModule::create($data);
        ActivityLog::catat('Menambah Modul Edukasi', $modul->judul);

        return redirect()->route('admin.edukasi.index')->with('success', 'Modul edukasi berhasil ditambahkan.');
    }

    public function edit(EducationModule $modul): View
    {
        return view('admin.edukasi.form', compact('modul'));
    }

    public function update(Request $request, EducationModule $modul): RedirectResponse
    {
        $data = $this->validated($request, true);
        $uploadedFile = $data['file'] ?? null;
        unset($data['file']);
        $previousFilePath = $modul->file_path;

        if ($uploadedFile) {
            $filePath = $uploadedFile->storePublicly('edukasi', 'public');

            if ($filePath === false) {
                throw new RuntimeException('Gagal menyimpan berkas modul edukasi.');
            }

            $data['file_path'] = $filePath;
        }

        $modul->update($data);

        if ($uploadedFile && $previousFilePath) {
            Storage::disk('public')->delete($previousFilePath);
        }

        ActivityLog::catat('Mengubah Modul Edukasi', $modul->judul);

        return redirect()->route('admin.edukasi.index')->with('success', 'Modul edukasi berhasil diperbarui.');
    }

    public function destroy(EducationModule $modul): RedirectResponse
    {
        $judul = $modul->judul;
        $filePath = $modul->file_path;
        $modul->delete();

        if ($filePath) {
            Storage::disk('public')->delete($filePath);
        }

        ActivityLog::catat('Menghapus Modul Edukasi', $judul);

        return redirect()->route('admin.edukasi.index')->with('success', 'Modul edukasi berhasil dihapus.');
    }

    private function validated(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'kategori' => ['required', Rule::in(EducationModule::KATEGORI)],
            'konten' => ['required', 'string'],
            'file' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,bmp,pdf,doc,docx',
                'extensions:jpg,jpeg,png,gif,webp,bmp,pdf,doc,docx',
                'max:5120',
            ],
        ], [
            'file.required' => 'Berkas wajib diunggah.',
            'file.mimes' => 'Berkas harus berupa gambar atau dokumen yang didukung.',
            'file.extensions' => 'Ekstensi berkas tidak didukung.',
            'file.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);
    }
}
