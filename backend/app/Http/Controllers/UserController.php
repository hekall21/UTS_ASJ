<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all()->map(function ($user) {
            $user->display_photo = $user->photo_url ? Storage::disk('minio')->url($user->photo_url) : null;
            return $user;
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'photo' => 'required|image'
        ]);

        $path = Storage::disk('minio')->put('profiles', $request->file('photo'));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'photo_url' => $path
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($request->hasFile('photo')) {
            // Hapus yang lama
            if ($user->photo_url) {
                Storage::disk('minio')->delete($user->photo_url);
            }
            $user->photo_url = Storage::disk('minio')->put('profiles', $request->file('photo'));
        }

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Proses Hapus di MinIO
        if ($user->photo_url) {
            // Kita ltrim untuk memastikan tidak ada '/' di awal yang bikin gagal hapus
            $cleanPath = ltrim($user->photo_url, '/');
            Storage::disk('minio')->delete($cleanPath);
        }
        
        $user->delete();
        return response()->json(['message' => 'Success']);
    }
}