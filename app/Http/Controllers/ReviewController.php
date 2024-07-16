<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('book')->orderBy('created_at', 'DESC');

        if (!empty($request->keyword)) {
            $reviews = $reviews->where('review', 'like', '%' . $request->keyword . '%');
        }

        $reviews = $reviews->paginate(3);

        return view('account.review.list', compact('reviews'));
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        return view('account.review.edit', compact('review'));
    }

    public function updateReview(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'review' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.reviews.edit', ['id' => $id])
                ->withInput()
                ->withErrors($validator);
        }

        $review->review = $request->review;
        $review->status = $request->status;
        $review->save();

        session()->flash('success', 'Review updated successfully');

        return redirect()->route('account.reviews');
    }

    public function deleteReview(Request $request)
    {
        $id = $request->id;
        $review = Review::find($id);
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
