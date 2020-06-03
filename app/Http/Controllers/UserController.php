<?php

namespace App\Http\Controllers;

use App\Photo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function register(Request $request){
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $email = $request['email'];
        $password = $request['password'];

        $userChecker = User::where("email", $email)->first();

        if($userChecker == null) {

            $user = new User();
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->auth_key = Str::random(32);
            $user->save();

            return response()->json(['success' => "Created Successfully"], 200);
        }else{
            return response()->json(['success' => "Used Email"], 200);
        }

    }

    public function login(Request $request) {
        $email = $request['email'];
        $password = $request['password'];


        $user = User::where('email', $email)->first();

        if($user == null){
            return response()->json(['failed'=>'User is not exist'], 401);
        }

        if (Hash::check($password, $user->password)) {
            return response()->json($user, 200);
        }
        return response()->json(['failed' => 'wrong password'], 401);
    }

    public function uploadImages(Request $request){
        $user_id = $request['user_id'];
        $image = $request['image'];
        $folderPath = public_path('/images/');



        $image_parts = explode(";base64,", $image);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $image_name = uniqid() . '.png';

        $file = $folderPath . $image_name;

        file_put_contents($file, $image_base64);

        $photo = new Photo();
        $photo->user_id = $user_id;
        $photo->photo = $image_name;
        $photo->save();

        return response()->json(['success'=>'imaged added'], 200);

        return $image_name;

    }

    public function getUser(Request $request){
        $id = $request['user_id'];
        $user = User::findOrFail($id);
        return response()->json(['success'=>$user], 200);
    }

    public function updateUser(Request $request){
        $id = $request['user_id'];
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];


        $user = User::findOrFail($id);
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        if($request['password'] !== null){
            $user->password =  Hash::make($request['password']);
        }

        $user->save();

        return response()->json(['success'=>$user], 200);
    }

    public function getImages() {
        $photos = Photo::with('users')->get();
        return $photos;
    }

    public function searchImages(Request $request){
        $value = $request['value'];
        $value = explode(" ", $value);

        if($request['Empty'] == true){
            $photos = Photo::leftJoin('users', 'photos.user_id', '=', 'users.id')->get();
        }else{
            if(count($value) == 1){
                $photos = Photo::leftJoin('users', 'photos.user_id', '=', 'users.id')
                    ->where('users.first_name', 'like', "%" . $request['value'] . "%")
                    ->orwhere('users.last_name', 'like', "%" . $request['value'] . "%")
                    ->get();
            }else{
                $photos = Photo::leftJoin('users', 'photos.user_id', '=', 'users.id')
                    ->where('users.first_name', 'like', "%" . $value[0] . "%")
                    ->where('users.last_name', 'like', "%" . $value[1] . "%")
                    ->get();
            }
        }
        return $photos;
    }
}
