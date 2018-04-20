<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?php
    if( is_page() ){
        $title = get_post_meta($post->ID, 'seo_title',true);
        $keywords = get_post_meta($post->ID, 'meta_k',true);
        $description = get_post_meta($post->ID, 'meta_d',true);
    }else{
        $title = get_bloginfo('name');
    }


    ?>

    <meta name="keywords" content="<?=$keywords?>" />
    <meta name="description" content="<?=$description?>" />


    <title><?= $title ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="page-header">
    <div class="container page-header__inner">
        <div class="city">
            <p class="city__name">Санкт-Петербург</p>
            <select name="" id="" class="city__select">
                <option value="">Выбрать другой город</option>
                <option value="">Санкт-Петербург</option>
            </select>
        </div>
        <div class="logo">
            <p class="logo__title">POSEOSH</p>
            <p class="logo__text">СЕРВИС УВЕЛИЧЕНИЯ ПРОДАЖ С САЙТА</p>
        </div>
    </div>
</header>