# mp-china-region
微信小程序国家行政区域划分数据

各个平台（高德地图、百度地图、腾讯地图、微信小程序）的地区划分都有所差异，对比后感觉微信小程序的数据最好用，比如东莞、海南的一些地区都做了划分；

微信官方没有开放的数据，甚至腾讯地图的数据都是不一致的，所以整理了个脚本处理下数据。

region文件为小程序开发者工具提取，文件位置（macOS）

```
/微信开发者工具/WeappCode/1.02.1910120/js/libs/region
```

## 使用方式

使用环境: PHP

### PHP环境下使用

#### 定制数据

start.php 中可以定制自己需要每一条数据的key
```php
$idKey = 'id'; // 每个地名行政代码的key
$nameKey = 'name'; // 每个地名的key
$childrenKey = 'children'; // 子节点的key
```

#### 生成数据

任何方式运行`start.php`文件即可，控制台或走web，控制台示例：
```shell script
php start.php
```
