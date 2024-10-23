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
        $users = $this->user->getAllUser();
        return view('users.index', compact('users'));
    }

    // public function index(): View
    // {
    //     $users = DB::select('select * from users');
    //     return view('users.index', ['users' => $users]);
    // }

    // public function create()
    // {
    //     return view('users.create');
    // }

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
        // Find the user by ID
        // $user = $user->findOrFail($id);
        // return view('users.edit', compact('user'));
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

    // public function store(Request $request){
    //     $data = $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6'
    //     ]);

    //     $data['password'] = Hash::make($data['password']);

    //     $user = User::updateOrCreate(
    //         ['email' => $data['email']], // Check if email exists
    //         $data
    //     );

    //     return redirect(route('user.index'))->with('success', 'User saved successfully.');
    // } 


}
