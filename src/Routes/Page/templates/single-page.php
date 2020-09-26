<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' ); ?>

<main id="page-content" class="l-main"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
    <?php
    do_action( 'us_before_page' );

    // Get ACF header image
    global $post;
    $header_img = get_field('page_header_image', $post->ID);
    if ($header_img) {
        ['url' => $url, 'width' => $w, 'height' => $h] = $header_img;
        $caption = get_field('page_header_image_caption', $post->ID);

        ?>
        <div class="header-image">
            <div class="img-container" style="height: calc(100vw * <?php echo $h / $w ?>);">
                <img data-src="<?php echo $url ?>" class="image"/>
            </div>
            <div class="caption"><?php echo $caption ?></div>
        </div>
        <?php
    }

    if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {

        // Titlebar, if it is enabled in Theme Options
        us_load_template( 'templates/titlebar' );

        // START wrapper for Sidebar
        us_load_template( 'templates/sidebar', array( 'place' => 'before' ) );
    }

    while ( have_posts() ) {
        the_post();

        $content_area_id = us_get_page_area_id( 'content' );

        if ( $content_area_id != '' AND get_post_status( $content_area_id ) != FALSE ) {
            us_load_template( 'templates/content' );
        } else {
            $no_filter_content = get_the_content();
            $the_content = apply_filters( 'the_content', get_the_content() );

            // The page may be paginated itself via <!--nextpage--> tags
            $pagination = us_wp_link_pages();

            // If content has no sections, we'll create them manually
            $has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
            if ( ! ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) AND ( ! $has_own_sections OR get_post_type() == 'tribe_events' ) ) {
                $the_content = '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '"><div class="l-section-h i-cf">' . $the_content . $pagination . '</div></section>';
            } elseif ( ! empty( $pagination ) ) {
                $the_content .= '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '"><div class="l-section-h i-cf">' . $pagination . '</div></section>';
            }

            echo $the_content;

            // Post comments
            if ( comments_open() OR get_comments_number() != '0' ) {

                $show_comments = TRUE;
                // Check comments option of Events Calendar plugin
                if ( function_exists( 'tribe_get_option' ) AND get_post_type() == 'tribe_events' ) {
                    $show_comments = tribe_get_option( 'showComments' );
                }

                if ( $show_comments ) {
                    ?>
                <section class="l-section height_<?php echo us_get_option( 'row_height', 'medium' ) ?> for_comments">
                    <div class="l-section-h i-cf"><?php
                        wp_enqueue_script( 'comment-reply' );
                        comments_template();
                        ?></div>
                    </section><?php
                }
            }
        }
    }

    if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {
        // AFTER wrapper for Sidebar
        us_load_template( 'templates/sidebar', array( 'place' => 'after' ) );
    }

    do_action( 'us_after_page' );
    ?>
</main>
