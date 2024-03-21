<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Http\Requests\HomePageDynamicRequest;
use App\Http\Resources\HomePageDynamicResource;
use App\Http\Resources\HomePageResource;
use App\Http\Resources\ProductResource;
use App\Models\HomePageDynamic;
use App\Models\HomePageDynamicContent;
use App\Services\HomePageService;
use App\Services\UserService;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function __construct(private HomePageService $homePageService, private UserService $userService)
    {
    }

    public function homePage()
    {

        $homePage = $this->homePageService->getAll();
        return $this->successResponse(
            $homePage,
            'dataFetchedSuccessfully'
        );
    }

    public function index()
    {
        $homePageDynamic = HomePageDynamic::with(["content"])->orderBy("order")->paginate(3);

        return HomePageDynamicResource::collection($homePageDynamic)->response()->getData(true);
    }

    public function show($id)
    {
        $homePageDynamic = HomePageDynamic::with(["content"])->find($id);

        if (!$homePageDynamic) {
            return $this->errorResponse(
                'not_found',
                404
            );
        }
        if ($homePageDynamic->type != HomePageDynamic::TYPE_PRODUCT) {
            return $this->errorResponse(
                'no_products',
                400
            );
        }
        return $this->getProducts($homePageDynamic->content);
    }

    public function getProducts($contents)
    {
        $getProducts = [];
        foreach ($contents as $content) {
            $getProducts[] =
                ProductResource::make($content["product"])->getAllResource();
        }
        return $getProducts;
    }


    public function store(HomePageDynamicRequest $request)
    {
        $homePageDynamic =  HomePageDynamic::create([
            'type' => $request->type,
            'order' => $request->order,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
        ]);

        foreach ($request->content as $content) {
            HomePageDynamicContent::create([
                'home_page_dynamic_id' => $homePageDynamic->id,
                'product_id' => $content["product_id"] ?? null,
                'category_id' => $content["category_id"] ?? null,
                'banner_id' => $content["banner_id"] ?? null,
            ]);
        }

        return $this->successResponse(
            $this->resource($homePageDynamic, HomePageDynamicResource::class),
            'dataAddedSuccessfully'
        );
    }

    public function update(HomePageDynamicRequest $request)
    {
        $homePageDynamic =  HomePageDynamic::find($request->id);

        $homePageDynamic->type = $request->type ?? $homePageDynamic->type;
        $homePageDynamic->order = $request->order ?? $homePageDynamic->order;
        $homePageDynamic->title_ar = $request->title_ar ?? $homePageDynamic->title_ar;
        $homePageDynamic->title_en = $request->title_en ?? $homePageDynamic->title_en;

        $homePageDynamic->content()->delete();

        if (isset($request->content)) {

            foreach ($request->content as $content) {
                HomePageDynamicContent::create([
                    'home_page_dynamic_id' => $homePageDynamic->id,
                    'product_id' => $content["product_id"] ?? null,
                    'category_id' => $content["category_id"] ?? null,
                    'banner_id' => $content["banner_id"] ?? null,
                ]);
            }
        }

        $homePageDynamic->save();

        return $this->successResponse(
            $this->resource($homePageDynamic, HomePageDynamicResource::class),
            'dataUpdatedSuccessfully'
        );
    }

    public function destroy(HomePageDynamicRequest $request)
    {
        HomePageDynamic::destroy($request->id);
        return $this->successResponse(
            null,
            'dataDeletedSuccessfully'
        );
    }
}
