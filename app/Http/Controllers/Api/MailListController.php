<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mail;
use App\Models\MailList;

class MailListController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => MailList::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mail_list_textarea' => 'required',
        ]);

        $mail_list =  MailList::create([
            'name' => $request->name,
        ]);

        $this->mailCreateOrUpdate($request, $mail_list->id);

        return response()->json([
            'success' => true,
            'data' => "Saved",
        ]);
    }

    public function show(MailList $mailList)
    {
        return response()->json([
            'success' => true,
            'mailLists' => $mailList,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'mail_list_textarea' => 'required',
        ]);

        $mail_list = MailList::where('id' , $id)
        ->update([
                'name' => $request->name,
        ]);

        $this->mailCreateOrUpdate($request, $id);

        return response()->json([
            'success' => true,
            'data' => "Update",
        ]);
    }

    public function destroy(MailList $mail_list,string $id)
    {
        MailList::where("id",$id)->delete();
        return response()->json([
            'success' => true,
            'data' => 'Successfuly deleted',
        ]);
    }

    function mailCreateOrUpdate($request, $mail_list_id) {
        $text = trim($request->mail_list_textarea);
        $textAr = array_map('trim', explode("\r\n", $text));

        $existingMails = Mail::where('mail_list_id', $mail_list_id)->whereIn('mail', $textAr)->get()->pluck('mail')->toArray();
        Mail::where('mail_list_id', $mail_list_id)->whereNotIn('mail', $textAr)->delete();

        foreach ($textAr as $line) {
            $email = $this->email_control(trim($line));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if(!in_array($line, $existingMails)) {
                    Mail::create(
                        [
                            'mail' => $line,
                            'mail_list_id' => $mail_list_id,
                    ]);
                }
            }
        }
    }

    function email_control($email) {
        $email = trim($email);
        $email = stripslashes($email);
        $email = htmlspecialchars($email);
        return $email;
    }
}
