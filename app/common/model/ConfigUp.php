<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;

class ConfigUp extends R
{
    public function getList($data = [])
    {
        $map = $this;
        $map = !empty($data['id']) ? $map->whereIn('id',is_array($data['id']) ? $data['id'] : (string) $data['id']) : $map;
        $map = isset($data['status']) && $data['status'] != 'a' ? $map->whereStatus($data['status']) : $map;
        $map = $map->order(empty($data['order']) ? 'id desc' : $data['order']);
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->paginate($limit);
        $getlist = Common::JobArray($getlist);
        return $getlist;
    }
    public function getOne($id = '')
    {
        $getlist = $this->getList(empty($id) ? : ['id' => $id]);
        return $getlist['total'] > '0' ? $getlist['data']['0'] : [];
    }
    public function getUplist(){
        $webdb = getWebdb();
        $res = Common::request_post("http://www.wormcms.com/api/upcms.html?uri={$webdb['web_url']}&edition_no={$webdb['web_edition_no']}");
        if(!$res){
            $this->endUp(['edittime' => time()]);
        }else if($res){
            $res = json_decode($res,true);
            if($res['code'] == '1'){
                foreach ($res['data'] as $k => $v){
                    $v['addtime'] = time();
                    $v['status'] = '0';
                    $res['data'][$k] = $v;
                }
                $this->saveAll($res['data']);
            }
        }
        return $res;
    }
    public function getUpfile($data){
        $res = $this->whereEditionNo($data['nos'])->find();
        if(empty($res)){
            return false;
        }
        $header = get_headers($res['edition_uri'], 1);
        if(isset($header[0]) && (strpos($header[0], '200') || strpos($header[0], '304'))){
            $file = file_get_contents($res['edition_uri']);
            file_put_contents(runtime_path("update/{$data['nos']}").$data['nos'].'.zip',$file);
            return true;
        }
        return false;
    }
    protected function endUp($data){
        $old = $this->order('id desc')->find()->toArray();
        $this->whereId($old['id'])->update($data);
        return true;
    }
}