<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class UserController extends Controller
{
    protected $userRepository;
    protected $userService;

    public function __construct(UserRepository $userRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userRepository->get();
        return view('users.index', compact('users'));
    }

    public function store(CreateUserRequest $request)
    {
        $users = $this->userService->storeUser($request);
        return redirect(route('user.index'));
    }

    public function edit($id)
    {
        $user = $this->userRepository->getById($id);
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $this->userService->updateUser($id, $request);
        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return redirect(route('user.index'))->with('success', 'user deleted successfully');
    }

    public function toggleStatus($id)
    {
        return $this->userService->toggleStatus($id);
    }
}
