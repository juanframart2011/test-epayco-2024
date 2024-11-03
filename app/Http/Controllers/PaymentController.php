<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Mail\PaymentMail;

use App\Models\Payment;
use App\Models\SessionModel;
use App\Models\Token;
use App\Models\User;
use App\Models\Wallet;

use Illuminate\Support\Str;
use Validator;

use SoapFault;

class PaymentController extends Controller
{
    public function confirmation( Request $request, $url ){

        $messages = [
            'token.required' => 'El token es obligatorio',
        ];

        $validate = Validator::make( $request->all(), [
            'token' => 'required',
        ], $messages );

        if( $validate->fails() ){

            $errors = $validate->errors()->all();
                
            return response()->json(['success' => false, 'cod_error' => 'campos obligatorios','message_error' => $errors], 200);
        }
        else{
            try{
                
                DB::beginTransaction();

                $paymentDetail = Token::Where([
                    "name" => $request->get( "token" ),
                    "token_statu_id" => 1
                ])->get();

                if( count( $paymentDetail ) == 0 ){

                    DB::rollback();
                    return response()->json(['success' => false, 'cod_error' => 'error de confirmación','message_error' => 'El token no existe'], 200);
                }

                $amount = $paymentDetail[0]->payment->wallet->amount - $paymentDetail[0]->payment->amount;

                $walletUpdate = Wallet::where( "id", $paymentDetail[0]->payment->wallet->id )->Update([
                   "amount" => $amount
                ]);

                if( !$walletUpdate ){

                    DB::rollback();
                    return response()->json(['success' => false, 'cod_error' => 'error al confirmar compra','message_error' => 'Ocurrio un error en el sistema, intentalo nuevamente'], 200);
                }

                Token::Where([
                    "id" => $paymentDetail[0]->id
                ])->update([
                    "token_statu_id" => 3
                ]);

                Payment::Where([
                    "id" => $paymentDetail[0]->payment->id
                ])->update([
                    "payment_statu_id" => 3
                ]);

                DB::commit();

                return response()->json(['success' => true, 'cod_error' => 00,'message_error' => 'Se confirmo la compra'], 200);
            }
            catch( Throwable $e ){

                return response()->json(['success' => false, 'cod_error' => 'error al confirmar compra','message_error' => $e->getMessage()], 200);
            }
        }
    }

    public function pay( Request $request ){

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

                if( $request->get( "amount" ) > $userDetail[0]->wallet->amount ){

                    DB::rollback();
                    return response()->json(['success' => false, 'cod_error' => 'Saldo','message_error' => 'Su saldo es insuficiente para la compra'], 200);
                }

                $token = Str::random(6);
                
                $tokenNew = new Token;
                $tokenNew->name = $token;
                $tokenNew->save();

                if( $tokenNew ){

                    $amount = $request->get( "amount" );

                    $paymentNew = new Payment;
                    $paymentNew->token_id = $tokenNew->id;
                    $paymentNew->amount = $amount;
                    $paymentNew->wallet_id = $userDetail[0]->wallet->id;
                    $paymentNew->save();

                    if( !$paymentNew ){

                        DB::rollback();
                        return response()->json(['success' => false, 'cod_error' => 'Pago','message_error' => 'Error al crear el pago'], 200);
                    }

                    $sessionNew = new SessionModel;
                    $sessionNew->user_id = $userDetail[0]->id;
                    $sessionNew->payload = md5( $token );
                    $sessionNew->save();

                    $paymentData = (object) array(
                        'nombre' => $userDetail[0]->name,
                        'asunto' => $request->get( "asunto" ),
                        'email' => $userDetail[0]->email,
                        'phone' => $userDetail[0]->phone,
                        'amount' => $amount,
                        'token' => $token,
                        'session' => env( "APP_URL" ) . '/conformation-pay/' . $sessionNew->id
                    );

                    Mail::to( $userDetail[0]->email )->send( new PaymentMail( $paymentData ) );

                    DB::commit();

                    return response()->json(['success' => true, 'cod_error' => 00,'message_error' => 'Se envío un correo para confirmar la compra'], 200);
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