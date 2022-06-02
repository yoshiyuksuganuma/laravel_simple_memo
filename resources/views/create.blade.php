@extends('layouts.app')

@section('content')
        <div class="card">
        <div class="card-header">新規メモ作成</div>
            <form class="card-body" action="{{route('store')   }}" method="post" enctype="multipart/form-data">
                @csrf
             <div class="form-group mb-2">
                 <textarea name="content" rows="3" class="form-controll" placeholder="ここにメモを入力"></textarea>
             </div>
             @error('content')
            <div class="alert alert-danger">メモ内容を入力してください</div>
            @enderror
             @foreach($tags as $tag) 
             <div class="form-check form-check-inline mb3">
                 <input type="checkbox" class="form-check-input" value="{{ $tag['id'] }}" id="{{ $tag['id'] }}" name="tags[]">
                 <label for="{{ $tag['id'] }}" class="form-check-label">{{ $tag['name'] }}</label>
             </div>
             @endforeach
             <input type="text" class="form-controll w-50 d-block mb-3" name="new_tag" placeholder="新しいタグを入力">
             <input type="file" name="thumbnail" class="d-block mb-3" />
             <button class="btn btn-primary" type="submit">保存</button>
            </form>
        </div>
      
@endsection