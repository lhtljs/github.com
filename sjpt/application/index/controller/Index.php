<?php
namespace app\index\controller;

use think\Db;
use think\facade\Request;
use think\Controller;
use think\facade\Session;


class Index extends Controller
{
//    首页=》显示登录页
    public function index()
    {
        return $this->view->fetch('index/login/login');

    }

//    首页=》注销用户
    public function logout()
    {
        if (Session::has('user')) {
            Session::delete('user');
            return 1;
        } else {
            return 1;
        }
    }

//    首页=》修改密码
    public function checkpwd()
    {
        $get_data = Request::post();

        $find = Db::name('user')->where('id', intval($get_data['userid']))->find();

        if ($find['password'] != sha1(trim($get_data['oldpwd']))) {
            return -1;
        } else {
            $result = Db::name('user')->where('id', intval($get_data['userid']))->setField('password', sha1(trim($get_data['newpwd'])));
            return $result;
        }
    }


//  首页=》  用户登录，1,使用依赖注入。2,使用门脸技术
    public function log()
    {
        $data = Request::param();
        $where = [
            'username' => $data['username'],
            'password' => sha1($data['password'])
        ];
        $find = Db::name('user')->where($where)->find();

        if ($find) {
            session(null); //登录前清理所有session
            $user_data = Db::name('sjperson')->where('username', $find['username'])->find();
            cookie('username12', $data['username'], 360000); // 增加登录成功后cookie
            session('user', [
                'id' => $find['id'],
                'orgname' => $user_data['orgname'],
                'fullname' => $user_data['fullname'],
                'depart' => $user_data['depart'],
                'position' => $user_data['position'],
                'group' => $user_data['group'],
                'dw_name' => $user_data['dw_name'],
                'fzr' => $user_data['fzr'],
                'user' => $find['username'],
                'uid' => md5($find['username'])
            ]);
            return 1;
        } else {
            return -1;
        }
    }


