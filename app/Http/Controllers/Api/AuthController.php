<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Import the Storage facade
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'gender' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required',
                    'password' => 'required',
                    'country' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'country' => $request->country,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $user = Auth::user(); // Get the authenticated user

            // Validate the update data
            $validateUser = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'gender' => 'required',
                'country' => 'required',
                'phone' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            // Update user fields
            $user->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'gender' => $request->gender,
                'country' => $request->country,
                'phone' => $request->phone,
            ]);

            // Update user password if provided
            if (!empty($request->password)) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Handle photo upload (if provided in the request)
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public'); // Store the uploaded photo

                // Check if a photo record already exists for the user
                if ($user->photos()->exists()) {
                    // Get the existing photo record
                    $existingPhoto = $user->photos()->first();

                    // Verify that the file exists before attempting to delete it
                    if (Storage::disk('public')->exists($existingPhoto->url)) {
                        // Delete the existing file
                        Storage::disk('public')->delete($existingPhoto->url);
                    }

                    // Delete the row from the photos table
                    $existingPhoto->delete();
                }

                // Crop and resize the uploaded photo to 200x200 pixels
                $image = Image::make(storage_path("app/public/$photoPath"));
                $image->fit(200, 200);
                $image->save();

                // Create a new photo record for the user
                $user->photos()->create(['url' => $photoPath]);
            }

            return response()->json([
                'status' => true,
                'message' => 'User Profile Updated Successfully',
                'user' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
