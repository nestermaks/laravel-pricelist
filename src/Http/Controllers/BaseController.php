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


class BaseController
{

    //Changes order of price list items within a pricelist

    public function changeOrder(Request $request): JsonResponse
    {
        try {
            $pricelist = Pricelist::where('id', $request->pricelist_id)->first();
            $item = PricelistItem::where('id', $request->pricelist_item_id)->first();

            $pricelist->changeItemOrder($item, $request->item_order);
        } catch (\Exception $e) {
            return \response()->json(['error' => 'error'], 500);
        }
        return \response()->json(['success' => 'success'], 200);

    }

    //Attach or detach items from a pricelist or pricelists from an item

    public function relation(Request $request): JsonResponse
    {
        try {
            $action = $request->action;
            if (gettype($request->pricelist_id) === 'array') {
                $model = PricelistItem::where('id', $request->pricelist_item_id)->firstOrFail();
                $related_items = Pricelist::whereIn('id', $request->pricelist_id)->get();
            }
            else {
                $model = Pricelist::where('id', $request->pricelist_id)->firstOrFail();
                $related_items = PricelistItem::whereIn('id', $request->pricelist_item_id)->get();
            }

            $model->$action($related_items);

        } catch (\Exception $e) {
            return \response()->json(['error' => 'error'], 500);
        }

        return \response()->json(['success' => 'success'], 200);
    }
}