<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class AdminPageController extends Controller
{
    protected $firebase;
    protected $db;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->db = $firebase->getDatabase();
    }

    public function index()
    {
        $driversData = $this->firebase->getData('drivers') ?? [];
        $drivers = collect();
        foreach ($driversData as $id => $driver) {
            $driver['id'] = $id;
            $drivers->push($driver);
        }

        $pages = $this->db->getReference('blogs')->getValue();

        if (!$pages || !is_array($pages)) {
            $pages = [];
        }

        return view('pages.index', compact('drivers', 'pages'));
    }

    public function create()
    {
        $driversData = $this->firebase->getData('drivers') ?? [];
        $drivers = collect();
        foreach ($driversData as $id => $driver) {
            $driver['id'] = $id;
            $drivers->push($driver);
        }
        return view('pages.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'focus_keyword' => 'nullable|string|max:255',
                'slug' => 'nullable|string|max:255',
                'status' => 'nullable|in:Published,Draft',
            ]);

            $content = $request->content;
            $imageUrl = null;

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $fileName = 'blogs/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

                $bucket = $this->firebase->getStorage()->getBucket();
                $bucket->upload(
                    file_get_contents($file->getRealPath()),
                    ['name' => $fileName]
                );

                $imageUrl = "https://firebasestorage.googleapis.com/v0/b/"
                    . $bucket->name()
                    . "/o/" . urlencode($fileName) . "?alt=media";
            }

            $pageData = [
                'title' => $request->title,
                'slug' => Str::slug($request->slug ?: $request->title),
                'content' => $content,
                'meta_title' => $request->meta_title ?? '',
                'meta_description' => $request->meta_description ?? '',
                'canonical_url' => $request->canonical_url ?? '',
                'redirect_type' => $request->redirect_type ?? 'none',
                'redirect_url' => $request->redirect_url ?? '',
                'featured_image' => $imageUrl,
                'focus_keyword' => $request->focus_keyword ?? '',
                'image_alt' => $request->image_alt ?? '',
                'status' => $request->status ?? 'Published',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            $this->db->getReference('blogs')->push($pageData);

            return redirect()->route('pages.index')->with('success', 'Blog created successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating blog: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $driversData = $this->firebase->getData('drivers') ?? [];
        $drivers = collect();
        foreach ($driversData as $driverKey => $driver) {
            $driver['id'] = $driverKey;
            $drivers->push($driver);
        }

        $page = $this->db->getReference('blogs/' . $id)->getValue();

        if (!$page) {
            return redirect()->route('pages.index')->with('error', 'Blog not found.');
        }

        return view('pages.edit', compact('page', 'id', 'drivers'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'focus_keyword' => 'nullable|string|max:255',
                'slug' => 'nullable|string|max:255',
                'status' => 'nullable|in:Published,Draft',
            ]);

            $pageRef = $this->db->getReference('blogs/' . $id);

            if (!$pageRef->getSnapshot()->exists()) {
                return redirect()->route('pages.index')->with('error', 'Blog not found.');
            }

            $imageUrl = $request->old_featured_image;

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $fileName = 'blogs/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

                $bucket = $this->firebase->getStorage()->getBucket();
                $bucket->upload(
                    file_get_contents($file->getRealPath()),
                    ['name' => $fileName]
                );

                $imageUrl = "https://firebasestorage.googleapis.com/v0/b/"
                    . $bucket->name()
                    . "/o/" . urlencode($fileName) . "?alt=media";
            }

            $pageRef->update([
                'title' => $request->title,
                'slug' => Str::slug($request->slug ?: $request->title),
                'content' => $request->content,
                'meta_title' => $request->meta_title ?? '',
                'meta_description' => $request->meta_description ?? '',
                'canonical_url' => $request->canonical_url ?? '',
                'redirect_type' => $request->redirect_type ?? 'none',
                'redirect_url' => $request->redirect_url ?? '',
                'featured_image' => $imageUrl,
                'focus_keyword' => $request->focus_keyword ?? '',
                'image_alt' => $request->image_alt ?? '',
                'status' => $request->status ?? 'Published',
                'updated_at' => now()->toDateTimeString(),
            ]);

            return redirect()->route('pages.index')->with('success', 'Blog updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating blog: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $pageRef = $this->db->getReference('blogs/' . $id);

            if (!$pageRef->getSnapshot()->exists()) {
                return redirect()->route('pages.index')->with('error', 'Blog not found.');
            }

            $pageRef->remove();

            return redirect()->route('pages.index')->with('success', 'Blog deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('pages.index')->with('error', 'Error deleting blog: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $pageRef = $this->db->getReference('blogs/' . $id);
            $page = $pageRef->getValue();

            if (!$page) {
                return redirect()->route('pages.index')->with('error', 'Blog not found.');
            }

            $newStatus = ($page['status'] ?? 'Draft') === 'Published' ? 'Draft' : 'Published';
            $pageRef->update([
                'status' => $newStatus,
                'updated_at' => now()->toDateTimeString(),
            ]);

            return redirect()->route('pages.index')->with('success', "Blog status changed to {$newStatus}!");
        } catch (\Exception $e) {
            return redirect()->route('pages.index')->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }
}
