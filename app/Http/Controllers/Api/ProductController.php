<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('is_visible', true)
            ->with(['categories:id,name,slug']);

        if ($slug = $request->query('category')) {
            $category = Category::where('slug', $slug)->where('is_visible', true)->first();
            if ($category) {
                $query->whereHas('categories', fn($q) => $q->where('categories.id', $category->id));
            } else {
                // se categoria non esiste/visibile, ritorna lista vuota coerente
                return response()->json([
                    'data' => [],
                    'meta' => ['current_page'=>1,'last_page'=>1,'total'=>0]
                ]);
            }
        }

        // ordinamento semplice: recenti visibili
        $products = $query->orderByDesc('id')->paginate(12);

        return response()->json($products);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_visible', true)
            ->with(['categories:id,name,slug'])
            ->firstOrFail();

        return response()->json($product);
    }
}
