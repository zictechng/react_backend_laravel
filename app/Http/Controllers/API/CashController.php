<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CashOut;
use App\Models\HistoryTransaction;
use App\Models\TransferFunds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CashController extends Controller
{
    //

    public function cashOut(Request $request)
    {
        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        // ends

        // check if user is login
        if (auth('sanctum')->check()) {

            // validate user input request
            $validator = Validator::make($request->all(), [
                'send_amount' => 'required|max:191',

            ]);
            if ($validator->fails()) {
                return response()->json([
                    $errors = $validator->errors(),
                    // validate error message here
                    'status' => 422,
                    'errors' => $errors,
                ]);
            } else {
                // get sender details
                $sender_details = auth('sanctum')->user();
                $senderp_details = User::where('id', $sender_details->id)->where('acct_status', 'Active')->first();

                $amt_send = $request['send_amount'];

                $user_balance = $senderp_details->gamount;
                // check if sender have enough balance
                if ($user_balance < $amt_send) {
                    return response()->json([
                        'status' => 403,
                        'message' => 'Failed! Account Balance is Low',
                    ]);
                } else if ($user_balance > $amt_send) {
                    $cash_out = new CashOut();
                    $cash_out->user_id = $sender_details->id;
                    $cash_out->name = $sender_details->name;
                    $cash_out->user_email = $sender_details->email;
                    $cash_out->user_phone = $sender_details->phone;
                    $cash_out->amount_send = $amt_send;
                    $cash_out->note_send = $request['send_note'];
                    $cash_out->tid_code = $tid;
                    $cash_out->request_status = 'Processing';

                    $cash_out->save();


                    $sender_bal =  ($user_balance - $amt_send);
                    if ($cash_out->save()) {
                        // update sender user total balance...
                        $senderp_details->update(['gamount' => $sender_bal]);

                        // create history record for sender here
                        $sender_history = new HistoryTransaction();
                        $sender_history->uid = $sender_details->id;
                        $sender_history->user_email = $sender_details->email;
                        $sender_history->status = 'Debit';
                        $sender_history->send_amt = $amt_send;
                        $sender_history->action_nature = 'Cash Out';
                        $sender_history->tid_code = $tid;
                        $sender_history->save();

                        return response()->json([
                            'status' => 200,
                            'message' => 'Cash out request successful.',
                        ]);
                    } else {
                        return response()->json([
                            'status' => 501,
                            'message' => 'Operation Failed! Try again',
                        ]);
                    }

                    /* create history record for sender user */
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    // fetch user wallet details here

    public function walletDetails(Request $request)
    {

        // check if user is login
        if (auth('sanctum')->check()) {

            // get sender details
            $sender_details = auth('sanctum')->user();
            $userWallet = User::where('id', $sender_details->id)->where('acct_status', 'Active')->sum('gamount');

            return response()->json([
                'status' => 200,
                'wallet' => $userWallet,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue',
            ]);
        }
    }

    // get user transaction history details here
    public function fetchHistory()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            // get sender details
            $user_details = auth('sanctum')->user();
            //$userWallet = User::where('id', $sender_details->id)->where('acct_status', 'Active')->sum('gamount');
            $tranc_history = HistoryTransaction::where('uid', $user_details->id)->orderByDesc('id')->get();
            return response()->json([
                'status' => 200,
                'history_record' => $tranc_history,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue',
            ]);
        }
    }

    public function fetchNav()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            // get sender details
            $userdetails = auth('sanctum')->user();

            $tranchistory = HistoryTransaction::where('uid', $userdetails->id)->orderByDesc('id')->get();
            return response()->json([
                'status' => 200,
                'nav_record' => $tranchistory,
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue',
            ]);
        }
    }
    // create function to fetch user dashboard details here

    public function userTransaction()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            // get sender details
            $user_details = auth('sanctum')->user();
            // get user balance here
            $myBalance = HistoryTransaction::where('uid', $user_details->id)->where('status', 'Credit')->sum('send_amt');
            //get total transfer receive here
            $totalReceive = TransferFunds::where('receiver_id', $user_details->id)->sum('amt_send');

            //get total transfer here
            $totalTransfer = TransferFunds::where('user_id', $user_details->id)->sum('amt_send');
            //get total withdraw here
            $totalWithdraw = CashOut::where('user_id', $user_details->id)->sum('amount_send');


            return response()->json([
                'status' => 200,
                'record_data' => [
                    'total_balance' => $myBalance,
                    'total_transfer' => $totalTransfer,
                    'total_withdraw' => $totalWithdraw,
                    'total_receive' => $totalReceive,
                ]
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Login to continue',
            ]);
        }
    }
}