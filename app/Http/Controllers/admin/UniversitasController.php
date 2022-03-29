<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use File;
use App\Universitas;
use Validator;
use Cache;

class UniversitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData()
    {
     $data = Universitas::where('sub_menu','Ingin Korea')->get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit-ingin-korea" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/universitas/'.$row->picture.'" alt="universitas" title="universitas" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        return view('kursus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('universitas.create');
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
        'link' => 'required|max:191',
        'negara' => 'required',
        'picture' => 'required|max:2048|mimes:png,jpg,jpeg',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
             
            $request->picture->move(public_path('images/universitas'), $filename);
            //  Storage::put('public/images/universitas/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Universitas::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'picture'=>$filename,
                 'negara' => request('negara'),
                 'sub_menu' => 'Ingin Korea',
                 
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
        // return view('universitas.edit');
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
        'link_edit' => 'required|max:191',
        'picture_edit' => 'max:2048|mimes:png,jpg,jpeg',
        'negara_edit' => 'required',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Universitas::find($id);
        if(!empty($request->picture_edit)){
        $image_path = public_path().'/images/universitas/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
          $request->picture_edit->move(public_path('images/universitas'), $filename);
          // Storage::put('public/images/universitas/' . $filename, File::get($file));
          // Storage::delete('public/images/universitas/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->negara   = $request->negara_edit;
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
        $data = Universitas::find($id);
        if($data->sub_menu=="Ingin Korea"){
        $image_path = public_path().'/images/universitas/'.$data->picture;
        
        }
        else if($data->sub_menu=="Kursus Bahasa Indonesia"){
          $image_path = public_path().'/images/b-indonesia/'.$data->picture;
        }
        else if($data->sub_menu=="Kursus Topik"){
          $image_path = public_path().'/images/kursus-topik/'.$data->picture;
        }
        else if($data->sub_menu=="Kursus Bahasa Korea"){
          $image_path = public_path().'/images/kursus-topik/'.$data->picture;
        }
        else if($data->sub_menu=="Uji Kemampuan"){
          $image_path = public_path().'/images/uji-kemampuan/'.$data->picture;
        }
        if(file_exists($image_path)){
         unlink($image_path);
         }
        $data->delete();
    }
    public function picture($id)
    {
      // $data = Banner::find($id);
      // return Image::make(Storage::get('public/images/banner/'.$data->picture))->response();

      $data = Cache::remember('universitas'.$id, 24*60, function() use ($id) {
        return Universitas::find($id)->picture;
      });
      return Image::make(Storage::get('public/images/universitas/'.$data))->response();

    }

    // START Kurus Bahasa Indoensia

    public function getDataBIndonesia()
    {
     $data = Universitas::where('sub_menu','Kursus Bahasa Indonesia')->get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit-b-indonesia" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/b-indonesia/'.$row->picture.'" alt="pic" title="" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }

    public function storeBIndonesia(Request $request)
    {
        $rules = [
        'picture' => 'required|mimes:png,jpg,jpeg',
        'name' => 'required|max:191',
        
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/b-indonesia'), $filename);
            //  Storage::put('public/images/b-indonesia/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Universitas::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'picture'=>$filename,
                 'description' => request('description'),
                 'sub_menu' => 'Kursus Bahasa Indonesia',
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    public function updateBIndonesia(Request $request, $id)
    {
        $rules = [
        'picture_edit' => 'mimes:png,jpg,jpeg',
        'name_edit' => 'required|max:191',
        
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Universitas::find($id);
        if(!empty($request->picture_edit)){
          $image_path = public_path().'/images/b-indonesia/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
          $request->picture_edit->move(public_path('images/b-indonesia'), $filename);
          // Storage::put('public/images/b-indonesia/' . $filename, File::get($file));
          // Storage::delete('public/images/b-indonesia/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->description   = $request->description_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    // END Kurus Bahasa Indoensia

    // START kursus Topik

    public function getDataKursusTopik()
    {
     $data = Universitas::where('sub_menu','Kursus Topik')->get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit-kursus-topik" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/kursus-topik/'.$row->picture.'" alt="pic" title="" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }

    public function storeKursusTopik(Request $request)
    {
        $rules = [
        'picture' => 'required|mimes:png,jpg,jpeg',
        'name' => 'required|max:191',
        
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/kursus-topik'), $filename);
            //  Storage::put('public/images/kursus-topik/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Universitas::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'picture'=>$filename,
                 'description' => request('description'),
                 'sub_menu' => 'Kursus Topik',
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    public function updateKursusTopik(Request $request, $id)
    {
        $rules = [
        'name_edit' => 'required|max:191',
       
        
        'picture_edit' => 'max:2048|mimes:png,jpg,jpeg',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Universitas::find($id);
        if(!empty($request->picture_edit)){
          $image_path = public_path().'/images/kursus-topik/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
            $request->picture_edit->move(public_path('images/kursus-topik'), $filename);
          // Storage::put('public/images/kursus-topik/' . $filename, File::get($file));
          // Storage::delete('public/images/kursus-topik/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->description   = $request->description_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    // END kursus Topik

    // START Kursus Bahasa KOREA

    public function getDataBKorea()
    {
     $data = Universitas::where('sub_menu','Kursus Bahasa Korea')->get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit-b-korea" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/b-korea/'.$row->picture.'" alt="pic" title="" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }

    public function storeBKorea(Request $request)
    {
        $rules = [
        'picture' => 'required|mimes:png,jpg,jpeg',
        'name' => 'required|max:191',
      
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/b-korea'), $filename);
            //  Storage::put('public/images/b-korea/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Universitas::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'picture'=>$filename,
                 'description' => request('description'),
                 'sub_menu' => 'Kursus Bahasa Korea',
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    public function updateBKorea(Request $request, $id)
    {
        $rules = [
        'picture_edit' => 'mimes:png,jpg,jpeg',
        'name_edit' => 'required|max:191',
        
        
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Universitas::find($id);
        if(!empty($request->picture_edit)){
          $image_path = public_path().'/images/b-korea/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
            $request->picture_edit->move(public_path('images/b-korea'), $filename);
          // Storage::put('public/images/b-korea/' . $filename, File::get($file));
          // Storage::delete('public/images/b-korea/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->description   = $request->description_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    // END Kursus Bahasa KOREA

    // START Uji Kemampuan

    public function getDataUjiKemampuan()
    {
     $data = Universitas::where('sub_menu','Uji Kemampuan')->get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit-uji-kemampuan" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('pictures', function($row){
          $picture = '<img class="img-responsive" src="/images/uji-kemampuan/'.$row->picture.'" alt="pic" title="" width="50%">';
          return $picture;
        })
        ->rawColumns(['action','pictures'])
        ->addIndexColumn()
        ->make(true);
    }

    public function storeUjiKemampuan(Request $request)
    {
        $rules = [
        'picture' => 'required|mimes:png,jpg,jpeg',
        'name' => 'required|max:191',
       
        'description' => 'max:191',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
        if(!empty($request->picture)){
             $file = $request->file('picture');
             $extension = strtolower($file->getClientOriginalExtension());
             $filename = uniqid() . '.' . $extension;
            $request->picture->move(public_path('images/uji-kemampuan'), $filename);
            //  Storage::put('public/images/uji-kemampuan/' . $filename, File::get($file));
           }else{
             $filename='';
           }

         $data = Universitas::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'picture'=>$filename,
                 'description' => request('description'),
                 'sub_menu' => 'Uji Kemampuan',
                 
           ]
         );
        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    public function updateUjiKemampuan(Request $request, $id)
    {
        $rules = [
        'picture_edit' => 'mimes:png,jpg,jpeg',
        'name_edit' => 'required|max:191',
        
        'description' => 'max:191',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Universitas::find($id);
        if(!empty($request->picture_edit)){
        $image_path = public_path().'/images/uji-kemampuan/'.$data->picture;
         if (file_exists($image_path)){
         unlink($image_path);
         }
          $file = $request->file('picture_edit');
          $extension = strtolower($file->getClientOriginalExtension());
          $filename = uniqid() . '.' . $extension;
          $request->picture_edit->move(public_path('images/uji-kemampuan'), $filename);
          // Storage::put('public/images/uji-kemampuan/' . $filename, File::get($file));
          // Storage::delete('public/images/uji-kemampuan/'.$data->picture);
          $data->picture = $filename;
        }
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->description   = $request->description_edit;
        $data->save();

        return response()->json(['success'=>'Data added successfully','data'=>$data]);
      }
    }
    // END Kurus Uji Kemampuan

}
