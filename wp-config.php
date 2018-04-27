<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'poseosh_landing');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8mb4');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ICT}m+aWZD`N8VYf@8D:F7yiGNlbo=fg8,BqH}Ba?9womry@LVyLp#Na8Y!9BoB|');
define('SECURE_AUTH_KEY',  'UoUO/e8bNh<vanb$P7p-lq+U:8(7C(6NBf&ln,kxKZe1=pZ)G: 1g`s{/[(#P1Hm');
define('LOGGED_IN_KEY',    '6nZC4m>i_)>jo?k $^|~r1x|t1puL[+W/xp<U:Y Ei.E]~u(GnHWOFJgX)|?~]10');
define('NONCE_KEY',        '$h5F5D{`1*^SH?7o@q:c_H$E:_5MSj*b*7n7q8ld;q gAaIV1M$+)tTe6eZp+k>X');
define('AUTH_SALT',        'v@6-D;o~H9H$#pj6RY8bV!zZ{#vDL1C+!a/xn;(@3R<WQDofkMYj9h0~E@_etH:n');
define('SECURE_AUTH_SALT', 'x7Da2=!5JTL>0~/|WTFdKT>ItLzQ4H~Nn2e%&lm~{EOfL8hgQb-Vq]EeV<BBh1RX');
define('LOGGED_IN_SALT',   'eEtbV1:!B$fO$zlC9sA:pSOtR~;FI^ij!Z^)?Jr63@C~x[xTG`elWdg){ADlbZ^2');
define('NONCE_SALT',       ')FS<b7oQ=w(3u?_}5>^W|bP+_Q*3g x{=mE<y)DJsQj-$H R3UD<q2v44hO8_B|!');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'poseosh.landing.im.work.ru');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);


/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
