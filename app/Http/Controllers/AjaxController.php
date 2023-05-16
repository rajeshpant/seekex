<?php

namespace App\Http\Controllers;
use DB; 
use Illuminate\Http\Request;
use App\Models\Ball;
use App\Models\Bucket;
use Response;
use Session;
class AjaxController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index(){
        //$buckets = Bucket::all();
        return view('home');//->with(compact('buckets'));
    }
    public function show(Request $request) {
        $results = [];
        if($request->type =='bucket'){
            $results = Bucket::all(); 
        } else if($request->type =='ball'){
            $results = Ball::all(); 
        } else {
            $results = $this->get_ball_bucket();
        } 
        return Response::json($results);
    }
    public function update(Request $request){
        if($request->type =='bucket'){
            $request->validate([
                'name' => 'required',
                'volume' => 'required',
            ]);
            Bucket::create($request->all()); 
        } else if($request->type =='ball') {
            $request->validate([
                'name' => 'required',
                'volume' => 'required',
            ]);
            Ball::create($request->all()); 
        } else {
            $request = $this->fill_bucket($request->ball_id, $request->ball_q, $request->ball_volume);
        } 
        return Response::json($request);
    }
    public function fill_bucket($ball_id, $ball_q, $ball_volume){
        $sessionId = Session::getId();
        $data = Bucket::leftJoin('ball_buckets', function($q) {
                    $q->on('ball_buckets.bucket_id', '=', 'buckets.id');
                    $q->where('ball_buckets.session_id', '=', Session::getId());
                })
                ->select('ball_buckets.no_of_ball', 'ball_buckets.ball_id', 'buckets.id', 'buckets.name', 'buckets.volume', 'ball_buckets.id as bid', 'ball_buckets.volume as bvalume')
                ->get();
        $bucket_arr = [];
        foreach($data as $key => $value){
            $bucket_arr[$value->id]['empty_volume'] = isset($bucket_arr[$value->id]['empty_volume']) ? bcsub($bucket_arr[$value->id]['empty_volume'], $value->bvalume) : bcsub($value->volume, $value->bvalume)  ;
            $bucket_arr[$value->id][$value->ball_id]['total_ball'] = isset($bucket_arr[$value->id]['total_ball']) ? bcadd($bucket_arr[$value->id]['total_ball'], $value->no_of_ball) : $value->no_of_ball  ;
            $bucket_arr[$value->id][$value->ball_id]['ball_id'] = $value->ball_id;
            $bucket_arr[$value->id][$value->ball_id]['ball_basket_id'] = $value->bid;
            $bucket_arr[$value->id][$value->ball_id]['volume'] = $value->bvalume;
        }
        $is_empty = 1 ;
        foreach($bucket_arr as $buck_id => $value){
            $results = [];
            $empty_volume = $value['empty_volume'];
            foreach($ball_id as $key=> $id){
                $used_volume  = $total_ball_volume = bcmul($ball_q[$key], $ball_volume[$key], 2);
                $used_ball = $ball_q[$key];
                if($total_ball_volume <= $empty_volume){
                    $empty_volume= bcsub($empty_volume, $total_ball_volume);
                   // $total_ball =  bcadd($value['total_ball'], $ball_q[$key]);
                    $used_volume = bcadd($total_ball_volume, (!empty($value[$id]['volume']) ? $value[$id]['volume']:0 ));
                    $used_ball = bcadd($used_ball, (!empty($value[$id]['total_ball']) ? $value[$id]['total_ball']:0 ));
                    unset($ball_id[$key]);
                    $is_empty = 0;
                } else if($empty_volume>0) {
                    $used_ball = bcdiv($empty_volume, $ball_volume[$key]);
                    $ball_q[$key] = bcsub($ball_q[$key], $used_ball);
                    $used_volume  =bcmul($ball_volume[$key], $used_ball, 2);
                    $empty_volume= bcsub($empty_volume, $used_volume);
                   // $total_ball =  bcadd($value['total_ball'], $used_ball);
                    $is_empty = 0 ;
                    $used_volume = bcadd($used_volume, (!empty($value[$id]['volume']) ? $value[$id]['volume']:0 ));
                    $used_ball = bcadd($used_ball, (!empty($value[$id]['total_ball']) ? $value[$id]['total_ball']:0 ));
                } else {
                    break;
                }
                if(!empty($value[$id]['ball_basket_id'])) {
                    DB::table('ball_buckets')->where('id', $value[$id]['ball_basket_id'])->update(['volume' => $used_volume, 'no_of_ball' => $used_ball]);
                } else {
                    DB::insert('insert into ball_buckets (bucket_id, ball_id, no_of_ball, volume, session_id, created_at, updated_at) values (?, ?, ?, ?, ? , now(), now())', [$buck_id, $id, $used_ball, $used_volume, $sessionId]);
                }
            }
        }
        return $is_empty;
    }

    public function get_ball_bucket(){
        return DB::table('buckets')
            ->select('ball_buckets.no_of_ball', 'ball_buckets.ball_id', 'buckets.id', 'buckets.name', 'buckets.volume', 'ball_buckets.id as bid', 'ball_buckets.volume as bvalume', 'balls.name as ball_name')
            ->leftJoin('ball_buckets', function($q) {
                    $q->on('ball_buckets.bucket_id', '=', 'buckets.id');
                    $q->where('ball_buckets.session_id', '=', Session::getId());
                })
            ->leftJoin('balls','balls.id','=','ball_buckets.ball_id')
            ->get();
        } 
}
