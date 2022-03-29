<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Video;
use Validator;

class VideoController extends Controller
{
    public function getData()
    {
     $data = Video::get();
     return datatables()->of($data)
        ->addColumn('action', function($row){
            $btn = '';  
            $btn = $btn.'<button title="Edit" id="btn-edit" class="btn btn-warning btn-icon"><i class="icon-pencil4"></i></button>';
            $btn = $btn.'<button title="Delete" id="" class="btn-delete btn btn-danger btn-icon ml-1"><i class="icon-trash"></i></button>';
            return $btn;
        })
        ->addColumn('video', function($row){
            $row->url_embed = $row->link;          
            $row->url_embed = str_replace('youtu.be/','www.youtube.com/embed/', $row->url_embed);
            $row->url_embed = str_replace('watch?v=','embed/', $row->url_embed);
            $video = '<iframe width="250" src="'.$row->url_embed.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            return $video;
        })
        ->rawColumns(['action','video'])
        ->addIndexColumn()
        ->make(true);
    }
    public function index()
    {
        return view('video.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('video.create');
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
        'description' => 'max:191',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }else{
         $data = Video::create(
           [
                 'name'=>request('name'),
                 'link'=>request('link'),
                 'description' => request('description'),
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
        // return view('video.edit');
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
        'description_edit' => 'max:191',
      ];

      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      } else {
        $data = Video::find($id);
        $data->name   = $request->name_edit;
        $data->link   = $request->link_edit;
        $data->description   = $request->description_edit;
        // // $data->link   = $request->link_edit;
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
        $data = Video::find($id);
        $data->delete();
    }
}
