<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // solo visibili, con count prodotti visibili
        $cats = Category::query()
            ->where('is_visible', true)
            ->withCount(['products' => function ($q) {
                $q->where('is_visible', true);
            }])
            ->orderBy('name')
            ->get();

        return response()->json($cats);
    }
}
