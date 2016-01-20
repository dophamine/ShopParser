<meta charset="utf-8">
<?php
require 'simple_html_dom.php';

function getExtension1($filename) {
    return end(explode(".", $filename));
}

function parseURL($url) {
    $page = file_get_html($url);

    $h1 = $page->find('h1');
    $h1 = $h1[0]->innertext();
    $md5 = md5($h1);
    $description = $page->find('.sh-itempage-rightcol');

    $str = $description[0]->innertext();

    $pos1 = strpos($str, '<hr');
    $pos2 = strpos($str, '<hr', $pos1 + 2);

    $result = mb_substr($str, $pos1 + 4, $pos2 - $pos1 - 8);

    $image = $page->find('.sh-ipage-mainimg .gphoto')[0]->src;
    $imageSrc = file_get_contents('http://toyone.com.ua' . $image);
    $ext = '.'.getExtension1($image);

    file_put_contents('storage/images/' . $md5 .$ext, $imageSrc);

    $categories = [];
    foreach ($page->find('.breadcrumbs > a > span') as $cat) {
        $categories[] = $cat->innertext();
    }
    unset($categories[0]);

    $json = [
        "title" => $h1,
        "description" => $result,
        "image" => $md5 . $ext,
        "baseprice" => 0,
        "basecurrency" => '',
        "price" => 0,
        "published" => true,
        "color" => '',
        "manufacture" => '',
        "stock" => 0,
        "categories" => $categories
    ];

    file_put_contents('storage/products/' . $md5 . '.json', json_encode($json));
}

parseURL('http://toyone.com.ua/shop/2899/desc/detskij-manezh-carrello-grande-crl-7401-goluboj');