<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class QuickLinkController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebase = $firebaseService->getDatabase();
    }

    public function index()
    {
        $snapshot = $this->firebase->getReference('quick_links')->getValue();
        $links = $snapshot ? $snapshot : [];
        
            $driversData = $this->firebase->getReference('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('quick_links.index', compact('links','drivers'));
    }

    public function create()
    {
            $driversData = $this->firebase->getReference('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('quick_links.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $id = Str::slug($request->title, '_'); 

        $this->firebase->getReference("quick_links/$id")->set([
            'title'       => $request->title,
            'url'         => $request->url,
            'description' => $request->description,
        ]);

        return redirect()->route('quick.links')->with('success', 'Quick link added!');
    }

    public function edit($id)
    {
        $link = $this->firebase->getReference("quick_links/$id")->getValue();
        
            $driversData = $this->firebase->getReference('drivers') ?? [];
$drivers = collect();

foreach ($driversData as $id => $driver) {
    $driver['id'] = $id;
    $drivers->push($driver);
}

        return view('quick_links.edit', compact('link', 'id','drivers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'url'         => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $this->firebase->getReference("quick_links/$id")->update([
            'title'       => $request->title,
            'url'         => $request->url,
            'description' => $request->description,
        ]);

        // return redirect()->route('quick-links.index')->with('success', 'Quick link updated!');
        
        return redirect()->back()->with('success', 'Quick link updated!');
    }

    public function destroy($id)
    {
        $this->firebase->getReference("quick_links/$id")->remove();
        return redirect()->route('quick-links.index')->with('success', 'Quick link deleted!');
    }
}
