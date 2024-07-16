<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    public function register()
    {
        return view('account.register');
    }
    public function processRegister(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:5',
            'password_confirmation' => 'required',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        // If validation passes, create the user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        // Optionally, you could log the user in or send a confirmation email here

        // Redirect to a success page or dashboard with a success message
        return redirect()->route('account.login')->with('success', 'Registration successful. Please log in.');
    }
    public function login()
    {
        return view('account.login');
    }
    public function authenticate(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('account.profile');
        } else {
            return redirect()->route('account.login')->with('error', 'Either your email or password is incorrect');
        }
    }
    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return view('account.profile', get_defined_vars());
    }
    public function updateProfile(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . Auth::user()->id,
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048' // Updated input name
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
        }

        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;

        // Handle the profile image upload
        if ($request->hasFile('image')) {
            // Get the file from the request
            $file = $request->file('image');

            // Define the file path
            $path = 'uploads/profile_images/';

            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Move the file to the desired location
            $file->move(public_path($path), $fileName);

            // Delete the old image if exists
            if ($user->profile_image) {
                $oldImagePath = public_path($path) . $user->profile_image;
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath); // Use @ to suppress error if file does not exist
                }
            }

            // Save the new image path to the database
            $user->profile_image = $fileName;
            $manager = new ImageManager(Driver::class);
            $img =  $manager->read($path . $fileName);
            $img->cover(150, 150);
            $img->save($path . $fileName);
        }

        $user->save();

        return redirect()->route('account.profile')->with('success', 'Profile updated successfully');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function myReviews(Request $request)
    {
        $reviews = Review::with('book')->orderBy('created_at', 'DESC')->where('user_id', Auth::user()->id);

        if (!empty($request->keyword)) {
            $reviews = $reviews->where('review', 'like', '%' . $request->keyword . '%');
        }

        $reviews = $reviews->paginate(3);
        return view('account.myReviews.list', compact('reviews'));
    }

    public function myReviewsEdit($id)
    {
        $review = Review::where('user_id', Auth::user()->id)->findOrFail($id);
        return view('account.myReviews.edit', compact('review'));
    }
    public function updateMyReview(Request $request, $id)
    {
        $review = Review::where('user_id', Auth::user()->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'review' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.reviews.edit', ['id' => $id])
                ->withInput()
                ->withErrors($validator);
        }

        $review->review = $request->review;
        $review->rating = $request->rating;
        $review->save();

        session()->flash('success', 'Review updated successfully');

        return redirect()->route('account.myReviews');
    }
    public function deleteMyReview(Request $request)
    {
        $id = $request->id;
        $review = Review::where('user_id', Auth::user()->id)->find($id);
        if ($review == null) {
            session()->flash('success', 'Review not found');
            return response()->json([
                'status' => false,
            ]);
        } else {
            $review->delete();
            session()->flash('success', 'Review deleted successfully');
            return response()->json([
                'status' => true,
            ]);
        }
    }
}
