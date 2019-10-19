<?php
/**
 * Created by PhpStorm.
 * User: Shinelon
 * Date: 2019/10/6
 * Time: 18:03
 */

namespace app\index\controller;

use think\Db;
use think\facade\Request;
use think\facade\Session;

class Audit extends Authc
{

//    显示首页右下待办事项，双击核查正常流程页面
    public function double_review()
    {
        echo $this->view->fetch('index/index/double_review');
    }

//    审计复核流程：根据todo表nkid获得reviews的数据
    public function get_review_date()
    {
        $data = Request::post('nkid');
        $user = Db::name('review');
        $result = $user->where('nkid', trim($data))->find();
        echo json_encode($result);
    }

//    显示审计复核的附件
    public function show_files_list()
    {
        $nkid = Request::post('nkid');
        $pro_kid = Request::post('pro_kid');
        $map = array(
            'nkid' => intval($nkid),
            'type' => '审计结论',
            'pro_kid' => $pro_kid
        );
        $user = Db::name('files');
        $result = $user->where($map)->field('files_old_name,files_add')->select();
        echo json_encode($result);
    }

//    =============以上是项目首页的部分需要接口

//    MENU=>“审计管理”=》“进行审计跟踪项目”
    public function showtrail()
    {
      echo  $this->view->fetch('index/audit/auditTrail');
    }

//   进行审计跟踪项目=》 显示项目列表
    public function showproject()
    {
//        $getsearch = I('post.pro');
        $getsearch = Request::post('pro');
        $search = [];
        if (isset($getsearch) && !empty($getsearch)) {
            $search[] = ['project_name','like', '%'.$getsearch .'%'];
        }
        $orgname = Session::get('user.orgname') ;
        $position = Session::get('user.position') ;
        if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {

            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
            $types = ['审计报告','审计建议书','移送处理书','审计决定'];

            $count = Db::name('projectlist')
                ->where($search)
                ->where('aduit_track','是')
                ->count();
            if($count){
                $datalist = Db::name('projectlist')
                    ->where($search)
                    ->where('aduit_track','是')
                    ->page($page,$pageSize)
                    ->select();
                foreach($datalist as $i=>$v){
                    $datalist[$i]['fil'] = Db::name('files')
                        ->where('pro_kid','=',$v['kid'])
                        ->where('type','in',$types)
                        ->select();
                }
                echo '{"total":' . $count . ',"rows":' . json_encode($datalist) . '}';
            }else{
                echo [];
            }

        } else{
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
            $types = ['审计报告','审计建议书','移送处理书','审计决定'];

            $count = Db::name('projectlist')
                ->where($search)
                ->where('aduit_track','是')
                ->where('sjuid','=',session('user.uid'))
                ->count();
            if($count){
                $datalist = Db::name('projectlist')
                    ->where($search)
                    ->where('aduit_track','是')
                    ->where('sjuid','=',session('user.uid'))
                    ->page($page,$pageSize)
                    ->select();
                foreach($datalist as $i=>$v){
                    $datalist[$i]['fil'] = Db::name('files')
                        ->where('pro_kid','=',$v['kid'])
                        ->where('type','in',$types)
                        ->select();
                }
                echo '{"total":' . $count . ',"rows":' . json_encode($datalist) . '}';
            }else{
                echo [];
            }
        }

    }

//    进行审计跟踪羡慕=》双击显示（可修改页面）
    public function show_auditParticular()
    {
        $kid = Request::get('kid');
        $this->assign('kid',$kid);
        echo $this->view->fetch('index/audit/auditParticulars');
    }

//    进行审计跟踪项目=》双击页面（可修改页面） 显示建设单位的列表
    public function show_sjdwlist()
    {
        $data = Db::name('sjperson')
            ->where('orgname','=', '建设单位')
            ->select();
        echo json_encode($data);
    }


//   进行审计跟踪项目=》双击页面（可修改页面） 显示项目类别列表
    public function show_xmlb()
    {
        $data = Db::name('xmlb')
            ->where('parentid=0')
            ->select();
        $json = json_encode($data);
        echo $json;
    }

//     进行审计跟踪项目=》双击页面（可修改页面） 显示项目列表二级目录
    public function show_xmlb2()
    {
        $data = Request::get('id');
        $json = Db::name('xmlb')
            ->where('parentid',$data)
            ->select();
        echo json_encode($json);
    }

//    进行审计跟踪项目=》双击页面（可修改页面） 显示审计类型
    public function show_type()
    {
        $data =  Db::name('aduittype')->select();
        $json = json_encode($data);
        echo $json;
    }

//    进行审计跟踪项目=》双击页面（可修改页面） 显示审计跟踪小组
    public function show_depart()
    {
        $data = Db::name('sjgroup')->field('listname')->select();
        $json = json_encode($data);
        echo $json;
    }

//    进行审计跟踪项目=》双击页面（可修改页面） 显示审计跟踪小组成员
    public function show_leader()
    {
        $getdata = Request::get('listname');
        $data = Db::name('sjperson')
            ->where('group',$getdata)
            ->Field('fullname,uid')
            ->select();
        echo json_encode($data);
    }

//        进行审计跟踪项目=》双击页面（可修改页面）  取得审计项目编号
    public function get_sjjnum()
    {
        $year = Date('Y');
        $has_num = Db::name('project_num')->max('num');
        if (!$has_num) {
            $data = ['num'=>1];
            Db::name('project_num')->insert($data);
            echo 'TZSJ' . $year . '0001';
        } else {
            $pad = str_pad(($has_num + 1), 4, 0, STR_PAD_LEFT);
            $data = ['num' => $has_num + 1];
            Db::name('project_num')->insert($data);
            echo 'TZSJ' . $year . $pad;
        }
    }

//       进行审计跟踪项目=》双击页面（可修改页面）      新增项目保存
    public function save_proform()
    {
        $data = Request::post();
        $data['year'] = date('Y');
        $data['dw_name'] = $data['dwname'];
        $find = Db::name('projectlist')
            ->where('project_name',$data['project_name'])
            ->find();
        if($find){
            echo -1;
        }else{
            $result = Db::name('projectlist')
                ->insert($data);
            echo $result;
        }
    }

//       进行审计跟踪项目=》双击页面（可修改页面）      双击项目修改
    public function edit_proform()
    {
        $data = Request::post();
        $find = Db::name('projectlist')
            ->where('id','<>',intval($data['id']))
            ->where('project_name',$data['project_name'])
            ->find();
        if($find){
            echo -1;
        }else{
            $result = Db::name('projectlist')
                ->where('kid',$data['kid'])
                ->update($data);
            echo $result;
        }
    }

//    尽心审计跟踪项目=》 双击页面（只读页面）
    public function show_auditParticularswatch()
    {
        echo $this->view->fetch('index/audit/auditParticularswatch');
    }


//  进行审计跟踪项目=》点击按钮“点击进入” 查找该项目数据
    public function pro_data()
    {
        $data = Request::post('kid');
        $prodata = Db::name('projectlist')
            ->where('kid',$data)
            ->find();
        echo json_encode($prodata);
    }

//    进行审计跟踪项目=》点击进入=》显示只读页面
    public function show_auditImplementwatch()
    {
        echo $this->view->fetch('index/audit/auditImplementwatch');
    }

//   进行审计跟踪项目=》点击进入=》（只读页面）审计方案附件
    public function show_sjfa_files()
    {
        $data =Request::post();

//        $where = [
//            ['kid',$data['kid']],
//            ['type',$data['type']]
//        ];
//
        $result = Db::name('files')
            ->where('kid',$data['kid'])
            ->where('type',$data['type'])
            ->field('files_old_name,files_add')
            ->select();
        echo json_encode($result);
    }
}