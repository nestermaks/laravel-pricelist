<?php


namespace Nestermaks\LaravelPricelist\Http\Controllers;



//use App\Http\Resources\PricelistResource;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse as JsonResponse;
use League\Flysystem\Exception;
use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistCollection;
use Nestermaks\LaravelPricelist\Models\PricelistItem;


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

    public function store(Request $request): JsonResponse
    {
        try {
            $pricelist = new Pricelist();

            $pricelist->translateOrNew($request->lang)->title = $request->title;
            $pricelist->translateOrNew($request->lang)->description = $request->description;
            $pricelist->order = $request->order;
            $pricelist->active = $request->active;

            $pricelist->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong']);
        }
        return \response()->json(['success' => 'success'], 201);


    }

    public function show($id)
    {
        return new PricelistResource (
            Pricelist::where('id', $id)
                ->withTranslation()
                ->with('related_items')
                ->with('related_items.translations')
                ->first()
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $pricelist = Pricelist::where('id', $id)->firstOrFail();

            $pricelist->translateOrNew($request->lang)->title = $request->title;
            $pricelist->translateOrNew($request->lang)->description = $request->description;
            $pricelist->order = $request->order;
            $pricelist->active = $request->active;

            $pricelist->save();
        } catch (\Exception $e) {
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
            return response()->json(['error' => 'Something went wrong']);
        }
        return \response()->json(['success' => 'success'], 200);

    }
}