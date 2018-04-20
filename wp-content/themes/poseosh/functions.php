<?php

function styleConnect(){

    wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css');

    wp_enqueue_style('font-style', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700&amp;subset=cyrillic');

    wp_enqueue_script('js', get_template_directory_uri() . 'js/script.min.js');

}
add_action( 'wp_enqueue_scripts', 'styleConnect' );

if (function_exists('add_theme_support')) {
    add_theme_support('menus');
}


/* Метабокс для для SEO
 * Этап 1. Добавление
 */
function true_meta_boxes() {
    add_meta_box('truediv', 'SEO', 'edit_box', 'page', 'normal', 'high');
}

add_action( 'admin_menu', 'true_meta_boxes' );
/*
 * также можно использовать и другие хуки:
 * add_action( 'add_meta_boxes', 'tr_meta_boxes' );
 * если версия WordPress ниже 3.0, то
 * add_action( 'admin_init', 'tr_meta_boxes', 1 );
 */

/*
 * Этап 2. Заполнение
 */
function edit_box($post) {
    wp_nonce_field( basename( __FILE__ ), 'seo_metabox_nonce' );
    /*
     * добавляем текстовое поле
     */
    //$html .= '<label>Title <input type="text" name="seotitle" value="' . get_post_meta($post->ID, 'seo_title',true) . '" /></label> ';
    //$html .= '</br> ';
    /*
     * добавляем текстовое поле
     */
    $html .= '<label>Keywords<input type="text" name="metak" value="' . get_post_meta($post->ID, 'meta_k',true) . '" /></label> ';
    $html .= '</br> ';
    /*
     * добавляем текстовое поле
     */
    $html .= '<label>Description <input type="text" name="metad" value="' . get_post_meta($post->ID, 'meta_d',true) . '" /></label> ';
    $html .= '</br> ';

    echo $html;
}

/*
 * Этап 3. Сохранение
 */
function true_save_box_data ( $post_id ) {
    // проверяем, пришёл ли запрос со страницы с метабоксом
    if ( !isset( $_POST['seo_metabox_nonce'] )
        || !wp_verify_nonce( $_POST['seo_metabox_nonce'], basename( __FILE__ ) ) )
        return $post_id;
    // проверяем, является ли запрос автосохранением
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
    // проверяем, права пользователя, может ли он редактировать записи
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
    // теперь также проверим тип записи
    $post = get_post($post_id);
    if ($post->post_type == 'page') { // укажите собственный
        //update_post_meta($post_id, 'seo_title', esc_attr($_POST['seotitle']));
        update_post_meta($post_id, 'meta_k', esc_attr($_POST['metak']));
        update_post_meta($post_id, 'meta_d', esc_attr($_POST['metad']));
    }
    return $post_id;
}
add_action('save_post', 'true_save_box_data');



/*
 * Добавление собтсвенного меню
 */
class New_Walker_Nav_Menu extends Walker_Nav_Menu {
    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output
     * @param object $item Объект элемента меню, подробнее ниже.
     * @param int $depth Уровень вложенности элемента меню.
     * @param object $args Параметры функции wp_nav_menu
     */
    function start_el(&$output, $item, $depth, $args) {
        global $wp_query;
        /*
         * Некоторые из параметров объекта $item
         * ID - ID самого элемента меню, а не объекта на который он ссылается
         * menu_item_parent - ID родительского элемента меню
         * classes - массив классов элемента меню
         * post_date - дата добавления
         * post_modified - дата последнего изменения
         * post_author - ID пользователя, добавившего этот элемент меню
         * title - заголовок элемента меню
         * url - ссылка
         * attr_title - HTML-атрибут title ссылки
         * xfn - атрибут rel
         * target - атрибут target
         * current - равен 1, если является текущим элементов
         * current_item_ancestor - равен 1, если текущим является вложенный элемент
         * current_item_parent - равен 1, если текущим является вложенный элемент
         * menu_order - порядок в меню
         * object_id - ID объекта меню
         * type - тип объекта меню (таксономия, пост, произвольно)
         * object - какая это таксономия / какой тип поста (page /category / post_tag и т д)
         * type_label - название данного типа с локализацией (Рубрика, Страница)
         * post_parent - ID родительского поста / категории
         * post_title - заголовок, который был у поста, когда он был добавлен в меню
         * post_name - ярлык, который был у поста при его добавлении в меню
         */
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        /*
         * Генерируем строку с CSS-классами элемента меню
         */
        $class_names = $value = '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // функция join превращает массив в строку
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = ' class="' . esc_attr( $class_names ) . '"';

        /*
         * Генерируем ID элемента
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

        /*
         * Генерируем элемент меню
         */
        $output .= $indent . '<li' . $id . $value . $class_names .'>';

        // атрибуты элемента, title="", rel="", target="" и href=""
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        // ссылка и околоссылочный текст
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}