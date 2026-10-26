@extends('layouts.app', ['title' => 'Moderasi Ulasan NADI'])

@section('content')
<div style="max-width:1100px;margin:24px auto;padding:0 16px;color:#183153">
    <h1 style="color:#163a93">Moderasi Ulasan NADI</h1>
    @if(session('success'))<p style="padding:12px;background:#eaf8f0;border-radius:8px">{{ session('success') }}</p>@endif
    <nav style="display:flex;gap:10px;flex-wrap:wrap;margin:18px 0">
        @foreach(['pending'=>'Menunggu','published'=>'Dipublikasikan','rejected'=>'Ditolak','all'=>'Semua'] as $key=>$label)
            <a href="{{ route('admin.nadi-reviews.index',['status'=>$key]) }}" style="padding:9px 13px;border-radius:9px;text-decoration:none;background:{{ $status===$key?'#163a93':'#edf2f8' }};color:{{ $status===$key?'#fff':'#183153' }}">{{ $label }}</a>
        @endforeach
    </nav>
    @forelse($reviews as $review)
        <article style="border:1px solid #e3eaf2;border-radius:14px;padding:18px;margin:14px 0;background:#fff">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
                <strong>{{ $review->patient_label }} · {{ $review->rating }}/5 ★</strong>
                <small>{{ $review->created_at->format('d-m-Y H:i') }} · {{ $review->service_name }}</small>
            </div>
            <p style="white-space:pre-line;line-height:1.7">{{ $review->review }}</p>
            <form method="POST" action="{{ route('admin.nadi-reviews.update',$review) }}">
                @csrf
                @method('PATCH')
                <label for="reply-{{ $review->id }}">Tanggapan resmi</label>
                <textarea id="reply-{{ $review->id }}" name="admin_reply" rows="2" maxlength="2000" style="display:block;width:100%;box-sizing:border-box;margin:8px 0 12px;padding:10px;border:1px solid #dbe3ee;border-radius:8px">{{ old('admin_reply',$review->admin_reply) }}</textarea>
                <label for="status-{{ $review->id }}">Status</label>
                <select id="status-{{ $review->id }}" name="status" style="padding:9px;border:1px solid #dbe3ee;border-radius:8px">
                    @foreach(['pending'=>'Menunggu','published'=>'Publikasikan','rejected'=>'Tolak'] as $key=>$label)
                        <option value="{{ $key }}" {{ $review->status===$key?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" style="padding:10px 14px;margin-left:8px;background:#163a93;color:#fff;border:0;border-radius:8px;cursor:pointer">Simpan</button>
            </form>
        </article>
    @empty
        <p>Belum ada ulasan pada kategori ini.</p>
    @endforelse
    {{ $reviews->links() }}
</div>
@endsection
