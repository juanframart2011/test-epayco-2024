<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Wallet;

use SoapFault;
use Validator;

class WalletController extends Controller
{
    public function balance( Request $request ){

        $messages = [
            'phone.required' => 'El Número telefonico del cliente es obligatorio',
            'document.required' => 'El documento es obligatorio',
        ];

        $validate = Validator::make( $request->all(), [
            'document' => 'required',
             'phone' => 'nullable|numeric',
        ], $messages );

        if( $validate->fails() ){

            $errors = $validate->errors()->all();
                
            return response()->json(['success' => false, 'cod_error' => 'campos obligatorios','message_error' => $errors], 200);
        }
        else{
            
            $userDetail = User::Where([
                "phone" => $request->get( "phone" ),
                "document" => $request->get( "document" )
            ])->get();

            if( count( $userDetail ) == 0 ){

                DB::rollback();
                return response()->json(['success' => false, 'cod_error' => 'usuario no existe','message_error' => 'Lo datos proporcionado no existe nadie'], 200);
            }

            return response()->json(['success' => true, 'cod_error' => 00,'message_error' => 'El saldo es: ' . $userDetail[0]->wallet->amount], 200);
        }
    }

    public function recharge( Request $request ){

        $messages = [
            'phone.required' => 'El Número telefonico del cliente es obligatorio',
            'document.required' => 'El documento es obligatorio',
            'amount.required' => 'El monto es obligatorio',
        ];

        $validate = Validator::make( $request->all(), [
            'document' => 'required',
            'amount' => 'required',
            'phone' => 'nullable|numeric',
        ], $messages );

        if( $validate->fails() ){

            $errors = $validate->errors()->all();
                
            return response()->json(['success' => false, 'cod_error' => 'campos obligatorios','message_error' => $errors], 200);
        }
        else{
            try{
                
                DB::beginTransaction();

                $userDetail = User::Where([
                    "phone" => $request->get( "phone" ),
                    "document" => $request->get( "document" )
                ])->get();

                if( count( $userDetail ) == 0 ){

                    DB::rollback();
                    return response()->json(['success' => false, 'cod_error' => 'usuario no existe','message_error' => 'Lo datos proporcionado no existe nadie'], 200);
                }

                $amount = $userDetail[0]->wallet->amount + $request->get( "amount" );

                $walletUpdate = Wallet::where( "id", $userDetail[0]->wallet->id )->Update([
                   "amount" => $amount
                ]);

                if( $walletUpdate ){

                    DB::commit();

                    return response()->json(['success' => true, 'cod_error' => 00,'message_error' => 'Recarga exitosa'], 200);
                }
                else{
                    DB::rollback();

                    return response()->json(['success' => false, 'cod_error' => 'error al crear usuario','message_error' => 'error inesperado'], 200);
                }
            }
            catch( Throwable $e ){

                return response()->json(['success' => false, 'cod_error' => 'error al crear usuario','message_error' => $e->getMessage()], 200);
            }
        }
    }
}