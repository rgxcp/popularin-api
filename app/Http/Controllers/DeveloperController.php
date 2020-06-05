<?php

namespace App\Http\Controllers;

use App\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    public function showStatus() {
        return response()->json([
            101 => 'Request Retrieved',
            202 => 'Request Created',
            303 => 'Request Updated',
            404 => 'Request Deleted',
            505 => 'Signed Up',
            515 => 'Signed In',
            525 => 'Signed Out',
            606 => 'Request Not Found',
            616 => 'Invalid Credentials',
            626 => 'Validator Fails',
            636 => 'Can\'t Follow Self',
            646 => 'Already Favorited',
            656 => 'Already Followed',
            666 => 'Already Liked',
            676 => 'Already Watchlisted'
        ]);
    }

    public function self(Request $request) {
        $authID = $request->header('Auth-ID');
        $authToken = $request->header('Auth-Token');

        $isAuth = Developer::where([
            'id' => $authID,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            $self = Developer::findOrFail($authID);
            
            return response()->json([
                'status' => 101,
                'message' => 'Request Retrieved',
                'result' => $self
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|alpha_dash|min:5|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|different:username'
        ],[
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
            $full_name = $request['full_name'];
            $profile_picture = 'https://ui-avatars.com/api/?name='.preg_replace('/\s+/', '+', $full_name).'&size=512';
    
            $developer = Developer::create([
                'full_name' => $full_name,
                'username' => $request['username'],
                'email' => $request['email'],
                'profile_picture' => $profile_picture,
                'password' => Hash::make($request['password']),
                'api_token' => Str::random(100),
                'token' => Str::random(100)
            ]);
    
            return response()->json([
                'status' => 505,
                'message' => 'Developer Signed Up',
                'result' => $developer
            ]);
        }
    }

    public function signin(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ],[
            'username.required' => 'Username harus di isi',
            'password.required' => 'Kata sandi harus di isi'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $developer = Developer::select(
                'id',
                'token',
                'password'
            )->where('username', $request['username'])
             ->firstOrFail();

            $isAuth = Hash::check(
                $request['password'],
                $developer->password
            );

            if ($isAuth) {
                $developer->update([
                    'token' => Str::random(100)
                ]);

                return response()->json([
                    'status' => 515,
                    'message' => 'Developer Signed In',
                    'result' => $developer
                ]);
            } else {
                return response()->json([
                    'status' => 616,
                    'message' => 'Invalid Credentials'
                ]);
            }
        }
    }

    public function signout(Request $request) {
        $authID = $request->header('Auth-ID');
        $authToken = $request->header('Auth-Token');
        
        $isAuth = Developer::where([
            'id' => $authID,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            Developer::findOrFail($authID)->update([
                'token' => null
            ]);
            
            return response()->json([
                'status' => 525,
                'message' => 'Developer Signed Out'
            ]);
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function update(Request $request, $id) {
        $authToken = $request->header('Auth-Token');

        $isAuth = Developer::where([
            'id' => $id,
            'token' => $authToken
        ])->exists();
        
        if ($isAuth) {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'username' => 'required|alpha_dash|min:5|unique:users,username,'.$id,
                'email' => 'required|email|unique:users,email,'.$id
            ],[
                'full_name.required' => 'Nama lengkap harus di isi',
                'username.required' => 'Username harus di isi',
                'email.required' => 'Alamat email harus di isi',
                'alpha_dash' => 'Username tidak bisa mengandung spasi',
                'email' => 'Format alamat email tidak sesuai',
                'min' => 'Username minimal 5 karakter',
                'username.unique' => 'Username tersebut sudah digunakan',
                'email.unique' => 'Alamat email tersebut sudah digunakan'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 626,
                    'message' => 'Validator Fails',
                    'result' => $validator->errors()->all()
                ]);
            } else {
                $full_name = $request['full_name'];
                $profile_picture = 'https://ui-avatars.com/api/?name='.preg_replace('/\s+/', '+', $full_name).'&size=512';
                
                $developer = Developer::findOrFail($id);
                
                $developer->update([
                    'full_name' => $full_name,
                    'username' => $request['username'],
                    'email' => $request['email'],
                    'profile_picture' => $profile_picture,
                    'token' => Str::random(100)
                ]);
                    
                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated',
                    'result' => $developer
                ]);
            }
        } else {
            return response()->json([
                'status' => 616,
                'message' => 'Invalid Credentials'
            ]);
        }
    }

    public function updatePassword(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password'
        ],[
            'current_password.required' => 'Kata sandi lama harus di isi',
            'new_password.required' => 'Kata sandi baru harus di isi',
            'confirm_password.required' => 'Konfirmasi kata sandi harus di isi',
            'min' => 'Kata sandi baru minimal 8 karakter',
            'different' => 'Kata sandi baru dan lama harus berbeda',
            'same' => 'Kata sandi baru dan konfirmasi tidak sama'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 626,
                'message' => 'Validator Fails',
                'result' => $validator->errors()->all()
            ]);
        } else {
            $developer = Developer::select(
                'id',
                'token',
                'password'
            )->findOrFail($id);

            $isAuth = Hash::check(
                $request['current_password'],
                $developer->password
            );

            if ($isAuth) {
                $developer->update([
                    'password' => Hash::make($request['confirm_password']),
                    'token' => Str::random(100)
                ]);
                    
                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated',
                    'result' => $developer
                ]);
            } else {
                return response()->json([
                    'status' => 616,
                    'message' => 'Invalid Credentials'
                ]);
            }
        }
    }
}
