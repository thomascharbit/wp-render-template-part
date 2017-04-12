<?php 

/**
 * An alternative to the native WP function `get_template_part` that
 * can pass arguments to the local scope
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param array  $args The arguments to pass to this file. Optional.
 * @param bool   $echo Wether to echo or return the rendered template. Default to true.
 * 
 * @return string The rendered template if $echo is false.
 */

use Symfony\Component\DomCrawler\Crawler;


function render_template_part( $slug, $name = null, $args = array(), $echo = true) {
    global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

    // If template name is omitted, shift parameters
    if ( is_array($name) ) {
        $echo = $args;
        $args = $name;
        $name = null;
    }

    do_action( "get_template_part_{$slug}", $slug, $name );

    do_action( "render_template_part_{$slug}", $slug, $name, $args, $echo );
    
    $args = apply_filters( "render_template_part_{$slug}_args", $args, $slug, $name, $echo );

    $templates = array();

    $name = (string) $name;
    if ( '' !== $name ) {
        if ( strpos($name, ' ') !== false ) {
            $cssSelector = substr($name, strrpos($name, ' ') + 1);
            $name = substr($name, 0, strrpos($name, ' '));
        }
        $templates[] = "{$slug}-{$name}.php";
    }

    if ( strpos($slug, ' ') !== false ) {
        $cssSelector = substr($slug, strrpos($slug, ' ') + 1);
        $slug = substr($slug, 0, strrpos($slug, ' '));
    }
    $templates[] = "{$slug}.php";

    if ( '' === locate_template($templates) ) {
        return;
    }

    if ( is_array( $wp_query->query_vars ) ) {
        extract( $wp_query->query_vars, EXTR_SKIP );
    }

    if ( isset( $s ) ) {
        $s = esc_attr( $s );
    }

    if ( is_array($args) ) {
        extract($args, EXTR_SKIP);
    }

    // If $query variable extracted, assume we need to set up as $wp_query
    if ( isset($query) ) {
        $wp_query = $query;
    }

    // If $post_object variable extracted, assume we need to set up as $post
    if ( isset($post_object) ) {
        $post = $post_object;
        setup_postdata( $post );
    }

    ob_start();
    require( locate_template($templates) );
    $html = ob_get_clean();

    if ( isset($cssSelector) ) {
        $crawler = new Crawler();
        $crawler->addContent($html);
        try {
            $html = $crawler->filter($cssSelector)->html();
        }
        catch (Exception $e) {
            $html = '';
        }
    }

    if ( true === $echo ) {
        echo $html;
    }

    if ( isset($query) ) {
        wp_reset_query();
    }
    
    if ( isset($post_object) ) {
        wp_reset_postdata();
    }

    if ( false === $echo ) {
        return $html;
    }

}
