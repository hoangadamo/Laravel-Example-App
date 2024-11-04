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
    protected $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function index()
    {
        $users = $this->userModel->getUsers();
        return view('users.index', compact('users'));
    }

    public function store(CreateUserRequest $request)
    {
        $users = $this->userModel->storeUser($request);
        return redirect(route('user.index'));
    }

    public function edit(User $user, $id)
    {
        return response()->json($user->findOrFail($id));
    }

    public function update(UpdateUserRequest $request, $id)
    {

        $this->userModel->updateUser($request, $id);

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $this->userModel->deleteUser($id);
        return redirect(route('user.index'))->with('success', 'user deleted successfully');
    }

    public function toggleStatus($id)
    {
        try {
            $user = $this->userModel->getUserById($id);
            $user->where('id', $id)->update(['isActive' => !$user->isActive]);
            return redirect()->back()->with('success', 'User status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
