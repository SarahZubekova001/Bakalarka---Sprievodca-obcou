<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function toggleRole($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Nemôžeš meniť svoju vlastnú rolu!'], 403);
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return response()->json(['message' => 'Rola používateľa bola úspešne zmenená.', 'role' => $user->role]);
    }

 
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Nemôžeš vymazať svoj vlastný účet!'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Používateľ bol odstránený.']);
    }
}
