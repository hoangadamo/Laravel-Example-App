<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create($data)
    {
        return $this->user->create($data);
    }

    public function get()
    {
        return $this->user->get();
    }

    public function getById($id)
    {
        return $this->user->where('id', $id)->first();
    }

    public function update($id, $data)
    {
        return $this->user->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $this->user->where('id', $id)->delete();
    }

    public function paginate($limit)
    {
        return $this->user->paginate($limit);
    }

    public function getByEmail($email)
    {
        return $this->user->where('email', $email)->first();
    }
}
