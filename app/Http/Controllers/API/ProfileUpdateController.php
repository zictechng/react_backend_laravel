<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryTransaction;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileUpdateController extends Controller
{
    //

    //fetch user details here
    public function fetchUsers()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            $userDetails = auth('sanctum')->user();

            $userProfile = User::where('id', $userDetails->id)->first();
            if ($userProfile) {
                return response()->json([
                    'status' => 200,
                    'profile_details' => $userProfile,
                ]);
            } else {
                return response()->json([
                    'status' => 422,
                    'message' => 'No profile details found',
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }


    //update user profile image here

    public function updateUserProfileImage(Request $request)
    {
        if (auth('sanctum')->check()) {
            // validate user input request
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    $errors = $validator->errors(),
                    // validate error message here
                    'status' => 422,
                    'errors' => $errors,
                ]);
            } else {
                $myDetails = auth('sanctum')->user();
                $get_userProfile = User::where('id', $myDetails->id)->first();

                /* this check if there is an image the uploade or do not process */
                if ($request->hasFile('image')) {
                    /* check if the previous image exist then delete before uplaoding new one */
                    $path = $get_userProfile->photo; // this image colunm already have the image path in the database
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    /* image deleting ends here --*/

                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('uploads/profile_image/', $filename);
                    $myphoto = 'uploads/profile_image/' . $filename;

                    $get_userProfile->update([
                        'photo' => $myphoto,

                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Profile updated successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 403,
                        'message' => 'Error occurred why updating profile image',
                    ]);
                }
                /* ends here */
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    // update user profile
    public function updateUserProfile(Request $request)
    {
        if (auth('sanctum')->check()) {
            // validate user input request
            $validator = Validator::make($request->all(), [
                'email' => 'required|max:191|email',
                'username' => 'required|max:191',
                'sex' => 'required|max:191',
                'state' => 'required|max:191',
                'location' => 'required|max:191',
                'address' => 'required|max:191',
                'dob' => 'required|max:191',
                'occupation' => 'required|max:191',
                'married_status' => 'required|max:191',
                'acctno' => 'required|max:10',
                'acct_name' => 'required|max:191',
                'bankname' => 'required|max:191',


            ]);
            if ($validator->fails()) {
                return response()->json([
                    $errors = $validator->errors(),
                    // validate error message here
                    'status' => 422,
                    'errors' => $errors,
                ]);
            } else {
                $myDetails = auth('sanctum')->user();
                $get_userProfile = User::where('id', $myDetails->id)->first();
                if ($get_userProfile) {
                    $get_userProfile->update([
                        'email' => $request['email'],
                        'username' => $request['username'],
                        'sex' => $request['email'],
                        'state' => $request['state'],
                        'location' => $request['location'],
                        'address' => $request['address'],
                        'dob' => $request['dob'],
                        'occupation' => $request['occupation'],
                        'married_status' => $request['married_status'],
                        'acctno' => $request['acctno'],
                        'acct_name' => $request['acct_name'],
                        'bankname' => $request['bankname'],

                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Profile updated successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 402,
                        'message' => 'Sorry, profile not updated',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    // save setting here
    public function saveSetting(Request $request)
    {


        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        // ends

        // check if user is login
        if (auth('sanctum')->check()) {

            $userDetails = auth('sanctum')->user();
            // check if this user have save any setting before
            $checkSetting = Setting::where('user_id', $userDetails->id)->first();
            // get all the values from the post request
            $topup_alert = $request['topup_email'];
            $credit_alert = $request['debit_email'];
            $debit_alert = $request['login_email'];
            $promo_alert = $request['credit_email'];
            $login_alert = $request['promo_email'];
            $system_alert = $request['system_update'];


            // if the checking is empty, create new record otherwise, update
            if (empty($checkSetting)) {
                $setting = new Setting();
                $setting->user_id = $userDetails->id;
                $setting->topup_email = $topup_alert;
                $setting->debit_email = $debit_alert;
                $setting->login_email = $login_alert;
                $setting->credit_email = $credit_alert;
                $setting->system_update = $system_alert;
                $setting->promo_email = $promo_alert;

                $setting->system_status = "Active";

                $setting->save();
                if ($setting->save()) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Setting saved successfully.',
                    ]);
                } else {
                    return response()->json([
                        'status' => 501,
                        'message' => 'Setting not save! Try again.',
                    ]);
                }
            } else if (!empty($checkSetting)) {
                $checkSetting->update([
                    'topup_email' => $topup_alert,
                    'debit_email' => $debit_alert,
                    'login_email' => $login_alert,
                    'credit_email' => $credit_alert,
                    'system_update' => $system_alert,
                    'promo_email' => $promo_alert,

                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Setting updated successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 501,
                    'message' => 'Setting not updated! Try again.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }


    public function saveSetting2(Request $request)
    {
        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        // ends

        // check if user is login
        if (auth('sanctum')->check()) {

            $userDetails = auth('sanctum')->user();
            // check if this user have save any setting before
            $checkSetting = Setting::where('user_id', $userDetails->id)->first();
            // get all the values from the post request

            $fa2_alert = $request['fa2_email'];
            $otp_alert = $request['otp_email'];


            // if the checking is empty, create new record otherwise, update
            if (empty($checkSetting)) {
                $setting = new Setting();
                $setting->user_id = $userDetails->id;
                $setting->otp_email = $otp_alert;
                $setting->fa2_email = $fa2_alert;

                $setting->save();
                if ($setting->save()) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Setting saved successfully.',
                    ]);
                } else {
                    return response()->json([
                        'status' => 501,
                        'message' => 'Setting not save! Try again.',
                    ]);
                }
            } else if (!empty($checkSetting)) {
                $checkSetting->update([
                    'fa2_email' => $fa2_alert,
                    'otp_email' => $otp_alert,
                ]);
                return response()->json([
                    'status' => 200,
                    'message' => 'Setting updated successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 501,
                    'message' => 'Setting not updated! Try again.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    public function fetchSetting()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            // get sender details
            $user_details = auth('sanctum')->user();
            $mySetting = Setting::where('user_id', $user_details->id)->first();

            if ($mySetting) {
                return response()->json([
                    'status' => 200,
                    'my_setting' => $mySetting,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Record not found',
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    // update user password here

    public function updateUserPassword(Request $request)
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            // validate user input request
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8',

                'password_confirmation' => 'required|same:new_password|min:8',
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
                $user_details = auth('sanctum')->user();
                $myPasswordDetails = User::where('id', $user_details->id)->first();

                if ($myPasswordDetails) {

                    $myPasswordDetails->update([
                        'password' => Hash::make($request->new_password),
                    ]);

                    return response()->json([
                        'status' => 200,
                        'message' => 'Password updated successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 501,
                        'message' => 'Operation failed, try again',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }

    //send ticketing message here

    public function submitTicket(Request $request)
    {
        /* Generate unique transaction ID for each cash request record */
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $tid = substr(str_shuffle($permitted_chars), 0, 16);
        // ends

        // check if user is login
        if (auth('sanctum')->check()) {

            // validate user input request
            $validator = Validator::make($request->all(), [
                'subject' => 'required|max:191',
                'title' => 'required|max:191',
                'message' => 'required',
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
                $mypro = auth('sanctum')->user();
                $uDetails = User::where('id', $mypro->id)->first();
                $ticket = new Ticket();
                $ticket->uid = $mypro->id;
                $ticket->u_email = $mypro->email;
                $ticket->u_name = $mypro->name;
                $ticket->t_title = $request['title'];
                $ticket->t_subject = $request['subject'];
                $ticket->t_message = $request['message'];
                $ticket->t_tid = $tid;
                $ticket->t_status = "Open Ticket";
                $ticket->t_action = "In-Progress";
                $saved = $ticket->save();
                if ($saved) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'Ticket submitted successfully',
                    ]);
                } else {
                    return response()->json([
                        'status' => 501,
                        'message' => 'Operation failed, Try again',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => 'Please, login to continue',
            ]);
        }
    }


    // fetch user details into context hooks

    public function getUser($email)
    {

        $user = User::where('email', $email)->where('acct_status', 'Active')->first();
        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'Missing parameter in your account',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                // 'validator_err' => $validator->messages(),
                'userDetails' => $user,
            ]);
        }
    }

    public function getLogin()
    {
        // check if user is login
        if (auth('sanctum')->check()) {

            $user_login = auth('sanctum')->user();
            if ($user_login) {
                return response()->json([
                    'status' => 200,

                    'message' => 'Authenticated',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'No Access Granted',
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,

                'message' => 'Unauthorized, Access Denied',
            ]);
        }
    }

    public function search(Request $request)
    {
        $key = $request->key;
        $search = HistoryTransaction::where('action_nature', 'LIKE', '%' . $key . '%')
            ->orWhere('send_amt', 'LIKE', '$' . $key . '%')
            ->get();
        return $search;
    }

    public function delete($id)
    {
        $history = HistoryTransaction::find($id);
        if ($history) {
            $history->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Record deleted successfully',
            ]);
        }
        if (!$history) {
            return response()->json([
                'status' => 404,
                'message' => 'Operation failed! Try again',
            ]);
        } else {
            return response()->json([
                'status' => 500,

                'message' => 'Try again',
            ]);
        }
    }
}