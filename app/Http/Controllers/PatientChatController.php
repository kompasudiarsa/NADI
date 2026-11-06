<?php
namespace App\Http\Controllers\NadiSupport;
use App\Models\NadiChatSession;
use App\Models\NadiChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PatientChatController extends BaseController {
    public function page(Request $r) { $this->patient($r); return view('nadi-support.patient'); }
    public function current(Request $r) {
        $p=$this->patient($r);
        $session=NadiChatSession::where('patient_key',$p['patient_id'])->whereIn('status',['waiting','active'])->latest('id')->first();
        return response()->json(['session'=>$session]);
    }
    public function history(Request $r) {
        $p = $this->patient($r);
        $sessions = NadiChatSession::where('patient_key', $p['patient_id'])
            ->orderByDesc('id')->limit(50)
            ->get(['id','ticket','category','subject','status','created_at','closed_at']);
        return response()->json(['sessions' => $sessions]);
    }
    public function start(Request $r) {
        $p=$this->patient($r);
        $v=$r->validate(['category'=>'required|string|in:Pendaftaran,Reservasi,Jadwal Dokter,Hasil Pemeriksaan,Penggunaan NADI,Lainnya','subject'=>'nullable|string|max:180','message'=>'required|string|max:3000']);
        // Lock per pasien untuk menahan dua tab menciptakan dua tiket.
        $s=DB::transaction(function () use($p,$v) {
            // MySQL/MariaDB/Postgres advisory lock alternatif: lock pada row pasien bila tersedia.
            // Di sini pastikan duplikasi ditangani pula oleh unique index produksi bila parallel traffic tinggi.
            DB::table('nadi_patient_chat_locks')->insertOrIgnore(['patient_key'=>$p['patient_id']]);
            DB::table('nadi_patient_chat_locks')->where('patient_key',$p['patient_id'])->lockForUpdate()->first();
            $existing=NadiChatSession::where('patient_key',$p['patient_id'])->whereIn('status',['waiting','active'])->lockForUpdate()->first();
            if($existing) return $existing;
            $s=NadiChatSession::create(['ticket'=>'NADI-'.now()->format('ymd').'-'.strtoupper(Str::random(8)),'patient_key'=>$p['patient_id'],'patient_rm'=>$p['medical_record']??null,'patient_name'=>$p['patient_name']??null,'category'=>$v['category'],'subject'=>$v['subject']??null,'status'=>'waiting','last_message_at'=>now(),'last_patient_message_at'=>now()]);
            NadiChatMessage::create(['session_id'=>$s->id,'sender_type'=>'patient','message'=>$v['message']]);
            $tz=config('nadi_support.office_timezone','Asia/Makassar');$t=now($tz);$inOffice=in_array($t->dayOfWeekIso,config('nadi_support.office_days',[1,2,3,4,5]),true) && $t->format('H:i')>=config('nadi_support.office_start','08:00') && $t->format('H:i')<config('nadi_support.office_end','16:00');
            NadiChatMessage::create(['session_id'=>$s->id,'sender_type'=>'system','message'=>$inOffice?'Pesan diterima. Petugas CS akan segera membalas.':'Pesan diterima di luar jam operasional CS. Petugas akan membalas pada jam layanan berikutnya.']);
            $this->event($s->id,null,'created');
            return $s;
        });
        return response()->json(['session'=>$s]);
    }
    public function messages(Request $r, NadiChatSession $session) {
        $p=$this->patient($r);
        abort_unless($session->patient_key===$p['patient_id'],403);
        $after=max(0,(int)$r->query('after',0));
        return response()->json(['session'=>$session->fresh(),'messages'=>$session->messages()->where('id','>',$after)->orderBy('id')->limit(200)->get()]);
    }
    public function send(Request $r,NadiChatSession $session) {
        $p=$this->patient($r); abort_unless($session->patient_key===$p['patient_id'],403);
        $v=$r->validate(['message'=>'required|string|max:3000']);
        DB::transaction(function()use($session,$v){
            $locked=NadiChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($locked->status,['waiting','active'],true),409,'Sesi sudah ditutup.');
            NadiChatMessage::create(['session_id'=>$locked->id,'sender_type'=>'patient','message'=>$v['message']]);
            $locked->update(['last_message_at'=>now(),'last_patient_message_at'=>now()]);
            if($locked->assigned_cs_id) DB::table('nadi_support_notifications')->insert(['cs_id'=>$locked->assigned_cs_id,'session_id'=>$locked->id,'type'=>'patient_message','message'=>'Pesan pasien baru pada '.$locked->ticket,'created_at'=>now(),'updated_at'=>now()]);
        });
        return response()->json(['ok'=>true]);
    }
    public function close(Request $r,NadiChatSession $session) {
        $p=$this->patient($r); abort_unless($session->patient_key===$p['patient_id'],403);
        DB::transaction(function()use($session){
            $s=NadiChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            if(!in_array($s->status,['waiting','active'],true)) return;
            $s->update(['status'=>'closed','closed_at'=>now(),'close_reason'=>'Ditutup oleh pasien']);
            $this->event($s->id,null,'closed_by_patient');
        });
        return response()->json(['ok'=>true]);
    }
}
