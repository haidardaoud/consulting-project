<?php

namespace App\Http\Controllers;

use App\Models\consult;
use App\Models\exp_con;
use App\Models\expert;
use App\Models\expert_time;
use App\Models\favourite;
use App\Models\product;
use App\Models\rate;
use App\Models\reserve;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use phpseclib3\Crypt\Hash;

class usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
            'name'=>'required',
            'email'=>'required|string',
            'password'=>'required',
            'phone'=>'required'
        ]);
        if ($data->fails()) {
            return Response()->json(['error' => $data->errors()]);
        }
        $request['role']='user';
        $request['bank_account']=0;
        $user = User::create([
            'name' => $request['name'],
            'password'=>\Illuminate\Support\Facades\Hash::make($request['password']),
            'email' => $request['email'],
            'role'=>$request['role'],
            'phone'=>$request['phone'],
            'bank_account'=>$request['bank_account']
        ]);
            $token = $user->createtoken('auth-token')->plainTextToken;
        return Response()->json(['user' => $user, 'token' => $token]);
    }
 public function login(Request $request){

         $user = User::query();
         if ($user->where('email', '=', $request['email'])->exists()) {
             if (!Auth::attempt($request->only('email', 'password'))) {
                 return Response()->json(['error' => 'invalid data']);
             } else {
                 $user = User::where('email', $request['email'])->firstorfail();
                 $token = $user->createtoken('auth-token')->plainTextToken;
                 return Response()->json(['user' => $user, 'token' => $token]);
             }
         } else {
             $expert = expert::query();
             if ($expert->where('email', '=', $request['email'])->exists()) {
                 if (!Auth::guard('expert')->attempt($request->only('email', 'password'))) {
                     return Response()->json(['error' => 'invalid data']);
                 } else {
                     $expert = expert::where('email', $request['email'])->firstorfail();
                     $token = $expert->createtoken('auth-token')->plainTextToken;
                     return Response()->json(['expert' => $expert, 'token' => $token]);
                 }
             }
         }
     }



    public function logout(){
        $token=auth()->user()->tokens();
        $token->delete();
        return Response()->json(['message'=>'logged out successfully']);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($type){
        $data=expert::join('expert-con','expert-con.expert_id','expert.id')
            ->join('consult', 'consult.id','=','expert-con.consult_id')->select('expert.*')->select('name')
            ->where('type','like',$type)->get();
        return  response()->json(["data"=>$data],200);
    }
     public function search(Request $request)
     {
         $expert = expert::query();
         $consult = consult::query();
         $name = $request['name'];
         if ($name != null) {
             if ($expert->where('name', 'like', '%' . $name . '%') && $consult->where('type', 'like', '%' . $name . '%')->exists()) {

                 return response()->json(['expert'=>$expert->get('name'), 'consult' => $consult->get('type')]);
             }
          elseif ($consult->where('type','like', '%'.$name . '%')->exists()) {
             return Response()->json(['consult'=>$consult->get('type')]);
         }
             elseif ($expert->where('name', 'like', '%' . $name . '%')->exists()) {
                 return Response()->json(['expert' => $expert->get('name')]);
             }
     }
         else
         {
             return Response()->json(['message'=>'not found']);
         }
     }
     public function reserve(Request $request,$id)
     {
         $data=validator::make($request->all(),[
            'day'=>'required',
            'hour'=>'required',
         ]);
         if ($data->fails()){
             return response()->json(['message'=>$data->errors()]);
         }
         $user=User::find(Auth::user()->id);
         $expert=expert::find($id);
         if ($user->bank_account==0){
             return \response()->json(['message'=>'cannot reserve charge first']);
         }
         $time=expert_time::query()->where('expert_id','=',$id)->get();
         $reserve=reserve::query()->where('expert_id','=',$id)->where('day','=',$request['day'])->get();
         if ($reserve->isEmpty()){
             foreach ($time as $array){
                 if ($array->day==$request['day']&&$array->start_time <=$request['hour'] &&$array->end_time>$request['hour']) {
                     $hour=$request['hour'];
                     $timeE=Carbon::parse($hour);
                     $timeL=Carbon::parse($hour);
                     $request['end_session']=$timeE->addMinute(60);
                     $request['last_session']=$timeL->subMinute(60);
                     $user->bank_account=$user->bank_account-$expert->seprice;
                     $expert->bank_account=$expert->bank_account+$expert->seprice;
                     $user->save();
                     $expert->save();
                     $reserve = reserve::create([
                         'day' => $request['day'],
                         'hour' => $request['hour'],
                         'end_session' => $request['end_session'],
                         'last_session'=>$request['last_session'],
                         'user_id' => Auth::user()->id,
                         'expert_id' => $id,
                     ]);
                     return Response()->json(['message' => 'reserved']);
                 }
                     return Response()->json(['message'=>'there is no working in this time']);
             }
         }
         else{

             if ($user->bank_account==0){
                 return response()->json(['message'=>'cannot reserve charge first']);
             }
             foreach ($reserve as $E) {
                 $h=$E->end_session < $request['hour'] || $request['hour'] <= $E->last_session;
    if($h==false)
    {
        return \response()->json(['mesage'=>'there is reserve at this hour']);
    }
        foreach ($time as $array) {
                         if ($array->day==$request['day']&&$array->start_time <=$request['hour'] &&$array->end_time>$request['hour']) {
                                $hour=$request['hour'];
                             $timeE=Carbon::parse($hour);
                             $timeL=Carbon::parse($hour);
                             $request['end_session']=$timeE->addMinute(60);
                             $request['last_session']=$timeL->subMinute(60);
                             $user->bank_account=$user->bank_account-$expert->seprice;
                             $expert->bank_account=$expert->bank_account+$expert->seprice;
                             $user->save();
                              $expert->save();
                             $reserve = reserve::create([
                                 'day' => $request['day'],
                                 'hour' => $request['hour'],
                                 'end_session' => $request['end_session'],
                                 'last_session'=>$request['last_session'],
                                 'user_id' => Auth::user()->id,
                                 'expert_id'=>$id,
                             ]);
                             return Response()->json(['message' => 'reserved']);
                         }
                         return Response()->json(['message' => 'there is no working in this time']);
                     }
                 }

                 return Response()->json(['message' => 'there is reserve in this time']);
             }
         }

     //.................................
     public function addrate(Request $request,$expert_id)
     {
        $data=validator::make($request->all(),[
            'num'=>'required|integer|min:1|max:5'
        ]);
         if ($data->fails()){
             return response()->json(['message'=>$data->errors()]);
         }
         $added=Rate::query();
         if (!$added->where('user_id','=',Auth::user()->id)->exists()){
             if (!$added->where('expert_id','=',$expert_id)->exists()){
         $rate= rate::create([
             'expert_id'=>$expert_id,
             'user_id'=>Auth::user()->id,
             'num'=>$request['num']
         ]);}}
         else{
             $rate=Rate::query()->where('user_id','=',Auth::user()->id);
             $rate->update($request->only('num'));
         }
         return response()->json(['added']);
     }
      public function all_rate(Request $request,$expert_id)
      {
        $Ratenum=Rate::where('expert_id','=',$expert_id)->sum('num');
        $count=Rate::where('expert_id','=',$expert_id)->count();
        $rate=$Ratenum/$count;
        return response()->json(['rate'=>$rate]);
}
    public function favourites(Request $request,$expert_id){
        $expert=expert::find($expert_id);
        if (!$expert){
            return Response()->json(['message','expert does not exist']);
        }
        if(favourite::query()->where('expert_id','=',$expert_id)->where('user_id','=',auth::user()->id)->exists()) {
            return Response()->json(['message', 'favourite for expert already added']);
        }
        else{
            $favourite=favourite::create(['expert_id'=>$expert_id,
                'user_id'=>auth()->user()->id
            ]);
            return Response()->json(['favourite'=>$favourite]);
        }}
    public function allfavourite(Request $request): \Illuminate\Http\JsonResponse
    {
        $data=expert::join('favourite','expert_id','=','favourite.expert_id')->where('favourite.user_id','=',Auth::user()->id)->get();
        return response()->json(['expert'=>$data]);
    }
public function charge(Request $request){
        $data=validator::make($request->all(),[
            'charge'=>'required'
        ]);
    if ($data->fails()){
        return response()->json(['message'=>$data->errors()]);
    }

    $user=User::find(Auth::user()->id);
        $user->bank_account=$user->bank_account+$request['charge'];
        $user->save();
        return \response()->json(['message'=>'added']);

}

    public function getreserve($id){
        $reserve=reserve::query();
        if ($reserve->where('expert_id','=',$id)){
            return response()->json(['reserve'=>$reserve->get()]);
        }
        else{
            return response()->json(['message'=>'there is no reserve']);
        }
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
            'phone' => 'required|string'
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

    }
}
