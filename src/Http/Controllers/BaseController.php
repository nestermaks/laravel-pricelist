<?php


namespace Nestermaks\LaravelPricelist\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse as JsonResponse;
use Illuminate\Http\Request;
use Nestermaks\LaravelPricelist\Http\Resources\PricelistCollection;
use Nestermaks\LaravelPricelist\Models\Pricelist;
use Nestermaks\LaravelPricelist\Models\PricelistItem;

class BaseController
{

    //Changes order of pricelist items within a pricelist

    public function changeOrder(Request $request): JsonResponse
    {
        try {
            $pricelist = Pricelist::where('id', $request->pricelist_id)->first();
            $item = PricelistItem::where('id', $request->pricelist_item_id)->first();

            $pricelist->changeItemOrder($item, $request->item_order);
        } catch (\Exception $e) {
            return response()->json($e);
        }

        return \response()->json(['success' => 'success'], 200);
    }

    //Attach or detach pricelist items from a pricelist or pricelists from an item

    public function relation(Request $request): JsonResponse
    {
        try {
            $action = $request->action;
            if (gettype($request->pricelist_id) === 'array') {
                $model = PricelistItem::where('id', $request->pricelist_item_id)->firstOrFail();
                $related_items = Pricelist::whereIn('id', $request->pricelist_id)->get();
            } else {
                $model = Pricelist::where('id', $request->pricelist_id)->firstOrFail();
                $related_items = PricelistItem::whereIn('id', $request->pricelist_item_id)->get();
            }

            $model->$action($related_items);
        } catch (\Exception $e) {
            return response()->json($e);
        }

        return \response()->json(['success' => 'success'], 200);
    }

    //Get related pricelist for your model

    public function getPricelistsOfModel(string $model_name, int $model_id): PricelistCollection
    {
        $model = $this->getModelInstance($model_name, $model_id);

        return new PricelistCollection(
            $model->pricelists()
                ->withTranslation()
                ->with('relatedItems')
                ->with('relatedItems.translations')
                ->paginate(config('pricelist.pricelists-per-page'))
        );
    }

    //Attach and detach pricelist for your model

    public function relationWithModel(Request $request): JsonResponse
    {
        try {
            $model = $this->getModelInstance($request->model_name, $request->model_id);
            $action = $request->action;
            $pricelist = Pricelist::where('id', $request->pricelist_id)->first();

            $model->$action($pricelist);
        } catch (\Exception $e) {
            return response()->json($e);
        }

        return \response()->json(['success' => 'success'], 200);
    }

    protected function getModelInstance(string $model_name, int $model_id): Model
    {
        return $model_name::where('id', $model_id)->first();
    }
}
