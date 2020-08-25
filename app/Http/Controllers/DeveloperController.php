<?php

namespace App\Http\Controllers;

use App\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    public function showStatus()
    {
        return response()->json([
            101 => 'Request Retrieved',
            202 => 'Request Created',
            303 => 'Request Updated',
            404 => 'Request Deleted',
            505 => 'Signed Up',
            515 => 'Signed In',
            606 => 'Request Not Found',
            616 => 'Invalid Credentials',
            626 => 'Validator Fails',
            636 => 'Can\'t Follow Self',
            646 => 'Already Favorited',
            656 => 'Already Followed',
            666 => 'Already Liked',
            676 => 'Already Watchlisted',
            919 => 'Unauthenticated Developer',
            929 => 'Unauthenticated User',
            939 => 'Unauthorized'
        ]);
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|alpha_dash|min:5|unique:developers,username',
            'email' => 'required|email|unique:developers,email',
            'password' => 'required|min:8|different:username'
        ], [
            'full_name.required' => 'Nama lengkap harus di isi',
            'username.required' => 'Username harus di isi',
            'email.required' => 'Alamat email harus di isi',
            'password.required' => 'Kata sandi harus di isi',
            'alpha_dash' => 'Username tidak bisa mengandung spasi',
            'email' => 'Format alamat email tidak sesuai',
            'username.min' => 'Username minimal 5 karakter',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'username.unique' => 'Username tersebut sudah digunakan',
            'email.unique' => 'Alamat email tersebut sudah digunakan',
            'different' => 'Kata sandi dan username harus berbeda'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $fullName = $request['full_name'];
            $profilePicture = 'https://ui-avatars.com/api/?name=' . preg_replace('/\s+/', '+', $fullName) . '&size=512';

            $developer = Developer::create([
                'full_name' => $fullName,
                'username' => strtolower($request['username']),
                'email' => $request['email'],
                'profile_picture' => $profilePicture,
                'password' => Hash::make($request['password']),
                'api_key' => Hash('SHA256', Str::random(100)),
                'api_token' => Hash('SHA256', Str::random(100))
            ]);

            return response()->json([
                'status' => 505,
                'message' => 'Signed Up',
                'result' => $developer->makeVisible('email', 'api_key', 'api_token')
            ]);
        }
    }
}
