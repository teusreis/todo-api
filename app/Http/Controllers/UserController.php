<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function vefiryEmail(string $email)
    {
        $exists = User::where('email', $email)->exists();

        return response()->json([
            'status' => 'ok',
            'data' => $exists
        ]);
    }
}
