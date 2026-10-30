<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_the_matching_login_portal(): void
    {
        $this->get(route('pengguna.beranda'))
            ->assertRedirect(route('login.pengguna'));

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login.admin'));
    }

    public function test_login_and_registration_pages_are_public(): void
    {
        $this->get(route('login.pengguna'))->assertOk();
        $this->get(route('login.admin'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_log_in_and_reach_their_dashboard(): void
    {
        $user = $this->createUser();

        $this->post(route('login.pengguna.submit'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ])
            ->assertRedirect(route('pengguna.beranda'))
            ->assertSessionHas('user_id', $user->id);

        $this->get(route('pengguna.beranda'))->assertOk();
    }

    public function test_admin_can_log_in_through_the_admin_portal(): void
    {
        $admin = $this->createUser('admin');

        $this->post(route('login.admin.submit'), [
            'email' => $admin->email,
            'password' => 'correct-password',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('user_id', $admin->id);
    }

    public function test_regular_user_cannot_log_in_through_the_admin_portal(): void
    {
        $user = $this->createUser();

        $this->from(route('login.admin'))
            ->post(route('login.admin.submit'), [
                'email' => $user->email,
                'password' => 'correct-password',
            ])
            ->assertRedirect(route('login.admin'))
            ->assertSessionHas('error', 'Akun ini tidak memiliki akses ke portal Administrator.')
            ->assertSessionMissing('user_id');
    }

    public function test_invalid_password_displays_a_clear_error_without_authenticating(): void
    {
        $user = $this->createUser();

        $this->from(route('login.pengguna'))
            ->post(route('login.pengguna.submit'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login.pengguna'))
            ->assertSessionHas('error', 'Email atau kata sandi tidak sesuai.')
            ->assertSessionMissing('user_id');
    }

    public function test_login_rejects_an_invalid_email_format(): void
    {
        $this->from(route('login.pengguna'))
            ->post(route('login.pengguna.submit'), [
                'email' => 'bukan-email',
                'password' => 'correct-password',
            ])
            ->assertRedirect(route('login.pengguna'))
            ->assertSessionHasErrors([
                'email' => 'Masukkan alamat email yang valid.',
            ]);
    }

    public function test_registration_stores_a_hashed_password_and_redirects_to_login(): void
    {
        $this->post(route('register.submit'), [
            'nama' => 'Pengguna Baru',
            'email' => 'pengguna.baru@example.com',
            'password' => 'password123',
            'konfirmasi_password' => 'password123',
        ])
            ->assertRedirect(route('login.pengguna'))
            ->assertSessionHas('success', 'Registrasi berhasil! Silakan masuk.');

        $this->assertDatabaseHas('users', [
            'nama_lengkap' => 'Pengguna Baru',
            'email' => 'pengguna.baru@example.com',
            'role' => 'mahasiswa',
        ]);

        $user = User::where('email', 'pengguna.baru@example.com')->firstOrFail();
        $this->assertNotSame('password123', $user->password_hash);
        $this->assertTrue(Hash::check('password123', $user->password_hash));
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        $this->createUser();

        $this->from(route('register'))
            ->post(route('register.submit'), [
                'nama' => 'Pengguna Baru',
                'email' => 'TEST@example.com',
                'password' => 'password123',
                'konfirmasi_password' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors([
                'email' => 'Email sudah terdaftar.',
            ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_rejects_a_short_password_with_a_clear_error(): void
    {
        $this->from(route('register'))
            ->post(route('register.submit'), [
                'nama' => 'Pengguna Baru',
                'email' => 'pengguna.baru@example.com',
                'password' => 'short',
                'konfirmasi_password' => 'short',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors([
                'password' => 'Kata sandi minimal 6 karakter.',
            ]);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_a_mismatched_password_confirmation(): void
    {
        $this->from(route('register'))
            ->post(route('register.submit'), [
                'nama' => 'Pengguna Baru',
                'email' => 'pengguna.baru@example.com',
                'password' => 'password123',
                'konfirmasi_password' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('konfirmasi_password');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_regular_user_is_redirected_from_an_admin_page(): void
    {
        $user = $this->createUser();
        $this->withSession(['user_id' => $user->id]);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login.admin'));
    }

    public function test_logout_clears_the_authenticated_session(): void
    {
        $user = $this->createUser();
        $this->withSession(['user_id' => $user->id]);

        $this->post(route('logout'))
            ->assertRedirect(route('landing.home'))
            ->assertSessionMissing('user_id');

        $this->get(route('pengguna.beranda'))
            ->assertRedirect(route('login.pengguna'));
    }

    public function test_database_seeder_registers_five_faker_users_with_hashed_passwords(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 9);

        $fakerUsers = User::whereNotIn('id', [1, 2, 3, 4])->get();
        $this->assertCount(5, $fakerUsers);

        foreach ($fakerUsers as $user) {
            $this->assertTrue(Hash::check('password123', $user->password_hash));
            $this->assertSame('mahasiswa', $user->role);
        }
    }

    private function createUser(string $role = 'mahasiswa'): User
    {
        return User::create([
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'no_telepon' => '',
            'role' => $role,
            'password_hash' => Hash::make('correct-password'),
        ]);
    }
}
