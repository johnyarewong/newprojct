<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
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
                    $user = Db::name('userinfo')
                        ->where(function ($query) use ($username) {
                            $query->where('username', $username)
                                ->whereOr('nickname', $username);

                            $encryptedPhone = $this->encryptPhoneForLookup($username);
                            if ($encryptedPhone !== null) {
                                $query->whereOr('utel', $encryptedPhone);
                            }
                        })
                        ->find();
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

    /**
     * Encrypt the supplied username so it can match phone numbers stored in the utel column.
     */
    private function encryptPhoneForLookup(string $value)
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return $this->encrypt($value, 'E', 'e10adc3949ba59abbe56e057f20f883e');
    }

    /**
     * Minimal implementation of the encrypt helper from the legacy backend to maintain compatibility.
     */
    private function encrypt(string $string, string $operation, string $key = '')
    {
        $key = md5($key);
        $keyLength = strlen($key);
        if ($operation === 'D') {
            $string = base64_decode($string, true);
            if ($string === false) {
                return '';
            }
        } else {
            $string = substr(md5($string . $key), 0, 8) . $string;
        }

        $stringLength = strlen($string);
        $rndkey = $box = [];
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $keyLength]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation === 'D') {
            if (substr($result, 0, 8) === substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            }

            return '';
        }

        return str_replace('=', '', base64_encode($result));
    }

    /**
     * 简单的数据库连接测试页面，帮助验证配置是否正确。
     */
    public function testDatabase()
    {
        $result = [
            'status' => false,
            'message' => '数据库连接失败，请检查配置。',
            'details' => [],
        ];

        try {
            $connection = Db::connect();
            $connection->connect();

            $result['status'] = true;
            $result['message'] = '数据库连接成功。';

            try {
                $version = $connection->query('SELECT VERSION() AS version');
                if (!empty($version) && isset($version[0]['version'])) {
                    $result['details']['version'] = $version[0]['version'];
                }
            } catch (\Exception $innerException) {
                $result['details']['version'] = '无法读取数据库版本信息：' . $innerException->getMessage();
            }
        } catch (\Exception $exception) {
            $result['message'] = '数据库连接失败：' . $exception->getMessage();
        }

        return $this->fetch('index/test_database', [
            'result' => $result,
        ]);
    }
}
