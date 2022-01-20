<?php
declare(strict_types = 1);
namespace app\api\controller;
use app\common\controller\Base;
use app\common\wormview\Upload as cxModel;
use app\facade\hook\Common;
class Upload extends Base {

    public function index(cxModel $cxmodel){
        $data = Common::data_trim(input('post.'));
        if(!empty($data['file_check_name'])){
            if($cxmodel->checkFile($data['file_check_name'])){
                return $this->success("文件已上传");
            }else{
                return $this->error("文件未上传");
            }
        }
        $set_file = [];
        //  合并文件
        if(!empty($data['flieSet']) && $data['flieSet'] == 'end'){
            if($set_file = $cxmodel->pushFiles($data)){
                return $this->success("上传成功",'',$set_file);
            }else{
                return $this->error("上传失败");
            }
        }
        //  上传文件
        $files = $this->request->file();

        if(is_array($files) && $files){
            foreach ($files as $k => $v){
                $set_file = $cxmodel->setBig($v,$data);
            }
        }
        return $this->success("上传成功",'',$set_file);
    }
    public function ckeditor(cxModel $cxmodel){
        $data = Common::data_trim(input('post.'));
        $files = $this->request->file();
        if(is_array($files) && $files){
            foreach ($files as $k => $v){
                $set_file = $cxmodel->setSmall($v);
            }
        }
        $res = [
            "uploaded" => 1,
            "url" => $set_file['uri']
        ];
        return json($res);
    }
    public function upone(cxModel $cxmodel){
        $files = $this->request->file();
        if(is_array($files) && $files){
            foreach ($files as $k => $v){
                $set_file = $cxmodel->setSmall($v);
            }
        }
        $this->result($set_file,'1','上传成功');
    }
    public function base64(cxModel $cxmodel){
        $data = Common::data_trim(input('post.'));
        if(empty($data['file'])){
            $this->error("非法访问");
        }
        $data = $cxmodel->saveBase64($data['file']);
        if($data === false){
            $this->error("上传失败");
        }
        $this->success('上传成功','',$data);
    }
}