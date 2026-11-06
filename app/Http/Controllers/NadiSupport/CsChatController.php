<?php
namespace App\Http\Controllers\NadiSupport;
use App\Models\NadiChatSession;
use App\Models\NadiChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CsChatController extends BaseController {
    public function page(Request $r) { $this->cs($r); return view('nadi-support.cs'); }
    public function index(Request $r) {
        $u=$this->cs($r); $type=$r->query('status','waiting');
        abort_unless(in_array($type,['waiting','active','closed'],true),422);
        $q=NadiChatSession::where('status',$type)->orderBy('created_at',$type==='waiting'?'asc':'desc');
        if($type==='active') $q->where('assigned_cs_id',$u->id);
        return response()->json(['sessions'=>$q->limit(100)->get(), 'stats'=>[
            'waiting'=>NadiChatSession::where('status','waiting')->count(),
            'mine'=>NadiChatSession::where('status','active')->where('assigned_cs_id',$u->id)->count(),
            'closed_today'=>NadiChatSession::where('status','closed')->whereDate('closed_at',today())->count(),
        ]]);
    }
    public function claim(Request $r,NadiChatSession $session) {
        $u=$this->cs($r);
        abort_unless(DB::table('nadi_cs_presence')->where('cs_id',$u->id)->where('status','online')->where('last_seen_at','>=',now()->subMinutes(2))->exists(),409,'Ubah status menjadi Online sebelum mengambil chat.');
        DB::transaction(function()use($session,$u){
            $s=NadiChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            abort_unless($s->status==='waiting' && !$s->assigned_cs_id,409,'Chat sudah diambil CS lain.');
            $s->update(['status'=>'active','assigned_cs_id'=>$u->id,'assigned_at'=>now()]);
            $this->event($s->id,$u->id,'claimed');
        });
        return response()->json(['ok'=>true]);
    }
    private function assigned(Request $r,NadiChatSession $session) {
        $u=$this->cs($r);
        abort_unless((int)$session->assigned_cs_id === (int)$u->id,403,'Chat ini milik CS lain.');
        return $u;
    }
    public function messages(Request $r,NadiChatSession $session) {
        if ($session->status === 'waiting') {
            $this->cs($r);
            return response()->json(['session'=>$session->fresh(), 'messages'=>[], 'needs_claim'=>true]);
        }
        $this->assigned($r,$session);
        return response()->json(['session'=>$session->fresh(),'messages'=>$session->messages()->where('id','>',max(0,(int)$r->query('after',0)))->orderBy('id')->limit(200)->get()]);
    }
    public function send(Request $r,NadiChatSession $session) {
        $u=$this->assigned($r,$session); $v=$r->validate(['message'=>'required|string|max:3000']);
        DB::transaction(function()use($session,$u,$v){
            $s=NadiChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            abort_unless($s->status==='active' && (int)$s->assigned_cs_id === (int)$u->id,409,'Percakapan tidak aktif.');
            NadiChatMessage::create(['session_id'=>$s->id,'sender_type'=>'cs','sender_cs_id'=>$u->id,'message'=>$v['message']]);
            $s->update(['last_message_at'=>now(),'first_response_at'=>$s->first_response_at ?: now()]);
        });
        return response()->json(['ok'=>true]);
    }
    public function close(Request $r,NadiChatSession $session) {
        $u=$this->assigned($r,$session); $v=$r->validate(['reason'=>'nullable|string|max:250']);
        DB::transaction(function()use($session,$u,$v){
            $s=NadiChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            abort_unless($s->status==='active' && (int)$s->assigned_cs_id === (int)$u->id,409);
            $s->update(['status'=>'closed','closed_by'=>$u->id,'closed_at'=>now(),'close_reason'=>$v['reason']??'Selesai ditangani']);
            $this->event($s->id,$u->id,'closed_by_cs',['reason'=>$s->close_reason]);
        });
        return response()->json(['ok'=>true]);
    }
}
