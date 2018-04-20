<?php get_header(); ?>


<div class="main">
    <div class="container main__inner">
        <section class="text">


            <?php if (have_posts()): while (have_posts()): the_post(); ?>
                <h1><?=get_post_meta( get_the_id(), 'seo_h1', true)?></h1>
            <div class="text__wrapper">
                <?php the_content(); ?>
            </div>
            <?php endwhile; endif; ?>
            <a href="#" class="text__button">Хочу много денег!</a>
        </section>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>