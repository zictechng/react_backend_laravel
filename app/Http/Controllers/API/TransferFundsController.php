<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryTransaction;
use App\Models\TransferFunds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransferFundsController extends Controller
{
    //

    public function transferFunds(Request $request)
    {
        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        // ends

        // check if user is login
        if (auth('sanctum')->check()) {

            $validator = Validator::make($request->all(), [
                'amount_cash' => 'required|max:191',
                'receiver_email' => 'required|max:191|email',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    $errors = $validator->errors(),
                    'status' => 422,
                    'errors' => $errors,
                ]);
            } else {
                // get sender details
                $sender_details = auth('sanctum')->user();

                $receiverEmail = $request['receiver_email'];
                $receiver_details = User::where('email', $receiverEmail)->where('acct_status', 'Active')->first();

                $senderp_details = User::where('id', $sender_details->id)->where('acct_status', 'Active')->first();

                $amt_send = $request['amount_cash'];

                $user_balance = $senderp_details->gamount;
                // check if sender have enough balance
                if ($user_balance < $amt_send) {
                    return response()->json([
                        'status' => 403,
                        'message' => 'Failed! Account Balance is Low',
                    ]);
                }
                // check if receiver email exist
                if (empty($receiver_details)) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Failed! Receiver Email ID Not Found',
                    ]);
                } elseif (!empty($receiver_details)) {
                    $transfer = new TransferFunds();
                    $transfer->user_id = $sender_details->id;
                    $transfer->receiver_id = $receiver_details->id;
                    $transfer->sender_email = $sender_details->email;
                    $transfer->sender_name = $sender_details->name;
                    $transfer->receiver_email = $receiver_details->email;
                    $transfer->receiver_name = $receiver_details->name;
                    $transfer->status = 'Processing';
                    $transfer->amt_send = $amt_send;
                    $transfer->tran_code = $tid;
                    $transfer->reciever_acct_status = 'Credit';
                    $transfer->note_purpose = $request->send_note;
                    $transfer->save();

                    $sender_bal =  ($user_balance - $amt_send);
                    $receiver_bal = ($receiver_details->gamount + $amt_send);

                    if ($transfer->save()) {
                        // update sender user total balance...
                        $receiver_details->update(['gamount' => $receiver_bal]);

                        $senderp_details->update(['gamount' => $sender_bal]);

                        // create history record for sender here
                        $sender_history = new HistoryTransaction();
                        $sender_history->uid = $sender_details->id;
                        $sender_history->user_email = $sender_details->email;
                        $sender_history->status = 'Debit';
                        $sender_history->send_amt = $amt_send;
                        $sender_history->action_nature = 'Transfer';
                        $sender_history->tid_code = $tid;
                        $sender_history->save();

                        // create history record for receiver here
                        $sender_history = new HistoryTransaction();
                        $sender_history->uid = $receiver_details->id;
                        $sender_history->user_email = $receiver_details->email;
                        $sender_history->status = 'Credit';
                        $sender_history->send_amt = $amt_send;
                        $sender_history->action_nature = 'Transfer';
                        $sender_history->tid_code = $tid;
                        $sender_history->save();

                        $transfer->update(['sender_acct_status2' => 'Debit']);
                        return response()->json([
                            'status' => 200,
                            'message' => 'Transfer was successful.',
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
}