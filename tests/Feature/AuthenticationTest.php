<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
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
}
