<?php
declare (strict_types = 1);

namespace app\home\middleware;


class Login{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next){
        if(empty($_SERVER['HTTP_REFERER'])){
            return $next($request);
        }
        $login_url = $_SERVER['HTTP_REFERER'];
        $setbase = true;
        if(count(explode('/reg',$login_url)) > 1 || count(explode('open.weixin.qq.com',$login_url)) > 1 || count(explode('/wx-bind',$login_url)) > 1 || count(explode('/login',$login_url)) > 1 || count(explode('/repwd',$login_url)) > 1 || count(explode('/loginqu',$login_url)) > 1){
            $setbase = false;
        }
        if($setbase){
            cache('login_url',$_SERVER['HTTP_REFERER']);
        }
        return $next($request);
    }

}
