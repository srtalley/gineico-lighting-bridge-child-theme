<?php 
/**
 * v2.1.0
 */
namespace GineicoLighting\Theme;

class GL_Redirect {

    public function __construct() {
        add_filter( 'query_vars', array($this, 'add_query_vars_filter') );

        add_action('template_redirect', array($this, 'get_gi_ref_links') );
    }

    /**
     * Add the redirect parameter filter
     */
    public function add_query_vars_filter( $vars ){
        $vars[] = "gi_ref";
        return $vars;
    }
    /**
     * Check for 404 errors and potentially redirect
     */
    function get_gi_ref_links() {
        // see if the query var exists
        if(get_query_var( 'gi_ref' )) {

            // remove_query_arg('gi_ref', false); //FALSE, means to use the current URL
            if( is_404() ) {
                $this->check_for_alternate_slug($_SERVER['REQUEST_URI']);
            } else {
                // not a 404 so we need to remove the query arg
                wp_redirect(remove_query_arg('gi_ref', false), 301); //FALSE, means to use the current URL
                exit();
            }
        }
    }

    /**
     * Searches for possible alternate pages
     */
    public function check_for_alternate_slug($slug) {

        $slug = remove_query_arg('gi_ref', strtolower($slug));
        $post_types = array('product', 'post', 'page');
        foreach($post_types as $post_type) {
            $post_exists = get_page_by_path( $slug, OBJECT, $post_type );
            if ( $post_exists ) {
                // redirect to the first match
                wp_redirect('/' . $post_type . $slug, 301);
                exit();
            } else {
                // try modifying the slug
                $new_slug = str_replace('_', '-', $slug);
                $post_exists_with_new_slug = get_page_by_path( $new_slug, OBJECT, $post_type );
                if($post_exists_with_new_slug) {
                    // redirect to the first match
                    wp_redirect('/' . $post_type . $new_slug, 301);
                    exit();

                // } else {
                //     wl('No ' . $post_type . ' exists with this slug ' . $new_slug );
                }
                // has failed all checks. redirect to products
                // wl('No ' . $post_type . ' exists with this slug.');
                wp_redirect(site_url('/products'), 301);
                exit();
            }
        }

    }
} // end class

$gl_redirect = new GL_Redirect();