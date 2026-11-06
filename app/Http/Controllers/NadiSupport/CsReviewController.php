<?php
namespace App\Http\Controllers\NadiSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class CsReviewController extends BaseController {
    private function cfg(): array {
        $table=config('nadi_support.review_table');
        abort_unless($table && preg_match('/^[A-Za-z0-9_]+$/',$table) && Schema::hasTable($table),503,'Atur NADI_REVIEW_TABLE untuk menghubungkan ulasan yang ada.');
        $fields=[];
        foreach(['id','patient_name','content','rating','created_at'] as $k) {
            $col=config('nadi_support.review_'.$k);
            abort_unless($col && preg_match('/^[A-Za-z0-9_]+$/',$col) && Schema::hasColumn($table,$col),503,'Mapping kolom ulasan belum valid: '.$k);
            $fields[$k]=$col;
        }
        return [$table,$fields];
    }
    public function index(Request $r) {
        $this->cs($r); [$table,$f]=$this->cfg();
        $focus=config('nadi_support.review_focus_category');
        $hasFocus=$focus && preg_match('/^[A-Za-z0-9_]+$/',$focus) && Schema::hasColumn($table,$focus);
        $rows=DB::table($table)
            ->select($f['id'].' as id',$f['patient_name'].' as patient_name',$f['content'].' as content',$f['rating'].' as rating',$f['created_at'].' as created_at')
            ->when($hasFocus,fn($q)=>$q->addSelect($focus.' as focus_category'))->orderBy($f['created_at'],'desc')->paginate(20);
        $ids=collect($rows->items())->pluck('id')->map(fn($x)=>(string)$x)->all();
        $replies=DB::table('nadi_review_replies')->whereIn('review_id',$ids)->get()->keyBy('review_id');
        foreach($rows->items() as $review) {
            $reply=$replies->get((string)$review->id);
            $review->reply=$reply->reply??null;
            $review->cs_id=$reply->cs_id??null;
            $review->replied_at=$reply->updated_at??null;
        }
        return response()->json($rows);
    }
    public function reply(Request $r,string $id) {
        $u=$this->cs($r); $v=$r->validate(['reply'=>'required|string|max:3000']);
        [$table,$f]=$this->cfg();
        abort_unless(DB::table($table)->where($f['id'],$id)->exists(),404);
        DB::table('nadi_review_replies')->updateOrInsert(['review_id'=>$id],['reply'=>$v['reply'],'cs_id'=>$u->id,'updated_at'=>now(),'created_at'=>now()]);
        return response()->json(['ok'=>true]);
    }
}
