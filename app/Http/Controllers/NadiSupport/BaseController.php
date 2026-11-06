<?php
namespace App\Http\Controllers\NadiSupport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
abstract class BaseController extends Controller {
    protected function patient(Request $r): array {
        $p = $r->session()->get('pasien', []);
        abort_unless(is_array($p) && !empty($p['patient_id']), 401, 'Silakan masuk ke NADI terlebih dahulu.');
        return $p;
    }
    protected function cs(Request $r) {
        $u = \Illuminate\Support\Facades\Auth::guard('nadi_cs')->user();
        abort_unless($u, 401);
        $field = config('nadi_support.cs_role_field','role');
        $allowed = config('nadi_support.cs_role_values',['cs','admin']);
        abort_unless(in_array((string) data_get($u,$field), array_map('strval',$allowed),true),403,'Anda bukan petugas CS.');
        return $u;
    }
    protected function event(int $sessionId, ?int $actor, string $event, array $details=[]): void {
        DB::table('nadi_chat_events')->insert(['session_id'=>$sessionId,'actor_cs_id'=>$actor,'event'=>$event,'details'=>json_encode($details),'created_at'=>now(),'updated_at'=>now()]);
    }
}
