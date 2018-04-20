<?php get_header(); ?>


<div class="main">
    <div class="container main__inner">
        <section class="text">

            <?php if (have_posts()): while (have_posts()): the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; endif; ?>
            <a href="#" class="text__button">Хочу много денег!</a>
        </section>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>