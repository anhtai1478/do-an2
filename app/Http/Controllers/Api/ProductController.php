<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $request->user()->load('country'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'=> 'required|string|max:255',
            'phone' => 'nullable|srting',
        ])
    }
}
