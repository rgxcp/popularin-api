<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Following;
use App\Point;
use App\Review;
use App\User;
use App\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function search($query)
    {
        $users = User::where('full_name', 'like', '%' . $query . '%')
            ->orWhere('username', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => isset($users[0]) ? 101 : 606,
            'message' => isset($users[0]) ? 'Request Retrieved' : 'Request Not Found',
            'result' => isset($users[0]) ? $users : null
        ]);
    }

    public function showSelf()
    {
        $self = Auth::user();

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $self->makeVisible('email')
        ]);
    }

    public function show($id)
    {
        Carbon::setLocale('id');

        $authID = Auth::check() ? Auth::id() : 0;

        $user = User::findOrFail($id);

        $totalPoint = Point::where('user_id', $id)->sum('total');
        $totalPoint = $totalPoint < 0 ? $totalPoint : "+$totalPoint";

        $recentFavorites = Favorite::with([
            'film'
        ])->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $recentReviews = Review::with([
            'film'
        ])->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $metadata = collect([
            'joined_since' => $user->created_at->diffForHumans(),
            'is_following' => Following::where(['user_id' => $authID, 'following_id' => $id])->exists(),
            'is_follower' => Following::where(['user_id' => $id, 'following_id' => $authID])->exists(),
            'total_point' => $totalPoint,
            'total_following' => Following::where('user_id', $id)->count(),
            'total_follower' => Following::where('following_id', $id)->count(),
            'total_favorite' => Favorite::where('user_id', $id)->count(),
            'total_review' => Review::where('user_id', $id)->count(),
            'total_watchlist' => Watchlist::where('user_id', $id)->count()
        ]);

        $activity = collect([
            'recent_favorites' => isset($recentFavorites[0]) ? $recentFavorites : null,
            'recent_reviews' => isset($recentReviews[0]) ? $recentReviews : null
        ]);

        $collection = collect([
            'user' => $user,
            'metadata' => $metadata,
            'activity' => $activity
        ]);

        return response()->json([
            'status' => 101,
            'message' => 'Request Retrieved',
            'result' => $collection
        ]);
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|alpha_dash|min:5|unique:users,username',
            'email' => 'required|email|unique:users,email',
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

            $user = User::create([
                'full_name' => $fullName,
                'username' => strtolower($request['username']),
                'email' => $request['email'],
                'profile_picture' => $profilePicture,
                'password' => Hash::make($request['password']),
                'api_token' => Hash('SHA256', Str::random(100))
            ]);

            return response()->json([
                'status' => 505,
                'message' => 'Signed Up',
                'result' => $user->makeVisible('email', 'api_token')
            ]);
        }
    }

    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ], [
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
            $user = User::where('username', $request['username'])->firstOrFail();

            $isValid = Hash::check(
                $request['password'],
                $user->password
            );

            if ($isValid) {
                return response()->json([
                    'status' => 515,
                    'message' => 'Signed In',
                    'result' => $user->makeVisible('api_token')->makeHidden('full_name', 'username', 'profile_picture')
                ]);
            } else {
                return response()->json([
                    'status' => 616,
                    'message' => 'Invalid Credentials'
                ]);
            }
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'username' => 'required|alpha_dash|min:5|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id
        ], [
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
            $fullName = $request['full_name'];
            $profilePicture = 'https://ui-avatars.com/api/?name=' . preg_replace('/\s+/', '+', $fullName) . '&size=512';

            $user->update([
                'full_name' => $fullName,
                'username' => $request['username'],
                'email' => $request['email'],
                'profile_picture' => $profilePicture
            ]);

            return response()->json([
                'status' => 303,
                'message' => 'Request Updated',
                'result' => $user->makeVisible('email')
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password'
        ], [
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
            $isValid = Hash::check(
                $request['current_password'],
                $user->password
            );

            if ($isValid) {
                $user->update([
                    'password' => Hash::make($request['confirm_password'])
                ]);

                return response()->json([
                    'status' => 303,
                    'message' => 'Request Updated'
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
