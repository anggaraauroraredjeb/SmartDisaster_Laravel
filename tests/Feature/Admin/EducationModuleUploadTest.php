<?php

namespace Tests\Feature\Admin;

use App\Models\EducationModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EducationModuleUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_module_with_its_file(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $this->withSession(['user_id' => $admin->id]);

        $this->post(route('admin.edukasi.store'), [
            'judul' => 'Panduan Gempa',
            'kategori' => 'gempa',
            'konten' => 'Langkah keselamatan saat gempa.',
            'file' => UploadedFile::fake()->create('panduan.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('admin.edukasi.index'));

        $module = EducationModule::firstOrFail();
        $oldFilePath = $module->file_path;
        $this->assertIsString($oldFilePath);
        $this->assertStringStartsWith('edukasi/', $oldFilePath);
        $this->assertDatabaseHas('education_modules', [
            'id' => $module->id,
            'file_path' => $oldFilePath,
        ]);
        $this->assertTrue(Storage::disk('public')->exists($oldFilePath));

        $this->get(route('admin.edukasi.index'))
            ->assertOk()
            ->assertSee('/storage/'.$oldFilePath);

        $this->put(route('admin.edukasi.update', $module), [
            'judul' => 'Panduan Gempa Terbaru',
            'kategori' => 'gempa',
            'konten' => 'Konten diperbarui.',
            'file' => UploadedFile::fake()->create('panduan-baru.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('admin.edukasi.index'));

        $module->refresh();
        $newFilePath = $module->file_path;
        $this->assertIsString($newFilePath);
        $this->assertNotSame($oldFilePath, $newFilePath);
        $this->assertFalse(Storage::disk('public')->exists($oldFilePath));
        $this->assertTrue(Storage::disk('public')->exists($newFilePath));

        $this->delete(route('admin.edukasi.destroy', $module))
            ->assertRedirect(route('admin.edukasi.index'));

        $this->assertDatabaseMissing('education_modules', ['id' => $module->id]);
        $this->assertFalse(Storage::disk('public')->exists($newFilePath));
    }

    public function test_file_is_required_when_creating_a_module(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $this->withSession(['user_id' => $admin->id]);

        $this->from(route('admin.edukasi.create'))
            ->post(route('admin.edukasi.store'), [
                'judul' => 'Panduan Gempa',
                'kategori' => 'gempa',
                'konten' => 'Langkah keselamatan saat gempa.',
            ])
            ->assertRedirect(route('admin.edukasi.create'))
            ->assertSessionHasErrors(['file' => 'Berkas wajib diunggah.']);

        $this->assertDatabaseCount('education_modules', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('edukasi'));
    }

    public function test_upload_rejects_unsupported_file_extensions(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $this->withSession(['user_id' => $admin->id]);

        $this->from(route('admin.edukasi.create'))
            ->post(route('admin.edukasi.store'), [
                'judul' => 'File Tidak Valid',
                'kategori' => 'umum',
                'konten' => 'Konten.',
                'file' => UploadedFile::fake()->create('program.exe', 100, 'application/x-msdownload'),
            ])
            ->assertRedirect(route('admin.edukasi.create'))
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('education_modules', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('edukasi'));
    }

    public function test_upload_rejects_files_larger_than_five_megabytes(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $this->withSession(['user_id' => $admin->id]);

        $this->from(route('admin.edukasi.create'))
            ->post(route('admin.edukasi.store'), [
                'judul' => 'Panduan Gempa',
                'kategori' => 'gempa',
                'konten' => 'Langkah keselamatan saat gempa.',
                'file' => UploadedFile::fake()->create('terlalu-besar.pdf', 5121, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.edukasi.create'))
            ->assertSessionHasErrors(['file' => 'Ukuran berkas maksimal 5 MB.']);

        $this->assertDatabaseCount('education_modules', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('edukasi'));
    }

    public function test_uploaded_file_is_linked_from_the_user_education_page(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('edukasi/panduan.pdf', '%PDF-1.4');
        $user = $this->createUser('Pengguna Biasa', 'user@example.com', 'mahasiswa');
        $module = EducationModule::create([
            'judul' => 'Panduan Gempa',
            'kategori' => 'gempa',
            'konten' => 'Langkah keselamatan saat gempa.',
            'file_path' => 'edukasi/panduan.pdf',
        ]);
        $this->withSession(['user_id' => $user->id]);

        $this->get(route('pengguna.edukasi'))
            ->assertOk()
            ->assertSee('/storage/'.$module->file_path)
            ->assertSee('Lihat lampiran')
            ->assertSee('Unduh lampiran');
    }

    public function test_non_admin_cannot_access_education_management(): void
    {
        $user = $this->createUser('Pengguna Biasa', 'user@example.com', 'mahasiswa');

        $this->withSession(['user_id' => $user->id])
            ->get(route('admin.edukasi.index'))
            ->assertRedirect(route('login.admin'));
    }

    private function createAdmin(): User
    {
        return $this->createUser('Admin Test', 'admin@example.com', 'admin');
    }

    private function createUser(string $name, string $email, string $role): User
    {
        return User::create([
            'nama_lengkap' => $name,
            'email' => $email,
            'no_telepon' => '',
            'role' => $role,
            'password_hash' => Hash::make('password123'),
        ]);
    }
}
