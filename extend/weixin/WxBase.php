<?php
declare (strict_types = 1);
namespace weixin;

use think\facade\Cache;
use think\facade\Request;

class WxBase {
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    protected function getNonceStr($length = 32):string
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    /**
     * @param string $url   访问的链接
     * @param array $post_data  附加参数
     * @return bool|string  返回值
     */
    public function request_get($url = '', $post_data = array()){
        $postdata = http_build_query($post_data);
        $options = [
            'https' => [
                'method' => 'GET',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    public function request_post($url = '', $post_data = array()){
        $postdata = http_build_query($post_data);
        $options = [
            'https' => [
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    /**
     * @param $url  请求链接
     * @param $arr  需要转换成json的数组
     * @return string   返回值
     */
    public function request_json($url, $data = null,$pem = false,$upfile = false) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if($pem == true){
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, Env::get('extend_path').'weixin/cart/apiclient_cert.pem');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, Env::get('extend_path').'weixin/cart/apiclient_key.pem');
        }
        if(!$upfile) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data)
            ]);
        }
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        return $return_content;
    }
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($data )
    {
        if (strlen($data['session_key']) != 24) {
            return '-41001';
        }
        $aesKey=base64_decode($data['session_key']);
        if (strlen($data['iv']) != 24) {
            return '-41002';
        }
        $aesIV=base64_decode($data['iv']);

        $aesCipher=base64_decode($data['encryptedData']);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return '-41003';
        }
        if( $dataObj->watermark->appid != $data['appid'])
        {
            return '-41003';
        }
        $result = json_decode($result,true);
        return $result;
    }
    /**
     * 获取基础TOKEN
     * @param $appid
     * @param $secret
     * @return mixed
     */
    public function get_base_token($appid,$secret){
        if(Cache::has('wxbasetoken')){
            $basetoken = Cache::get('wxbasetoken');
        }else{
            $basetoken = $this->request_post("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}");
            $basetoken = json_decode($basetoken,true);
            Cache::set('wxbasetoken',$basetoken,$basetoken['expires_in']);
        }
        return $basetoken['access_token'];
    }
    /**
     * @param $appid    微信appid
     * @param $secret   微信secret
     * @param $code 用户code
     * @return bool|mixed|string    返回openid和token token有效期为30天
     */
    public function get_user_token($appid,$secret,$code,$type='h5'){
        if($type == 'h5'){
            $usertoken = $this->request_post("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code");
        }elseif ($type == 'wx-mp'){
            $usertoken = $this->request_post("https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code");
        }
        $usertoken = json_decode($usertoken,true);
        return $usertoken;
    }
    /**
     * 获取用户详细信息
     * @param $access_token
     * @param $openid
     * @param string $type
     * @return bool|mixed|string
     */
    public function get_user_data($access_token,$openid,$type = 'h5',$info = 'base'){
        if($type == 'wx-mp'){
            $userdata = $this->request_post("https://api.weixin.qq.com/wxa/getpaidunionid?access_token={$access_token}&openid={$openid}");
        }else if($info == 'base'){
            $userdata = $this->request_post("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN");
        }else if($info == 'group'){
            $data = json_encode(["user_list" => $openid],LIBXML_NOCDATA);
            $userdata = $this->request_json("https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$access_token}",$data);
        }else{
            $userdata = $this->request_post("https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN");
        }
        $userdata = json_decode($userdata,true);
        return $userdata;
    }
    //  获取jsapi
    public function get_jsapi($access_token){
        if(Cache::has('wxjsapi')){
            $jsapi = Cache::get('wxjsapi');
        }else{
            $jsapi = $this->request_post("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi");
            $jsapi = json_decode($jsapi,true);
            if($jsapi['errcode'] == '0'){
                $jsapi = Cache::set('wxjsapi',$jsapi,$jsapi['expires_in']);
            }else{
                return false;
            }
        }
        return $jsapi['ticket'];
    }
    //  获取js签名
    public function get_jssing($ticket,$urls){
        $jssing = array(
            'noncestr' => $this->getNonceStr(),
            'jsapi_ticket' => $ticket,
            'timestamp' => time(),
            'url' => $urls,
        );
        ksort($jssing);
        foreach ($jssing as $k => $v){
            $newdata[] = "{$k}={$v}";
        }
        $string = implode('&',$newdata);
        $jssing['sing'] = sha1($string);
        return $jssing;
    }
    /**
     * @param $webdatas 微信配置
     * @param $order    订单信息
     * @return array|bool|mixed|string  返回统一下单信息
     */
    public function pay_add($webdatas,$order,$tarde_type='JSAPI'){
        $AppId = $webdatas['wx_appid'];
        if(!empty($order['usertoken_type']) && $order['usertoken_type'] == 'h5'){
            $AppId = $webdatas['wx_appid'];
        }
        if($tarde_type == "wx-mp"){
            $AppId = $webdatas['wx_mp_appid'];
            $tarde_type='JSAPI';
        }else if($tarde_type == "APP"){
            $AppId = $webdatas['wx_openapp_appid'];
        }
        $payadd = array(
            'appid' => $AppId,
            'mch_id' => $webdatas['wx_shopid'],
            'nonce_str' => $this->getNonceStr(),
            'body' => $order['title'], //商品标题
            'out_trade_no' => $order['lsoid'], //订单号
            'total_fee' => $order['payzmoney'], //标价金额
            'spbill_create_ip' => Request::ip(), //IP
            'notify_url' => $webdatas['web_url'].'/api/wxpayend.html', //返回地址
            'trade_type' => $tarde_type, //交易类型公众号
        );
        //  非公众交易这个是不存在的http://aiyuyujia.com/api/endwxpay.html
        if(!empty($order['openid'])){
            $payadd['openid'] = $order['openid'];
        }
        //  非H5交易，这个是不存在的
        if($tarde_type == 'MWEB'){
            $payadd['scene_info'] = $order['h5_info'];
        }
        ksort($payadd);
        $payadd['sign'] = $this->res_sign($payadd,$webdatas['wx_shopkey']); //签名
        $payadd = $this->res_xml($payadd);
        $payadd = $this->request_json("https://api.mch.weixin.qq.com/pay/unifiedorder",$payadd);
        $payadd = $this->res_data($payadd);

        return $payadd;
    }
    //  公众号支付
    public function get_play($webdatas,$playadd,$type = 'wx'){
        $playdata = array(
            'appId' => $playadd['appid'],
            'timeStamp' => time(),
            'nonceStr' => $this->getNonceStr(),
            'package' => "prepay_id={$playadd['prepay_id']}",
            'signType' => 'MD5',
        );
        if($type == 'app'){
            $playdata = [
                'appid' => $playadd['appid'],
                'partnerid' => $playadd['mch_id'],
                'prepayid' => $playadd['prepay_id'],
                'package' => "Sign=WXPay",
                'noncestr' => $this->getNonceStr(),
                'timestamp' => time(),
            ];
        }
        ksort($playdata);
        $playdata['paySign'] = $this->res_sign($playdata,$webdatas['wx_shopkey']); //签名
        return $playdata;
    }
    //  查询订单
    public function get_payend($webdatas,$out_trade_no,$type = 'wx'){
        $payend = array(
            'appid' => $type == 'wx' ? $webdatas['wx_appid'] : $webdatas['wx_mp_appid'],
            'mch_id' => $webdatas['wx_shopid'],
            'out_trade_no' => $out_trade_no,
            'nonce_str' => $this->getNonceStr(),
            'sign_type' => "MD5",
        );
        ksort($payend);
        $payend['sign'] = $this->res_sign($payend,$webdatas['wx_shopkey']); //签名
        $payend = $this->res_xml($payend);
        $payend = $this->request_json("https://api.mch.weixin.qq.com/pay/orderquery",$payend);
        $payend = $this->res_data($payend);
        return $payend;
    }
    //  生成签名
    public function res_sign($data,$wxmckey = null){
        foreach ($data as $k => $v){
            $newdata[] = "{$k}={$v}";
        }
        if(empty($wxmckey)){
            $string = implode('&',$newdata);
        }else{
            $string = implode('&',$newdata)."&key=".$wxmckey;
        }
        $string = strtoupper(md5($string));
        return $string;
    }
    //  生成xml
    public function res_xml($data){
        $xml = "<xml>";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }//  生成xml
    public function res_xmls($data){
        $xml = "<xml> ";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><! [CDATA[".$val."] ]></".$key."> ";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function res_data($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 获取微信菜单
     */
    public function getMenu($token){
        $menus = $this->request_post("https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token={$token}");
        $menus = json_decode($menus,true);
        return $menus;
    }
    /**
     * 获取微信素材数量
     */
    public function getSucaiNum($token){
        $menus = $this->request_post("https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token={$token}");
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 获取素材列表
     * @param $token
     * @param $type
     * @param $offset
     * @return mixed
     */
    public function getSucai($token,$type,$offset,$count = '20'){
        $data = json_encode(["type" => $type,"offset" => $offset,"count" => $count],LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 获取单个素材
     * @param $token
     * @param $media_id
     * @return mixed
     */
    public function getSucaiData($token,$media_id){
        $data = json_encode(["media_id" => $media_id],LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/material/get_material?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 删除素材
     * @param $token
     * @param $media_id
     * @return mixed
     */
    public function delSucaiData($token,$media_id){
        $data = json_encode(["media_id" => $media_id],LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/material/del_material?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 获取模板
     * @param $token
     * @return mixed
     */
    public function getSmsBase($token){
        $menus = $this->request_post("https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$token}");
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 删除模板
     * @param $token
     * @param $template_id
     * @return mixed
     */
    public function delSmsBase($token,$template_id){
        $data = json_encode(["template_id" => $template_id],LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }
    /**
     * 获取客服
     * @param $token
     * @return mixed
     */
    public function getKefuList($token){
        $menus = $this->request_post("https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token={$token}");
        $menus = json_decode($menus,true);
        return $menus;
    }
    /**
     * 添加客服账号
     */
    public function setKefu($token,$data){
        $data = json_encode($data,LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/customservice/kfaccount/add?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }
    public function setIcon($token,$data){
        $media = root_path().$data['media'];
        $media = new \CURLFile($media, $data['content-type'], $data['filename']);
        $_data = ['file' => $media];
        $menus = $this->request_json("https://api.weixin.qq.com/customservice/kfaccount/uploadheadimg?access_token={$token}&kf_account={$data['kf_account']}",$_data,false,true );
        $menus = json_decode($menus,true);
        return $menus;
    }

    public function getKefuWx($token,$data){
        $data = json_encode($data,LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }
    public function delKefu($token,$kf_account){
        $menus = $this->request_post("https://api.weixin.qq.com/customservice/kfaccount/del?access_token={$token}&kf_account={$kf_account}");
        $menus = json_decode($menus,true);
        return $menus;
    }

    /**
     * 获取用户列表
     */
    public function getUserList($token,$nextopenid = ''){
        $next_openid = empty($next_openid) ? '' : "&next_openid={$nextopenid}";
        $menus = $this->request_post("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$token}{$next_openid}");
        $menus = json_decode($menus,true);
        return $menus;
    }
    /**
     * 发送模板消息
     */
    public function sendMessage($token,$data){
        $data = json_encode($data,LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }
    /**
     * 获取用户列表
     */
    public function getTicket($token,$data){
        $data = json_encode($data,LIBXML_NOCDATA);
        $menus = $this->request_json("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token}",$data);
        $menus = json_decode($menus,true);
        return $menus;
    }
    public function getLsMa($ticket){
        $menus = $this->request_post("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}",$data);
        halt($menus);
        $menus = json_decode($menus,true);
        return $menus;
    }
}