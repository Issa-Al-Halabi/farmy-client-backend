<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return match ($this->route()->getActionMethod()) {
            'index'               =>  $this->getIndexRules(),
            'store'               =>  $this->getCreateRules(),
            'update'               =>  $this->getUpdateRules(),
            'addRelatedProducts'   =>  $this->addRelatedProducts(),
            'deleteRelatedProducts' =>  $this->deleteRelatedProducts(),
            'getCartProductsInfo'   =>  $this->getCartProductsInfoRules(),
        };
    }

    public function getIndexRules()
    {
        return [
            'subCategoryId'             => 'sometimes|required|integer|exists:sub_categories,id',
        ];
    }
    public function getCreateRules()
    {

        return [
            'name'                  => 'required|string',
            'description'           => 'required|string',
            'price'                 => 'required|numeric',
            'tax'                   => 'required|numeric',
            'slug'                  => '',
            'quantity'              => 'required|numeric',
            'status'                => 'sometimes|required|boolean',
            'availability'          => 'sometimes|required|boolean',
            'seller_id'             => 'sometimes|required|exists:sellers,id',
            'subcategory_id'        => 'required|exists:sub_categories,id',
            'discount'              => 'sometimes|required|numeric',
            'discount_status'       => 'sometimes|required|boolean',
            'product_source'        => 'sometimes|required|boolean',
            'commission_id'         => 'sometimes|required|exists:commissions,id',
            'commission_value'      => 'sometimes|required|numeric',
            'attributes'            => 'required|array',
            'attributes.*.id'       => 'required|exists:attributes,id',
            'attributes.*.value'    => 'required|string',
            'image'                 => 'sometimes|image',
            // 'values'           => 'sometimes|required|array',
        ];
    }

    public function getUpdateRules()
    {
        return [
            'name'                  => 'sometimes|string',
            'description'           => 'sometimes|string',
            'price'                 => 'sometimes|numeric',
            'tax'                   => 'numeric',
            'slug'                  => '',
            'quantity'              => 'sometimes|numeric',
            'status'                => 'sometimes|boolean',
            'availability'          => 'sometimes|boolean',
            'seller_id'             => 'sometimes|exists:sellers,id',
            'subcategory_id'        => 'sometimes|exists:sub_categories,id',
            'discount'              => 'sometimes|numeric',
            'discount_status'       => 'sometimes|boolean',
            'product_source'        => 'sometimes|boolean',
            'commission_id'         => 'sometimes|exists:commissions,id',
            'commission_value'      => 'sometimes|numeric',
            'attributes'            => 'sometimes|array',
            'attributes.*.id'       => 'exists:attributes,id',
            'attributes.*.value'    => 'string',
            'image'                 => 'sometimes|image',
            // 'values'           => 'sometimes|array',
        ];
    }
    public function addRelatedProducts()
    {
        return [
            'products'         => 'required|array',
            'related_products' => 'required|array',
        ];
    }
    public function deleteRelatedProducts()
    {
        return [
            'relation_id'      => 'required|integer',
        ];
    }

    public function getCartProductsInfoRules()
    {
        return [
            'products'              => 'required|array|min:1',
            'products.*' => [
                'integer',
                'exists:products,id',
            ],
        ];
    }
}
