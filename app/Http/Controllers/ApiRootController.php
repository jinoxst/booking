<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class ApiRootController extends Controller
{
    protected $RES_JSON = array('code' => 'OK', 'message' => '');

    protected function jsonValidator(array $data, array $rules){
        $validator = Validator::make($data, $rules);
        // Log::info('*** data:'.var_export($data, true));
        // Log::info('*** rules:'.var_export($rules, true));
        if($validator->fails()){
            $errors = $validator->errors();
            $errMsg = '';
            foreach ($errors->all() as $message) {
                Log::error($message);
                $errMsg .= $message . '\n';
            }
            $this->RES_JSON = array('code' => 'NG', 'message' => $errMsg);
        }else{
            $cnt = DB::table('shops')->where('acc_cd', $data['acc_cd'])->count();
            if($cnt == 0){
                $this->RES_JSON = array('code' => 'NG', 'message' => '店舗番号が確認出来ませんでした。');
            }else{
                try{
                    $decrypt_cypher_code = decrypt($data['cypher_code']);
                    if($decrypt_cypher_code != config('constants.cypher_pass')){
                        $this->RES_JSON = array('code' => 'NG', 'message' => 'cypher_code値を確認した下さい。');
                    }
                }catch(DecryptException $e){
                    Log::error('msg:('.$e->getMessage().'), code:('.$e->getCode().'), file:('.$e->getFile().':'.$e->getLine().')');
                    $this->RES_JSON = array('code' => 'NG', 'message' => 'cypher_code値を確認した下さい。');
                }
            }
        }
    }

    protected function writeErrorMessage(\Exception $e){
        Log::error('msg:('.$e->getMessage().'), code:('.$e->getCode().'), file:('.$e->getFile().':'.$e->getLine().')');
        Log::error($e->getTraceAsString());
        $this->RES_JSON = array('code' => 'NG', 'message' => $e->getMessage());
    }
}