    //    首页=》显示到首页
    public function showhome()
    {
        if (session('user.fullname')) {
            return $this->view->fetch('index/index/home');
        } else {
            return $this->view->fetch('index/login/login');
        }
    }


//    首页=》自己先写一个获取菜单的接口。使用依赖注入方式（已经完成，菜单表需要修改）
    public function get_menu(\think\auth\Auth $auth, \app\extend\PHPTree $PHPTree)
    {
        $get_group = $auth->getGroups(Session::get('user.id'));

        if (!empty($get_group)) {
            if ($get_group[0]['short'] == 'JSDW') {
                $menu_data = Db::name('menue')
                    ->where('FIND_IN_SET(:rule,rules)', ['rule' => $get_group[0]['short']])
                    ->order('ord asc')
                    ->where('text', '<>', '坐标取拾')
                    ->select();
            } else {
                $menu_data = Db::name('menue')
                    ->where('FIND_IN_SET(:rule,rules)', ['rule' => $get_group[0]['short']])
                    ->order('ord asc')
                    ->select();
            }

            foreach ($menu_data as $i => $v) {
                $menu_data[$i]['parent_id'] = $menu_data[$i]['pid'];
                unset($menu_data[$i]['pid']);
            }
            $treedata = $PHPTree::makeTree($menu_data);

            $orgname = session('user.orgname');
            $position = session('user.position');
            if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {
                $treedata[3]['children'][2] = [
                    'text' => '远程审计跟踪项目',
                    'state' => 'open',
                    't_url' => '/sjjdpt/index/yun/show_sjgzxm?dbname=yundb1&hostname=yundb1host',
                    'iconCls' => 'icon-nva_sjgz'
                ];
                $treedata[4]['children'][3] = [
                    'text' => '远程项目列表（所有项目）',
                    'state' => 'open',
                    't_url' => '/sjjdpt/index/yun/show_xmlbjxz.html?dbname=yundb1&hostname=yundb1host',
                    'iconCls' => 'icon-nva_xmlb'
                ];
            }
            return json_encode($treedata);
        } else {
            return json_encode([]);
        }
    }

//    首页=》显示首页工作通知详情，在JS中添加href需要使用echo输出
    public function show_gztzinfo()
    {
        echo $this->view->fetch('index/gztz/gztzinfo');
    }

//    首页=》显示工作通知数据（显示和双击已经完成）
    public function show_gztzlist()
    {
        $getdata = Request::post();
        $search = [];
        if (isset($getdata['pro']) && !empty($getdata['pro'])) {
            $search[] = ['tznr', 'like', '%' . $getdata . '%'];
        }
//        $model = Db::name('gztz');
        $page = isset($getdata['page']) ? intval($getdata['page']) : 1;
        $pageSize = isset($getdata['rows']) ? intval($getdata['rows']) : 10;
        $count = Db::name('gztz')->where($search)->count();
        if ($count) {
            $datalist = Db::name('gztz')
                ->where($search)
                ->page($page, $pageSize)
                ->order('id asc')
                ->select();
            foreach ($datalist as $i => $v) {
                $datalist[$i]['fil'] = Db::name('files')
                    ->where('kid', $v['kid'])
                    ->field('files_old_name,files_add,type,nkid,kid')
                    ->select();
            }
            echo '{"total":' . $count . ',"rows":' . json_encode($datalist) . '}';
        } else {
            echo [];
        }
    }

//    首页=》显示首页综合信息页面(已完成)
    public function show_zhxxlist()
    {
        $orgname = session('user.orgname');
        $position = session('user.position');
//        $where = array('jsdw_ok'=>1);
        if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {
            $count = Db::name('prounderway')->where('jsdw_ok', 1)->count();
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
            $map = Db::name('prounderway')->page($page, $pageSize)->where('jsdw_ok', 1)->select();
            $json = '{"total":' . $count . ',"rows":' . json_encode($map) . '}';
            echo $json;
        } else {
//            $rule['sjuid|dwuid'] = array('eq',session('user')['uid']);
            $count = Db::name('prounderway')->where('sjuid|dwuid','=',session('user.uid'))->where('jsdw_ok', 1)->count();
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $pageSize = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
            $map = Db::name('prounderway')->page($page, $pageSize)->where('jsdw_ok', 1)->where('sjuid|dwuid','=',session('user.uid'))->select();
            $json = '{"total":' . $count . ',"rows":' . json_encode($map) . '}';
            echo $json;
        }
    }

//    首页=》获取首页当前年下的投资总数、进行审计跟踪总数、未进行审计跟踪总数及审计文书总数(已完成)
    public function get_index_data()
    {
        $orgname = session('user.orgname');
        $position = session('user.position');
        if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {
            $tzzs = Db::name('projectlist')->count();
            $gzzs = Db::name('projectlist')->where("aduit_track= '是'")->count();
            $wgzzs = Db::name('projectlist')->where("aduit_track= '否'")->count();
            $wssl = Db::name('writ')->count();
            $result = array($tzzs, $gzzs, $wgzzs, $wssl);
            echo json_encode($result);
        } else {
            $tzzs = Db::name('projectlist')->where('sjuid|dwuid','=',session('user.uid'))->count();
            $gzzs = Db::name('projectlist')->where('sjuid|dwuid','=',session('user.uid'))->where("aduit_track= '是'")->count();
            $wgzzs = Db::name('projectlist')->where('sjuid|dwuid','=',session('user.uid'))->where("aduit_track= '否'")->count();

            $wssl =  Db::name('writ')->where('sjuid|dwuid','=',session('user.uid'))->count();
            $result = array($tzzs, $gzzs, $wgzzs, $wssl);
            echo json_encode($result);
        }
    }

//   首页=》综合信息页面， 获取远程视频（已完成）
    public function get_camVideo()
    {
        $data = Request::post('kid');
        $where = array('kid' => $data);
        $user = Db::name('projectlist');
        $result = $user->where($where)->field('video_ip,video_user,video_pass,video_num')->find();
        echo json_encode($result);
    }

//   首页=》 显示待办事项数据，根据接收人判别谁可以看(已完成)
    public function show_todo()
    {
        $request = Request::post();
        $search = [
            'recuid' => session('user.uid'),
            'read' => 0
        ];
        $user = Db::name('todo');
        $count = $user->where($search)->count();
        $page = isset($request['page']) ? intval($request['page']) : 1;
        $pageSize = isset($request['rows']) ? intval($request['rows']) : 10;
        $map = $user->where($search)->page($page, $pageSize)->order('xdtime desc')->select();
        $json = '{"total":' . $count . ',"rows":' . json_encode($map) . '}';
        echo $json;
    }

//    双击待办事项，显示核查下达页面(做到这里，没有做完！！！！！！)
    public function show_hecha()
    {
        echo $this->view->fetch('index/index/double_todo');
    }



//    以下为GIS地图页面及逻辑。

//    检测远程数据库是否连接。没有连接则不取数据。
    public function checkyun()
    {
        try{
            Db::connect('db_config1')->table('sjj_user')->find(1);
        }catch(\Exception $e){
            return 0;
        }
        return 1;
    }

//    首页=》显示gis地图页面(已完成)
    public function showgis()
    {
        echo $this->view->fetch('index/BmapProject/Bgis');
    }

//    显示gis地图坐标，取坐标值用的页面（未完成！！！！！！！！！！！！！！！）
    public function giszb()
    {
        $this->display('BmapProject/Bgiszb');
    }

//    首页=》GIS地图左侧列表，（已完成）
    public function getleftgis()
    {
        $orgname = Session::get('user.orgname');
        $position = Session::get('user.position');
        if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {
            $kids = Db::name('xmzb')->distinct(true)->where("sfhx='是'")->column('kid');
            if (!empty($kids)) {
                $where_way[] = ['kid', 'in', $kids];
                $where_way[] = ['jsdw_ok', '=', 1];
                $proname = Db::name('prounderway')->where($where_way)->field('kid,pro_name')->select();

                $where_sin[] = ['kid', 'in', $kids];
                $sin = Db::name('singleproject')->where($where_sin)->field('kid,count(kid) as count')->group('kid')->column('count(kid) as count', 'kid');

                foreach ($proname as &$v) {
                    $sn = isset($sin[$v['kid']]) ? $sin[$v['kid']] : 0;
                    $v['count'] = $sn;
                }
//          连接远程（这里只有矿区审计局）！！！！！！！！！！！！！！！！！
                if ($this->checkyun()) {
                    $yundata = $this->getyunleftgis();
                    if ($yundata) {
                        $proname = array_merge($proname, $yundata);
                    }
                }
//          后面如果有其他远程服务器加入，则写在这里……！！！！！！！！
                echo json_encode($proname);
            } else {
                echo json_encode(0);
            }
        } else {
            $kids = Db::name('xmzb')->distinct(true)->where("sfhx='是'")->column('kid');
            if (!empty($kids)) {
                $where_way[] = ['kid', 'in', $kids];
                $where_way[] = ['jsdw_ok', '=', 1];
                $fullname = Session::get('user.fullname');
                $proname = Db::name('prounderway')->where($where_way)->where('dw_fullname|aduit_leader', 'like', '%' . $fullname . '%')->field('kid,pro_name')->select();

                $where_sin[] = ['kid', 'in', $kids];
                $sin = Db::name('singleproject')->where($where_sin)->field('kid,count(kid) as count')->group('kid')->column('count(kid) as count', 'kid');
                foreach ($proname as &$v) {
                    $sn = isset($sin[$v['kid']]) ? $sin[$v['kid']] : 0;
                    $v['count'] = $sn;
                }
                echo json_encode($proname);
            } else {
                echo json_encode(0);
            }
        }
    }

//    获取远程（这里是矿区审计局），左侧数据列表。（已完成）
    protected function getyunleftgis()
    {
        $kids = Db::connect('db_config1')->table('sjj_xmzb')->distinct(true)->where("sfhx='是'")->column('kid');
        if (!empty($kids)) {
            $where_way[] = ['kid', 'in', $kids];
            $where_way[] = ['jsdw_ok', '=', 1];
            $proname = Db::connect('db_config1')->table('sjj_prounderway')->where($where_way)->field('kid,pro_name')->select();

            $where_sin[] = ['kid', 'in', $kids];
            $sin = Db::connect('db_config1')->table('sjj_singleproject')->where($where_sin)->field('kid,count(kid) as count')->group('kid')->column('count(kid) as count', 'kid');

            foreach ($proname as &$v) {
                $sn = isset($sin[$v['kid']]) ? $sin[$v['kid']] : 0;
                $v['count'] = $sn;
                $v['from'] = '矿区审计局';//可以用邮编代替！！！
            }
            return $proname;
        } else {
            return false;
        }
    }

//    首页=》gis地图抛洒坐标，当前服务器数据获取已完成。(页面JS需要重写！！！！！！！！！！！！)
    public function getgisdata()
    {
        $orgname = Session::get('user.orgname');
        $position = Session::get('user.position');
        if (($orgname == '审计局' && ($position == '科长' || $position == '分管领导')) || $orgname == '市领导') {
            $kids = Db::name('xmzb')->distinct(true)->where("sfhx='是'")->field('kid,jwd')->select();
            $test = [];
            foreach ($kids as $k) {
                $where = [
                    ['kid', 'in', $k['kid']],
                    ['jsdw_ok', '=', 1]
                ];
                $test[] = Db::name('prounderway')
                    ->where($where)
                    ->field('kid,pro_name,dw_name,aduit_num,pro_category,pro_cost,pro_time')->find();
            }
//          获取本地数据库，各项数据
            $getdata = $this->testgis($test, $kids);
//          连接远程数据库、先判断远程是否能连接上。（已完成）
            if($this->checkyun()){
                $yundata = $this->getyungis();
                if($yundata){
                    $getdata = array_merge($getdata,$yundata);
                }
            }
            echo json_encode($getdata);
        } else {
//            根据用户uid 在prounderway表中获取项目kids集合
            $can_kids = Db::name('prounderway')
                ->where('sjuid|dwuid','like',session('user.uid'))
                ->where('jsdw_ok','=',1)
                ->column('kid');
//            根据用户权限kids集合取得xmzb表中，实际的kid和jwd数据
            $kids = Db::name('xmzb')
                ->distinct(true)
                ->where('sfhx','=','是')
                ->field('kid,jwd')
                ->select();
            $test = [];
            foreach($kids as $k){
                $test[] = Db::name('prounderway')
                    ->where('kid','=',$k['kid'])
                    ->field('kid,pro_name,dw_name,aduit_num,pro_category,pro_cost,pro_time')
                    ->find();
            }
            $getdata = $this->testgis($test,$kids);
            echo json_encode($getdata);
        }
    }

//    获取本服务器数据库GIS的数据。特别长的语句，从多个表中获取数据。（已完成，需要重写前台！！！！！！！！！）
    private function testgis($test, $kids)
    {
        $tmp = [];
        foreach ($kids as $c) {
            $tmp[$c['kid']] = $c['jwd'];
        }
        foreach ($test as &$v) {
            $sn = isset($tmp[$v['kid']]) ? $tmp[$v['kid']] : '';
            $v['jwd'] = $sn;
        }

//        项目建设状态（已完成）
        $state = [];
        foreach ($kids as $a) {
//            $where[] = ['xmzb|kyjd|cbsj|sgxk|gcsg|jgys|qrxmjs','=',1]; //这个条件有待商榷
            $st[] = Db::name('spjd_state')->where('kid', '=', $a['kid'])->field('kid,xmzb,kyjd,cbsj,sgxk,gcsg,jgys,qrxmjs')->find();
            foreach ($st as $b) {
                if ($b['xmzb'] == 1) {
                    $state[$b['kid']] = '项目准备';
                } else if ($b['kyjd'] == 1) {
                    $state[$b['kid']] = '可研阶段';
                } else if ($b['cbsj'] == 1) {
                    $state[$b['kid']] = '初步设计';
                } else if ($b['sgxk'] == 1) {
                    $state[$b['kid']] = '施工许可';
                } else if ($b['gcsg'] == 1) {
                    $state[$b['kid']] = '工程施工';
                } else if ($b['jgys'] == 1) {
                    $state[$b['kid']] = '竣工验收';
                } else if ($b['qrxmjs'] == 1) {
                    $state[$b['kid']] = '项目完成';
                }
            }
        }

        foreach ($test as &$v) {
            $sn = isset($state[$v['kid']]) ? $state[$v['kid']] : '';
            $v['spjd'] = $sn;
        }

//        资金到账汇总（已完成）
        $zjdz = [];
        foreach ($kids as $k) {
            $zj = Db::name('zjdz')->where('kid', '=', $k['kid'])->field('kid,sum(zjje) as sum')->group('kid')->select();
            foreach ($zj as $a) {
                $zjdz[$a['kid']] = $a['sum'];
            }
        }

        foreach ($test as &$v) {
            $sn = isset($zjdz[$v['kid']]) ? $zjdz[$v['kid']] : 0;
            $v['zjdz'] = $sn;
        }

//        资金支付。（已完成）

//        资金支付遇到不明问题，需要新写一个接口处理
        $zjzf = $this->localzjzf($kids);

        foreach ($test as &$v) {
            $sn = isset($zjzf[$v['kid']]) ? $zjzf[$v['kid']] : 0;
            $v['zjzf'] = $sn;
        }

//        远程视频（已完成）
        $ycsp = [];
        $sp = [];
        foreach ($kids as $k) {
            $sp[] = Db::name('projectlist')->where('kid', '=', $k['kid'])->field('kid,video_ip,video_user,video_pass')->find();
            foreach($sp as $a){
                if($a['video_ip']){
                    $ycsp[$a['kid']] = trim($a['video_ip']) . "/dispatch.asp?user=" . trim($a['video_user']) . "&pass=" .
                        $a['video_pass'] . "&page=preview.asp[&slice=9&open={1,2,3,4,5,6,7,8}]";
                }
            }
        }

        foreach ($test as &$v) {
            $sn = isset($ycsp[$v['kid']]) ? $ycsp[$v['kid']] : '';
            $v['ycsp'] = $sn;
        }


//    拼接审计文书附件(已完成)
        $sjws = [];
        $ws = [];
        foreach($kids as $k){
            $types = ['审计报告','审计建议书','移送处理书','审计决定'];
            $ws[] = Db::name('files')
                ->where('pro_kid','=',$k['kid'])
                ->where('type','in',$types)
                ->field('pro_kid,files_add,files_old_name,ctime')->select();

            foreach($ws as $b){
                foreach($b as $i=>$a){
                    $sjws[$a['pro_kid']][$i]['files_url'] = "<a target='_blank' href='".$a['files_add']."'>".$a['files_old_name']."</a>";
                    $sjws[$a['pro_kid']][$i]['ctime'] = $a['ctime'];
                }
            }
        }

        foreach($test as &$v){
            $sn = isset($sjws[$v['kid']]) ? $sjws[$v['kid']] :[];
            $v['sjws'] = $sn;
        }
        return $test;
    }
//    当前服务器资金支付。（已完成）
    private function localzjzf($kids)
    {
        $zjzf = [];
        $zj_ht = [];
        $zj_glf = [];
        foreach ($kids as $i=>$k) {
//            合同支付
            $htzf = Db::name('htzf')->where('kid', '=', $i)->field('kid,sum(fkje) as sum')->group('kid')->select();
            if(count($htzf)){
                foreach ($htzf as $a) {
                    $zj_ht[$a['kid']] = floatval($a['sum']);
                }
            }
//            管理费支出
            $glf = Db::name('zjglf')->where('kid', '=', $i)->field('kid,sum(gz) as gz,sum(bgf) as bgf,sum(bgcd) as bgcd,
                sum(cljtf) as cljtf,sum(ldbhf) as ldbhf,sum(gjyjsyf) as gjyjsyf,sum(gdzcsyf) as gdzcsyf,
                sum(zmscgrf) as zmscgrf,sum(jstszlf) as jstszlf,sum(zdf) as zdf,sum(sgbt) as sgbt,
                sum(jgysf) as jgysf,sum(qtfy) as qtfy')->group('kid')->select();
            if(count($glf)){
                foreach ($glf as $b) {
                    $sum = 0;
                    $sum += $b['gz'] + $b['bgf'] + $b['bgcd'] + $b['cljtf'] + $b['ldbhf'] + $b['gjyjsyf']
                        + $b['gdzcsyf'] + $b['zmscgrf'] + $b['jstszlf'] + $b['zdf'] + $b['sgbt'] + $b['jgysf'] + $b['qtfy'];
                    $zj_glf[$b['kid']] =$sum;
                }
            }
        }
//       将相同键名的数组，值相加。
        foreach($zj_ht as $i=>$v){
            if(isset($zj_glf[$i])){
                $zjzf[$i] = strval($zj_glf[$i]+$v);
            }
        }
        return $zjzf;
    }


//    获取远程服务器上，GIS地图抛洒点及信息（已完成，需要重写前台！！！！！！！！！）
    public function getyungis()
    {
        $kids = Db::connect('db_config1')->table('sjj_xmzb')->distinct(true)->where("sfhx='是'")->field('kid,jwd')->column('jwd','kid');
        $test = [];
        foreach($kids as $i=>$k){
            $where = [
                ['kid', '=', $i],
                ['jsdw_ok', '=', 1]
            ];
            $test[] =  Db::connect('db_config1')->table('sjj_prounderway')
                ->where($where)
                ->field('kid,pro_name,dw_name,aduit_num,pro_category,pro_cost,pro_time')->find();
        }
//        获取经纬度（已完成）
        foreach($test as &$v){
            $sn = isset($kids[$v['kid']]) ? $kids[$v['kid']] : '';
            $v['jwd'] = $sn;
            $v['from'] = '矿区审计局';//可以用邮政编码代替！！！！！！！！！！！！！！！！！
        }

//        测试建设状态(已完成)
        $state = [];
        foreach ($kids as $i=>$k) {
//            $where[] = ['xmzb|kyjd|cbsj|sgxk|gcsg|jgys|qrxmjs','=',1]; //这个条件有待商榷
            $st[] =  Db::connect('db_config1')
                ->table('sjj_spjd_state')
                ->where('kid', '=', $i)
                ->field('kid,xmzb,kyjd,cbsj,sgxk,gcsg,jgys,qrxmjs')
                ->find();
            foreach ($st as $b) {
                if ($b['xmzb'] == 1) {
                    $state[$b['kid']] = '项目准备';
                } else if ($b['kyjd'] == 1) {
                    $state[$b['kid']] = '可研阶段';
                } else if ($b['cbsj'] == 1) {
                    $state[$b['kid']] = '初步设计';
                } else if ($b['sgxk'] == 1) {
                    $state[$b['kid']] = '施工许可';
                } else if ($b['gcsg'] == 1) {
                    $state[$b['kid']] = '工程施工';
                } else if ($b['jgys'] == 1) {
                    $state[$b['kid']] = '竣工验收';
                } else if ($b['qrxmjs'] == 1) {
                    $state[$b['kid']] = '项目完成';
                }
            }
        }
        foreach ($test as &$v) {
            $sn = isset($state[$v['kid']]) ? $state[$v['kid']] : '';
            $v['spjd'] = $sn;
        }

//        资金到账汇总（已完成）
        $zjdz = [];
        foreach ($kids as $i=>$k) {
            $zj =  Db::connect('db_config1')->table('sjj_zjdz')->where('kid', '=', $i)->field('kid,sum(zjje) as sum')->group('kid')->select();
            foreach ($zj as $a) {
                $zjdz[$a['kid']] = $a['sum'];
            }
        }
        foreach ($test as &$v) {
            $sn = isset($zjdz[$v['kid']]) ? $zjdz[$v['kid']] : 0;
            $v['zjdz'] = $sn;
        }

//        资金支付。分为合同支付和管理费支出，然后将二者结合。不明错误，需要在另一个程序内完成！
        $zjzf = $this->zjzf($kids);

        foreach ($test as &$v) {
            $sn = isset($zjzf[$v['kid']]) ? strval($zjzf[$v['kid']]) : 0;
            $v['zjzf'] = $sn;
        }

//        远程视频（已完成）
        $ycsp = [];
        $sp = [];
        foreach ($kids as $i=>$k) {
            $sp[] = Db::connect('db_config1')->table('sjj_projectlist')->where('kid', '=', $i)->field('kid,video_ip,video_user,video_pass')->find();
            foreach($sp as $a){
                if($a['video_ip']){
                    $ycsp[$a['kid']] = trim($a['video_ip']) . "/dispatch.asp?user=" . trim($a['video_user']) . "&pass=" .
                        $a['video_pass'] . "&page=preview.asp[&slice=9&open={1,2,3,4,5,6,7,8}]";
                }
            }
        }

        foreach ($test as &$v) {
            $sn = isset($ycsp[$v['kid']]) ? $ycsp[$v['kid']] : '';
            $v['ycsp'] = $sn;
        }


//    拼接审计文书附件(已完成)
        $sjws = [];
        $ws = [];
        foreach($kids as $c=>$k){
            $types = ['审计报告','审计建议书','移送处理书','审计决定'];
            $ws[] =  Db::connect('db_config1')->table('sjj_files')
                ->where('pro_kid','=',$c)
                ->where('type','in',$types)
                ->field('pro_kid,files_add,files_old_name,ctime')->select();

            foreach($ws as $b){
                foreach($b as $i=>$a){
                    $sjws[$a['pro_kid']][$i]['files_url'] = "<a target='_blank' href='".$a['files_add']."'>".$a['files_old_name']."</a>";
                    $sjws[$a['pro_kid']][$i]['ctime'] = $a['ctime'];
                }
            }
        }

        foreach($test as &$v){
            $sn = isset($sjws[$v['kid']]) ? $sjws[$v['kid']] :[];
            $v['sjws'] = $sn;
        }
        return $test;
    }
//    远程服务器（矿区审计），资金支付（已完成）
    private function zjzf($kids)
    {
        $zjzf = [];
        $zj_ht = [];
        $zj_glf = [];
        foreach ($kids as $i=>$k) {
//            合同支付
            $htzf = Db::connect('db_config1')->table('sjj_htzf')->where('kid', '=', $i)->field('kid,sum(fkje) as sum')->group('kid')->select();
            if(count($htzf)){
                foreach ($htzf as $a) {
                    $zj_ht[$a['kid']] = floatval($a['sum']);
                }
            }
//            管理费支出
            $glf = Db::connect('db_config1')->table('sjj_zjglf')->where('kid', '=', $i)->field('kid,sum(gz) as gz,sum(bgf) as bgf,sum(bgcd) as bgcd,
                sum(cljtf) as cljtf,sum(ldbhf) as ldbhf,sum(gjyjsyf) as gjyjsyf,sum(gdzcsyf) as gdzcsyf,
                sum(zmscgrf) as zmscgrf,sum(jstszlf) as jstszlf,sum(zdf) as zdf,sum(sgbt) as sgbt,
                sum(jgysf) as jgysf,sum(qtfy) as qtfy')->group('kid')->select();
            if(count($glf)){
                foreach ($glf as $b) {
                    $sum = 0;
                    $sum += $b['gz'] + $b['bgf'] + $b['bgcd'] + $b['cljtf'] + $b['ldbhf'] + $b['gjyjsyf']
                        + $b['gdzcsyf'] + $b['zmscgrf'] + $b['jstszlf'] + $b['zdf'] + $b['sgbt'] + $b['jgysf'] + $b['qtfy'];
                    $zj_glf[$b['kid']] =$sum;
                }
            }
        }
//       将相同键名的数组，值相加。
        foreach($zj_ht as $i=>$v){
            if(isset($zj_glf[$i])){
                $zjzf[$i] = strval($zj_glf[$i]+$v);
            }
        }
        return $zjzf;
    }


//    获得单项工程GIS数据，泼洒坐标。(已完成，需要重写前台！！！！！！！！！！！！！)
    public function getdxgcmap(){
        $orgname = Session::get('user.orgname');
        $position = Session::get('user.position');
        if(($orgname=='审计局' && ($position=='科长' || $position=='分管领导')) ||$orgname=='市领导'){
            $single = Db::name('singleproject')->distinct(true)->field('single_project as sn,kid,longitude')->select();
            $get_wc = [];//获取工程进度
            $gcsg = [];//工程完成。
//        在工程进度表（gcjd）进行循环，获取累计单项工程支出情况。
            foreach($single as $a){
                $get_wc[] = Db::name('gcjd')
                    ->where('gcmc','=',$a['sn'])
                    ->where('kid','=',$a['kid'])
                    ->field('gcmc,sgdw,sum(bywc) as wc,kid')
                    ->group('gcmc')
                    ->select();
            }

            foreach($get_wc as $i=>$b){
                if(count($b)){
                    foreach($b as $c=>$d){
                        $gcsg[md5($d['gcmc'])] = $d;
                    }
                };
            }

            foreach($single as &$v){
                $sn = isset($gcsg[md5($v['sn'])]['sgdw']) ? $gcsg[md5($v['sn'])]['sgdw'] : '';
                $v['sgdw'] = $sn;
                $sn1 = isset($gcsg[md5($v['sn'])]['wc']) ? $gcsg[md5($v['sn'])]['wc'] : '';
                $v['ljwc'] = $sn1;
            }

//            连接远程服务器的单项工程。
            if($this->checkyun()){
                $yundata = $this->getyunsingle();
                if($yundata){
                    $single = array_merge($single,$yundata);
                }
            }

            echo json_encode($single);
        }
//        各组只能看自己的数据
        else
        {
            $get_kids = Db::name('prounderway')->where('sjuid|dwuid','=',session('user.uid'))->field('kid')->select();
            if($get_kids){
                $kids = [];
                foreach($get_kids as $k){
                    $kids[] = $k['kid'];
                }

                $single = Db::name('singleproject')
                    ->distinct(true)
                    ->where('kid','in',$kids)
                    ->field('single_project as sn,kid,longitude')
                    ->select();
                $get_wc = [];//获取工程进度
                $gcsg = [];//工程完成。
//        在工程进度表（gcjd）进行循环，获取累计单项工程支出情况。
                foreach($single as $a){
                    $get_wc[] = Db::name('gcjd')
                        ->where('gcmc','=',$a['sn'])
                        ->where('kid','=',$a['kid'])
                        ->field('gcmc,sgdw,sum(bywc) as wc,kid')
                        ->group('gcmc')
                        ->select();
                }

                foreach($get_wc as $i=>$b){
                    if(count($b)){
                        foreach($b as $c=>$d){
                            $gcsg[md5($d['gcmc'])] = $d;
                        }
                    };
                }

                foreach($single as &$v){
                    $sn = isset($gcsg[md5($v['sn'])]['sgdw']) ? $gcsg[md5($v['sn'])]['sgdw'] : '';
                    $v['sgdw'] = $sn;
                    $sn1 = isset($gcsg[md5($v['sn'])]['wc']) ? $gcsg[md5($v['sn'])]['wc'] : '';
                    $v['ljwc'] = $sn1;
                }
                echo json_encode($single);
            }else{
                echo json_encode('');
            }
        }
    }

//    获取远程服务器（矿区审计局）单项工程,（已完成）
    public function getyunsingle(){
        $single = Db::connect('db_config1')->table('sjj_singleproject')->distinct(true)->field('single_project as sn,kid,longitude')->select();
        $get_wc = [];//获取工程进度
        $gcsg = [];//工程完成。
//        在工程进度表（gcjd）进行循环，获取累计单项工程支出情况。
        foreach($single as $a){
            $get_wc[] = Db::connect('db_config1')->table('sjj_gcjd')
                ->where('gcmc','=',$a['sn'])
                ->where('kid','=',$a['kid'])
                ->field('gcmc,sgdw,sum(bywc) as wc,kid')
                ->group('gcmc')
                ->select();
        }

        foreach($get_wc as $i=>$b){
            if(count($b)){
                foreach($b as $c=>$d){
                    $gcsg[md5($d['gcmc'])] = $d;
                }
            };
        }

        foreach($single as &$v){
            $sn = isset($gcsg[md5($v['sn'])]['sgdw']) ? $gcsg[md5($v['sn'])]['sgdw'] : '';
            $v['sgdw'] = $sn;
            $sn1 = isset($gcsg[md5($v['sn'])]['wc']) ? $gcsg[md5($v['sn'])]['wc'] : '';
            $v['ljwc'] = $sn1;
        }
        return $single;

    }


//    获得项目坐标数据(已完成，没有做远程的信息！！！！！！！！！！！！)
    public function get_xmzb_map(){
        $orgname = Session::get('user.orgname');
        $position = Session::get('user.position');
        if(($orgname=='审计局' && ($position=='科长' || $position=='分管领导')) ||$orgname=='市领导'){
            $result = Db::name('xmzb')
                ->alias('zb')
                ->join('prounderway py ',' py.kid=zb.kid')
                ->where("zb.sfhx = '否'")
                ->field('zb.*,py.pro_category,py.pro_category1')->select();
            echo json_encode($result);

        }else{
            $result = Db::name('xmzb')
                ->alias('zb')
                ->join('prounderway py','py.kid=zb.kid')
                ->where("zb.sfhx = '否'")
                ->where('py.sjuid|py.dwuid','=',session('user.uid'))
                ->field('zb.*,py.pro_category,py.pro_category1')->select();
            echo json_encode($result);

        }
    }

//   MENU=> 显示法规库页面
    public function show_fgk()
    {
        echo $this->view->fetch('index/fgk/fgk');
    }

//  显示法规库datagrid数据
    public function show_fgklist()
    {
        $getsearch = Request::post('pro');
        $search = [];
        if (isset($getsearch) && !empty($getsearch)) {
            $search[] =['name','like', '%'.$getsearch.'%'];
        }
        $user = Db::name('fgk');
        $count = Db::name('fgk')
            ->where($search)
            ->count();
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $pageSize = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
        $map = $user->page($page,$pageSize)->where($search)->select();
        $json = '{"total":'.$count.',"rows":'.json_encode($map).'}';
        echo $json;
    }
}
