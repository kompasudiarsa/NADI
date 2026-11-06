<?php
namespace App\Http\Controllers\NadiSupport;
use App\Models\NadiChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PatientFeedbackController extends BaseController {
 public function store(Request $r,NadiChatSession $session){$patient=$this->patient($r);abort_unless(hash_equals((string)$session->patient_key,(string)$patient['patient_id']),403);abort_unless($session->status==='closed',409,'Selesaikan chat terlebih dahulu.');$v=$r->validate(['rating'=>'required|integer|between:1,5','comment'=>'nullable|string|max:1500']);abort_if(DB::table('nadi_chat_feedback')->where('session_id',$session->id)->exists(),409,'Penilaian telah dikirim.');DB::table('nadi_chat_feedback')->insert(['session_id'=>$session->id,'rating'=>$v['rating'],'comment'=>$v['comment']??null,'created_at'=>now(),'updated_at'=>now()]);return response()->json(['ok'=>true]);}
}
