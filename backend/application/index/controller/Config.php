<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cookie;
use think\Log;

class Config extends Controller
{
    
    public function index(){
        
        $map['group'] = 1;
        $map['status'] = 1;
        $map['name'] = ['not in', ['msm_SignName', 'msm_appkey', 'msm_secretkey', 'msm_TCode', 'is_close']];
        $data = Db::name('config')->where($map)->order('id asc')->select();
        
        return WPreturn($data,1);
    }
    
    public function get_config(){
        $name = input('name');
        $data = Db::name('config')->where('name', $name)->find();
        return WPreturn($data,1);
    }
    
}