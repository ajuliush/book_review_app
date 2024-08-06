<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{

    // Code to list all books
    public function index(Request $request)
    {
        $books = Book::withCount(['reviews' => function ($query) {
            $query->where('status', 1);
        }])->withSum('reviews', 'rating')->orderBy('created_at', 'DESC');
        if (!empty($request->keyword)) {
            $books->where('title', 'like', '%' . $request->keyword . '%');
        }
        $books = $books->paginate(5);
        return view('books.list', get_defined_vars());
    }

    // Code to show a form to create a new book
    public function create()
    {
        return view('books.create');
    }

    // Code to update an existing book
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required',
        ];
        if (!empty($request->image)) {
            $rules['image'] = 'image';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('books.create')->withInput()->withErrors($validator);
        }
        // save book in database
        $book = new Book();
        $book->title = $request->title;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;
        $book->save();
        //upload image here
        if (!empty($request->image)) {
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/books'), $imageName);
            $book->image = $imageName;
            $book->save();

            $manager = new ImageManager(Driver::class);
            $img = $manager->read(public_path('uploads/books/' . $imageName));

            $img->resize(990);
            $img->save(public_path('uploads/books/thumb/' . $imageName));
        }
        return redirect()->route('books.index')->with('success', 'Book created successfully');
    }
    // Code to display a specific book
    public function show($id)
    {
    }


    // Code to show a form to edit an existing book
    public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('books.edit', get_defined_vars());
    }


    // Code to update an existing book
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $rules = [
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required',
        ];
        if (!empty($request->image)) {
            $rules['image'] = 'image';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('books.edit', $book->id)->withInput()->withErrors($validator);
        }
        // save book in database
        $book->title = $request->title;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;
        $book->save();
        //upload image here
        if (!empty($request->image)) {
            // this will delete old image
            File::delete(public_path('uploads/books/' . $book->image));
            File::delete(public_path('uploads/books/thumb/' . $book->image));
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $image->move(public_path('uploads/books'), $imageName);
            $book->image = $imageName;
            $book->save();

            $manager = new ImageManager(Driver::class);
            $img = $manager->read(public_path('uploads/books/' . $imageName));

            $img->resize(990);
            $img->save(public_path('uploads/books/thumb/' . $imageName));
        }
        return redirect()->route('books.index')->with('success', 'Book update successfully');
    }

    // Code to delete a book
    public function destroy(Request $request)
    {
        $book = Book::find($request->id);
        if ($book == null) {
            session()->flash('error', 'Book not found');
            return response()->json(['status' => false, 'message' => 'Book not found']);
        } else {
            File::delete(public_path('uploads/books/' . $book->image));
            File::delete(public_path('uploads/books/thumb/' . $book->image));
            $book->delete();
            session()->flash('success', 'Book delete successfully');
            return response()->json(['status' => true, 'message' => 'Book delete successfully']);
        }
    }
}