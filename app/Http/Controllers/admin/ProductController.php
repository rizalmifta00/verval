<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use File;
use App\Product;
use Validator;
use Cache;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function getData()
    {
     $data = Product::all();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="btn-delete" class="delete-modal btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/product/'.$row->picture.'" alt="product" title="product" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        return view('product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
        'name' => 'required|max:191',
        
        'jenis' => 'required',
        'picture' => 'max:2048|mimes:png,jpg,jpeg',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/product'), $filename);
            //  Storage::put('public/images/product/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Product::create(
           [
                 'name'=>request('name'),
                 'picture'=>$filename,
                 'jenis' => request('jenis'),
                 'description' => request('description')
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('product.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
        'name_edit' => 'required|max:191',
        'picture_edit' => 'max:2048|mimes:png,jpg,jpeg',
        'jenis_edit' => 'required',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Product::find($id);
        if(!empty($request->picture_edit)){
        $image_path = public_path().'/images/product/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
           $request->picture_edit->move(public_path('images/product'), $filename);
          // Storage::put('public/images/product/' . $filename, File::get($file));
          // Storage::delete('public/images/product/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->description   = $request->description_edit;
        $data->jenis   = $request->jenis_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Product::find($id);
        $image_path = public_path().'/images/product/'.$data->picture;
        if(file_exists($image_path)){
        unlink($image_path);
         }
        $data->delete();
    }
    public function picture($id)
    {
      // $data = Banner::find($id);
      // return Image::make(Storage::get('public/images/banner/'.$data->picture))->response();

      $data = Cache::remember('product'.$id, 24*60, function() use ($id) {
        return Banner::find($id)->picture;
      });
      return Image::make(Storage::get('public/images/product/'.$data))->response();

    }
    
}
