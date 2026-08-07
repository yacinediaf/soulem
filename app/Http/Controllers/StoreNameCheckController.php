<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreNameCheckController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $slug = Str::slug($request->input('name', ''));

        $exists = Store::where('slug', $slug)->exists();

        return response()->json(['available' => ! $exists]);
    }
}
