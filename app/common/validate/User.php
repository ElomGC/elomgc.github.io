<?php
declare(strict_types = 1);

namespace app\common\validate;

use think\Validate;
class User extends Validate {
    protected $rule =   [
        "uid" => "require|number",
        "u_groupid" => "require|number",
        "u_name" => "unique:user|require|max:20|min:6|regex:/^[a-zA-Z0-9_.@~!?]{5,20}$/",
        "u_uniname" => "min:2|max:80|chsDash",
        "u_uname" => "min:2|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z-\_\s·]+$/u",
        "u_sex" => "number|in:0,1,2",
        "u_phone" => "unique:user|mobile",
        "u_icon" => "min:10|max:150|regex:^[^%@#&',;$]*$",
        "u_mail" => "unique:user|email",
        "u_password|密码" => "min:6|max:80|regex:/^[a-zA-Z0-9_.@~!?]{6,20}$/",
        "u_paypassword" => "min:6|max:80|regex:/^[a-zA-Z0-9_.@~!?]{6,20}$/",
        "u_hasword" => "min:6|max:80|regex:/^[a-zA-Z0-9_.@~!?]{6,20}$/",
        "u_bdy" => "dateFormat:Y-m-d",
        "u_card" => "unique:user|idCard",
        "u_cardimg" => "array",

        "id" => "require|number",
        "uname|姓名" => "min:2|max:80|regex:/^[\x{4e00}-\x{9fa5}A-Za-z-\_\s·]+$/u",
        "uphone|手机" => "mobile",
        "zoneid|地区" => "require|number",
        "uaddress|地址" => "require",
        "zipcode|邮编" => "number",
        "type|默认地址" => "number",
    ];
    protected $message =   [
        'id.number' => '非法访问！',
        'id.require' => '非法访问！',
        'uid.number' => '非法访问！',
        'uid.require' => '非法访问！',
        'u_groupid.number' => '用户组选择错误！',
        'u_groupid.require' => '请选择用户组！',
        "u_name.unique" => "用户名已存在",
        "u_name.require" => "用户名不得为空",
        "u_name.max" => "用户名不得超过20个字符",
        "u_name.min" => "用户名不得小于6个字符",
        "u_name.regex" => "用户名不得存在特殊字符",

        "u_uniname.require" => "用户昵称不得为空",
        "u_uniname.min" => "用户昵称不得小于2个字符",
        "u_uniname.max" => "用户昵称不得大于80个字符",
        "u_uniname.chsDash" => "用户昵称不得存在特殊字符",

        "u_uname.min" => "姓名填写错误",
        "u_uname.max" => "姓名填写错误",
        "u_uname.regex" => "姓名填写错误",

        "u_sex.number" => "性别选择错误",
        "u_sex.in" => "性别选择错误",

        "u_phone.unique" => "手机号码已存在",
        "u_phone.mobile" => "手机号码填写错误",

        "u_icon.min" => "头像选择错误1",
        "u_icon.max" => "头像选择错误2",
        "u_icon.regex" => "头像选择错误3",

        "u_mail.unique" => "邮箱已存在",
        "u_mail.email" => "邮箱填写错误",

        "u_password.min" => "密码不得小于6位",
        "u_password.max" => "密码不得大于80位",
        "u_password.regex" => "密码禁止输入特殊字符",
        "u_paypassword.min" => "支付密码不得小于6位",
        "u_paypassword.max" => "支付密码不得大于80位",
        "u_paypassword.regex" => "支付密码禁止输入特殊字符",

        "u_bdy.dateFormat" => "生日填写错误",

        "u_card.unique" => "身份证号已存在",
        "u_card.idCard" => "身份证号填写错误",

        "u_cardimg.array" => "身份证照片上传错误",

        '__token__' => 'token',
        //`id` int(11) NOT NULL AUTO_INCREMENT,
        //  `uid` int(11) DEFAULT NULL,
        //  `uname` varchar(255) DEFAULT NULL,
        //  `uphone` varchar(255) DEFAULT NULL,
        //  `zoneid` varchar(255) DEFAULT NULL,
        //  `cartid` varchar(255) DEFAULT NULL,
        //  `uaddress` varchar(255) DEFAULT NULL,
        //  `zipcode` varchar(255) DEFAULT NULL,
        //  `custadd_id` varchar(255) DEFAULT NULL,
        //  `type` tinyint(1) NOT NULL DEFAULT '0',
    ];
    protected $scene = [
        'add' => ['u_groupid','u_name','u_uniname','u_uname','u_sex','u_phone','u_icon','u_mail','u_password','u_paypassword','u_hasword','u_bdy','u_card','u_cardimg'],
        'edit' => ['uid','u_groupid','u_uniname','u_uname','u_sex','u_phone','u_icon','u_mail','u_password','u_paypassword','u_hasword','u_bdy','u_card','u_cardimg'],
        'apiedit' => ['uid','u_uniname','u_uname','u_sex','u_phone','u_icon','u_mail','u_password','u_paypassword','u_hasword','u_bdy','u_card','u_cardimg'],
        'reg' => ['u_name' => 'max:20|min:6|regex:/^[a-zA-Z0-9_.@~!?]{5,20}$/','u_phone','u_password'],
        'repwd' => ['u_phone' => 'mobile','u_password'],
    ];
    public function sceneLogin(){
        return $this->only(['u_name','u_phone','u_password'])->remove('u_name', 'unique|require|min')->remove('u_phone', 'unique')->append('u_password', 'require');
    }
    public function sceneWxlogin(){
        return $this->only(['u_phone'])->remove('u_phone', 'unique');
    }
    public function sceneAddress(){
        return $this->only(['uname','uphone','zoneid','uaddress','zipcode','type']);
    }
    public function sceneEditress(){
        return $this->only(['id','uname','uphone','zoneid','uaddress','zipcode','type']);
    }

}