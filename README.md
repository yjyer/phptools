# PHP 常用函数工具集合

## 起步

---

```shell
# 安装扩展
composer require yjyer/phptools
# 更新扩展
composer update yjyer/phptools
# 卸载扩展
composer remove yjyer/phptools
```

## 使用方式

---

```php
<?php

namespace app\test;

use yjyer\YJYTools;
use yjyer\YJYHttp;

/**
 * 测试类
 */
class Developer extends Base
{
    /**
     * 测试
     */
    public function test()
    {
        // 数组转完成xml字符串
        $xmlFull = YJYTools::arrayToXmlFull([
            'response' => $response_xml,
            'sign' => $sign,
            'sign_type' => $sign_type,
        ], 'alipay', 'item', '', 'id', $charset);
        print_r($xmlFull);

        // 判断当前环境是否为微信浏览器
        YJYTools::isWeixin();

        // 发送模拟GET或POST请求
        YJYHttp::getRequest($url)

        $data=[
            'user_name' => 'admin',
            'pwd' => '123'
        ];
        YJYHttp::postRequest($url,$data)

        // 更多请查看对应文件里的方法

    }
}


```
