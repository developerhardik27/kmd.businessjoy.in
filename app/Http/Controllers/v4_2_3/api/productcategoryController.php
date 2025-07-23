<?php

namespace App\Http\Controllers\v4_2_3\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class productcategoryController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $productModel, $productCategoryModel;

    public function __construct(Request $request)
    {
        $this->companyId = $request->company_id ?? session('company_id');
        $this->userId = $request->user_id ?? session('user_id');
        
        $this->dbname($this->companyId);
        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->value('rp');

        if (empty($user_rp)) {
            $this->customerrorresponse();
        }

        $this->rp = json_decode($user_rp, true);

        $this->masterdbname = DB::connection()->getDatabaseName();

        $this->productModel = $this->getmodel('product');
        $this->productCategoryModel = $this->getmodel('product_category');
    }

    public function fetchCategory(Request $request)
    {
        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['productcategory']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $productcategoryres = $this->productCategoryModel::where('is_deleted', 0)->where('is_active', 1);

        if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
            $productcategoryres->where('products.created_by', $this->userId);
        }

        $productcategory = $productcategoryres->get();

        if ($productcategory->isEmpty()) {
            return $this->successresponse(404, 'productcategory', 'No Records Found');
        }
       
        return $this->successresponse(200, 'productcategory', $productcategory);
    }

    public function datatable()
    {
        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['productcategory']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $productcategoryres = $this->productCategoryModel::where('is_deleted', 0);

        if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
            $productcategoryres->where('products.created_by', $this->userId);
        }

        $totalcount = $productcategoryres->get()->count(); // count total record
        $productcategory = $productcategoryres->get();

        if ($productcategory->isEmpty()) {
            return DataTables::of($productcategory)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($productcategory)
            ->with([
                'status' => 200,
                'recordsTotal' => $totalcount, // Total records count
            ])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['productcategory']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $productcategoryres = $this->productCategoryModel::where('is_deleted', 0);

        if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
            $productcategoryres->where('products.created_by', $this->userId);
        }

        $productcategory = $productcategoryres->get();

        if ($productcategory->isEmpty()) {
            return $this->successresponse(404, 'productcategory', 'No Records Found');
        }
        
        return $this->successresponse(200, 'productcategory', $productcategory);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // condition for check if user has permission to add new records
        if ($this->rp['inventorymodule']['productcategory']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100',
            'parent_category' => 'required',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            if (isset($request->parent_category) && $request->parent_category == null) {
                $validator->errors()->add('parent_category', 'The parent category field is required.');
            }

            return $this->errorresponse(422, $validator->messages());

        } else {

            if (isset($request->parent_category) && $request->parent_category == null) {
                $validator->errors()->add('parent_category', 'The parent category field is required.');
                return $this->errorresponse(422, $validator->messages());
            } 

            $parentcategory = $request->parent_category == 'main' ? null : $request->parent_category;

            $isduplicateproductcategory = $this->productCategoryModel::where('cat_name', $request->category_name)->where('is_deleted', 0);

            if ($parentcategory != null) {
                $isduplicateproductcategory->where('parent_id', $parentcategory);
            }

            $isduplicateproductcategory = $isduplicateproductcategory->exists();

            if ($isduplicateproductcategory) {
                return $this->successresponse(500, 'message', 'Product category is already exists.');
            }

            $productCategory = $this->productCategoryModel::create([
                'cat_name' => $request->category_name,
                'parent_id' => $parentcategory,
                'created_by' => $this->userId,
            ]);

            if ($productCategory) {
                return $this->successresponse(200, 'message', 'Product category succesfully created');
            } else {
                return $this->successresponse(500, 'message', 'Product category not succesfully created');
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['productcategory']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $productcategory = $this->productCategoryModel::find($id);

        if (!$productcategory) {
            return $this->successresponse(404, 'message', "No such product category Found!");
        }

        if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
            if ($productcategory->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'productcategory', $productcategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['productcategory']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100',
            'parent_category' => 'required',
            'user_id' => 'required|numeric',
            'company_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if (isset($request->parent_category) && $request->parent_category == null) {
                $validator->errors()->add('parent_category', 'The parent category field is required.');
            }
            return $this->errorresponse(422, $validator->messages());
        } else {

            if (isset($request->parent_category) && $request->parent_category == null) {
                $validator->errors()->add('parent_category', 'The parent category field is required.');
                return $this->errorresponse(422, $validator->messages());
            }


            $productcategory = $this->productCategoryModel::find($id);

            if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
                if ($productcategory->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }


            $parentcategory = $request->parent_category == 'main' ? null : $request->parent_category;

            $isduplicateproductcategory = $this->productCategoryModel::where('cat_name', $request->category_name)
                ->where('is_deleted', 0)
                ->whereNot('id', $id);

            if ($parentcategory != null) {
                $isduplicateproductcategory->where('parent_id', $parentcategory);
            }

            $isduplicateproductcategory = $isduplicateproductcategory->exists();

            if ($isduplicateproductcategory) {
                return $this->successresponse(500, 'message', 'Product category is already exists.');
            }

            if ($productcategory) {
                $productcategory->update([
                    'cat_name' => $request->category_name,
                    'parent_id' => $parentcategory,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d')
                ]);
                return $this->successresponse(200, 'message', 'product category succesfully updated');
            } else {
                return $this->successresponse(404, 'message', 'No such product category found!');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        return $this->executeTransaction(function () use ($id) {

            //condition for check if user has permission to delete record
            if ($this->rp['inventorymodule']['productcategory']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $productcategory = $this->productCategoryModel::find($id);

            if (!$productcategory) {
                return $this->successresponse(404, 'message', 'No such product category found!');
            }

            if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
                if ($productcategory->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            $productcategory->update([
                'is_deleted' => 1
            ]);

            $product = $this->productModel::where('product_category', $id)->update([
                'product_category' => null
            ]);

            // Function to update child categories based on parent_id
            $this->updateChildCategoriesStatus($productcategory, 'is_deleted', 1);


            return $this->successresponse(200, 'message', 'Product succesfully deleted');
        });
    }

    /**
     * Status update 
     */
    public function statusupdate(Request $request, string $id)
    {

        return $this->executeTransaction(function () use ($request, $id) {
           
            if ($this->rp['inventorymodule']['productcategory']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $productcategory = $this->productCategoryModel::find($id);

            if (!$productcategory) {
                return $this->successresponse(404, 'message', 'No such product category found!');
            }

            if ($this->rp['inventorymodule']['productcategory']['alldata'] != 1) {
                if ($productcategory->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', "You are Unauthorized!");
                }
            }

            $productcategory->update([
                'is_active' => $request->status
            ]);

            // Function to update child categories based on parent_id
            $updateupdateChildCategorie = $this->updateChildCategoriesStatus($productcategory, 'is_active', $request->status);

            return $this->successresponse(200, 'message', 'status succesfully updated');
        });
    }

    private function updateChildCategoriesStatus($category, $column, $value)
    {
        // Find child categories where parent_id is the category's id
        $childCategories = $this->productCategoryModel::where('parent_id', $category->id)->get();

        if (!$childCategories) {
            return true;
        }

        // Iterate over each child category
        foreach ($childCategories as $childCategory) {
            // Update the child category's status
            $childCategory->update([
                $column => $value,
            ]);

            if ($column == 'is_deleted') {
                $product = $this->productModel::where('product_category', $childCategory->id)->update([
                    'product_category' => null
                ]);
            }

            // Recursively update the child categories of this child (if any)
            $this->updateChildCategoriesStatus($childCategory, $column, $value);
        }

        return true;
    }
}
