<?php

namespace App\Http\Controllers;

use App\Actions\Stores\CreateStore;
use App\Http\Requests\CreateStoreRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StoreController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('stores/Create');
    }

    public function store(CreateStoreRequest $request, CreateStore $createStore): RedirectResponse
    {
        $createStore->handle(
            $request->user(),
            $request->validated('name'),
        );

        return redirect()->route('dashboard');
    }
}
