<?php

namespace App\Http\Livewire;

use App\Models\ChatBox as ModelsChatBox;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;

class ChatBox extends Component
{
    public $message;

    public $chatBoxMaxTokens = 600;

    public $chatBoxTemperature = 0.6;

    public $transactions = [];

    public $messages = [];

    public $chatBoxRole;

    public $chatBoxModel = [
        "gpt-3.5-turbo"
    ];

    public function ask()
    {
        $this->transactions[] = ['role' => 'system', 'content' => 'You are Laravel ChatGPT clone. Answer as concisely as possible.'];

        // If the user has typed something, then asking the ChatGPT API
        if (!empty($this->message)) {
            $this->transactions[] = ['role' => 'user', 'content' => $this->message];
            $response = OpenAI::chat()->create([
                'model' =>"gpt-3.5-turbo",
                'messages' => $this->transactions,
                'max_tokens' => $this->chatBoxMaxTokens,
                'temperature' => (float) $this->chatBoxTemperature,
            ]);

            Log::info($response->choices[0]->message->content);
            $this->transactions[] = ['role' => 'assistant', 'content' => $response->choices[0]->message->content];
            $this->messages = collect($this->transactions)->reject(fn ($message) => $message["role"] === "system");
            $this->message = '';
        }
    }

    public function updateCheckBoxRole($value)
    {
        dd($value);
        $this->message = $value;
    }

    public function resetChatBox()
    {
        $this->transactions = [];
        $this->messages = [];
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chat-box.chat-box', [
            'availableModels' => ModelsChatBox::availableModels(),
            'availableRoles' => ModelsChatBox::availableRoles(),
        ]);
    }
}