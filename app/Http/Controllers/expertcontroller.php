<?php

namespace App\Http\Controllers;

use App\Models\exp_con;
use App\Models\expert;
use App\Models\expert_time;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class expertcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data=validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required|string',
           'expert_details'=>'required',
           'address'=>'required',
            'consult_id'=>'required',
            'seprice'=>'required'
        ]);
        if ($data->fails()) {
            return Response()->json(['error' => $data->errors()]);
        }
            //$uploadfolder='expert_image';
           // $image=$request->file('C:\xampp\htdocs\coproject\storage\app\image');
       // $name=time().'.jpg';
            //$path='storage/'.$uploadfolder.'/'.$name;
            //Storage::disk('public')->putFileAs($uploadfolder,$image,$name);
        if($request['image'])
        {
        if($request->hasFile('image')){
            $filenameWithExt=$request->file('image')->getClientOriginalName();
            $filename=pathinfo($filenameWithExt,PATHINFO_FILENAME);
            $extension=$request->file('image')->getClientOriginalExtension();
            $filenameToStore =$filename.'_'.time().'_'.$extension;
            $path =$request->file('image')->storeAs('image',$filenameToStore);
            $request['role']='expert';
            $request['bank_account']=0;
            $expert=expert::create([
                'name' => $request['name'],
                'password'=>\Illuminate\Support\Facades\Hash::make($request['password']),
                'email' => $request['email'],
                'role'=>$request['role'],
                'bank_account'=>$request['bank_account'],
                'image'=>URL::asset('storage'.$path),
                'expert_details'=>$request['expert_details'],
                'phone'=>$request['phone'],
                'address'=>$request['address'],
                'seprice'=>$request['seprice']
            ]);
            $found=expert::query()->where('email','=',$request['email'])->first();
              $consult=exp_con::create([
                 'consult_id'=>$request['consult_id'],
                 'expert_id'=>$found->id,
              ]);
            $token = $expert->createtoken('auth-token')->plainTextToken;
            return Response()->json(['expert' => $expert, 'token' => $token]);
        }
        }
        else{
            $request['role']='expert';
            $request['bank_account']=0;
            $expert=expert::create([
                'name' => $request['name'],
                'password'=>\Illuminate\Support\Facades\Hash::make($request['password']),
                'email' => $request['email'],
                'role'=>$request['role'],
                'bank_account'=>$request['bank_account'],
                'expert_details'=>$request['expert_details'],
                'phone'=>$request['phone'],
                'address'=>$request['address'],
                'seprice'=>$request['seprice']
            ]);
            $found=expert::query()->where('email','=',$request['email'])->first();
            $consult=exp_con::create([
                'consult_id'=>$request['consult_id'],
                'expert_id'=>$found->id,
            ]);
            $token = $expert->createtoken('auth-token')->plainTextToken;
            return Response()->json(['expert' => $expert, 'token' => $token]);
        }
        }

    public function expert_time(Request $request){
        $data=validator::make($request->all(),[
            'day'=>'required',
            'start_time'=>'required',
            'end_time'=>'required',
        ]);
        if ($data->fails()){
            return response()->json(['error'=>$data->errors()]);
        }
        $expert_time=expert_time::create([
            'expert_id'=>Auth::user()->id,
            'day'=>$request['day'],
            'start_time'=>$request['start_time'],
            'end_time'=>$request['end_time'],
        ]);
        return Response()->json(['expert time'=>$expert_time]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        $expert=expert::find($id);
        $image=expert::query();
        if (!$image){
            $data=expert::join('expert-con','expert-con.expert_id','expert.id')
                ->join('consult', 'consult.id','=','expert-con.consult_id')->select('consult.*')->select('type')
                ->where('expert_id','like',$id)->get();
            return Response()->json(['expert'=>$expert->only('name','phone','address'),'consult'=>$data]);
        }
        $data=expert::join('expert-con','expert-con.expert_id','expert.id')
            ->join('consult', 'consult.id','=','expert-con.consult_id')->select('consult.*')->select('type')
            ->where('expert_id','like',$id)->get();
           return Response()->json(['expert'=>$expert->only('name','phone','address','image'),'consult'=>$data]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
            'phone'=>'required|string',
            'address'=>'required',
            'expert_details'=>'required',

        ]);
        if ($data->fails()) {

            return Response()->json(['error' => $data->errors()]);
        }
        $user=User::find(Auth::user()->id);
        $user->update($request->all());
        $user->save();
        return Response()->json(['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
