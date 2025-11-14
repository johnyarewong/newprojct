<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\facade\Cookie;
use think\facade\Session;

class Index extends Controller
{
    /**
     * 应用首页，提供登录功能。
     */
    public function index(Request $request)
    {
        $errorMessage = null;
        $successMessage = null;
        $username = '';
        $remember = false;

        if (!$request->isPost()) {
            $rememberedUsername = (string) Cookie::get('remember_username');
            if ($rememberedUsername !== '') {
                $username = $rememberedUsername;
                $remember = true;
            }
        }

        if ($request->isPost()) {
            $username = trim((string) $request->post('username'));
            $password = (string) $request->post('password');
            $remember = $request->post('remember') ? true : false;

            if ($username === '' || $password === '') {
                $errorMessage = '请输入用户名和密码。';
            } else {
                try {
                    $user = Db::name('userinfo')->where('username', $username)->find();
                } catch (\Exception $exception) {
                    $user = null;
                    $errorMessage = '登录服务暂时不可用，请稍后再试。';
                }

                if (!isset($user) || $user === null) {
                    if ($errorMessage === null) {
                        $errorMessage = '用户名或密码错误，请重试。';
                    }
                } elseif ((int) $user['ustatus'] === 1) {
                    $errorMessage = '您的账号已被禁用，请联系管理员。';
                } else {
                    $loginTimestamp = isset($user['utime']) ? (string) $user['utime'] : '';
                    $expectedHash = strtolower((string) $user['upwd']);
                    $calculatedHash = strtolower(md5($password . $loginTimestamp));

                    if ($expectedHash !== $calculatedHash) {
                        $errorMessage = '用户名或密码错误，请重试。';
                    }
                }

                if ($errorMessage === null) {
                    Session::set('user_id', $user['uid']);
                    Session::set('username', $user['username']);
                    $successMessage = '登录成功，欢迎您回来！';

                    if ($remember) {
                        Cookie::set('remember_username', $username, ['expire' => 7 * 24 * 60 * 60]);
                    } else {
                        Cookie::delete('remember_username');
                    }
                }
            }
        }

        return $this->fetch('index/login', [
            'errorMessage' => $errorMessage,
            'successMessage' => $successMessage,
            'username' => $username,
            'remember' => $remember,
        ]);
    }
}
