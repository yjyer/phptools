<?php

namespace yjyer;

/**
 * 工具类
 */
class YJYTools
{
    /**
     *
     * 判断是否微信浏览器内打开
     * @param  string  $userAget 可选参数 用户浏览器useAgent头
     * @return boolean
     */
    public static function isWeixin($userAget = '')
    {
        if (!$userAget) {
            $userAget = $_SERVER['HTTP_USER_AGENT'];
        }
        if (strpos($userAget, 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否移动端浏览器
     * @return boolean
     */
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        }
        //userAgent匹配
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 得到手机系统类型
     *
     * @return void
     */
    public static function getDeviceType()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type  = 'other';
        //分别进行判断
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 'ios';
        }

        if (strpos($agent, 'android')) {
            $type = 'android';
        }
        return $type;
    }

    /**
     * 得到来源
     * @return int 来源ID
     */
    public static function getAgentType()
    {
        //获取软件类别
        $app_type = 'other';
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'icroMessenger') !== false) {
            $app_type = 'wx';
        }

        //项目APP
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'YJY_APP') !== false) {
            $app_type = 'yjyApp';
        }

        //获取手机类别
        $device_type = self::getDeviceType();

        //微信-苹果
        if ($app_type == 'wx' && $device_type == 'ios') {
            return 1;
        }
        //微信-安卓
        if ($app_type == 'wx' && $device_type == 'android') {
            return 2;
        }
        //微信-其他
        if ($app_type == 'wx' && $device_type == 'other') {
            return 3;
        }

        //项目APP-苹果
        if ($app_type == 'yjyApp' && $device_type == 'ios') {
            return 4;
        }
        //项目APP-安卓
        if ($app_type == 'yjyApp' && $device_type == 'android') {
            return 5;
        }
        //项目APP其他
        if ($app_type == 'yjyApp' && $device_type == 'other') {
            return 6;
        }

        //手机网页-苹果
        if ($app_type == 'other' && $device_type == 'ios') {
            return 7;
        }
        //手机网页-安卓
        if ($app_type == 'other' && $device_type == 'android') {
            return 8;
        }
        //手机网页其他
        if ($app_type == 'other' && $device_type == 'other') {
            return 9;
        }

        //其他设备
        return 10;
    }

    /**
     * 得到当前浏览器
     *
     * @return string
     */
    public static function getBrowser()
    {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        if (strpos($agent, 'MSIE') !== false || strpos($agent, 'rv:11.0')) //ie11判断
        {
            return "ie";
        } else if (strpos($agent, 'Firefox') !== false) {
            return "firefox";
        } else if (strpos($agent, 'Chrome') !== false) {
            return "chrome";
        } else if (strpos($agent, 'Opera') !== false) {
            return 'opera';
        } else if ((strpos($agent, 'Chrome') == false) && strpos($agent, 'Safari') !== false) {
            return 'safari';
        } else {
            return 'unknown';
        }
    }

    /**
     * 用户名加星隐藏核心信息
     * @param  string $nickname 用户名、昵称
     * @return string 隐藏处理后的用户名、昵称
     */
    public static function hideName($nickname)
    {
        if (mb_strlen($nickname) <= 3) {
            return '***';
        }
        $begin = self::mbsubstr($nickname, 0, 1, 'utf8');
        $end   = self::mbsubstr($nickname, -1, 1, 'utf8');
        return $begin . '***' . $end;
    }

    /**
     * 隐藏ip v4地址的中间两位
     * @param  string $ip_v4 ipV4的地址
     * @return string 处理隐藏后的地址
     */
    public static function hideIpv4($ip_v4)
    {
        $ip = explode('.', $ip_v4);
        if (count($ip) == 4) {
            $ip[1] = '**';
            $ip[2] = '**';
            return implode('.', $ip);
        }
        return $ip_v4;
    }

    /**
     * 将一个Unix时间戳转换成“xx前”模糊时间表达方式
     * @param  mixed $timestamp Unix时间戳
     * @return boolean
     */
    public static function timeAgo($timestamp)
    {
        $etime = time() - $timestamp;
        if ($etime < 1) {
            return '刚刚';
        }

        $interval = array(
            12 * 30 * 24 * 60 * 60 => '年前 (' . date('Y-m-d', $timestamp) . ')',
            30 * 24 * 60 * 60      => '个月前 (' . date('m-d', $timestamp) . ')',
            7 * 24 * 60 * 60       => '周前 (' . date('m-d', $timestamp) . ')',
            24 * 60 * 60           => '天前',
            60 * 60                => '小时前',
            60                     => '分钟前',
            1                      => '秒前',
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        }
    }

    /**
     * 判断是否SSL协议
     * @return boolean
     */
    public static function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址
     * @param  mixed $type 返回类型 0|false 返回IP地址 1|true 返回IPV4地址数字
     * @param  boolean $adv 是否进行高级模式获取（有可能被伪装）---代理情况
     * @return mixed
     */
    public static function getClientIp($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }

                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 用于调试:浏览器友好的变量输出
     * @param mixed   $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string  $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    public static function dump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo ($output);
            return null;
        } else {
            return $output;
        }
    }

    /**
     * 可逆的字符串加密和解密方法-discuz中的方法
     * 该函数密文的安全性主要在于密匙并且是可逆的
     *
     * 该可逆加密主要用于一些需要时间有效性效验的数据交换中，加密强度很弱
     * 若用于密码处理建议使用password_hash和password_verfiy
     *
     *                       ###警告###
     * ********过期时间参数并不意味着过期后就无法解密出明文了********
     *
     * @param  string  $string    明文或密文
     * @param  boolean $isEncode  是否解密，true则为解密 false默认表示加密字符串
     * @param  string  $key       密钥 默认jjonline
     * @param  int     $expiry    密钥有效期 单位：秒 默认0为永不过期
     * @return string 空字符串表示解密失败|密文已过期
     */
    public static function reversibleCrypt($string, $isEncode = false, $key = 'jjonline', $expiry = 0)
    {
        $ckey_length = 4;
        // 密匙
        $key = md5($key ? $key : 'jjonline');
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($isEncode ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey   = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string        = $isEncode ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result        = '';
        $box           = range(0, 255);
        $rndkey        = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($isEncode) {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生成不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 对时间有效性的数据进行可逆的加密，对reversibleCrypt方法的可识别封装
     * @param  string  $string 待加密字符串
     * @param  string  $key    加密秘钥
     * @param  integer $expiry 加密的密文失效时间，0默认表示：永不失效
     * @return string
     */
    public static function transfer_encrypt($string, $key = 'jjonline', $expiry = 0)
    {
        return self::reversibleCrypt($string, false, $key, $expiry);
    }

    /**
     * 对时间有效性的数据进行效验并解密
     * 由reversible_encrypt加密的密文进行解密
     *
     *                       ###警告###
     * ********过期时间参数并不意味着过期后就无法解密出明文了********
     *
     * 密文过期并不意味着无法解密出明文，只是在密文中加入了一种过期效验机制由方法体自动完成效验罢了
     *
     * @param  string $string 密文字符串
     * @param  string $key    解密秘钥
     * @return string
     */
    public static function transferDecrypt($string, $key = 'jjonline')
    {
        return self::reversibleCrypt($string, true, $key);
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @param  string $str 需要转换的字符串
     * @param  int    $start 开始位置
     * @param  int    $length 截取长度
     * @param  string $charset 编码格式，默认utf8
     * @return string
     */
    public static function mbsubstr($str = '', $start = 0, $length = 1, $charset = "utf-8")
    {
        if (empty($str)) return '***';

        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $slice;
    }

    /**
     * 产生随机字串
     * 默认长度6位 字母和数字混合 支持中文
     * @param string $len 长度
     * @param string $type 字串类型
     * 0 字母 1 数字 2大写字母 3小写字母 4中文
     * 默认：大小写字母和数字混合并且去除了容易混淆的字母oOLl和数字01
     * @param string $addChars 额外添加进去的字符
     * @return string
     */
    public static function getRandString($len = 6, $type = '', $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
                break;
            default:
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {
            //位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str   = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= self::mbsubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1, 'utf-8');
            }
        }
        return $str;
    }

    //============================================
    //===操作数组
    //============================================

    /**
     * 二维数组根据键排序
     * @param array $arr
     * @param string $keys
     * @param string $type
     * @return void
     */
    public static function arraySort($arr, $keys, $type = 'desc')
    {
        $keysvalue = $new_array = [];
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 多维数组排序
     * @author gongzhe
     * @param $data
     * @param $sort_order_field
     * @param int $sort_order
     * @param int $sort_type
     * @return mixed
     */
    public static function myArrayMultiSort($data, $sort_order_field, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC)
    {

        foreach ($data as $val) {
            $key_arrays[] = $val[$sort_order_field];
        }

        array_multisort($key_arrays, $sort_order, $sort_type, $data);
        return $data;
    }

    /**
     * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
     * @param $pArray 一个二维数组
     * @param $pKey  数组的键的名称
     * @return array 返回新的一维数组
     */
    public static function getSubByKey($pArray, $pKey)
    {
        if (is_object($pArray)) {
            $pArray = $pArray->toArray();
        }

        $result = [];
        if (is_array($pArray)) {
            foreach ($pArray as $temp_array) {
                if (is_object($temp_array)) {
                    $temp_array = $temp_array->toArray();
                }

                $result[] = isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
            }
        }

        return $result;
    }

    /**
     *将数组转换为JSON字符串（兼容中文）
     * @param array $array 要转换的数组
     * @return string 转换得到的json字符串
     * @access public
     */
    public static function arrayToJsonStr($array)
    {
        $json = json_encode($array, JSON_UNESCAPED_UNICODE);
        return $json;
    }

    /**
     * 不区分大小写的in_array实现
     * @param string $value
     * @param array $array
     * @return void
     */
    public static function inArrayCase($value, $array)
    {
        return in_array(strtolower($value), array_map('strtolower', $array));
    }

    //============================================
    //===日期工具
    //============================================

    /**
     * 返回当前的毫秒时间戳
     * @return void
     */
    public static function msectime()
    {
        list($tmp1, $tmp2) = explode(' ', microtime());
        return sprintf('%.0f', (floatval($tmp1) + floatval($tmp2)) * 1000);
    }

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    public static function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

    /**
     * 时间戳格式化
     * @param int $time
     * @return string 完整的时间显示
     */
    public static function timeFormat($time = null, $format = 1, $field = '')
    {
        if (empty($time)) {
            return '';
        }

        //定义时间格式字典
        $timeFormats = [
            '1'  => 'Y-m-d H:i:s',
            '2'  => 'Y-m-d H:i',
            '3'  => 'Y-m-d H',
            '4'  => 'Y-m-d',
            '5'  => 'Y/m/d H:i:s',
            '6'  => 'Y/m/d H:i',
            '7'  => 'Y/m/d H',
            '8'  => 'Y/m/d',
            '9'  => 'm/d H:i:s',
            '10' => 'm/d H:i',
            '11' => 'm/d H',
        ];

        $timeFormat = '';
        $key_prefix = 'time_format';
        //数组
        if (is_array($format)) {
            if ($field != '' && !empty($format[$key_prefix . '_' . $field])) {
                $key        = $format[$key_prefix . '_' . $field];
                $timeFormat = empty($timeFormats[$key]) ? 1 : $timeFormats[$key];
            } else if (!empty($format[$key_prefix])) {
                $timeFormat = empty($timeFormats[$format[$key_prefix]]) ? 1 : $timeFormats[$format[$key_prefix]];
            }
        }
        //字符串
        else if (is_string($format)) {
            $timeFormat = $format;
        }
        //数字
        else {
            $timeFormat = !empty($timeFormats[$format]) ? $timeFormats[$format] : $timeFormats[1];
        }

        $time = $time === null ? time() : intval($time);
        return date(empty($timeFormat) ? $timeFormats[1] : $timeFormat, $time);
    }

    /**
     * Undocumented function
     * @param [type] $time
     * @return void
     */
    public static function day_format($time = null)
    {
        return self::timeFormat($time, 'Y-m-d');
    }

    /**
     * Undocumented function
     * @param [type] $time
     * @return void
     */
    public static function hour_format($time = null)
    {
        return self::timeFormat($time, 'H:i');
    }

    /**
     * Undocumented function
     * @param [type] $time
     * @return void
     */
    public static function timeOffset($time = null)
    {
        if (empty($time)) {
            return '00:00';
        }

        $mod = $time % 60;
        $min = ($time - $mod) / 60;

        $mod < 10 && $mod = '0' . $mod;
        $min < 10 && $min = '0' . $min;

        return $min . ':' . $mod;
    }

    /**
     * 友好的时间显示
     * @param int $sTime  待显示的时间
     * @param string $type 类型. normal | mohu | full | ymd | other
     * @param string $alt  已失效
     * @return string
     */
    public static function friendlyDate($sTime, $type = 'normal', $alt = 'false')
    {
        if (!$sTime) {
            return '';
        }

        // sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay  = intval(date("z", $cTime)) - intval(date("z", $sTime));
        // $dDay = intval($dTime/3600/24);
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        // normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'normal') {
            if ($dTime < 60) {
                // if ($dTime < 10) {
                return '刚刚'; // by yangjs
                // } else {
                // return intval ( floor ( $dTime / 10 ) * 10 ) . "秒前";
                // }
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
                // 今天的数据.年份相同.日期相同.
            } elseif ($dYear == 0 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
                // return '今天' . date ( 'H:i', $sTime );
            } elseif ($dYear == 0) {
                return date("m-d H:i", $sTime);
            } else {
                return date("Y-m-d H:i", $sTime);
            }
        } elseif ($type == 'mohu') {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay > 0 && $dDay <= 7) {
                return intval($dDay) . "天前";
            } elseif ($dDay > 7 && $dDay <= 30) {
                return intval($dDay / 7) . '周前';
            } elseif ($dDay > 30) {
                return intval($dDay / 30) . '个月前';
            }
            // full: Y-m-d , H:i:s
        } elseif ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } elseif ($type == 'ymd') {
            return date("Y-m-d", $sTime);
        } else {
            if ($dTime < 60) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dYear == 0) {
                return date("Y-m-d H:i:s", $sTime);
            } else {
                return date("Y-m-d H:i:s", $sTime);
            }
        }
    }

    /**
     * Undocumented function
     * @param [type] $number
     * @return void
     */
    public static function weekName($number = null)
    {
        if ($number === null) {
            $number = date('w');
        }
        $arr = array(
            "日",
            "一",
            "二",
            "三",
            "四",
            "五",
            "六",
        );
        return '星期' . $arr[$number];
    }

    /**
     * 日期转换成星期几
     * @param [type] $day
     * @return void
     */
    public static function daytoweek($day = null)
    {
        $day === null && $day = date('Y-m-d');
        if (empty($day)) {
            return '';
        }

        $number = date('w', strtotime($day));

        return self::weekName($number);
    }

    /**
     * 传入时间戳,计算距离现在的时间
     * @param  string $time 时间戳
     * @return string     返回多少以前
     */
    public static function wordTime($time)
    {

        $time = (int) substr($time, 0, 10);
        $int  = time() - $time;
        $str  = '';
        if ($int <= 2) {
            $str = sprintf('刚刚', $int);
        } elseif ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } elseif ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } elseif ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } elseif ($int < 1728000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }

    //============================================
    //===输入文本表情格式化
    //============================================

    /**
     *
     * Emoji原形转换为String
     * @param string $content
     * @return string
     */
    public static function emojiEncode($content)
    {
        return json_decode(preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, json_encode($content)));
    }

    /**
     * Emoji字符串转换为原形
     * @param string $content
     * @return string
     */
    public static function emojiDecode($content)
    {
        return json_decode(preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, json_encode($content)));
    }

    //============================================
    //===字符操作
    //============================================

    /**
     * 字符串转换为数组，主要用于把分隔符调整到第二个参数
     * @param string $str  要分割的字符串
     * @param string $glue 分割符
     * @return array
     */
    public static function str2arr($str, $glue = ',')
    {
        return explode($glue, $str);
    }

    /**
     * 数组转换为字符串，主要用于把分隔符调整到第二个参数
     * @param array $arr 要连接的数组
     * @param string $glue 要连接的数组
     * @return string $glue
     */
    public static function arr2str($arr, $glue = ',')
    {
        if (empty($arr)) {
            return '';
        }
        return implode($glue, $arr);
    }

    /**
     * 将一个字符串部分字符用*替代隐藏.
     *
     * @param string $string 待转换的字符串
     * @param int    $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
     * @param int    $len    需要转换成*的字符个数，当$type=4时，表示右侧保留长度
     * @param int    $type   转换类型：0，从左向右隐藏；
     *                       1，从右向左隐藏；
     *                       2，从指定字符位置分割前由右向左隐藏；
     *                       3，从指定字符位置分割后由左向右隐藏；
     *                       4，保留首末指定字符串
     * @param string $glue   分割符
     *
     * @return string 处理后的字符串
     */
    public static function hideStr($string, $bengin = 0, $len = 4, $type = 2, $glue = '@')
    {
        if (empty($string)) {
            return false;
        }

        $array = [];
        if (0 == $type || 1 == $type || 4 == $type) {
            $strlen = $length = mb_strlen($string);
            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, 'utf8');
                $string = mb_substr($string, 1, $strlen, 'utf8');
                $strlen = mb_strlen($string);
            }
        }
        if (0 == $type) {
            for ($i = $bengin; $i < ($bengin + $len); ++$i) {
                if (isset($array[$i])) {
                    $array[$i] = '*';
                }
            }
            $string = implode('', $array);
        } else {
            if (1 == $type) {
                $array = array_reverse($array);
                for ($i = $bengin; $i < ($bengin + $len); ++$i) {
                    if (isset($array[$i])) {
                        $array[$i] = '*';
                    }
                }
                $string = implode('', array_reverse($array));
            } else {
                if (2 == $type) {
                    $array = explode($glue, $string);
                    $array[0] = self::hideStr($array[0], $bengin, $len, 1);
                    $string = implode($glue, $array);
                } else {
                    if (3 == $type) {
                        $array = explode($glue, $string);
                        $array[1] = self::hideStr($array[1], $bengin, $len, 0);
                        $string = implode($glue, $array);
                    } else {
                        if (4 == $type) {
                            $left = $bengin;
                            $right = $len;
                            $tem = [];
                            for ($i = 0; $i < ($length - $right); ++$i) {
                                if (isset($array[$i])) {
                                    $tem[] = $i >= $left ? '*' : $array[$i];
                                }
                            }
                            $array = array_chunk(array_reverse($array), $right);
                            $array = array_reverse($array[0]);
                            for ($i = 0; $i < $right; ++$i) {
                                $tem[] = $array[$i];
                            }
                            $string = implode('', $tem);
                        }
                    }
                }
            }
        }

        return $string;
    }

    /**
     * 字段文字内容隐藏处理方法.
     *
     * @param $string
     * @param int $type 1 身份证 2 手机号 3 银行卡
     *
     * @return string
     */
    public static function hidePrivacyInfo($string, $type = 1)
    {
        if (empty($string)) {
            return $string;
        }
        if (1 == $type) {
            $string = substr($string, 0, 3) . str_repeat('*', 12) . substr($string, strlen($string) - 4); //身份证
        } elseif (2 == $type) {
            $string = substr($string, 0, 3) . str_repeat('*', 5) . substr($string, strlen($string) - 4); //手机号
        } elseif (3 == $type) {
            $string = str_repeat('*', strlen($string) - 4) . substr($string, strlen($string) - 4); //银行卡
        }

        return $string;
    }


    /**
     * xml转换成数组
     *
     * @param [type] $xml
     * @return void
     */
    public static function  xmlToArray($xml): array
    {
        try {

            //禁止引用外部xml实体
            //判断php版本小于8则禁止引用外部xml实体
            if (PHP_VERSION_ID < 80000) {
                libxml_disable_entity_loader(true);
            }

            //替换掉xml开头多余的文本，以免报错
            $xml = str_replace('<?xml version="1.0" encoding="gbk"?>', '', $xml);

            //xml内容编码处理
            $xml = (string)self::iconvStr($xml, 'GBK', 'UTF-8');

            $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $val = (array)json_decode(json_encode($xmlstring), true);
            return $val;
        } catch (\Exception $ex) {
            return [];
        }
    }

    /**
     * 强转字符编码
     *
     * @param [type] $str 待转换字符
     * @param [type] $beforeEncode 字符原编码
     * @param [type] $afterEncode 字符新编码
     * @return void
     */
    public static function iconvStr($str = '', $beforeEncode, $afterEncode): string
    {
        try {
            $postXml = '';
            $encode = mb_detect_encoding($str, array("ASCII", "UTF-8", "GB2312", "GBK", "BIG5"));
            if ($encode == $beforeEncode) {
                $postXml = iconv($beforeEncode, $afterEncode, $str);
            } else {
                $postXml = iconv($encode, $afterEncode, $str);
            }
            return $postXml;
        } catch (\Exception $ex) {
            return $str;
        }
    }


    /**
     * XML编码 - 转为完整xml格式字符
     * @param  mixed $data 数据
     * @param  string $root 根节点名
     * @param  string $item 数字索引的子节点名
     * @param  string $attr 根节点属性
     * @param  string $id   数字索引子节点key转换的属性名
     * @param  string $encoding 数据编码
     * @return string
     */
    public static function arrayToXmlFull($data, $root = 'root', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $array = [];
            foreach ($attr as $key => $value) {
                $array[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $array);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= self::arrayToXmlBody($data, $item, $id);
        $xml .= "</{$root}>";

        return $xml;
    }

    /**
     * 数据XML编码 - 转为xml内容键值对字符
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @param  string $id   数字索引key转换为的属性名
     * @return string
     */
    public static function arrayToXmlBody($data, $item, $id)
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key         = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? self::arrayToXmlBody($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }

        return $xml;
    }
}
