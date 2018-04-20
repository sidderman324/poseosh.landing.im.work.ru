<?php

function styleConnect(){

    wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css');

    wp_enqueue_style('font-style', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700&amp;subset=cyrillic');

    wp_enqueue_script('js', get_template_directory_uri() . '/js/script.min.js');

}
add_action( 'wp_enqueue_scripts', 'styleConnect' );

if (function_exists('add_theme_support')) {
    add_theme_support('menus');
}


/*
 * Метабокс для для SEO
 */
$seometabox = array(

    // ID of the metabox and custom field name prefix
    'id' =>	'seo',

    // Only users with this capability can see the metabox
    'capability' => 'edit_posts',

    // metabox title
    'name' => 'SEO',

    // custom post types names, you can use array( 'page', 'post', 'your_type' )
    'post_type' => array('page'),

    // metabox position: low | high | default
    'priority' => 'high',

    // array of all metabox input field and their params
    'args' => array(

        /* simple text input */
        array(
            'id'	=> 'h1',
            'label' => 'H1 Заголовок',
            'description' => '',
            'type'	=> 'text',
            'placeholder' 	=> 'Заголовок'
        ),

        /* simple text input */
        array(
            'id'	=> 'meta_k',
            'label' => 'Keywords',
            'description' => '',
            'type'	=> 'text',
            'placeholder' 	=> 'Ключевики'
        ),

        /* simple text input */
        array(
            'id'	=> 'meta_d',
            'label' => 'Description',
            'description' => '',
            'type'	=> 'textarea',
            'placeholder' 	=> 'Описание'
        )
    )

);
new trueMetaBox( $seometabox );


/*
 * Страничка настроек в Админке
 */
$main_options = array(
    // yes, slug is the part of the option name, so, to get the value, use
    // get_option( '{SLUG}_{ID}' );
    // get_option( 'styles_headercolor' );
    'slug'	=>	'main',

    // h2 title on your settings page
    'title' => 'Настройки для главной',

    // this displayed in admin menu, try to make it short
    'menuname' => 'Для Главной',

    'capability'=>	'manage_options',

    // WordPress option pages consist of sections, so,
    // at first we create an array of sections and add fields in each section
    'sections' => array(

        // first section
        array(

            // section ID isn't used anywhere, but it is required
            'id' => 'index',

            // section name is displayed as h2 heading
            'name' => 'SEO Главной страницы',

            // and only now the array of fields
            'fields' => array(
                array(
                    'id'			=> 'title',
                    'label'			=> 'Title',
                    'type'			=> 'text', // table of types is above
                    'placeholder' 	=> 'Здесь Title индексной страницы'
                ),
                array(
                    'id'			=> 'meta_k',
                    'label'			=> 'Keywords',
                    'type'			=> 'text', // table of types is above
                    'placeholder' 	=> 'Здесь Ключи индексной страницы'
                ),
                array(
                    'id'			=> 'meta_d',
                    'label'			=> 'Description',
                    'type'			=> 'text', // table of types is above
                    'placeholder' 	=> 'Здесь Описание индексной страницы'
                )
            )
        ),

    )
);

if( class_exists( 'trueOptionspage' ) )
    new trueOptionspage( $main_options );