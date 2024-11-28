<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    public function index(): View
    {
        $images = Image::all();
        return view('upload.index', compact('images'));
    }

    public function store(Request $request): JsonResponse
    {
        $uploadedImages = [];

        foreach($request->file('files') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'images/' . $imageName;
            
            $image->move(public_path('images'), $imageName);
            
            $imageData = [
                'name' => $imageName,
                'path' => $path,
                'filesize' => filesize(public_path($path))
            ];

            $uploadedImage = Image::create($imageData);
            $uploadedImages[] = [
                'name' => $uploadedImage->name,
                'path' => asset($uploadedImage->path)
            ];
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages
        ]);
    }
}
