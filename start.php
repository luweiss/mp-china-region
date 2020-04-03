<?php

$idKey = 'id'; // 每个地名行政代码的key
$nameKey = 'name'; // 每个地名的key
$childrenKey = 'children'; // 子节点的key

function convert_eol($string, $to = "\n")
{
    return preg_replace("/\r\n|\r|\n/", $to, $string);
}

function preg_search($pattern, $string)
{
    preg_match($pattern, $string, $matches);
    if ($matches && count($matches))
        return $matches[0];
    return false;
}

function is_province($id)
{
    return preg_search('/\d{2}0{4}/', "$id") !== false;
}

function is_city($id)
{
    if (is_province($id)) return false;
    return preg_search('/\d{4}0{2}/', "$id") !== false;
}

function is_county($id)
{
    if (is_province($id) || is_city($id)) return false;
    return preg_search('/\d{6}/', "$id") !== false;
}

$raw = file_get_contents(__DIR__ . '/region');
$raw = convert_eol(trim($raw), "\n");
$rows = explode("\n", $raw);

$list = [];

$rootList = []; // 树状结构根结点列表
$lastProvinceIndex = null;
foreach ($rows as $row) {
    $id = preg_search('/[0-9]{6}/', $row);
    $name = preg_search('/[\x{4e00}-\x{9fa5}]+/u', $row);
    $item = [
        $idKey => $id,
        $nameKey => $name,
    ];
    $list[] = $item;
    $lastNode = &$item;
    if (is_province($id)) {
        $rootList[] = $item;
        continue;
    }
    $lastProvinceIndex = count($rootList) - 1;
    if (is_city($id)) {
        if (!isset($rootList[$lastProvinceIndex][$childrenKey])) $rootList[$lastProvinceIndex][$childrenKey] = [];
        $rootList[$lastProvinceIndex][$childrenKey][] = $item;
        continue;
    }
    $lastCityIndex = count($rootList[$lastProvinceIndex][$childrenKey]) - 1;
    if (is_county($id)) {
        if (!isset($rootList[$lastProvinceIndex][$childrenKey][$lastCityIndex][$childrenKey]))
            $rootList[$lastProvinceIndex][$childrenKey][$lastCityIndex][$childrenKey] = [];
        $rootList[$lastProvinceIndex][$childrenKey][$lastCityIndex][$childrenKey][] = $item;
    }
}

$listJson = json_encode($list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents(__DIR__ . '/dist/region.json', $listJson);

$treeListJson = json_encode($rootList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents(__DIR__ . '/dist/region.tree.json', $treeListJson);

echo "\n处理完成，输出文件在dist目录下\n";
