<?php
/**
 * Created by PhpStorm.
 * User: Shinelon
 * Date: 2019/9/29
 * Time: 17:28
 */

namespace app\index\controller;

use think\Db;
use think\facade\Request;

class Underway extends Authc
{

    //    双击首页左侧综合信息，显示综合信息详情(项目-建设单位)
    public function show_index_xmzhxx()
    {
        echo $this->view->fetch('index/index/xmzhxx');
    }


    // 综合信息获取资金到位总计、资金支付总计（已完成）
    public function get_zjdwdata()
    {
        $data = Request::post('kid');
        $where = array('kid' => $data);
        $user_zjdz = Db::name('zjdz');//连接资金到账表
        $count_zjje = $user_zjdz->where($where)->sum('zjje');
        $user_htzf = Db::name('htzf');
        $count_htzf = $user_htzf->where($where)->sum('fkje');
        $user_glf = Db::name('zjglf');
        $count_gz = $user_glf->where($where)->sum('gz');
        $count_bgf = $user_glf->where($where)->sum('bgf');
        $count_bgcd = $user_glf->where($where)->sum('bgcd');
        $count_cljtf = $user_glf->where($where)->sum('cljtf');
        $count_ldbhf = $user_glf->where($where)->sum('ldbhf');
        $count_gjyjsyf = $user_glf->where($where)->sum('gjyjsyf');
        $count_gdzcsyf = $user_glf->where($where)->sum('gdzcsyf');
        $count_zmscgrf = $user_glf->where($where)->sum('zmscgrf');
        $count_jstszlf = $user_glf->where($where)->sum('jstszlf');
        $count_zdf = $user_glf->where($where)->sum('zdf');
        $count_sgbt = $user_glf->where($where)->sum('sgbt');
        $count_jgysf = $user_glf->where($where)->sum('jgysf');
        $count_qtfy = $user_glf->where($where)->sum('qtfy');
        $zf_sum = round(floatval($count_htzf) + floatval($count_gz) + floatval($count_bgf) + floatval($count_bgcd) + floatval($count_cljtf)
            + floatval($count_ldbhf) + floatval($count_gjyjsyf) + floatval($count_gdzcsyf) + floatval($count_zmscgrf) + floatval($count_jstszlf)
            + floatval($count_zdf) + floatval($count_sgbt) + floatval($count_jgysf) + floatval($count_qtfy), 2);
        $result = array(
            'inPlaces' => $count_zjje,
            'pays' => $zf_sum
        );
        echo json_encode($result);
    }

//      获得审批页面的状态值(已完成)
    public function spjd_state()
    {
//        $data = I('post.kid');
        $data = Request::post('kid');
        $where = array('kid' => $data);
        $user = Db::name('spjd_state');
        $result = $user->where($where)->find();
        echo json_encode($result);
    }

    //项目综合信息，获取图片（已完成）
    public function get_picdata()
    {
        $data = Request::post('kid');
        $where = array(
            'pro_kid' => $data,
            'type' => '现场审计'
        );
        $user = Db::name('files');
        $result = $user->where($where)->field('files_add')->select();
        echo json_encode($result);
    }

    //项目综合信息图片显示页面（已完成）
    public function show_index_pic()
    {
        echo $this->view->fetch('index/index/dispicture');
    }

//    显示远程视频页面（已完成）
    public function remote_video()
    {
        echo $this->view->fetch('index/xmgl/disvideo');
    }

//    显示远程视频页面（已完成）
    public function remote_video_eqh()
    {
        echo $this->view->fetch('index/xmgl/disvideoeqh');
    }

    //项目综合信息，获取3D模型图片（已完成）
    public function get_3Ddata()
    {
//        $data = I('post.kid');
        $data = Request::post('kid');
        $where = array(
            'pro_kid' => $data,
            'type' => '3D模型'
        );
        $user = Db::name('files');
        $result = $user->where($where)->field('files_add')->select();
        echo json_encode($result);
    }

    //项目综合信息，3D模型图片显示页面（已完成）
    public function show_index_3D()
    {
        echo $this->view->fetch('index/index/dis3D');
    }

//    综合信息查询，显示项目审计文书（已完成）
    public function get_writlist()
    {
//        获取全部参数
        $get_data = Request::param();
        $search_name = [
            'kid' => $get_data['project_kid'],
            'status' => 1
        ];
        $model = Db::name('writ');
        $page = isset($get_data['page']) ? intval($get_data['page']) : 1;
        $pageSize = isset($get_data['rows']) ? intval($get_data['rows']) : 10;
        $count = $model->where($search_name)->count();
        if ($count) {
            $datalist = $model
                ->where($search_name)
                ->page($page, $pageSize)
                ->order('id', 'asc')
                ->select();

            foreach ($datalist as $i => $v) {
                $datalist[$i]['fil'] = Db::name('files')
                    ->where('kid', '=', $v['nkid'])
                    ->field('kid,files_old_name,files_add,type,nkid,pro_kid')
                    ->select();
            }
            echo '{"total":' . $count . ',"rows":' . json_encode($datalist) . '}';
        } else {
            echo '{"total":0,"rows":' . json_encode([]) . '}';;
        }
    }

//    项目工作进度 只读页面。
    public function show_gzjdread(){
//        $data = I('get.kid');
        $data = Request::get('kid');
        $user = Db::name('prounderway');
        $insert = ['kid'=>$data];
        $find = Db::name('prounderway')
            ->where('kid',$data)
            ->find();
        if(!$find){
            $user->insert($insert);
        }
        $map['kid'] = $data;
        $map['pro_name'] = $find['pro_name'];
        $map['aduit_num'] = $find['aduit_num'];
        $map['dw_fullname'] = $find['dw_fullname'];
        $this->assign('pro_data',$map);
        $this->display('xmgl/gzjdread');
    }

//   进行审计跟踪项目=》点击进入，基本信息，点击“项目信息详情”， 根据kid查找项目综合信息数据
    public function get_pro_data(){
        $data = Request::post('kid');
        $result = Db::name('prounderway')
            ->where('kid',$data)
            ->find();
        if($result){
//            '{"total":' . $count . ',"rows":' . json_encode($datalist) . '}';
            echo '{"code":200,"data":'.json_encode($result).'}';
        }else{
            echo '{"code":-1,"data":0}';
        }
    }

//    进行审计跟踪项目=》点击进入=》项目详情页面
    public function show_zhxx(){
       echo $this->view->fetch('xmgl/zhxx');
    }

}