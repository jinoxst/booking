<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Rules\AttibuteRequired;

class ApiController extends ApiRootController
{
    public $bootstrap_log_only = [
        'zones_store',
        'seats_store'
    ];

    public function zones_store(Request $request)
    {
        Log::info($request);
        $acc_cd = $request->input('acc_cd');
        $cypher_code = $request->input('cypher_code');
        $zones_arr = $request->input('zones');

        if(isset($acc_cd) == false || isset($cypher_code) == false || count($zones_arr) == false){
            $this->RES_JSON = array('code' => 'NG', 'message' => 'json fileを確認して下さい。');
        }

        if($this->RES_JSON['code'] == 'OK'){
            //validation for zones json object - START
            $data = array('acc_cd' => $acc_cd, 'cypher_code' => $cypher_code);
            $rules = array('acc_cd'=>'required|numeric', 'cypher_code' => 'required');
            for($i=0;$i<count($zones_arr);$i++){
                $zones = $zones_arr[$i];
                $data['id'.$i] = isset($zones['id']) ? $zones['id'] : null;
                $data['name'.$i] = isset($zones['name']) ? $zones['name'] : null;
                $data['image'.$i] = isset($zones['image']) ? $zones['image'] : null;
                $rules['id'.$i] = 'required|numeric';
                $rules['name'.$i] = 'required';
                $rules['image'.$i] = 'nullable';
            }
            parent::jsonValidator($data, $rules);
            //validation for zones json object - END

            if($this->RES_JSON['code'] == 'OK'){
                $zones_for_ins = array();
                foreach($zones_arr as $zones){
                    $zones['acc_cd'] = $acc_cd;
                    array_push($zones_for_ins, $zones);
                }

                try{
                    DB::table('zones')->where('acc_cd', $acc_cd)->delete();
                    DB::table('zones')->insert($zones_for_ins);
                }catch(\Exception $e){
                    parent::writeErrorMessage($e);
                }
            }
        }

        Log::info($this->RES_JSON);
        return response()->json($this->RES_JSON);
    }

    public function seats_store(Request $request)
    {
        Log::info($request);
        $acc_cd = $request->input('acc_cd');
        $cypher_code = $request->input('cypher_code');
        $seats_arr = $request->input('seats');

        if(isset($acc_cd) == false || isset($cypher_code) == false || count($seats_arr) == false){
            $this->RES_JSON = array('code' => 'NG', 'message' => 'json fileを確認して下さい。');
        }

        if($this->RES_JSON['code'] == 'OK'){
            //validation for seats json object - START
            $data = array('acc_cd' => $acc_cd, 'cypher_code' => $cypher_code);
            $rules = array('acc_cd'=>'required|numeric', 'cypher_code' => 'required');
            for($i=0;$i<count($seats_arr);$i++){
                $zones = $seats_arr[$i];
                $data['id'.$i] = isset($zones['id']) ? $zones['id'] : null;
                $data['zones_id'.$i] = isset($zones['zones_id']) ? $zones['zones_id'] : null;
                $data['name'.$i] = isset($zones['name']) ? $zones['name'] : null;
                $data['using_yn'.$i] = isset($zones['using_yn']) ? $zones['using_yn'] : null;
                $data['childseatsgrp'.$i] = isset($zones['childseatsgrp']) ? $zones['childseatsgrp'] : null;
                $rules['id'.$i] = 'required|numeric';
                $rules['zones_id'.$i] = 'required|numeric';
                $rules['name'.$i] = 'required';
                $rules['using_yn'.$i] = 'required|numeric';
                $rules['childseatsgrp'.$i] = 'nullable';
            }
            parent::jsonValidator($data, $rules);
            //validation for seats json object - END

            if($this->RES_JSON['code'] == 'OK'){
                $seats_for_ins = array();
                $collection = collect();
                foreach($seats_arr as $seats){
                    $seats['acc_cd'] = $acc_cd;
                    array_push($seats_for_ins, $seats);

                    if($seats['childseatsgrp'] != ''){
                        if($collection->contains('zones_id', $seats['zones_id']) === false){
                            $capacity = count(explode(',',$seats['childseatsgrp']));
                            if($capacity >= 1){
                                $capacity++;
                            }
                            $collection->push([
                                'zones_id' => $seats['zones_id'], 
                                'capacity' => $capacity
                            ]);
                        }
                    }
                }

                try{
                    DB::transaction(function () use ($acc_cd, $seats_for_ins, $collection) {
                        DB::table('seats')->where('acc_cd', $acc_cd)->delete();
                        DB::table('seats')->insert($seats_for_ins);

                        foreach($collection as $zones){
                            DB::update('update zones set capacity=?, updated_at=now() where acc_cd=? and id=?', [
                                $zones['capacity'],
                                $acc_cd,
                                $zones['zones_id']
                            ]);
                        }
                    });
                }catch(\Exception $e){
                    parent::writeErrorMessage($e);
                }
            }
        }

        Log::info($this->RES_JSON);
        return response()->json($this->RES_JSON);
    }
}
