<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function index()
    {
        // Get all movies
        // $movies = Movie::with('user:id,name')->orderBy('created_at', 'desc')->get(); // Fetch movies with user info
        $user = Auth::user(); // Get the authenticated user
        //if (!$user) {
        //  return response()->json(['error' => 'Unauthorized'], 401);
        //}
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }
        // Get the authenticated user's movies
        $movies = Movie::where('user_id', Auth::id()) // Filter movies by authenticated user's ID
            ->with('user:id,name') // Optionally, include user info
            ->orderBy('created_at', 'desc') // Order movies by creation date
            // ->get() to get all movies
            ->paginate(); // this will get at most 15 movies per page by default


        return response()->json($movies);
    }

    // Store a new movie
    public function store(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'openingText' => 'required|string',
            'releaseDate' => 'required|date',
        ]);

        $movie = Movie::create([
            'title' => $request->title,
            'openingText' => $request->openingText,
            'releaseDate' => $request->releaseDate,
            'user_id' => Auth::id(), // Get authenticated user
            // 'user_id'=> 1,
        ]);

        // return response()->json($movie, 201);
        return response()->json(['message' => 'Movie added successfully'], 201);

    }

    // Show a single movie
    public function show(Movie $movie)
    {
        if ($movie->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        // Ensure the note belongs to the authenticated user
        // if ($movie->user_id !== Auth::id()) {
        //     abort(403); // Forbidden response
        // }

        return response()->json($movie);
    }

    // Update a movie
    public function update(Request $request, Movie $movie)
    {
        if ($movie->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        // Ensure the note belongs to the authenticated user
        // if ($movie->user_id !== Auth::id()) {
        //     abort(403); // Forbidden response
        // }

        $request->validate([
            'title' => 'string|max:255',
            'openingText' => 'string',
            'releaseDate' => 'date',
        ]);

        $movie->update($request->all());

        // return response()->json($movie);
        return response()->json(['message' => 'Movie updated successfully']);
    }

    // Delete a movie
    public function destroy(Movie $movie)
    {
        if ($movie->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        $movie->delete();

        return response()->json(['message' => 'Movie deleted successfully']);
    }

    // Delete all movies belonging to the authenticated user
    public function destroyAll()
    {
        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        // Delete all movies owned by the authenticated user
        $deleted = Movie::where('user_id', $user->id)->delete();

        return response()->json(['message' => "$deleted movies deleted successfully"]);
    }
}