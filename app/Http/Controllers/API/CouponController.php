<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponCodeGenerate;
use App\Models\HistoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    // save user request here

    public function saveCoupon(Request $request)
    {

        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        if (auth('sanctum')->check()) {

            $validator = Validator::make($request->all(), [
                'coupon_code' => 'required|max:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    $errors = $validator->errors(),
                    'status' => 422,
                    // 'validator_err' => $validator->messages(),
                    'errors' => $errors,
                ]);
            } else {
                $user_id = auth('sanctum')->user();
                $senderp_details = User::where('id', $user_id->id)->where('acct_status', 'Active')->first();

                $send_code = $request->coupon_code;
                $codeCoupon = CouponCodeGenerate::where('generate_code', $send_code)->where('code_status', 'Active')->first();
                if (empty($codeCoupon)) {
                    return response()->json([
                        $errors = $validator->errors(),
                        'status' => 402,
                        // 'validator_err' => $validator->messages(),
                        'message' => 'Invalid code entered! Check and try again',
                    ]);
                } else if (!empty($codeCoupon)) {
                    $coupon = new Coupon();
                    $coupon->user_id = $user_id->id;
                    $coupon->coupon_code = $send_code;
                    $coupon->code_amt = $codeCoupon->code_amount;
                    $coupon->status_code = 'Used';
                    $coupon->batch_code = $codeCoupon->partner_batch_code;
                    $coupon->save();


                    if ($coupon->save()) {
                        $user_bal =  ($senderp_details->gamount + $codeCoupon->code_amount);
                        $senderp_details->update(['gamount' => $user_bal]);

                        // create history record for user here
                        $sender_history = new HistoryTransaction();
                        $sender_history->uid = $user_id->id;
                        $sender_history->user_email = $user_id->email;
                        $sender_history->status = 'Credit';
                        $sender_history->send_amt = $codeCoupon->code_amount;
                        $sender_history->action_nature = 'Voucher Recharged';
                        $sender_history->tid_code = $tid;
                        $sender_history->save();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Code Redeemed Successfully',
                        ]);
                    } else {
                        return response()->json([
                            'status' => 500,
                            'message' => 'Error occurred! Try again',
                        ]);
                    }
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }
}