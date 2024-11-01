<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

use function App\Helpers\uploadFile;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'imageUrl',
        'age',
        'social_id',
        'social_type',
        'email_verified_at'
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'userId');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'userId');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getUsers()
    {
        return $this->get();
    }

    public function getUserById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function storeUser($request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'password' => $request->password
        ];
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('image')) {
            $IMAGE_URL = uploadFile($request->file('image'));
            $data['imageUrl'] = $IMAGE_URL;
        }
        $user = $this->create($data);
        return $user;
    }

    public function updateUser($request, $id)
    {
        $data = [
            'name' => $request->name,
            'age' => $request->age,
            'password' => $request->password
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            $IMAGE_URL = uploadFile($request->file('image'));
            $data['imageUrl'] = $IMAGE_URL;
        }
        $this->where('id', $id)->update(array_filter($data));
    }

    public function changePassword($request, $id)
    {
        $data = [
            'oldPassword' => $request->oldPassword,
            'newPassword' => $request->newPassword,
            'confirmPassword' => $request->confirmPassword
        ];

        $user = $this->getUserById($id);

        if (!Hash::check($data['oldPassword'], $user->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }
        if ($data['newPassword'] !== $data['confirmPassword']) {
            return response()->json(['error' => 'New password and confirm password do not match'], 400);
        }
        if ($data['newPassword'] === $data['oldPassword']) {
            return response()->json(['error' => 'New password must be different from the current password'], 400);
        }

        $this->where('id', $id)->update(['password' => Hash::make($data['newPassword'])]);
        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function deleteUser($id)
    {
        $this->where('id', $id)->delete();
    }
}
