<?php
declare(strict_types = 1);

namespace app\common\wormview;

use app\common\controller\Base;
use think\facade\Filesystem;

class Upload extends Base {

    protected $UpDir;
    protected $DefaultDir;
    protected $LinshiDir;
    protected function initialize(){
        parent::initialize();
        $webdb = getWebdb();
        $this->UpDir = "{$webdb['web_updir']}/";
        $this->DefaultDir = config('filesystem.disks');
        $this->LinshiDir = $this->UpDir.'linshi/';
        if (!file_exists($this->UpDir)) {
            @mkdir($this->UpDir,0777,true);
        }
        if (!file_exists($this->LinshiDir)) {
            @mkdir($this->LinshiDir,0777,true);
        }
    }
    //  定义新名称
    protected function newname($data){
        $imgmd5 = substr(md5($data.time()),0,8);
        $saveName = '';
        for ( $i = 0; $i < 10; $i++ ) {
            $saveName .= substr($imgmd5, mt_rand(0, strlen($imgmd5) - 1), 1);
        }
        return $saveName;
    }
    //  查询文件是否存在
    public function checkFile($data){
        if(is_file($this->LinshiDir.$data)){
            return true;
        }
        return false;
    }
    //  小文件上传
    public function setSmall($data){
        $info = Filesystem::putFile('linshi',$data,'md5');
        $info = $this->DefaultDir['local']['root'].'/'.$info;
        $name = $this->LinshiDir.basename($info);
        if(rename($info,root_path().$name)){
            $res = array(
                'title' => basename($info),
                'size' => filesize($name),
                'uri' => "/{$name}"
            );
            return $res;
        }
        return false;
    }
    //  大文件分割上传
    public function setBig($data,$base){
        $name = "{$base['md5']}.{$base['prefix']}";
        $info = Filesystem::putFile('linshi',$data,'md5');
        $info = $this->DefaultDir['local']['root'].'/'.$info;
        $name = empty($name) ? basename($info) : $name;
        $setname = $this->LinshiDir.$name;
        @rename($info,root_path().$setname);
        $res = array(
            'title' => $base['name'],
            'size' => filesize($setname),
            'uri' => "/{$setname}"
        );
        return $res;
    }
    //  合并文件
    public function pushFiles($data){
        $file_prefix = explode('.',$data['name']);
        $prefix = array_pop($file_prefix);
        $add_file = @fopen("{$this->LinshiDir}{$data['md5']}.{$prefix}","a");
        $data['file_list'] = array_unique($data['file_list']);
        foreach ($data['file_list'] as $k => $v){
            $newv = fopen($this->LinshiDir.$v,'r');
            flock($newv,LOCK_EX);
            fwrite($add_file,file_get_contents($this->LinshiDir.$v));
            flock($newv,LOCK_UN);
            @unlink($this->LinshiDir.$v);
        }
        @fclose($add_file);
        $res = array(
            'title' => $data['name'],
            'size' => filesize("{$this->LinshiDir}{$data['md5']}.{$prefix}"),
            'uri' => "/{$this->LinshiDir}{$data['md5']}.{$prefix}"
        );
        return $res;
    }
    /**
     * @param $data 要处理的数据
     * @param $file_dir 目标地址
     * @param string $moves 处理方式：copy为复制，rename为移动
     * @param bool $dels 是否删除源文件
     * @return string 返回文件地址
     */
    public function fileMove($data,$file_dir = null,$moves = 'rename',$dels=true){
        if(is_array($data)){
            foreach ($data as $key => $val){
                $newdata[$key] = $this->fileMove($val,$file_dir,$moves,$dels);
            }
            return $newdata;
        }
        if(empty($data)){
            return '';
        }
        $file_dir = empty($file_dir) ? date('Y-m',time()) : $file_dir;
        /*重命名*/
        $saveName = $this->newname($data);
        $fileFix = pathinfo($data, PATHINFO_EXTENSION);
        $fileName = $saveName.'.'.$fileFix;
        /*  检测文件夹是否存在 */
        if (!file_exists($this->UpDir.$file_dir)) {
            @mkdir($this->UpDir.$file_dir,0755,true);
        }
        //       检查文件路径
        if(preg_match("/{$this->UpDir}",$data)){
            $data = str_ireplace("/{$this->UpDir}",$this->UpDir,$data);
        }
        /*移动文件*/
        if($moves == 'rename'){
            @rename($data,root_path().$this->UpDir.$file_dir.'/'.$fileName);
        }else{
            @copy($data,root_path().$this->UpDir.$file_dir.'/'.$fileName);
        }
        if($dels && $moves != 'rename'){
            @unlink(root_path().$data);
        }
        return '/'.$this->UpDir.$file_dir.'/'.$fileName;
    }
    /**
     * @param $data 删除的文件内容
     * @return bool 返回结果
     */
    public function fileDel($data){
        if(!empty($data) && is_array($data)){
            foreach ($data as $key => $val){
                $this->fileDel($val);
            }
        }else if(!empty($data) && is_file(root_path().$data)){
            $filedir = explode('/'.$this->UpDir,$data);
            if(count($filedir) > '1'){
                @unlink(root_path().$data);
            }
        }
        return true;
    }
    //  隐藏真实地址，为防止更换网址出故障
    public function editadd($data,$adds = true){
        if(is_array($data)){
            foreach ($data as $k => $v){
                $data[$k] = $this->editadd($v,$adds);
            }
        }
        if($adds == true){
            $data = str_replace("/{$this->UpDir}","http://www_cxbs_net/Ls_dir/",$data);
        }else{
            $data = str_replace("http://www_cxbs_net/Ls_dir/","/{$this->UpDir}",$data);
        }
        return $data;
    }
    public function editaddApi($data,$adds = true){
        if(is_array($data)){
            foreach ($data as $k => $v){
                $data[$k] = $this->editaddApi($v,$adds);
            }
            return $data;
        }
        $old = explode("/{$this->UpDir}",(string) $data);
        $_old = explode("http://www_cxbs_net/Ls_dir/",(string) $data);
        if(count($old) < 2 && count($_old) < 2){
            return $data;
        }
        if($adds){
            $data = str_replace($this->webdb['web_url']."/{$this->UpDir}","http://www_cxbs_net/Ls_dir/",$data);
            $data = str_replace("/{$this->UpDir}","http://www_cxbs_net/Ls_dir/",$data);
        }else{
            $data = str_replace("http://www_cxbs_net/Ls_dir/","/{$this->UpDir}",$data);
            $data = str_replace("/{$this->UpDir}",$this->webdb['web_url']."/{$this->UpDir}",$data);

        }
        return $data;
    }
    //  处理编辑器图片文件
    public function setEditOr($data,$img_dir,$geturl = true){
        $oldimg = $this->img_list($data['oldimg']);
        $newimg = $this->img_list($data['newimg']);
        if(!empty($newimg)){
            $newimg = array_flip($newimg);
            $newimg = array_flip($newimg);
        }
        if(!empty($oldimg)){
            foreach ($oldimg as $v){
                $oldimgname[] = basename($v);
            }
        }
        //  处理新图片
        $newimgname = [];
        if(!empty($newimg)){
            foreach ($newimg as $k => $v){
                if(!empty($oldimgname)){
                    if(in_array(basename($v),$oldimgname)){
                        unset($newimg[$k]);
                    }
                }
                $newimgname[] = basename($v);
            }
        }
        //  处理老图片
        if(!empty($oldimg)){
            foreach ($oldimg as $k => $v){
                if(!empty($newimgname)){
                    if(in_array(basename($v),$newimgname)){
                        unset($oldimg[$k]);
                    }
                }
            }
        }
        if(!file_exists($this->UpDir.$img_dir)){
            @mkdir($this->UpDir.$img_dir,0755,true);
        }
        //  开始移动
        if(!empty($newimg)){
            if($geturl == true){
                foreach ($newimg as $k => $v){
                    if(preg_match('/http:\/\/*\??[\w=&\+\%]*/is',$v) || preg_match('/https:\/\/*\??[\w=&\+\%]*/is',$v)){
                        $newname = $this->range($v,$img_dir);
                    }elseif(preg_match('/^(data:\s*image\/(\w+);base64,)/',$v,$type)){
                        $newname = $this->base64($v,$type,$img_dir);
                    }elseif(preg_match("/{$this->UpDir}",$v)){
                        $newname = $this->fileMove($v,$img_dir);
                    }
                    if(isset($newname)){
                        $data['newimg'] = str_ireplace($v,$newname,$data['newimg']);
                    }
                }
            }else{
                foreach ($newimg as $k => $v){
                    if(preg_match("/{$this->UpDir}",$v)){
                        $newname = $this->fileMove($v,$img_dir);
                    }
                    if(isset($newname)){
                        $data['newimg'] = str_ireplace($v,$newname,$data['newimg']);
                    }
                }
            }
        }
        if(!empty($oldimg)){
            $this->fileDel($this->editadd($oldimg,false));
        }
        return $data['newimg'];
    }
    //  获取远程图片
    //  提取所有图片
    public function img_list($data){
        if(empty($data)){
            return [];
        }
        $imgps = "/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
        preg_match_all($pattern = $imgps, $data, $imgdata);
        return $imgdata[1];
    }
    protected function range($img,$imgurl){
        $mimes = array(
            'image/bmp'=>'bmp',
            'image/gif'=>'gif',
            'image/jpg'=>'jpg',
            'image/jpeg'=>'jpeg',
            'image/png'=>'png',
            'image/x-icon'=>'ico'
        );
        if(($headers=get_headers($img, 1))!==false) {
            // 获取响应的类型
            $type = $headers['Content-Type'];
            // 如果符合类型
            if (isset($mimes[$type])) {
                $imgmimes = $mimes[$type];
                $newname = $this->newname($img);
                $newname = $imgurl.'/'.$newname.'.'.$imgmimes;
                // 获取数据并保存
                file_put_contents($this->UpDir.$newname, file_get_contents($img));
                return '/'.$this->UpDir.$newname;
            }
        }
        return $img;
    }
    public function base64($img,$type,$imgurl){
        $imgpng = $type[2];
        $newname = $this->newname($img);
        $newname = $imgurl.'/'.$newname.'.'.$imgpng;
        file_put_contents($this->UpDir.'/'.$newname, file_get_contents($img));
        return '/'.$this->UpDir.$newname;
    }
}