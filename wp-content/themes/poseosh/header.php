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

<header class="page-header">
    <div class="container page-header__inner">
        <a href="/" class="logo">
            <p class="logo__title">POSEOSH</p>
            <p class="logo__text">СЕРВИС УВЕЛИЧЕНИЯ ПРОДАЖ С САЙТА</p>
        </a>

        <div class="city">
            <div class="city__wrapper">
                <a href="#" class="city__btn">Да</a>
                <a href="#" class="city__btn">Нет</a>
                <p class="city__name">Санкт-Петербург</p>
                <p class="city__name">Ваш город?</p>
            </div>
            <!-- <div class="city__wrapper">
            <p class="city__name">Санкт-Петербург</p>
            <select name="" id="" class="city__select">
            <option value="">Выбрать другой город</option>
            <option value="">Санкт-Петербург</option>
          </select>
        </div> -->
        </div>
    </div>
</header>