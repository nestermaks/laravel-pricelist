<?php


namespace Nestermaks\LaravelPricelist\Http\Controllers;

use Illuminate\Http\JsonResponse as JsonResponse;
use Illuminate\Http\Request;
use Nestermaks\LaravelPricelist\Http\Requests\StorePricelistRequest;
use Nestermaks\LaravelPricelist\Http\Requests\UpdatePricelistRequest;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistCollection;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistResource;
use Nestermaks\LaravelPricelist\Models\Pricelist;

class PricelistController
{
    public function index(): PricelistCollection
    {
        return new PricelistCollection(
            Pricelist::withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->paginate(config('pricelist.pricelists-per-page'))
        );
    }

//    public function store(Request $request): JsonResponse
    public function store(StorePricelistRequest $request): JsonResponse
    {
        $request->validated();

        try {
            $pricelist = new Pricelist();

            $pricelist->translateOrNew($request->lang)->title = $request->title;
            $pricelist->translateOrNew($request->lang)->description = $request->description;
            $pricelist->order = $request->order;
            $pricelist->active = $request->active;

            $pricelist->save();

        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json($e);
            }

            return response()->json(['error' => 'Something went wrong']);
        }

        return \response()->json(['success' => 'success'], 201);
    }

    public function show($id): PricelistResource
    {
        return new PricelistResource(
            Pricelist::where('id', $id)
                ->withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->first()
        );
    }

    public function update(UpdatePricelistRequest $request, int $id): JsonResponse
    {
        $request->validated();

        try {
            $pricelist = Pricelist::where('id', $id)->firstOrFail();

            if ($request->title) {
                $pricelist->translateOrNew($request->lang)->title = $request->title;
            }

            if ($request->description) {
                $pricelist->translateOrNew($request->lang)->description = $request->description;
            }

            if ($request->active !== null) {
                $pricelist->active = $request->active;
            }

            if ($request->order !== null) {
                $pricelist->order = $request->order;
            }

            $pricelist->save();

        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json($e);
            }

            return response()->json(['error' => 'Something went wrong']);
        }

        return \response()->json(['success' => 'success'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $pricelist = Pricelist::where('id', $id)->firstOrFail();
            $pricelist->delete();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json($e);
            }

            return response()->json(['error' => 'Something went wrong']);
        }

        return \response()->json(['success' => 'success'], 200);
    }
}
