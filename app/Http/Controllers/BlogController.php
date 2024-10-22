<?php

namespace App\Http\Controllers;

use App\Models\Post; // Changed from Blog to Post
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get(); // Changed from Blog to Post

        return response()->json($posts);
    }

    /**
     * Display a paginated list of active posts with optional search query.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function activePosts(Request $request, $limit = 10)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1); // Default to page 1

        // Start the query for published posts
        $posts = Post::where('status', 1); // Changed from Blog to Post

        // If a query is provided, filter by title, summary, and content
        if ($query) {
            $posts->where(function ($q) use ($query) {
                $q->where('title_en', 'like', '%' . $query . '%')
                    ->orWhere('summary_en', 'like', '%' . $query . '%')
                    ->orWhere('title_de', 'like', '%' . $query . '%') // Changed from title_nl to title_de
                    ->orWhere('summary_de', 'like', '%' . $query . '%') // Changed from summary_nl to summary_de
                    ->orWhere('title_ar', 'like', '%' . $query . '%')
                    ->orWhere('summary_ar', 'like', '%' . $query . '%');
            });
        }

        // Paginate the results
        $posts = $posts->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($posts);
    }

    /**
     * Display the specified post by slug along with its content items.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySlug($slug)
    {
        // Fetch the post by slug along with its content items
        $post = Post::where('slug', $slug)->with('contentItems')->first(); // Changed from Blog to Post

        if (!$post) {
            // If the post with the given slug is not found, return a 404 error
            return response()->json(['message' => 'Post not found'], 404); // Changed message from 'Blog post' to 'Post'
        }

        // If the post is found, return it along with its content items
        return response()->json($post);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title_en'   => 'required_without_all:title_ar,title_de|max:255', // Changed title_nl to title_de
            'title_ar'   => 'required_without_all:title_en,title_de|max:255', // Changed title_nl to title_de
            'title_de'   => 'required_without_all:title_en,title_ar|max:255', // Changed title_nl to title_de
            'summary_en' => 'nullable|max:1000',
            'summary_ar' => 'nullable|max:1000',
            'summary_de' => 'nullable|max:1000', // Changed summary_nl to summary_de
            'items'      => 'required',
            'image'      => 'nullable|file|image|mimes:jpg,jpeg,png',
        ]);

        // Create the post
        $post = new Post; // Changed from Blog to Post
        $post->title_en = $request->input('title_en');
        $post->summary_en = $request->input('summary_en');
        $post->title_ar = $request->input('title_ar');
        $post->summary_ar = $request->input('summary_ar');
        $post->title_de = $request->input('title_de'); // Changed from title_nl to title_de
        $post->summary_de = $request->input('summary_de'); // Changed from summary_nl to summary_de
        $post->author_id = Auth::id();

        // Handle main image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('upload/posts/', $filename);
            $post->image = 'upload/posts/' . $filename;
        }

        $post->slug = Str::slug(
            $request->input("title_en") ??
                $request->input("title_de") ?? // Changed from title_nl to title_de
                $request->input("title_ar")
        );

        $post->save();

        foreach ($request->input('items') as $index => $itemData) {
            $contentItem = new ContentItem();
            $contentItem->post_id = $post->id; // Changed from blog to post
            $contentItem->type = $itemData['type'];
            $contentItem->order = $itemData['order'];
            $contentItem->language = $itemData['language'];

            // Handle file uploads for images or PDFs
            if (($itemData['type'] === 'image' || $itemData['type'] === 'pdf') && $request->hasFile("items.$index.file")) {
                $file = $request->file("items.$index.file");
                $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $file->move('upload/content/', $filename);
                $contentItem->file_path = 'upload/content/' . $filename;
            } else {
                $contentItem->content = $itemData['content'];
            }

            $contentItem->save();
        }

        // Return the newly created post
        return response()->json(['message' => 'Post created successfully'], 201); // Changed message from 'Blog' to 'Post'
    }

    /**
     * Update the specified post in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $slug)
    {
        // Find the post by slug
        $post = Post::where('slug', $slug)->first(); // Changed from Blog to Post

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404); // Changed message from 'Blog' to 'Post'
        }

        // Update the post with new data
        $post->title_en = $request->input("title_en");
        $post->title_ar = $request->input("title_ar");
        $post->title_de = $request->input("title_de"); // Changed from title_nl to title_de
        $post->summary_en = $request->input("summary_en");
        $post->summary_ar = $request->input("summary_ar");
        $post->summary_de = $request->input("summary_de"); // Changed from summary_nl to summary_de

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('upload/posts/', $filename);
            $post->image = 'upload/posts/' . $filename;
        }

        $post->save();

        // Handle content items update
        foreach ($request->input('items') as $index => $itemData) {
            // Check if the content item already exists, otherwise create a new one
            $contentItem = ContentItem::where('post_id', $post->id)
                ->where('language', $itemData['language'])
                ->where('id', $itemData['id'] ?? null) // Ensure 'id' exists to prevent null issues
                ->first() ?? new ContentItem();

            $contentItem->post_id = $post->id;
            $contentItem->type = $itemData['type'];
            $contentItem->order = $itemData['order'];
            $contentItem->language = $itemData['language'];

            // Handle file uploads for images or PDFs
            if (($itemData['type'] === 'image' || $itemData['type'] === 'pdf') && $request->hasFile("items.$index.file")) {
                $file = $request->file("items.$index.file");
                $filename = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $file->move('upload/content/', $filename);
                $contentItem->file_path = 'upload/content/' . $filename;
            } else {
                $contentItem->content = $itemData['content'];
            }

            $contentItem->save();
        }

        // Return the updated post
        return response()->json($post, 200);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id); // Changed from Blog to Post
        $post->delete();

        // Return success message
        return response()->json(['message' => 'Post deleted successfully'], 200); // Changed message from 'Blog' to 'Post'
    }

    /**
     * Remove the specified content item from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteContentItem($id)
    {
        $contentItem = ContentItem::findOrFail($id);
        $contentItem->delete();

        // Return success message
        return response()->json(['message' => 'Content Item deleted successfully'], 200); // Updated message for clarity
    }
}
