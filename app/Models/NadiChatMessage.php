<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NadiChatMessage extends Model {
    protected $table = 'nadi_chat_messages';
    protected $guarded = [];
    protected $casts = ['read_at'=>'datetime'];
}
