<?php


namespace Nestermaks\LaravelPricelist\Http\Controllers;



use Nestermaks\LaravelPricelist\Http\Resources\PricelistResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as JsonResponse;
use Nestermaks\LaravelPricelist\Models\PricelistItem;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistCollection;

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

    public function store(Request $request): JsonResponse
    {
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

    public function show($id)
    {
        return new PricelistResource (
            PricelistItem::where('id', $id)
                ->withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->first()
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $item = PricelistItem::where('id', $id)->firstOrFail();

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