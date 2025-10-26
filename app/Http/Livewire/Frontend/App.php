<?php

namespace App\Http\Livewire\Frontend;

use App\Models\Message;
use Livewire\Component;
use App\Models\TMail;
use Illuminate\Support\Facades\Auth;

class App extends Component {

    public $messages = [];
    public $deleted = [];
    public $error = '';
    public $email;
    public $initial;
    public $overflow = false;

    protected $listeners = ['fetchMessages' => 'fetch', 'syncEmail'];

    public function mount() {
        $this->email = TMail::getEmail();
        $this->initial = false;
    }

    public function syncEmail($email) {
        $this->email = $email;
    }

    public function fetch() {
        try {
            $count = count($this->messages);
            $responses = [];
            if (env('BETA_FEATURE', false) || !config('app.settings.imap.cc_check', false)) {
                $responses = [
                    'to' => TMail::getMessages($this->email, 'to', $this->deleted),
                    'cc' => [
                        'data' => [],
                        'notifications' => []
                    ]
                ];
            } else {
                $responses = [
                    'to' => TMail::getMessages($this->email, 'to', $this->deleted),
                    'cc' => TMail::getMessages($this->email, 'cc', $this->deleted)
                ];
            }
            $this->deleted = [];
            $this->messages = array_merge($responses['to']['data'], $responses['cc']['data']);
            $notifications = array_merge($responses['to']['notifications'], $responses['cc']['notifications']);
            if (count($notifications)) {
                if ($this->overflow == false && count($this->messages) == $count) {
                    $this->overflow = true;
                }
            } else {
                $this->overflow = false;
            }
            foreach ($notifications as $notification) {
                $this->dispatchBrowserEvent('showNewMailNotification', $notification);
            }
            TMail::incrementMessagesStats(count($notifications));
        } catch (\Exception $e) {
            if (Auth::check() && Auth::user()->role == 7) {
                $this->error = $e->getMessage();
            } else {
                $this->error = 'Not able to connect to Mail Server';
            }
        }
        $this->dispatchBrowserEvent('stopLoader');
        $this->dispatchBrowserEvent('loadDownload');
        $this->initial = true;
    }

    public function delete($messageId) {
        if (env('BETA_FEATURE', false)) {
            Message::find($messageId)->delete();
        }
        array_push($this->deleted, $messageId);
        foreach ($this->messages as $key => $message) {
            if ($message['id'] == $messageId) {
                $directory = './tmp/attachments/' . $messageId;
                $this->rrmdir($directory);
                unset($this->messages[$key]);
            }
        }
    }

    public function render() {
        return view('themes.' . config('app.settings.theme') . '.components.app');
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }
}
