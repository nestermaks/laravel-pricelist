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
                ->with('relatedItems')
                ->with('relatedItems.translations')
                ->paginate(config('pricelist.pricelists-per-page'))
        );
    }

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

            return \response()
                ->json([
                    'error' => trans(
                        'pricelist::fail.pricelist-created',
                        ['title' => $pricelist->translateOrDefault(app()->getLocale())->title], app()->getLocale())],
                );
        }

        return \response()
            ->json([
                'success' => trans(
                    'pricelist::success.pricelist-created',
                    ['title' => $pricelist->translateOrDefault(app()->getLocale())->title], app()->getLocale())],
                    201
            );
    }

    public function show($id): PricelistResource
    {
        return new PricelistResource(
            Pricelist::where('id', $id)
                ->withTranslation()
                ->with('relatedItems')
                ->with('relatedItems.translations')
                ->first()
        );
    }

    public function update(UpdatePricelistRequest $request, int $id): JsonResponse
    {
        $request->validated();

        $pricelist = Pricelist::where('id', $id)->first();

        if (isset($pricelist)) {
            $pricelist_title = $pricelist->translateOrDefault(app()->getLocale())->title;
        } else {
            return \response()
                ->json([
                    'error' => trans(
                        'pricelist::fail.no-pricelist',
                        ['id' => $id], app()->getLocale())],
                        404
                );
        }

        try {

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

            return \response()
                ->json([
                    'error' => trans(
                        'pricelist::fail.pricelist-updated',
                        ['title' => $pricelist_title], app()->getLocale())],
                );
        }

        return \response()
            ->json([
                'success' => trans(
                    'pricelist::success.pricelist-updated',
                    ['title' => $pricelist->translateOrDefault(app()->getLocale())->title], app()->getLocale())],
                    200
            );
    }

    public function destroy(int $id): JsonResponse
    {
        $pricelist = Pricelist::where('id', $id)->first();

        if (isset($pricelist)) {
            $pricelist_title = $pricelist->translateOrDefault(app()->getLocale())->title;
        } else {
            return \response()
                ->json([
                    'error' => trans(
                        'pricelist::fail.no-pricelist',
                        ['id' => $id], app()->getLocale())],
                        404
                );
        }

        try {
            $pricelist->delete();
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return \response()->json($e);
            }

            return \response()
                ->json([
                    'error' => trans(
                        'pricelist::fail.pricelist-deleted',
                        ['title' => $pricelist_title], app()->getLocale())],
                );
        }

        return \response()
            ->json([
                'success' => trans(
                    'pricelist::success.pricelist-deleted',
                    ['title' => $pricelist_title], app()->getLocale())],
                    200
            );
    }
}
