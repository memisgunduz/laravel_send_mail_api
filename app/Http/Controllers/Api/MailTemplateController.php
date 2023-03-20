<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailTemplate;

class MailTemplateController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => MailTemplate::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'html' => 'required',
            'file' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if($request->file){
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('FILES', $fileName, '');
        }

        MailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'html' => $request->html,
            'attachment' => $filePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => "Saved",
        ]);
    }

    public function show(MailTemplate $mailTemplate)
    {
        return response()->json([
            'success' => true,
            'mailLists' => $mailTemplate,
        ]);
    }

    public function update(Request $request, string $template_id)
    {
        MailTemplate::where('id', $template_id)
        ->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'html' => $request->html,
            'attachment' => $filePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => "Updated",
        ]);
    }

    public function destroy(MailTemplate $mailTemplate)
    {
        $mailTemplate->delete();
        return response()->json([
            'success' => true,
            'data' => 'Successfuly deleted',
        ]);
    }
}
