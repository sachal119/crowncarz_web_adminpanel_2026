<?php


namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FirebaseService;


class PageController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->db = $firebase->getDatabase();
    }

    public function show($slug)
    {
        $pages = $this->db->getReference('pages')->getValue() ?? [];

        foreach ($pages as $page) {
            if ($page['slug'] === $slug) {

                if ($page['redirect_type'] == '404') abort(404);

                if (in_array($page['redirect_type'], ['301','302'])) {
                    return redirect($page['redirect_url'], $page['redirect_type']);
                }

                return view('pages.show', compact('page'));
            }
        }

        abort(404);
    }
}
