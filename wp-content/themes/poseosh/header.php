<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?php
    if( is_page() ){
        $title = get_the_title();
        $keywords = get_post_meta($post->ID, 'seo_meta_k',true);
        $description = get_post_meta($post->ID, 'seo_meta_d',true);
    }else{
        $title = get_option('main_title');
        $keywords = get_option('main_meta_k');
        $description = get_option('main_meta_d');
    }
    ?>

    <meta name="keywords" content="<?=$keywords?>" />
    <meta name="description" content="<?=$description?>" />


    <title><?= $title ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!--<div class="main__wrapper">-->

<?php
/*$ip = $_SERVER['REMOTE_ADDR'];
// Москва
// $ip='212.24.44.1';
// Новосибирск
// $ip='195.208.131.1';
get_template_part(  '/geoip/ipgeobase');
$gb = new IPGeoBase();

$data = $gb->getRecord($ip);

$city = iconv('windows-1251', 'UTF-8', $data['city']);

if ($city == 'Москва') {
    $geo = $msk;
} else {
    $geo = $spb;
}*/
?>

<header class="page-header">
    <div class="container page-header__inner">
        <a href="/" class="logo">
            <img src="/img/logo_color.png" alt="" class="logo__img">
            <div class="logo__text_wrapper">
                <p class="logo__title"><span>PO</span><span>SEO</span><span>SH</span></p>
                <p class="logo__text">СЕРВИС УВЕЛИЧЕНИЯ<br> ПРОДАЖ С САЙТА</p>
            </div>
        </a>

        <div class="city">
            <!-- <div class="city__wrapper">
            <a href="#" class="city__btn">Да</a>
            <a href="#" class="city__btn">Нет</a>
            <p class="city__name">Санкт-Петербург</p>
            <p class="city__name">Ваш город?</p>
          </div> -->
            <div class="city__wrapper">
                <p class="city__name">Санкт-Петербург</p>
                <select name="" id="" class="city__select">
                    <option value="">Выбрать другой</option>
                    <option value="">Санкт-Петербург</option>
                </select>
            </div>
        </div>

        <div class="title__wrapper">
            <p class="title"></p>
            <a href="http://seo.poseosh.im/site/login/" class="block__btn">Вход</a>
            <a href="http://seo.poseosh.im/site/signup/" class="block__btn">Регистрация</a>
        </div>


    </div>
</header>
