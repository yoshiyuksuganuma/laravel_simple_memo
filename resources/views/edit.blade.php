@extends('layouts.app')

@section('javascript')
<script src="/js/confirm.js"></script>
@endsection

@section('content')
        <div class="card">
        <div class="card-header card-header-flex">
        <span>メモ編集</span>

        
        <form  action="{{ route('destroy') }}" method="post" id="delete-form" >
            @csrf
            <input type="hidden" value="{{ $edit_memo[0]['id'] }}" name="memo_id" />
            <button style="border: none;" type="submit" onclick="deleteHandle(event);"><i class="fas fa-trash" onclick="deleteHandle(event);"></i>
        </form>
        </div>
        <div class="card-body">


        @if(empty($edit_memo[0]['thumbnail']))
            <form  action="{{ route('update') }}" method="post" enctype="multipart/form-data">
                @csrf
                <!-- クロスサイトリクエストフォージェリ対策 -->
                <input type="hidden" value="{{ $edit_memo[0]['id'] }}" name="memo_id" />
                <!-- DBを更新するzupdateに現在編集していメモid(edit_memo['id']を渡す、コントローラーのedit関数の$postsに配列として格納され、nameがキーでvalueが値になる -->
                  <!-- URLを直打ちして他のユーザーのメモが表示されないようにする -->
             <div class="form-group">
                 <textarea name="content" rows="3" class="form-controll" placeholder="ここにメモを入力"> {{$edit_memo[0]['content']}}</textarea>
             </div>
             @error('content')
            <div class="alert alert-danger">メモ内容を入力してください</div>
            @enderror
       
            
              <!-- ループで回っている各$tag['id']がinclude_tagの中に含まれていればcheckedをつける -->
             @foreach($tags as $tag) 
             <div class="form-check form-check-inline mb3">
                 <input type="checkbox" class="form-check-input" value="{{ $tag['id'] }}" id="{{ $tag['id'] }}" name="tags[]"
                 {{ in_array($tag['id'], $include_tags) ? 'checked' : '' }}>
                 <label for="{{ $tag['id'] }}" class="form-check-label">{{ $tag['name'] }}</label>
             </div>
             @endforeach
             <input type="text" class="form-controll w-50 d-block mb-3" name="new_tag" placeholder="新しいタグを入力">
            <input type="file" name="thumbnail_new" class="d-block" />
             <button class="btn btn-primary mt-3" type="submit">更新</button>
            </form>
            @endif
          
            @if(!empty($edit_memo[0]['thumbnail']))
            <form  action="{{ route('updateIncludedImg') }}" method="post" enctype="multipart/form-data">
                @csrf
                <!-- クロスサイトリクエストフォージェリ対策 -->
                <input type="hidden" value="{{ $edit_memo[0]['id'] }}" name="memo_id" />
                <!-- DBを更新するzupdateに現在編集していメモid(edit_memo['id']を渡す、コントローラーのedit関数の$postsに配列として格納され、nameがキーでvalueが値になる -->
                  <!-- URLを直打ちして他のユーザーのメモが表示されないようにする -->
             <div class="form-group">
                 <textarea name="content" rows="3" class="form-controll" placeholder="ここにメモを入力"> {{$edit_memo[0]['content']}}</textarea>
             </div>
             @error('content')
            <div class="alert alert-danger">メモ内容を入力してください</div>
            @enderror
       
            
              <!-- ループで回っている各$tag['id']がinclude_tagの中に含まれていればcheckedをつける -->
             @foreach($tags as $tag) 
             <div class="form-check form-check-inline mb3">
                 <input type="checkbox" class="form-check-input" value="{{ $tag['id'] }}" id="{{ $tag['id'] }}" name="tags[]"
                 {{ in_array($tag['id'], $include_tags) ? 'checked' : '' }}>
                 <label for="{{ $tag['id'] }}" class="form-check-label">{{ $tag['name'] }}</label>
             </div>
             @endforeach
             <input type="text" class="form-controll w-50 d-block mb-3" name="new_tag" placeholder="新しいタグを入力">
             <img src="{{ Storage::url($edit_memo[0]['thumbnail']) }}" width="25%" class="d-block mb-3">
            <input type="hidden" value="{{ Storage::url($edit_memo[0]['thumbnail']) }}" name="thum_id" />
            <div>
            <input type="checkbox" name="thumbnail[]" id="del">
            <label for="del">添付された画像を削除</label>
            </div>
             <button class="btn btn-primary mt-3" type="submit">更新</button>
            </form>
            @endif
           
        </div>
        </div>
       
       
@endsection
