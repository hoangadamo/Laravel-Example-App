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
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'password' => $request->password
        ];

        if ($request->hasFile('image')) {
            $data['imageUrl'] = $this->user->uploadFile($request->file('image'));
        }
        $data['password'] = Hash::make($data['password']);
        // dd($data);
        User::create($data);
        return redirect(route('user.index'));
    }

    public function edit(User $user, $id)
    {
        return response()->json($user->findOrFail($id));
    }

    public function getUserList()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function update(UpdateUserRequest $request, User $user, $id)
    {

        $data = [
            'name' => $request->name,
            'age' => $request->age,
            'password' => $request->password
        ];

        if ($request->hasFile('image')) {
            $data['imageUrl'] = $this->user->uploadFile($request->file('image'));
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // dd($data);
        $user->where('id', $id)->update(array_filter($data));

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, $id)
    {
        $user->where('id', $id)->delete();
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
