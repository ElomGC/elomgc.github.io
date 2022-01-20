<?php
declare(strict_types = 1);
namespace app\common\wormview;

use app\common\model\Read;
use app\facade\hook\Common;
use think\exception\ValidateException;
use worm\NodeFormat;
trait AddEditList {
    use Label;

    //  列表页
    public function index(){
        if(empty($this->index_list)){
            $this->index_list = true;
        }
        $listdb = $this->index_list ? $this->getListDb() : [];
        //  检测是否需要格式化数据
        $view_name = "view".ucwords(app('http')->getName())."List";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name($listdb);
    }
    public function getlist(){
        $listdb = $this->getListDb();
        $view_name = "view".ucwords(app('http')->getName())."List";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name($listdb);
    }
    //  添加数据
    public function create(){
        $view_name = "view".ucwords(app('http')->getName())."Add";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name();
    }
    //  编辑数据
    public function edit(){
        $pk = $this->model->getPk();
        if(method_exists($this->model,'getOne')){
            $data = $this->model->getOne($this->getdata[$pk]);
        }else{
            $data = $this->model->where($pk,$this->getdata['id'])->find()->toArray();
        }
        if(empty($this->getdata[$pk])){
            $this->error("非法访问");
        }
        $view_name = "view".ucwords(app('http')->getName())."Add";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name($data);
    }
    //  读取数据
    public function read(){
        $pk = $this->model->getPk();
        if(method_exists($this->model,'getOne')){
            $data = $this->model->getOne($this->getdata[$pk]);
        }else{
            $data = $this->model->where($pk,$this->getdata[$pk])->find()->toArray();
        }
        if(empty($this->getdata[$pk])){
            $this->error("非法访问");
        }
        $view_name = "view".ucwords(app('http')->getName())."Read";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name($data);
    }
    public function save(){
        if(!$this->request->isPost() && !$this->request->isPut()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        try {
            $validatename = $this->request->isPut() ? "edit" : "add";
            if(!empty($this->validatename)){
                $validatename = $this->request->isPut() ? $this->validatename['edit'] : $this->validatename['add'];
            }
            validate($this->validate)->scene($validatename)->check($data);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $res_title = $this->request->isPut() ? "编辑" : "添加";
        if(method_exists($this->model,'getEditAdd')){
            $data = $this->model->getEditAdd($data);
        }else{
            $fileList = $this->model->getTableFields();
            $_data = [];
            foreach ($fileList as $k => $v){
                if(isset($data[$v])){
                    $_data[$v] = $data[$v];
                }
                continue;
            }
            $data = $_data;
        }
        $pk = $this->model->getPk();
        if(method_exists($this->model,'setOne')){
            if(!$add = $this->model->setOne($data)){
                $this->error("{$res_title}失败");
            }
        }else{
            if(!empty($data[$pk])){
                $old = $this->model->where($pk,$data[$pk])->find();
                if(!$old->save($data)){
                    $this->error("{$res_title}失败");
                }
            }else if(!$add = $this->model->save($data)){
                $this->error("{$res_title}失败");
            }
        }
        $this->success("{$res_title}成功",url('index')->build());
    }
    //  回收站
    public function trash(){
        $listdb = $this->index_list ? $this->getListDb(['del_time' => '1001','status' => 'a']) : [];
        //  检测是否需要格式化数据
        $view_name = "view".ucwords(app('http')->getName())."List";
        if(!method_exists($this,$view_name)){
            $this->error("方法不存在");
        }
        return $this->$view_name($listdb);
    }
    public function trashone(){
        $data = Common::data_trim(input('post.'));
        if(empty($data['id']) && empty($data['ids'])){
            $this->error("非法访问");
        }
        $ids = empty($data['ids']) ? $data['id'] : $data['ids'];
        $pk = $this->model->getPk();
        $files = $this->model->getTableFields();
        if(in_array('del_time',$files)){
            $add['del_time'] = '0';
        }
        if(in_array('status',$files)){
            $add['status'] = '1';
        }
        if(method_exists($this->model,'TrashOne')){
            if($this->model->TrashOne($ids)){
                $this->success("还原成功");
            }
        }else{
            if($this->model->whereIn($pk,$ids)->update($add)){
                $this->success("还原成功");
            }
        }
        $this->error("还原失败");
    }
    public function del(){
        $data = Common::data_trim(input('post.'));
        if(!$this->request->isDelete() || empty($data['id'])){
            $this->error("非法访问");
        }
        $pk = $this->model->getPk();
        $del = $this->model->where($pk,$data['id'])->find();
        if(method_exists($this->model,'DeleteOne')){
            if($res = $this->model->DeleteOne($data['id'])){
                if($res === true || !empty($res['code'])){
                    $this->success("删除成功");
                }
                $this->error(empty($res['msg']) ? "删除失败" : $res['msg']);
            }
        }else if(array_key_exists('del_time', $del->toArray())){
            if($del['del_time'] > '0' && $del->force()->delete()){
                $this->success("删除成功");
            }else if($this->model->where($pk,$data['id'])->update(['del_time' => time()])){
                $this->success("删除成功");
            }
        }else if($del->delete()){
            $this->success("删除成功");
        }
        $this->error("删除失败");
    }
    public function fastedit(){
        if(!$this->request->isPost()){
            $this->error("非法访问");
        }
        $data = Common::data_trim(input('post.'));
        if(empty($data['id']) || empty($data['filed'])){
            $this->error("非法访问");
        }
        $pk = $this->model->getPk();
        $add[$pk] = $data['id'];
        $add[$data['filed']] = $data['value'];
        try {
            validate($this->validate)->scene("fastedit")->check($add);
        }catch (ValidateException $e){
            $this->error($e->getError());
        }
        $old = $this->model->where($pk,$add[$pk])->find();
        $old[$data['filed']] = $data['value'];
        if($old->save()){
            $this->success('处理成功');
        }
        $this->error("处理失败");
    }
    //  批量删除
    public function pdel(){
        $data = Common::data_trim(input('post.'));
        if(!$this->request->isPost() || empty($data['ids'])){
            $this->error("非法访问");
        }
        $pk = $this->model->getPk();
        $files = $this->model->getTableFields();
        if(method_exists($this->model,'DeleteOne')){
            if($res = $this->model->DeleteOne($data['ids'])){
                if($res === true || !empty($res['code'])){
                    $this->success("删除成功");
                }
                $this->error(empty($res['msg']) ? "删除失败" : $res['msg']);
            }
        }elseif (in_array('del_time',$files)){
            $_oldlist = $this->model->whereIn($pk,$data['ids'])->select()->toArray();
            $_deltime = Common::del_file($_oldlist,'del_time','0');
            if(!empty($_deltime)){
                if($this->model->whereIn($pk,$data['ids'])->update(['del_time' => time()])){
                    $this->success("删除成功");
                }
            }else{
                if($this->model->whereIn($pk,$data['ids'])->delete()){
                    $this->success("删除成功");
                }
            }
        }else{
            if($this->model->whereIn($pk,$data['ids'])->delete()){
                $this->success("删除成功");
            }
        }
        $this->error("删除失败");
    }
    //  快速启用/禁用
    public function fastswitch(){
        if(!$this->request->isPut()){
            $this->error("非法访问");
        }
        $pk = $this->model->getPk();
        $data = Common::data_trim(input('post.'));
        if(empty($data[$pk]) || empty($data['field'])){
            $this->error("非法访问");
        }
        $old = $this->model->where($pk,$data[$pk])->find();
        if(method_exists($this->model,'FastSwitch')){
            $add = [
                $pk => $data['id'],
                '_field' => $data['field'],
                $data['field'] => $data['value'],
            ];
            if($res = $this->model->FastSwitch($add)){
                if($res === true || !empty($res['code'])){
                    $this->success("处理成功");
                }
                $this->error(empty($res['msg']) ? "处理失败" : $res['msg']);
            }
        }
        $old[$data['field']] = $data['value'];
        if($old->save()){
            $this->success('处理成功');
        }
        $this->error("处理失败");
    }
    protected function getListDb($data = []){
        $map = array_merge($this->getMap(),$data);
        if(method_exists($this->model,'getList')){
            return $this->model->getList($map);
        }
        $filed_list = $this->model->getTableFields();
        $order = empty($order) ? $this->getOrder() ? $this->getOrder() : $this->model->getPk()." desc" : $order;
        $maps = $this->model->where($map)->order($order);
        if(in_array('del_time',$filed_list)){
            if(empty($map['del_time']) || !$map['del_time']){
                $maps = $maps->whereRaw("`del_time` is null or `del_time` = 0");
            }else if(!empty($map['del_time']) && $map['del_time']){
                $maps = $maps->where('del_time','>','0');
            }
        }
        if($this->list_base['page'] == '0'){
            $listdb = $maps->paginate();
            $page = $listdb->render();
            $listdb = Common::JobArray($listdb);
            $listdb['page'] = $page;
        }else{
            $listdb = $maps->select();
            $listdb = Common::JobArray($listdb);
        }
        return $listdb;
    }
    /**
     *   后台列表页
     * @param array $data 数据列表
     * @return \think\response\View 模板文件
     */
    protected function viewAdminList($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        //  读取模板
        $view_temp = $this->getTemp("public:table_base");
        return view($view_temp,["listdb" => $data,'list_file' => $this->list_file,'file_temp' => !empty($this->list_file) ? $this->editLayuiTable($this->list_file) : [],'list_base' => $this->list_base,'list_nav' => $this->list_nav,'list_search' => $this->list_search, "list_top" => $this->list_top,]);
    }
    /**
     * 后台添加数据页面
     * @param array $data
     * @return \think\response\View
     */
    protected function viewAdminAdd($data = []){
        $view_temp = $this->getTemp("public:form_base");
        $form_type = $this->getFormType($this->form_list);
        $form_group = $this->getFormType($this->form_list,'type_group');
        if(!empty($form_group)){
            foreach ($form_group as $k => $v){
                if(empty($v)){
                    unset($form_group[$k]);
                }
                continue;
            }
        }
        return view($view_temp,[
            "postdb" => $data,
            "list_file" => $this->form_list,
            "list_type" => $form_type,
            "list_group" => $form_group,
            "list_base" => $this->list_base,
        ]);
    }
    protected function viewAdminRead($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        $view_temp = $this->getTemp("public:form_base");
        $form_group = $this->getFormType($this->form_list,'type_group');
        if(!empty($form_group)){
            foreach ($form_group as $k => $v){
                if(empty($v)){
                    unset($form_group[$k]);
                }
                continue;
            }
        }
        return view($view_temp,[
            "postdb" => $data,
            "list_file" => $this->form_list,
            "list_group" => $form_group,
        ]);
    }
    /**
     * @param array $data
     * @return \think\response\Json|\think\response\View
     */
    protected function viewMemberList($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        $view_temp = $this->getTemp("public:list_base");
        return view($view_temp,[
            "listdb" => $data,
            "list_file" => $this->list_file,
            "list_base" => $this->list_base,
            "list_top" => $this->list_top,
            "list_nav" => $this->list_nav,
            "list_search" => $this->list_search,
            "list_rightbtn" => $this->list_rightbtn,
        ]);
    }
    protected function viewMemberRead($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        $view_temp = $this->getTemp("public:read");
        return view($view_temp,[
            "readdb" => $data,
        ]);
    }
    protected function viewMemberAdd($data = []){
        $view_temp = $this->getTemp("public:form_base");
        $form_type = $this->getFormType($this->form_list);
        $form_group = $this->getFormType($this->form_list,'type_group');
        if(!empty($form_group)){
            foreach ($form_group as $k => $v){
                if(empty($v)){
                    unset($form_group[$k]);
                }
                continue;
            }
        }
        return view($view_temp,[
            "postdb" => $data,
            "list_file" => $this->form_list,
            "list_type" => $form_type,
            "list_group" => $form_group,
            "list_base" => $this->list_base,
        ]);
    }
    /**
     * 前台列表页，调用标签解析
     * @param array $data
     * @return \think\response\Json|\think\response\View
     */
    protected function viewHomeList($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        //  读取模板
        $view_temp = $this->getTemp("public:list");
        $view_temp = $this->getLabel($view_temp);
        return view($view_temp,['listdb' => $data]);
    }
    protected function viewHomeRead($data = []){
        if($this->request->isAjax()){
            $this->result($data,'1','获取成功');
        }
        //  读取模板
        $view_temp = $this->getTemp("public:read");
        $view_temp = $this->getLabel($view_temp);
        return view($view_temp,['readdb' => $data]);
    }
    /**
     * API请求
     * @param array $data
     * @return \think\response\Json|\think\response\View
     */
    protected function viewApiList($data = []){
        $data = \app\facade\wormview\Upload::editaddApi($data,false);
        $this->result($data,'1','获取成功');
    }
    protected function viewApiRead($data = []){
        $data = \app\facade\wormview\Upload::editaddApi($data,false);
        $this->result($data,'1','获取成功');
    }
    protected function getFormType($data = [],$file = 'type'){
        if(empty($data)){
            return [];
        }
        $form_type = array_column($data,$file);
        return array_unique($form_type);
    }
    /**
     *  解析模板文件
     * @param $temp
     * @return mixed
     */
    protected function getTemp($temp){
        return empty($this->list_temp) ? $temp : $this->list_temp;
    }
    /**
     * 解析列表标签
     */
    protected function editLayuiTable($data){
        $pk = $this->model->getPk();
        foreach ($data as $k => $v){
            if($v['type'] == 'switch'){
                $res['switch'][] = "<script type='text/html' id='{$v['file']}'><input type='checkbox' name='{$v['file']}' value='{{ d.{$pk} }}' lay-skin='switch' lay-text='{$v['text']}' lay-filter='{$v['file']}' {{ d.{$v['file']} == {$v['default']} ? 'checked' : '' }} ></script>";
                $_uri = empty($v['uri']) ? url('fastswitch')->build() : $v['uri'];
                $res['js'][] = "layform.on('switch({$v['file']})', function(obj){ let v = '0'; if(obj.elem.checked){ v = '1'; } wormui.postUrl('{$_uri}',{_method:'PUT',{$pk}:this.value,field: this.name, value:v},function (res) { layer.closeAll('loading'); layer.msg(res.msg); if(res.code == '1'){ obj.del(); } }); });";
            }else if($v['type'] == 'link'){
                $uri = empty($v['uri']) ? '' : $this->EditLayuiUri($v['uri']);
                $res['link'][] = "<script type='text/html' id='{$v['file']}'><a onclick='upuri(this)' data-uri='{$uri}'>{{ d.{$v['file']} }}</a></script>";
            }else if($v['type'] == 'radio'){
                if(!empty($v['type_edit'])){
                    switch ($v['type_edit']){
                        case 'link':
                            $_v = '';
                            foreach ($v['data'] as $k1 => $v1){
                                $uri =  empty($v1['uri']) ? '' : $this->EditLayuiUri($v1['uri']);
                                $_v1open = !empty($v['open']) && $v['open'] ? true : false;
                                $_v1class = empty($v1['class']) ? '' : $v1['class'];
                                $_v1class = $_v1open ? "class='{$_v1class} cx-click'" : "class='{$_v1class}'";
                                $_v1icon = empty($v1['icon']) ? '' : "<i class='cx-icon {$v1['icon']}'></i>";
                                $_v1type =  $_v1open ? "data-type='addopen'" : "onclick='upuri(this)'";
                                $_full = !empty($v['full']) ? "data-full='y'" : '';
                                $v['title'] = empty($v1['title']) ? $v['title'] : $v1['title'];
                                $_v1text =  !empty($v['text']) && $v['text'] == true ? $_v1icon : "{$_v1icon} {$v['title']}";
                                $_opentitle = !empty($v['opentitle']) && $_v1open ? "data-title='{$v['opentitle']}'" : '';
                                $_v .= empty($_v) ? "{{# if(d.{$v['file']} == '{$k1}'){ }}<a {$_v1type} {$_v1class} {$_v1open} {$_opentitle} {$_full} data-uri='{$uri}'>{$_v1text}</a>" : "{{#  }else if(d.{$v['file']} == '{$k1}'){ }}<a {$_v1type} {$_v1class} {$_v1open} {$_opentitle} data-uri='{$uri}'>{$_v1text}</a>";
                            }
                            $_v .= "{{# } }}";
                            break;
                    }
                }else{
                    $_v = json_encode($v['data'],JSON_UNESCAPED_UNICODE);
                    $_v = "{{# let _d = {$_v}; }}{{ _d[d.{$v['file']}] }}";
                }
                $typeid = empty($v['type_edit']) ? $v['file'] : $v['file'].$v['type_edit'];
                $typeid = empty($v['file_edit']) ? $typeid : $v['file_edit'].$typeid;
                $res['link'][] = "<script type='text/html' id='{$typeid}'>{$_v}</script>";
            }else if($v['type'] == 'btn'){
                $uri = empty($v['uri']) ? '' : $this->EditLayuiUri($v['uri']);
                $icon = empty($v['icon']) ? null : "<i class='cx-icon {$v['icon']}'></i>";
                $filed_data['title'] = empty($filed_data['text']) ? $v['title'] : $v['text'];
                $_open = !empty($v['open']) && $v['open'] ? true : false;
                $class = empty($v['class']) ? "cx-button-s cx-bor-blue" : $v['class'];
                if(empty($v['open_edit'])){
                    $class = $_open ? "class='{$class} cx-click'" : "class='{$class}'";
                    $_type =  $_open ? "data-type='addopen'" : "onclick='upuri(this)'";
                }else{
                    $class = "class='{$class} cx-click'";
                    $_type = "data-type='layifuri'";
                }
                $_opentitle = !empty($v['opentitle']) && $_open ? "data-title='{$v['opentitle']}'" : '';
                $_event = !empty($v['event']) ? "lay-event='{$v['event']}'" : '';
                $_full = !empty($v['full']) ? "data-full='y'" : '';
                $_text =  !empty($v['text']) && $v['text'] == true ? $icon : "{$icon} {$v['title']}";
                if(!empty($v['event']) && $v['event'] == "del"){
                    $res['button'][] = "<script type='text/html' id='{$v['filed']}'><a data-uri='{$uri}' {$class} {$_event}>{$_text}</a></script>";
                    $_uri = empty($v['uri']) ? url('del')->build() : $this->EditLayuiUri($v['uri']);
                    $res['event'][] = " case 'del': layer.confirm('删除后无法恢复,确定删除吗?',{icon: 3, title:'温馨提示'}, function(index){ 
                wormui.postUrl('{$_uri}',{_method:'delete',{$pk}:obj.data.{$pk}},function (res) { layer.closeAll('loading'); layer.msg(res.msg); if(res.code == '1'){ obj.del(); }
                }); }); break;";
                }elseif(!empty($v['event']) && $v['event'] == "trash"){
                    $res['button'][] = "<script type='text/html' id='{$v['filed']}'><a data-uri='{$uri}' {$class} {$_event}>{$_text}</a></script>";
                    $_uri = empty($v['uri']) ? url('trashone')->build() : $this->EditLayuiUri($v['uri']);
                    $res['event'][] = " case 'trash': wormui.postUrl('{$_uri}',{{$pk}:obj.data.{$pk}},function (res) { layer.msg(res.msg); if(res.code == '1'){ obj.del(); }}); break;";
                }else{
                    $res['button'][] = "<script type='text/html' id='{$v['filed']}'><a data-uri='{$uri}' {$class} {$_type} {$_opentitle} {$_event} {$_full}>{$_text}</a></script>";
                }
            }

        }
        $res['tableedit'] = count(Common::del_file($data,'type','edit')) > '0' ? true : false;
        return $res;
    }
    protected function EditLayuiUri($uri){
        preg_match_all('/__([\w]+)__/i',$uri,$url);
        $url = count($url) >= '2' ? $url['1'] : null;
        if(empty($url)){
            return $uri;
        }
        foreach ($url as $k => $v){
            $uri = str_replace("__{$v}__","{{ d.{$v} }}",$uri);
        }
        return $uri;
    }
}