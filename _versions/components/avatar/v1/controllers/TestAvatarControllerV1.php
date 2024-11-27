<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestAvatarControllerV1 extends Controller
{
    public function create()
    {
        // Preparar la configuración para la vista
        $allowedTypes = env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp');
        $config = [
            'maxSize' => env('AVATAR_MAX_FILE_SIZE', 2048),
            'allowedTypes' => explode(',', $allowedTypes),
            'maxDimensions' => env('AVATAR_MAX_DIMENSIONS', 2048)
        ];

        return view('tests.media.versions.avatar-v1', compact('config'));
    }

    public function store(Request $request)
    {
        $maxSize = env('AVATAR_MAX_FILE_SIZE', 2048);
        $allowedTypes = explode(',', env('AVATAR_ALLOWED_TYPES', 'jpg,jpeg,png,webp'));
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'avatar' => [
                'required',
                'image',
                'mimes:' . implode(',', $allowedTypes),
                'max:' . $maxSize,
            ],
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
