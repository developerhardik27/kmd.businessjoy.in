<?php

namespace App\Http\Controllers\v4_2_1\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


class productController extends commonController
{

    public $userId, $companyId, $masterdbname, $rp, $productModel, $temp_imgModel, $inventoryModel, $product_column_mappingModel;

    public function __construct(Request $request)
    {
        $this->dbname($request->company_id);
        $this->companyId = $request->company_id;
        $this->userId = $request->user_id;
        $this->masterdbname = DB::connection()->getDatabaseName();

        // **** for checking user has permission to action on all data 
        $user_rp = DB::connection('dynamic_connection')->table('user_permissions')->select('rp')->where('user_id', $this->userId)->get();
        $permissions = json_decode($user_rp, true);
        if (empty($permissions)) {
            $this->customerrorresponse();
        }
        $this->rp = json_decode($permissions[0]['rp'], true);

        $this->productModel = $this->getmodel('product');
        $this->temp_imgModel = $this->getmodel('temp_image');
        $this->inventoryModel = $this->getmodel('inventory');
        $this->product_column_mappingModel = $this->getmodel('product_column_mapping');
    }

    public function datatable()
    {
        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['product']['view'] != 1) {
            return response()->json([
                'status' => 500,
                'message' => 'You are Unauthorized',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ]);
        }

