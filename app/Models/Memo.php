<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    public function getMyMemo () {
          //リクエストファザードでクエリパラメータを取得
          $query_tag = \Request::query('tag');

          //ベースクエリ クエリビルダを条件分岐する
          // ここでメモを取得 -> memosテーブルの全てのカラムの中でuser_udカラムと今ログインしているidが一致していてdeleted_atカラムが空(削除したメモは除外)のメモを取ってきて更新日時が新しい順に取得する
          $query = Memo::query()->select('memos.*')->
          where('user_id' ,'=', \Auth::id())->
          whereNull('deleted_at')->
          orderBy('updated_at','DESC');
          
          //もしクエリパラメータがあれば、タグを絞り込み、タグがなければ全て取得
          if(!empty($query_tag)) {
         $query->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')->
         where('memo_tags.tag_id', '=', $query_tag);
          }
          $memos = $query->get();
          
          
          //分岐して取得した$memosをappserviceproveider(呼び出し元)にreturnする
          return $memos;
          }

    }

