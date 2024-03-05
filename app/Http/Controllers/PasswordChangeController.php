<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class PasswordChangeController extends Controller
{
    public function index()
    {
        // echo 1;die;
        return view('forgot_password.index');
    }

    // public function change_password(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $new_password = $this->generateRandomString(8);
    //         // dd($new_password);
    //         // $new_password = $request->input('new_password');
    //         $whatsapp_no = $request->input('whatsapp_no');
    //         // $user_id = $request->input('user_id');
    //         $data = User::whereId(1)->update([
    //             'password' => Hash::make($new_password),
    //             'org_password' => $new_password
    //         ]);
    //         if ($data) {
    //             $whats_response =  $this->WA_forgot_password($whatsapp_no, $new_password);
    //         }
    //         // dd($whats_response);

    //         if ($whats_response) {

    //             DB::commit();
    //             return response(["success" => true, "message" => "Password Change Succesfully"]);
    //         } else {
    //             DB::rollBack();
    //             return response(["success" => false, "message" => "Password Change Failed"]);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Password updation failed: ' . $e->getMessage());
    //         return response([
    //             "success" => false,
    //             "message" => $e->getMessage(),
    //         ]);
    //     }
    // }
    public function change_password(Request $request)
    {
        try {
            DB::beginTransaction();
            $new_password = $this->generateRandomString(11);
            // dd($new_password);
            // $new_password = $request->input('new_password');
            $whatsapp_no = $request->input('whatsapp_no');
            // $validate_array = array("83412 23555","93813 46169");
            if (in_array($whatsapp_no, [8341223555, 9381346169, 6383883745])) {

                $data = User::whereId(1)->update([
                    'password' => Hash::make($new_password),
                    'org_password' => $new_password
                ]);

                if ($data) {
                    $whats_response =  $this->WA_forgot_password($whatsapp_no, $new_password);
                }
                // dd($whats_response);

                if ($whats_response) {

                    DB::commit();
                    return response(["success" => true, "message" => "Password Change Succesfully"]);
                } else {
                    DB::rollBack();
                    return response(["success" => false, "message" => "Password Change Failed"]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Password updation failed: ' . $e->getMessage());
            return response([
                "success" => false,
                "message" => $e->getMessage(),
            ]);
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+{}:"|<>?';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    public function WA_forgot_password($whatsapp_no, $new_password)
    {
        // dd($whatsapp_no);
        $data = urlencode("
*APEMCL*
					
	Your New Password Is : *" . $new_password . "* ");


        $contact_arr = array(91 . $whatsapp_no);
        foreach ($contact_arr as $key => $contact) {
            $url = "https://pingerbot.in/api/send?number=" . $contact . "&type=text&message=" . $data . "&instance_id=654C8239E65D9&access_token=654bbc7c4f749";
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HEADER => true
            ));

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
    }
}
