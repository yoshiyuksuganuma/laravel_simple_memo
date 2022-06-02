<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        //全てのメソッドがよばれる前に先に呼ばれるメソッド、共通関数
        view()->composer('*', function($view){
          //メモモデルをインスタンス化,別のファイルから使うモデルはインスタンス化する必要がある
          //概要はviewcomporser、詳細はモデルに任せるようにする
          $memo_model = new Memo();
          $memos = $memo_model->getMyMemo();

        

            $tags = Tag::where('user_id' ,'=', \Auth::id())->
            whereNull('deleted_at')->
            orderBy('id','DESC')->
            get();

            //１引 渡したいviewのファイル名 2引 渡したい変数 or 配列 
            $view->with('memos', $memos)->with('tags', $tags);
        });
    }
}
