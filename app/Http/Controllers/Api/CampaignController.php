<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\MailList;
use App\Models\MailLog;
use App\Models\MailTemplate;
use App\Jobs\SendEmailJob;

class CampaignController extends Controller
{
    public function campaign_start(Campaign $campaign)
    {
        if(!$campaign->mailTemplate) return "Template id null";

        foreach($campaign->mails as $mail) {
            $mailLog = MailLog::create([
                'campaign_id' => $campaign->id,
                'mail' => $mail
            ]);
            SendEmailJob::dispatch($mailLog->id, $campaign->mailTemplate->subject, $mail,$campaign->mailTemplate->html, $campaign->mailTemplate->attachment);
        }

        return response()->json([
            'success' => true,
            'data' => Campaign::all()
        ]);
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Campaign::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Campaign::create([
            'name' => $request->name,
            'mail_template_id' => $request->mail_template_id,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'data' => 'Saved'
        ]);
    }

    public function show(Campaign $campaign)
    {
        $mailLists=MailList::all();
        $mailTemplates=MailTemplate::all();

        return response()->json([
            'success' => true,
            'campaign' => $campaign,
            'mailLists' => $mailLists,
            'mailTemplates' => $mailTemplates,
        ]);
    }

    public function update(Request $request, string $campaign_id)
    {
        Campaign::where('id', $campaign_id)
        ->update([
            'name' => $request->name,
            'mail_template_id' => $request->mail_template_id,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'data' => 'Updated'
        ]);
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return response()->json([
            'success' => true,
            'data' => 'Successfuly deleted',
        ]);
    }
}
