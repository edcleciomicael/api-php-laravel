<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UsersController extends Controller
{
    public function index() {
        return User::all();
    }

    public function store(Request $request) {

        $user = User::create([
            "name"=>$request->input("name"),
            "email"=>$request->input("email")
        ]);

        return $user;

    }
}
