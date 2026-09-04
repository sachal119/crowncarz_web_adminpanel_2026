<?php


namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class AdminPageController extends Controller
{
    
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->db = $firebase->getDatabase();
    }
    // private $db;

    // public function __construct()
    // {
    //     $this->db = app('firebase')->createDatabase();
    // }

    public function index()
    {
        // 4️⃣ Prepare drivers collection
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();
    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }
        
     
        
        $pages = $this->db->getReference('blogs')->getValue();
        
        
        // print_r($pages);
        // die;

    if (!$pages) {
        $pages = [];
    }
    
    
        return view('pages.index', compact('drivers','pages'));
    }

    public function create()
    {
            // 4️⃣ Prepare drivers collection
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
            'title' => 'required',
            'content' => 'required',
            'focus_keyword' => 'nullable|string|max:255'
        ]);

        $content = $request->content;
        $imageUrl = null;
        
        

        if ($request->hasFile('featured_image')) {

            $file = $request->file('featured_image');

            

            $fileName = 'pages/' . time() . '_' . $file->getClientOriginalName();

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
            'slug' => Str::slug($request->slug ?? $request->title),
            'content' => $content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'canonical_url' => $request->canonical_url,
            'redirect_type' => $request->redirect_type,
            'redirect_url' => $request->redirect_url,
            'featured_image' => $imageUrl,
            'focus_keyword' => $request->focus_keyword,   // ✅ added
            'image_alt' => $request->image_alt,
            'status' => 'Published',
            'created_at' => now()->toDateTimeString(),
        ];

        $this->db->getReference('blogs')->push($pageData);

        return redirect()->route('pages.index')->with('success', 'Page Created');

    } catch (\Exception $e) {

        return back()->withErrors($e->getMessage());
    }
}

public function delete($id)
{
    try {

        $pageRef = $this->db->getReference('blogs/'.$id);

        if (!$pageRef->getSnapshot()->exists()) {
            return back()->with('error', 'Blog not found');
        }

        $pageRef->remove();

        return back()->with('success', 'Blog deleted successfully');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
}
    
    
    public function toggleStatus($id)
{
    $pageRef = $this->db->getReference('blogs/'.$id);
    $page = $pageRef->getValue();

    if (!$page) {
        return back()->withErrors('Page not found');
    }

    $newStatus = ($page['status'] ?? 'Draft') === 'Published' ? 'Draft' : 'Published';
    $pageRef->update(['status' => $newStatus]);

    return back()->with('success', 'Page status updated');
}

public function edit($id)
{
    
    $driversData = $this->firebase->getData('drivers') ?? [];
    $drivers = collect();
    foreach ($driversData as $id => $driver) {
        $driver['id'] = $id;
        $drivers->push($driver);
    }
    $page = $this->db->getReference('blogs/'.$id)->getValue();

    if (!$page) {
        return back()->with('error', 'Blog not found');
    }
    
echo $page;
die;
    return view('pages.edit', compact('page', 'id','drivers'));
}

public function update(Request $request, $id)
{
    try {

        $pageRef = $this->db->getReference('blogs/'.$id);

        if (!$pageRef->getSnapshot()->exists()) {
            return back()->with('error', 'Blog not found');
        }

        $imageUrl = $request->old_featured_image;

        if ($request->hasFile('featured_image')) {

            $file = $request->file('featured_image');

            // if ($file->getClientOriginalExtension() !== 'webp') {
            //     return back()->withErrors('Only WEBP allowed');
            // }

            $fileName = 'blogs/' . time() . '_' . $file->getClientOriginalName();

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
            'slug' => \Str::slug($request->slug ?? $request->title),
            'content' => $request->content,

            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'canonical_url' => $request->canonical_url,

            'redirect_type' => $request->redirect_type,
            'redirect_url' => $request->redirect_url,

            'featured_image' => $imageUrl,
            'image_alt' => $request->image_alt,

            'updated_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Blog updated successfully');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
}

}
