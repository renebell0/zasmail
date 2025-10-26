<?php

namespace App\Models;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Message extends Model {
    use HasFactory;

    public static function store(Request $request) {
        $message = new Message;
        $message->subject = $request->subject;
        $message->from = $request->from;
        $message->to = $request->to;
        if ($request->has('html')) {
            $message->body = $request->html;
        } else {
            $message->body = $request->text;
        }
        $message->save();
        if ($request->has('content-ids')) {
            $message->attachments = $request->get('attachment-info');
            $message->save();
            $directory = './attachments/' . $message->id;
            is_dir($directory) ?: mkdir($directory, 0777, true);
            $attachment_ids = json_decode($request->get('attachment-info'));
            foreach ($attachment_ids as $attachment_id => $attachment_info) {
                $allowed = explode(',', 'csv,doc,docx,xls,xlsx,ppt,pptx,xps,pdf,dxf,ai,psd,eps,ps,svg,ttf,zip,rar,tar,gzip,mp3,mpeg,wav,ogg,jpeg,jpg,png,gif,bmp,tif,webm,mpeg4,3gpp,mov,avi,mpegs,wmv,flx,txt');
                $file = explode('.', $attachment_info->filename);
                if (in_array($file[count($file) - 1], $allowed)) {
                    Storage::disk('tmp')->putFileAs($directory, $request->file($attachment_id), $attachment_info->filename);
                }
            }
        }
    }

    public static function getMessages($email) {
        $limit = config('app.settings.fetch_messages_limit');
        $messages = Message::where('to', $email)->orWhere('to', 'like', '%<' . $email . '>%')->limit($limit)->get();
        $response = [
            'data' => [],
            'notifications' => []
        ];
        foreach ($messages as $message) {
            $content = str_replace('<a', '<a target="blank"', $message->body);
            if (config('app.settings.enable_masking_external_link')) {
                $content = str_replace('href="', 'href="https://anon.ws/?', $content);
            }
            $obj = [];
            $obj['subject'] = $message->subject;
            $sender = explode('<', $message->from);
            $obj['sender_name'] = $sender[0];
            if (isset($sender[1])) {
                $obj['sender_email'] = str_replace('>', '', $sender[1]);
            } else {
                $obj['sender_email'] = $obj['sender_name'];
            }
            $obj['timestamp'] = $message->created_at;
            $obj['date'] = $message->created_at->format(config('app.settings.date_format', 'd M Y h:i A'));
            $obj['datediff'] = $message->created_at->diffForHumans();
            $obj['id'] = $message->id;
            $obj['content'] = $content;
            $obj['attachments'] = [];
            $domain = explode('@', $obj['sender_email'])[1];
            $blocked = in_array($domain, config('app.settings.blocked_domains'));
            if ($blocked) {
                $obj['subject'] = __('Blocked');
                $obj['content'] = __('Emails from') . ' ' . $domain . ' ' . __('are blocked by Admin');
            }
            if ($message->attachments && !$blocked) {
                $attachments = json_decode($message->attachments);
                foreach ($attachments as $id => $attachment) {
                    $url = env('APP_URL') . '/tmp/attachments/' . $message->id . '/' . $attachment->filename;
                    if (strpos($obj['content'], $id) !== false) {
                        $obj['content'] = str_replace('cid:' . $id, $url, $obj['content']);
                    } else {
                        if (Storage::disk('tmp')->exists('attachments/' . $message->id . '/' . $attachment->filename)) {
                            array_push($obj['attachments'], [
                                'file' => $attachment->filename,
                                'url' => $url
                            ]);
                        }
                    }
                }
            }
            array_push($response['data'], $obj);
            if (!$message->is_seen) {
                array_push($response['notifications'], [
                    'subject' => $obj['subject'],
                    'sender_name' => $obj['sender_name'],
                    'sender_email' => $obj['sender_email']
                ]);
                if (env('ENABLE_TMAIL_LOGS', true)) {
                    file_put_contents(storage_path('logs/tmail.csv'), request()->ip() . "," . date("Y-m-d h:i:s a") . "," . $obj['sender_email'] . "," . $email . PHP_EOL, FILE_APPEND);
                }
                $message->is_seen = true;
                $message->save();
            }
        }
        return $response;
    }
}
