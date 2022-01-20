<?php
declare(strict_types = 1);
namespace app\common\taglib;
use app\common\wormview\Tag;
use think\facade\Cookie;
use think\facade\Request;
use think\template\TagLib;

class Wr extends TagLib
{
    use Tag;
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        'newartlist'      => ['attr' => 'name'],
        'articlelist'      => ['attr' => 'name'],
        'partlist'      => ['attr' => 'name'],
        'navlist'      => ['attr' => 'name'],
        'cont'      => ['attr' => 'name'],
        'ad'      => ['attr' => 'name'],
    ];

    /**
     * @param name=标签及模板变量名 model=调用模块 mid=要调用的模型，不填则调用所有模型 fid=要调用的栏目，不填则调用所有栏目
     * filed=要显示的字段，如果有自订义模板，此项无效 limit=要显示的内容条数，为空则显示默认值20，order=排序规则
     * @param $content
     * @return string
     */
    public function tagarticlelist($name,$content){
        if(empty($name['name'])){
            return "____请输入标签名____".$content;
        }
        if(!empty($name['fid']) && $name['fid'] == 'fid'){
            $name['fid'] = Request::param(empty($name['fid_edit']) ? 'fid' : $name['fid_edit']);
        }
        $_label = $this->EditLabel($name,'articlelist');
        $_resname = $this->autoBuildVar($name['name']);
        $_label['vname'] = empty($_label['data']['title']) ? $_label['data']['TagName'] : $_label['data']['title'];
        //  获取渲染参数
        $_title_num = empty($_label['data']['conf']['title_num']) ? '0' : $_label['data']['conf']['title_num'];
        $_description_num = empty($_label['data']['conf']['description_num']) ? '0' : $_label['data']['conf']['description_num'];
        $_jian = empty($_label['data']['conf']['jian']) ? '0' : $_label['data']['conf']['jian'];
        $_picurl = empty($_label['data']['conf']['picurl']) ? '0' : $_label['data']['conf']['picurl'];
        //   渲染模板
        $_content = empty($_label['data']['template']) ? $content : " {volist name='{$_label['data']['TagName']}' id='res' empty=''}{$_label['data']['template']}{/volist}<?php  /** {$content} **/ ?>";
        if(isset($_label['data']['temptype']) && $_label['data']['temptype'] == '0' && !empty($_label['data']['template'])){
            $_resname = '$_LIST_';
            $_content = empty($_label['data']['status']) ? "<?php  /** {$content}  **/  ?>" : "{$_label['data']['template']} <?php  /** {$content}  **/  ?>";
        }
        $_parse = " {$_resname} =  WORMCMS('Tag@GetArticleList',['model' => '{$_label['data']['TagModel']}','mid' => '{$_label['data']['TagMid']}','fid' => '{$_label['data']['TagFid']}','getFile' => '{$_label['data']['TagFiled']}','title_num' => '{$_title_num}','description_num' => '{$_description_num}','jian' => '{$_jian}','picurl' => '{$_picurl}','limit' => '{$_label['data']['TagLimit']}','order' => '{$_label['data']['TagOrder']}','setname' => '{$_label['name']}']);";
        if(!empty($_label['data']['temptype']) && $_label['data']['temptype'] == '1' && !empty($_label['data']['template'])){
            $_parse .= " {$_resname} = {$_resname}['data'];  ";
        }
        $_parse = empty($_label['data']['status']) ?  " {$_resname} = [];" : $_parse;

        return $this->resView($_parse,$_content,$_label);
    }
    /**
     * @param name=标签及模板变量名 model=调用模块 mid=要调用的模型，不填则调用所有模型 fid=要调用的栏目，不填则调用所有栏目
     * @param $content
     * @return string
     */
    public function tagpartlist($name,$content){
        if(empty($name['name'])){
            return "____请输入标签名____".$content;
        }
        $_label = $this->EditLabel($name,'partlist');
        $_resname = $this->autoBuildVar($name['name']);
        $_label['vname'] = empty($_label['data']['title']) ? $_label['data']['TagName'] : $_label['data']['title'];
        //   渲染模板
        $_content = empty($_label['data']['template']) ? $content : " {volist name='{$_label['data']['TagName']}' id='res' empty=''}{$_label['data']['template']}{/volist}<?php  /** {$content} **/ ?>";
        if(isset($_label['data']['temptype']) && $_label['data']['temptype'] == '0' && !empty($_label['data']['template'])){
            $_resname = '$_LIST_';
            $_content = empty($_label['data']['status']) ? "<?php  /** {$content}  **/  ?>" : "{$_label['data']['template']} <?php  /** {$content}  **/  ?>";
        }
        $_parse = " {$_resname} =  WORMCMS('Tag@GetPartList',['model' => '{$_label['data']['TagModel']}','mid' => '{$_label['data']['TagMid']}','id' => '{$_label['data']['TagFid']}','setname' => '{$_label['name']}']);";
        $_parse = empty($_label['data']['status']) ?  " {$_resname} = [];" : $_parse;

        return $this->resView($_parse,$_content,$_label);
    }
    /**
     * @param name=标签及模板变量名 cid=要调用的导航分类，不填则调用所有分类
     * @param $content
     * @return string
     */
    public function tagnavlist($name,$content){
        if(empty($name['name'])){
            return "____请输入标签名____".$content;
        }
        $name['model'] = '@nav@';
        $name['mid'] = empty($name['cid']) ? '' : $name['cid'];
        $_label = $this->EditLabel($name,'navlist');
        $_resname = $this->autoBuildVar($name['name']);
        $_label['vname'] = empty($_label['data']['title']) ? $_label['data']['TagName'] : $_label['data']['title'];
        $_content = empty($_label['data']['template']) ? $content : " {volist name='{$_label['data']['TagName']}' id='res' empty=''}{$_label['data']['template']}{/volist}<?php  /** {$content} **/ ?>";
        if(isset($_label['data']['temptype']) && $_label['data']['temptype'] == '0' && !empty($_label['data']['template'])){
            $_resname = '$_LIST_';
            $_content = empty($_label['data']['status']) ? "<?php  /** {$content}  **/  ?>" : " {$_label['data']['template']} <?php  /** {$content}  **/  ?>";
        }
        $_parse = " {$_resname} = WORMCMS('Tag@GetNavList',['cid' => '{$_label['data']['TagMid']}','id' => '{$_label['data']['TagFid']}','setname' => '{$_label['name']}']);";
        $_parse = empty($_label['data']['status']) ?  " {$_resname} = [];" : $_parse;
        return $this->resView($_parse,$_content,$_label);
    }

    /**
     * @param $name  name=标签及模板变量名
     * @param $content
     * @return string
     */
    public function tagcont($name,$content){
        if(empty($name['name'])){
            return "____请输入标签名____".$content;
        }
        $name['model'] = '@txt@';
        $_label = $this->EditLabel($name,'htmlcont');
        $_resname = $this->autoBuildVar($name['name']);
        $_label['vname'] = empty($_label['data']['title']) ? $_label['data']['TagName'] : $_label['data']['title'];
        $_content = empty($_label['data']['template']) ? $content : " {$_label['data']['template']}<?php  /** {$content} **/ ?>";
        $_parse = " {$_resname} = WORMCMS('Tag@GetCont',['setname' => '{$_label['name']}']);";
        $_parse = empty($_label['data']['status']) ?  " {$_resname} = '';" : $_parse;
        $_content = empty($_label['data']['status']) ?  " <?php  /** {$content} **/ ?>" : $_content;
        return $this->resView($_parse,$_content,$_label);
    }
    public function tagad($name,$content){
        if(empty($name['name'])){
            return "____请输入标签名____".$content;
        }
        $name['model'] = '@ad@';
        $name['fid'] =  empty($name['id']) ? '' : $name['id'];
        $_label = $this->EditLabel($name,'advertising');
        $_resname = $this->autoBuildVar($name['name']);
        $_label['vname'] = empty($_label['data']['title']) ? $_label['data']['TagName'] : $_label['data']['title'];
        $_content = empty($_label['data']['template']) ? $content : " {$_label['data']['template']}<?php  /** {$content} **/ ?>";
        if(!empty($_label['data']['template'])){
            $_resname = $_label['data']['TagMid'] == '2' ? '$_LIST_' : '$res';
        }
        $_parse = " {$_resname} = WORMCMS('Tag@GetAdvertising',['id' => '{$_label['data']['TagFid']}','setname' => '{$_label['name']}']);";
        $_parse = empty($_label['data']['status']) ?  " {$_resname} = '';" : $_parse;
        $_content = empty($_label['data']['status']) ?  " <?php  /** {$content} **/ ?>" : $_content;
        return $this->resView($_parse,$_content,$_label);
    }
    /**
     * @param $resview  请求方法
     * @param $content  显示内容
     * @param $label    标签信息
     * @return string 输出到页面
     */
    protected function resView($resview,$content,$label){
        $parse = '<?php ';
        $parse .= " {$resview}";
        $parse .= ' ?>';
        if(session('_admin_') && session('_admin_.group_type') == '0' &&  session('_admin_.group_admin') == '1' && Cookie::get("label")){
            $_uid = session('_admin_.uid');
            $_uri = url('admin/wormtag/homeedit',empty($label['data']['id']) ? ['name' => $label['name'],'t' => $label['tempname']] : ['id' => $label['data']['id']])->build();
            $label = " {eq name=\"wormuser.uid|default='0'\" value=\"{$_uid}\"}<div class='layout cx-pos-r'><div class='layout cx-pos-a cx-fex-c' style='top：0;z-index: 19841220'><div data-title='【{$label['vname']}】' data-uri='{$_uri}' class='cx-button-s cx-bg-yellow cx-label' data-type='editlabel'>编辑【{$label['vname']}】</div></div></div>{/eq}";
            $parse .= $label;
        }
        $parse .= $content;
        return $parse;
    }

    /**
     * 格式化标签信息
     * @param $data
     * @param $_fun
     * @return mixed
     */
    protected function EditLabel($data,$_fun){
        $_tagdata = [
            'TagName' => $data['name'],
            'TagModel' => empty($data['model']) ? 'cms' : $data['model'],
            'TagMid' => empty($data['mid']) ? '0' : $data['mid'],
            'TagFid' => empty($data['fid']) ? '0' : $data['fid'],
            'TagFiled' => empty($data['filed']) ? '0' : $data['filed'],
            'TagLimit' => empty($data['limit']) ? '0' : $data['limit'],
            'TagOrder' => empty($data['order']) ? '0' : $data['order'],
            'conf' => [
                'jian' => empty($data['jian']) ? '0' : $data['jian'],
                'picurl' => empty($data['picurl']) ? '0' : $data['picurl'],
                'description_num' => empty($data['description_num']) ? '0' : $data['description_num'],
                'title_num' => empty($data['title_num']) ? '0' : $data['title_num'],
            ]
        ];
        //  检测是否为标签
        $_label = $this->GetTagRead($_tagdata,$_fun,$this->tpl->get('LABEL'));
        $_label['data'] = array_merge($_tagdata,$_label['data']);
        $_label['data']['TagLimit'] = empty($_label['data']['TagLimit']) ? $_tagdata['TagLimit'] : $_label['data']['TagLimit'];
        $_label['data']['TagFid'] = empty($_label['data']['TagFid']) ? $_tagdata['TagFid'] : $_label['data']['TagFid'];
        $_label['data']['TagMid'] = empty($_label['data']['TagMid']) ? $_tagdata['TagMid'] : $_label['data']['TagMid'];
        return $_label;
    }

}