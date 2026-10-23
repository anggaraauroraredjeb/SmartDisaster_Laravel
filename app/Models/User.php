<?php

namespace App\Models;

use App\Models\Concerns\StampsOnCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use StampsOnCreate;

    protected string $stampColumn = 'created_at';

    public $timestamps = false;

    protected $fillable = ['nama_lengkap', 'email', 'no_telepon', 'role', 'password_hash'];

    protected $hidden = ['password_hash'];

    protected $casts = ['created_at' => 'datetime'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Akun yang sedang login (dari session). */
    public static function current(): ?self
    {
        $id = session('user_id');

        return $id ? static::find($id) : null;
    }

    public static function pengguna(): ?self
    {
        return static::current();
    }

    public static function admin(): ?self
    {
        return static::current();
    }
}
