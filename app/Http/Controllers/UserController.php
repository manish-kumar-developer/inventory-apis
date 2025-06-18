<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Fetch users (you can paginate instead of get())
        $users = User::select('id','name','email','created_at')
                     ->orderBy('name')
                     ->get();

        // Render the UsersList page
        return Inertia::render('users/users-list', [
            'users' => $users,
        ]);
    }
}
