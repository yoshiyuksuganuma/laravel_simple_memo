<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//ファイル内で使うmodelをインポートする必要がある
use App\Models\Memo;
//DB::を使うのに必要なのでインポート
use DB;
use App\Models\Tag;
use App\Models\MemoTag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
      

    //タグ一覧を取得
        $tags = Tag::where('user_id' , '=' ,\Auth::id())->
        whereNull('deleted_at')->
        orderBy('updated_at', 'DESC')->
        get();
        return view('create', compact('tags'));//compact関数はコントローラーからビューに変数を渡す時に使用する(ここでは上のselect文から取得したデータを変数に代入してビュー側に渡して表示させる$は不要)
    }

    //新規メモ作成、タグの作成、画像の登録
    public function store(Request $request)
    {
        $posts = $request->all();

        //バリデーション 基本的にコントローラーか外部ファイルに作る、配列のキーはviewファイルのname属性
        $request->validate(['content' => 'required',]);
        DB::transaction(function() use($posts,$request) {  
          $query = ['content' => $posts['content'] , 'user_id' => \Auth::id()];

          //画像フォームが空じゃなかったらリクエストした画像を取得->storageに保存し->クエリに追加
          if(!empty($request->file('thumbnail'))){
          $img = $request->file('thumbnail');
          $path = $img->store('img','public');
          $query = ['content' => $posts['content'] , 'user_id' => \Auth::id(), 'thumbnail' => $path];
          }

         $memo_id = Memo::insertGetId($query);
         //ログインユーザーが同じタグ名がないか確認、変数に真偽値が入る
         $tag_exists = Tag::where('user_id' , '=' , \Auth::id())->where('name' , '=' , $posts['new_tag'])
         ->exists();
      
         //新規タグが入力されているかチェック
         //新規タグが既にtagsテーブルに存在するのかチェック
         if(!empty($posts['new_tag']) || $posts['new_tag'] === '0' && !$tag_exists ) {
             //新規タグが既に存在しなければタグ名をtagsテーブルにinsertし、idを取得
            $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
            //memotagsテーブル(中間テーブル)にinsertしてメモとタグを紐付ける
            MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
         }
         //既存タグが紐づけられた場合memo_tagsにインサート
         if(!empty($posts['tags'][0])) {
         foreach($posts['tags'] as $tag) {
             MemoTag::insert(['memo_id' => $memo_id , 'tag_id' => $tag]);
         }
        }

        });

        //transactionが成功したらホームにリダイレクト
        return redirect(route('home'));
    }

    //編集機能
    public function edit($id)//ルーティングから渡ってきたurlをパラメータを取得
    {
      

        $edit_memo = Memo::select('memos.*', 'tags.id AS tag_id')->
        //memo_tagsテーブルのmemo_idとmemosテーブルのidが一致したらテーブルをくっつける
        leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')->
        leftJoin('tags', 'memo_tags.tag_id', '=', 'tags.id')->
        where('memos.user_id' ,'=', \Auth::id())->
        where('memos.id' ,'=', $id)->
        whereNull('memos.deleted_at')->
        get();
        $include_tags = [];
        foreach($edit_memo as $memo){
        array_push($include_tags , $memo['tag_id']);
        };
        $tags = Tag::where('user_id' , '=' ,\Auth::id())->
        whereNull('deleted_at')->
        orderBy('updated_at', 'DESC')->
        get();
        return view('edit', compact('edit_memo','include_tags' ,'tags'));//compact関数はコントローラーからビューに変数を渡す時に使用する(ここでは上のselect文から取得したデータ、エディットのメモidを変数に代入してビューに渡して表示させる$は不要)
    }

    public function update(Request $request)
    {
        
      $posts = $request->all();

             //update関数を使う時な必ずwhere句を使う、whereがないと全ての行がアップデートされてしまい大事故になる
              $request->validate(['content' => 'required',]);
             DB::transaction(function() use($posts,$request){
                $img = $request->file('thumbnail_new');
                $path = $img->store('img','public');
                Memo::where('id',$posts['memo_id'])->update(['content' => $posts['content'] , 'user_id' => \Auth::id(),'thumbnail' => $path]);
                // 中間テーブルでは論理削除が使えないため一旦メモとタグの紐付けを物理削除
                MemoTag::where('memo_id',$posts['memo_id'])->delete();
                // 再度メモとタグの紐付け
                if(!empty($posts['tags'])) {
                foreach($posts['tags'] as $tag) {
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag]);
                }
            }

                // もし新しいタグの入力があれば、insertして紐付ける
                $tag_exists = Tag::where('user_id' , '=' , \Auth::id())->where('name' , '=' , $posts['new_tag'])
                ->exists();
                //新規タグが入力されているかチェック
                //新規タグが既にtagsテーブルに存在するのかチェック
                if(!empty($posts['new_tag']) || $posts['new_tag'] === '0' && !$tag_exists ) {
                    //新規タグが既に存在しなければタグ名をtagsテーブルにinsertし、idを取得
                   $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                   //memotagsテーブル(中間テーブル)にinsertしてメモとタグを紐付ける
                   MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag_id]);
                }
             });
              
            return redirect(route('home'));
            
}

    public function updateIncludedImg(Request $request)
    {
        
      $posts = $request->all();

      if(!empty($posts['thumbnail'])) {
        $DlPath = $posts['thum_id'];
        $DlPath = str_replace('/storage/','',$DlPath);
        \Storage::disk('public')->delete($DlPath);
        Memo::where('thumbnail', '=' ,$DlPath)->update(['thumbnail' => '']);
        } 

             //update関数を使う時な必ずwhere句を使う、whereがないと全ての行がアップデートされてしまい大事故になる
              $request->validate(['content' => 'required',]);
             DB::transaction(function() use($posts,$request){
                Memo::where('id',$posts['memo_id'])->update(['content' => $posts['content'] , 'user_id' => \Auth::id()]);
                // 中間テーブルでは論理削除が使えないため一旦メモとタグの紐付けを物理削除
                MemoTag::where('memo_id',$posts['memo_id'])->delete();
                // 再度メモとタグの紐付け
                if(!empty($posts['tags'])) {
                foreach($posts['tags'] as $tag) {
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag]);
                }
            }

                // もし新しいタグの入力があれば、insertして紐付ける
                $tag_exists = Tag::where('user_id' , '=' , \Auth::id())->where('name' , '=' , $posts['new_tag'])
                ->exists();
                //新規タグが入力されているかチェック
                //新規タグが既にtagsテーブルに存在するのかチェック
                if(!empty($posts['new_tag']) || $posts['new_tag'] === '0' && !$tag_exists ) {
                    //新規タグが既に存在しなければタグ名をtagsテーブルにinsertし、idを取得
                   $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                   //memotagsテーブル(中間テーブル)にinsertしてメモとタグを紐付ける
                   MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag_id]);
                }
             });
              
            return redirect(route('home'));
            
}

    //メモ削除
    public function destroy(Request $request)
    {

        $posts = $request->all();
        //delete()を使うと物理削除になってしまうのでupdate()を使って論理削除にする insertと一緒で入れたい値を配列の形で指定
        //index関数のところでwhereNull('deleted_at')としているのでメモ一覧に表示されなくなる
        //論理削除にしておけばデータベースを直接いじって復活させられる
        Memo::where('id',$posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s",time())]);
        return redirect(route('home'));
    }

   

   
}
