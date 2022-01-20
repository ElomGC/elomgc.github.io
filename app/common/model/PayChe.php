<?php
declare(strict_types = 1);

namespace app\common\model;


use app\facade\hook\Common;

class PayChe extends R
{
    protected $schema = [
        "id" => 'int',
        "uid" => 'int',
        "cont" => 'text',
        "addtime" => "int",
    ];
    public function getList($data = []){
        $map = $this;
        $map = !empty($data['uid']) ? $map->whereIn('uid',is_array($data['uid']) ? $data['uid'] : (string) $data['uid']) : $map;
        $limit = [
            'list_rows' => empty($data['limit']) ? '20' : $data['limit'],
        ];
        if(!empty($data['page'])){
            $limit['page'] = $data['page'];
        }
        $getlist = $map->order('addtime desc')->withoutField('del_time')->paginate($limit);
        $page = $getlist->render();
        $getlist = Common::JobArray($getlist);
        if($getlist['total'] > '0'){
            foreach ($getlist['data'] as $k => $v){
                $v['cont'] = empty($v['cont']) ? [] : json_decode($v['cont'],true);
                if(!empty($v['cont'])){
                    foreach ($v['cont'] as $k1 => $v1){
                        $v1['admincont'] = empty($v1['admincont']) ? [] : $v1['admincont'];
                        $v1['admincont'] = is_array($v1['admincont']) ? $v1['admincont'] : json_decode($v1['admincont'],true);
                        $v1['fu_cont'] = empty($v1['fu_cont']) ? [] : $v1['fu_cont'];
                        $v1['fu_cont'] = is_array($v1['fu_cont']) ? $v1['fu_cont'] : json_decode($v1['fu_cont'],true);
                        $v['cont'][$k1] = $v1;
                    }
                }
                $getlist['data'][$k] = $v;
            }
        }
        $getlist['page'] = $page;
        return $getlist;
    }
    public function getOne($uid){
        $getlist = $this->getList(['uid' => $uid]);
        return $getlist['total'] > '0' ? $getlist['data']['0'] : [];
    }
    public function editList($data,$uid,$del = false){
        $_old = $this->getOne($uid);
        if(!empty($_old['cont'])){
            foreach ($_old['cont'] as $k => $v){
                $_v = Common::del_file($data,'model',$v['model']);
                $_v = empty($_v) ? [] : Common::del_file($_v,'mid',$v['mid']);
                $_v = empty($_v) ? [] : Common::del_file($_v,'aid',$v['aid']);
                if(empty($_v)){
                    continue;
                }
                $_admincont = $v['admincont'];
                foreach ($data as $k1 => $v1){
                    if($v['model'] != $v1['model'] || $v['mid'] != $v1['mid'] || $v['aid'] != $v1['aid']){
                        continue;
                    }
                    $_admincont['parameter'] = empty($_admincont['parameter']) ? '' : $_admincont['parameter'];
                    $_admincont['num'] = empty($_admincont['num']) ? $v['num'] : $_admincont['num'];
                    $_vadmincont = !empty($v1['admincont']) ? json_decode($v1['admincont'],true) : [];
                    $_vadmincont['parameter'] = empty($_vadmincont['parameter']) ? '' : $_vadmincont['parameter'];
                    if((empty($_admincont['parameter']) && empty($_vadmincont['parameter'])) || (!empty($_admincont['parameter']) && !empty($_vadmincont['parameter']) && $_vadmincont['parameter'] == $_admincont['parameter'])){
                        if($del){
                            unset($_old['cont'][$k]);
                        }else{
                            $v['num'] = $_vadmincont['num'] = $_vadmincont['num'] + $_admincont['num'];
                            $v['money_zon'] = $v['money'] * $v['num'];
                            $v['admincont'] = json_encode($_vadmincont,JSON_UNESCAPED_UNICODE);
                            $_old['cont'][$k] = $v;
                            unset($data[$k1]);
                        }
                    }
                }
            }
            if(!empty($data) && !$del){
                $_old['cont'] = array_merge($_old['cont'],$data);
            }else{
                $_old['cont'] = array_merge([],$_old['cont']);
            }
            $data = $_old['cont'];
            $data['id'] = $_old['id'];
        }
        return $data;
    }
}