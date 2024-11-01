<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->getUsers();
        return view('users.index', compact('users'));
    }

    public function store(CreateUserRequest $request)
    {
        $users = $this->user->storeUser($request);
        return redirect(route('user.index'));
    }

    public function edit(User $user, $id)
    {
        return response()->json($user->findOrFail($id));
    }

    public function update(UpdateUserRequest $request, $id)
    {

        $this->user->updateUser($request, $id);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $this->user->deleteUser($id);
        return redirect(route('user.index'))->with('success', 'user deleted successfully');
    }

    public function toggleStatus($id)
    {
        try {
            $user = $this->user->getUserById($id);
            $user->where('id', $id)->update(['isActive' => !$user->isActive]);
            return redirect()->back()->with('success', 'User status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
