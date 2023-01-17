<?php

$dsn = "mysql:host=localhost;dbname=multilevel_menu;charset=utf8";
$opt = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$pdo = new PDO($dsn, 'root', '', $opt);

function dd($data)
{
    echo '<pre>' . print_r($data, 1) . "</pre>";
}

$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();

while($row = $stmt->fetch())
{
    $data[$row['id']] = $row;
}

//dd($data);

function getTree($data)
{
    $tree = [];
    foreach ($data as $id => &$node) {
        if(!$node['parent_id']) {
            $tree[$id] = &$node;
        } else {
            $data[$node['parent_id']]['children'][$id] = &$node;
        }
    }
    return $tree;
}

$tree = getTree($data);
//dd(getTree($data));

function build_menu_list($tree)
{
    $html = '<ul>';
    foreach ($tree as $item) {
        if(isset($item['children'])) {
            $html .= "<li><a href='?category={$item['id']}'>{$item['title']}</a>";
            $html .= build_menu_list($item['children']);
            $html .= "</li>";
        } else {
            $html .= "<li><a href='?category={$item['id']}'>{$item['title']}</a></li>";
        }
    }
    return $html . '</ul>';
}

echo build_menu_list($tree);

function build_menu_select($tree, $tab = '')
{
    $html = '';
    foreach ($tree as $item) {
        if(isset($item['children'])) {
            $html .= "<option value='{$item['title']}'>" . $tab . $item['title'] . "</option>";
            $html .= build_menu_select($item['children'], '&nbsp' . $tab . '&#10146');
        } else {
            $html .= "<option value='{$item['title']}'>" . $tab . $item['title'] . "</option>";
        }
    }
    return $html;
}

echo "<hr>";
echo "<form method='post' action='select.php'>";
echo "<select name='select'>" . build_menu_select($tree) . "</select>";
echo "<input type='submit'>";
echo "</form>";


