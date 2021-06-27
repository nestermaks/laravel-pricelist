<?php


namespace Nestermaks\LaravelPricelist\Http\Controllers;

use Illuminate\Http\JsonResponse as JsonResponse;
use Nestermaks\LaravelPricelist\Http\Requests\StorePricelistItemRequest;
use Nestermaks\LaravelPricelist\Http\Requests\UpdatePricelistItemRequest;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistCollection;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistResource;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class PricelistItemController
{
    public function index(): PricelistCollection
    {
        return new PricelistCollection(
            PricelistItem::withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->paginate(config('pricelist.pricelist-items-per-page'))
        );
    }

    public function store(StorePricelistItemRequest $request): JsonResponse
    {
        $request->validated();

        try {
            $item = new PricelistItem();

            $item->translateOrNew($request->lang)->title = $request->title;
            $item->translateOrNew($request->lang)->units = $request->units;
            $item->shortcut = $request->shortcut;
            $item->price = $request->price;
            $item->max_price = $request->max_price;
            $item->price_from = $request->price_from;
            $item->active = $request->active;

            $item->save();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json($e);
            }

            return response()->json(['error' => 'Something went wrong']);
        }

        return \response()->json(['success' => 'success'], 201);
    }

    public function show(int $id): PricelistResource
    {
        return new PricelistResource(
            PricelistItem::where('id', $id)
                ->withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->first()
        );
    }

    public function update(UpdatePricelistItemRequest $request, int $id): JsonResponse
    {
        $request->validated();

        try {
            $item = PricelistItem::where('id', $id)->firstOrFail();

            if ($request->title) {
                $item->translateOrNew($request->lang)->title = $request->title;
            }

            if ($request->units) {
                $item->translateOrNew($request->lang)->units = $request->units;
            }

            if ($request->shortcut) {
                $item->shortcut = $request->shortcut;
            }

            if ($request->price !== null) {
                $item->price = $request->price;
            }

            if ($request->max_price !== null) {
                $item->max_price = $request->max_price;
            }

            if ($request->price_from !== null) {
                $item->price_from = $request->price_from;
            }

            if ($request->active !== null) {
                $item->active = $request->active;
            }

            $item->save();
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
            $item = PricelistItem::where('id', $id)->firstOrFail();
            $item->delete();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return response()->json($e);
            }

            return response()->json(['error' => 'Something went wrong']);
        }

        return \response()->json(['success' => 'success'], 200);
    }
}
