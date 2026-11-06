<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NadiChatSession extends Model {
    protected $table = 'nadi_chat_sessions';
    protected $guarded = [];
    protected $casts = ['assigned_at'=>'datetime','first_response_at'=>'datetime','closed_at'=>'datetime','last_message_at'=>'datetime'];
    public function messages() { return $this->hasMany(NadiChatMessage::class,'session_id'); }
}
