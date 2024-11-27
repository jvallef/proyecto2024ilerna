<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestAvatarController extends Controller
{
    public function create()
    {
        return view('tests.media.avatar');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'avatar' => ['required', 'image', 'max:2048'], // max 2MB
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password'), // password por defecto para testing
        ]);

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        return redirect()->back()->with('success', 'Usuario creado con avatar exitosamente.');
    }
}
