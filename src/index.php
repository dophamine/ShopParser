<meta charset="utf-8">
<?php
require 'simple_html_dom.php';

function getExtension1($filename) {
    return end(explode(".", $filename));
}

function parseURL_toyOne($url) {
    $page = file_get_html($url);
    
    $title = $page->find('h1');
    $title = $title[0]->innertext();
    $md5 = md5($title);
    
    $description = $page->find('.sh-itempage-rightcol');
    $str = $description[0]->innertext();
    $pos1 = strpos($str, '<hr');
    $pos2 = strpos($str, '<hr', $pos1 + 2);
    $description = mb_substr($str, $pos1 + 4, $pos2 - $pos1 - 8);

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
        "title" => $title,
        "description" => $description,
        "image" => $md5 . $ext,
        "baseprice" => 0,
        "basecurrency" => '',
        "price" => 0,
        "published" => true,
        "color" => '',
        "manufacture" => '',
        "stock" => 0,
        "categories" => $categories,
        "basecategories" => ''
    ];

    file_put_contents('storage/products/' . $md5 . '.json', json_encode($json));
    var_dump($json);
}

function parseURL_12ka($url) {
    $page = file_get_html($url);

    $title = $page->find('h1.b-product__name');
    $title = trim($title[0]->innertext());
    $md5 = md5($title);
        
    $description = $page->find('.b-user-content > ul')[0]->plaintext;

    $image = $page->find('.b-product__image-panel  img')[0]->src;
    $imageSrc = file_get_contents($image);
    $ext = '.'.getExtension1($image);
    file_put_contents('storage/images/' . $md5 .$ext, $imageSrc);

    $categories = [];
    foreach ($page->find('.b-breadcrumb__bar .b-breadcrumb__item') as $cat) {
        trim($cat->plaintext);
        $categories[] = explode('&#8250;', $cat->plaintext)[0]; 
    }
    unset($categories[0]);
    unset($categories[1]);
    
    $price = $page->find('.b-product__price > span')[0]->getAttribute('content');
    $currency = $page->find('.b-product__price > span')[0]->getAttribute('content');
    $manufacture = $page->find('table.b-product-info > td')[1]->plaintext;
    trim($manufacture);
    $country = $page->find('table.b-product-info > td')[3]->plaintext;
    trim($country);
    $json = [
        "title" => $title,
        "description" => $description,
        "image" => $md5 . $ext,
        "baseprice" => 0,
        "basecurrency" => $currency,
        "price" => $price,
        "published" => true,
        "color" => '',
        "manufacture" => $manufacture,
        "country" => $country,
        "stock" => 0,
        "categories" => $categories,
        "basecategories" => ''
    ];

    file_put_contents('storage/products/' . $md5 . '.json', json_encode($json));
}

parseURL_toyOne('http://selavi.com.ua/shop/5529/desc/jubillux-detskij-konstruktor-j-5670-a-korabl');

parseURL_12ka('http://12ka.com.ua/p169402605-universalnyj-pylesos-karcher.html');