        $productres = $this->productModel::leftJoin('product_categories as pc', 'products.product_category', 'pc.id')
            ->join('inventory', 'products.id', 'inventory.product_id')
            ->select(
                'products.*',
                'pc.cat_name as category_name',
                'inventory.available as available_stock'
            )
            ->where('products.is_deleted', 0);

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            $productres->where('products.created_by', $this->userId);
        }

        $totalcount = $productres->get()->count(); // count total record

        $product = $productres->get();

        if ($product->isEmpty()) {
            return DataTables::of($product)
                ->with([
                    'status' => 404,
                    'message' => 'No Data Found',
                    'recordsTotal' => $totalcount, // Total records count
                ])
                ->make(true);
        }

        return DataTables::of($product)
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
        if ($this->rp['inventorymodule']['product']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $productres = $this->productModel::leftJoin('product_categories as pc', 'products.product_category', 'pc.id')
            ->join('inventory', 'products.id', 'inventory.product_id')
            ->select(
                'products.*',
                'pc.cat_name as category_name',
                'inventory.available as available_stock'
            )
            ->where('products.is_deleted', 0);

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            $productres->where('products.created_by', $this->userId);
        }

        $product = $productres->get();

        if ($product->isEmpty()) {
            return $this->successresponse(404, 'product', 'No Records Found');
        }

        return $this->successresponse(200, 'product', $product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return $this->executeTransaction(function () use ($request) {
            // condition for check if user has permission to add new records
            if ($this->rp['inventorymodule']['product']['add'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'short_description' => 'nullable',
                'description' => 'nullable',
                'category' => 'required|integer',
                'unit' => 'nullable',
                'price' => 'required|numeric',
                'status' => 'required|numeric',
                'product_type' => 'nullable',
                'track_quantity' => 'nullable',
                'continue_selling' => 'nullable',
                'sku' => 'nullable|max:50',
                'images' => 'nullable',
                'company_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'updated_by',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {



                // Check if images are provided in the request
                if ($request->has('images')) {
                    // Convert the comma-separated list of image names to an array
                    $images = explode(',', $request->images);

                    // Retrieve media names associated with the provided original image names
                    $images = $this->temp_imgModel::whereIn('original_name', $images)->distinct('original_name')->pluck('media_name');

                    $newImageNames = []; // Array to store the new image names

                    // Loop through each image and rename it
                    foreach ($images as $image) {
                        // Define the source path and destination directory
                        $sourcePath = public_path('/uploads/temp/' . $image);
                        $dirPath = public_path('/uploads/products/');

                        // Ensure the directory exists before moving the file
                        if (!File::exists($dirPath)) {
                            File::makeDirectory($dirPath, 0755, true);  // Create the directory with proper permissions
                        }

                        // Check if the source file exists
                        if (File::exists($sourcePath)) {
                            // Generate a new unique name for the image
                            $extension = File::extension($image); // Get the file extension
                            $newName = uniqid() . '-' . time() . '.' . $extension; // New file name with extension

                            // Define the destination path
                            $destinationPath = $dirPath . $newName;

                            // Move the file to the new destination
                            $moved = File::move($sourcePath, $destinationPath);

                            if ($moved) {
                                // Add the new image name to the array
                                $newImageNames[] = $newName;

                                // Delete the original file from the source directory after successful move
                                File::delete($sourcePath);

                                // Remove the record from the temp image model table
                                $this->temp_imgModel::where('media_name', $image)->delete();
                            } else {
                                // Log error if file move fails
                                Log::error("Failed to move the file: " . $image);
                            }

                        } else {
                            // Log error if the source file doesn't exist
                            Log::error("Source file not found: " . $sourcePath);
                        }
                    }

                    // Convert the new image names array into a comma-separated string
                    $newImageNamesString = implode(',', $newImageNames);

                } else {
                    // Handle the case where no images are provided (optional)
                    $newImageNamesString = null;
                }

                $product = $this->productModel::create([
                    'name' => $request->name,
                    'short_description' => $request->short_description,
                    'description' => $request->description,
                    'sku' => $request->sku,
                    'unit' => $request->unit,
                    'price_per_unit' => $request->price,
                    'is_active' => $request->status,
                    'product_type' => $request->product_type,
                    'product_category' => $request->category,
                    'product_media' => $newImageNamesString,
                    'track_quantity' => $request->track_quantity ? 1 : 0,
                    'continue_selling' => $request->continue_selling ? 1 : 0,
                    'company_id' => $this->companyId,
                    'created_by' => $this->userId,
                ]);

                if ($product) {

                    $createProductInventory = $this->inventoryModel::create([
                        'product_id' => $product->id
                    ]);

                    return $this->successresponse(200, 'message', 'product  succesfully created');
                } else {
                    return $this->successresponse(500, 'message', 'product not succesfully created');
                }
            }
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //condition for check if user has permission to search record
        if ($this->rp['inventorymodule']['product']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $product = $this->productModel::find($id);

        if (!$product) {
            return $this->successresponse(404, 'message', "No Such product Found!");
        }

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            if ($product->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'product', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['product']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        $product = $this->productModel::leftJoin('product_categories', 'products.product_category', 'product_categories.id')
            ->select(
                'products.*',
                'product_categories.is_active as pc_status',
                'product_categories.cat_name as category_name'
            )
            ->where('products.id', $id)
            ->first();

        if (!$product) {
            return $this->successresponse(404, 'message', "No Such product Found!");
        }

        if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
            if ($product->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'product', $product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        return $this->executeTransaction(function () use ($request, $id) {
            //condition for check if user has permission to edit record
            if ($this->rp['inventorymodule']['product']['edit'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'short_description' => 'nullable',
                'description' => 'nullable',
                'category' => 'required|integer',
                'unit' => 'nullable',
                'price' => 'required|numeric',
                'status' => 'required|numeric',
                'product_type' => 'nullable',
                'track_quantity' => 'nullable',
                'continue_selling' => 'nullable',
                'sku' => 'nullable|max:50',
                'images' => 'nullable',
                'company_id' => 'required|numeric',
                'user_id' => 'required|numeric',
                'created_at',
                'updated_at',
                'is_active',
                'is_deleted'
            ]);

            if ($validator->fails()) {
                return $this->errorresponse(422, $validator->messages());
            } else {
                $product = $this->productModel::find($id);

                if (!$product) {
                    return $this->successresponse(404, 'message', 'No such product found!');
                }

                if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
                    if ($product->created_by != $this->userId) {
                        return $this->successresponse(500, 'message', 'You are Unauthorized');
                    }
                }

                // Check if images are provided in the request
                if ($request->has('images') && isset($request->images)) {
                    // Convert the comma-separated list of image names to an array
                    $images = explode(',', $request->images);
                    $newImageNames = []; // Array to store the new image names

                    if (!empty($product->product_media)) {
                        $oldimgs = explode(',', $product->product_media);

                        foreach ($oldimgs as $oldimg) {
                            if (!in_array($oldimg, $images)) {
                                // The image is no longer in the new images array, so we remove it from the disk
                                $sourcepath = public_path() . '/uploads/products/' . $oldimg;
                                // Ensure the directory exists before delete the file
                                if (File::exists($sourcepath)) {
                                    unlink($sourcepath);
                                }
                            } else {
                                // If the old image is in the new images array, remove it from the new images array because its already stored
                                $images = array_diff($images, [$oldimg]);
                                $newImageNames[] = $oldimg; // old img name store to the new image names
                            }
                        }
                    }

                    if (!empty($images)) {
                        // Retrieve media names associated with the provided original image names
                        $images = $this->temp_imgModel::whereIn('original_name', $images)->distinct('original_name')->pluck('media_name');



                        // Loop through each image and rename it
                        foreach ($images as $image) {
                            // Define the source path and destination directory
                            $sourcePath = public_path('/uploads/temp/' . $image);
                            $dirPath = public_path('/uploads/products/');

                            // Ensure the directory exists before moving the file
                            if (!File::exists($dirPath)) {
                                File::makeDirectory($dirPath, 0755, true);  // Create the directory with proper permissions
                            }

                            // Check if the source file exists
                            if (File::exists($sourcePath)) {
                                // Generate a new unique name for the image
                                $extension = File::extension($image); // Get the file extension
                                $newName = uniqid() . '-' . time() . '.' . $extension; // New file name with extension

                                // Define the destination path
                                $destinationPath = $dirPath . $newName;

                                // Move the file to the new destination
                                $moved = File::move($sourcePath, $destinationPath);

                                if ($moved) {
                                    // Add the new image name to the array
                                    $newImageNames[] = $newName;

                                    // Delete the original file from the source directory after successful move
                                    File::delete($sourcePath);

                                    // Remove the record from the temp image model table
                                    $this->temp_imgModel::where('media_name', $image)->delete();
                                } else {
                                    // Log error if file move fails
                                    Log::error("Failed to move the file: " . $image);
                                }

                            } else {
                                // Log error if the source file doesn't exist
                                Log::error("Source file not found: " . $sourcePath);
                            }
                        }

                        // Convert the new image names array into a comma-separated string
                        $newImageNamesString = implode(',', $newImageNames);
                    } else {
                        // Convert the new image names array into a comma-separated string
                        $newImageNamesString = implode(',', $newImageNames);
                    }


                } else {
                    if (!empty($product->product_media)) {
                        $oldimgs = explode(',', $product->product_media);

                        foreach ($oldimgs as $oldimg) {
                            // The image is no longer in the new images array, so we remove it from the disk
                            $sourcepath = public_path() . '/uploads/products/' . $oldimg;
                            // Ensure the directory exists before delete the file
                            if (File::exists($sourcepath)) {
                                unlink($sourcepath);
                            }
                        }
                    }
                    // Handle the case where no images are provided (optional)
                    $newImageNamesString = null;
                }


                $product->update([
                    'name' => $request->name,
                    'short_description' => $request->short_description,
                    'description' => $request->description,
                    'sku' => $request->sku,
                    'unit' => $request->unit,
                    'price_per_unit' => $request->price,
                    'is_active' => $request->status,
                    'product_type' => $request->product_type,
                    'product_category' => $request->category,
                    'product_media' => $newImageNamesString,
                    'track_quantity' => $request->track_quantity ? 1 : 0,
                    'continue_selling' => $request->continue_selling ? 1 : 0,
                    'updated_by' => $this->userId,
                    'updated_at' => date('Y-m-d')
                ]);

                return $this->successresponse(200, 'message', 'Product succesfully updated');
            }
        });

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeTransaction(function () use ($id) {
            //condition for check if user has permission to delete record
            if ($this->rp['inventorymodule']['product']['delete'] != 1) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }

            $product = $this->productModel::find($id);

            if (!$product) {
                return $this->successresponse(404, 'message', 'No such product found!');
            }

            if ($this->rp['inventorymodule']['product']['alldata'] != 1) {
                if ($product->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }

            /* remove img */
            // $images = $product->product_media;

            // if (!empty($images)) {
            //     $imagesarray = explode(',', $images);

            //     foreach ($imagesarray as $image) {
            //         $sourcepath = public_path() . '/uploads/products/' . $image;
            //         // Ensure the directory exists before delete the file
            //         if (File::exists($sourcepath)) {
            //             unlink($sourcepath);
            //         }
            //     }

            // }

            $product->update([
                'is_deleted' => 1
            ]);

            $productinventory = $this->inventoryModel::where('product_id', $product->id)->update([
                'is_deleted' => 1
            ]);

            return $this->successresponse(200, 'message', 'Product succesfully deleted');
        });
    }


    /*
    column mapping list
    */
    public function columnmappingindex(Request $request)
    {

        // condition for check if user has permission to view records
        if ($this->rp['inventorymodule']['productcolumnmapping']['view'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $productcolumnmappingres = $this->product_column_mappingModel::where('is_deleted', 0);

        if ($this->rp['inventorymodule']['productcolumnmapping']['alldata'] != 1) {
            $productcolumnmappingres->where('created_by', $this->userId);
        }

        $productcolumnmapping = $productcolumnmappingres->get();

        if ($productcolumnmapping->isEmpty()) {
            return $this->successresponse(404, 'productcolumnmapping', 'No Records Found');
        }

        return $this->successresponse(200, 'productcolumnmapping', $productcolumnmapping);
    }


    /*
     store column mapping 
    */
    public function storecolumnmapping(Request $request)
    {
        // condition for check if user has permission to add new records
        if ($this->rp['inventorymodule']['productcolumnmapping']['add'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'product_column' => 'required',
            'invoice_column' => 'required',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {


            $isduplicate = $this->product_column_mappingModel::where('product_column', $request->product_column)
                ->where('invoice_column', $request->invoice_column)
                ->where('is_deleted', 0)->exists();

            if ($isduplicate) {
                return $this->successresponse(500, 'message', 'column links already exists');
            }

            $productcolumnmapping = $this->product_column_mappingModel::create([
                'product_column' => $request->product_column,
                'invoice_column' => $request->invoice_column,
                'created_by' => $this->userId,
            ]);

            if ($productcolumnmapping) {
                return $this->successresponse(200, 'message', 'columns succesfully linked.');
            } else {
                return $this->successresponse(500, 'message', 'columns not succesfully linked');
            }
        }
    }

    /*
    fetch record for column mapping update
    */
    public function editcolumnmapping(int $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['productcolumnmapping']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $productcolumnmapping = $this->product_column_mappingModel::find($id);

        if (!$productcolumnmapping) {
            return $this->successresponse(404, 'message', "No such column links found!");
        }

        if ($this->rp['inventorymodule']['productcolumnmapping']['alldata'] != 1) {
            if ($productcolumnmapping->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        return $this->successresponse(200, 'productcolumnmapping', $productcolumnmapping);
    }

    /*
    update column mapping  
    */
    public function updatecolumnmapping(Request $request, int $id)
    {
        //condition for check if user has permission to edit record
        if ($this->rp['inventorymodule']['productcolumnmapping']['edit'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'product_column' => 'required|string|max:50',
            'invoice_column' => 'required',
            'company_id' => 'required|numeric',
            'user_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->errorresponse(422, $validator->messages());
        } else {

            $isduplicate = $this->product_column_mappingModel::where('product_column', $request->product_column)
                ->where('invoice_column', $request->invoice_column)
                ->where('is_deleted', 0)
                ->whereNot('id', $id)
                ->exists();

            if ($isduplicate) {
                return $this->successresponse(500, 'message', 'column links already exists');
            }

            $productcolumnmapping = $this->product_column_mappingModel::find($id);

            if (!$productcolumnmapping) {
                return $this->successresponse(404, 'message', 'No such columns link found!');
            }
            if ($this->rp['inventorymodule']['productcolumnmapping']['alldata'] != 1) {
                if ($productcolumnmapping->created_by != $this->userId) {
                    return $this->successresponse(500, 'message', 'You are Unauthorized');
                }
            }
           
            $productcolumnmapping->update([
                'product_column' => $request->product_column,
                'invoice_column' => $request->invoice_column,
                'updated_by' => $this->userId,
                'updated_at' => date('Y-m-d')
            ]);
            return $this->successresponse(200, 'message', 'columns link succesfully updated');
        }
    }

    /*
    delete column mapping 
    */
    public function destroycolumnmapping(int $id)
    {
        //condition for check if user has permission to delete record
        if ($this->rp['inventorymodule']['productcolumnmapping']['delete'] != 1) {
            return $this->successresponse(500, 'message', 'You are Unauthorized');
        }
        
        $productcolumnmapping = $this->product_column_mappingModel::find($id);

        if (!$productcolumnmapping) {
            return $this->successresponse(404, 'message', 'No such columns link found!');
        }

        if ($this->rp['inventorymodule']['productcolumnmapping']['alldata'] != 1) {
            if ($productcolumnmapping->created_by != $this->userId) {
                return $this->successresponse(500, 'message', 'You are Unauthorized');
            }
        }

        $productcolumnmapping->update([
            'is_deleted' => 1
        ]);
        return $this->successresponse(200, 'message', 'columns link succesfully deleted');
    }
}
