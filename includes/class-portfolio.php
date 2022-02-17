<?php 
/**
 * v1.1.9.4
 */
namespace GineicoLighting\Theme;

class GL_Portfolio {

    public function __construct() {
        add_filter('acf/load_field/name=portfolio_used_products', array($this, 'acf_load_portfolio_used_products'));

    }
    function acf_load_portfolio_used_products( $field ) {
    
        // // reset choices
        // $field['choices'] = array();
        
        
        // // get the textarea value from options page without any formatting
        // $choices = get_field('my_select_values', 'option', false);
    
        
        // // explode the value so that each line is a new array piece
        // $choices = explode("\n", $choices);
    
        
        // // remove any unwanted white space
        // $choices = array_map('trim', $choices);
    
        
        // // loop through array and add to field 'choices'
        // if( is_array($choices) ) {
            
        //     foreach( $choices as $choice ) {
                
        //         $field['choices'][ $choice ] = $choice;
                
        //     }
            
        // }
        // $choices = array();
        // $products = new \WP_Query(array(
        // 'post_type' => 'product',
        // 'post_per_page' => -1,
        // 'orderby' => 'post_title',
        // 'order' => 'ASC'
        // ));
        // if ($products->have_posts()) {
        // global $post;
        // while ($products->have_posts()) {
        //     $products->the_post();
        //     $choices[$post->ID] = $post->post_title;
        // }
        // wp_reset_postdata();
        // }
        // wp_reset_query();
        // $field['choices'] = $choices;
    
        // return the field
        $field['choices'] = $this->get_post_type_values( 'product' );
        return $field;
        
    }
    function get_post_type_values( $post_type ) {
        $values = array();
        $defaults = array(
                            'post_type' => $post_type,
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        );
        $query = new \WP_Query( $defaults );
        if ( $query->found_posts > 0 ) {
            foreach ( $query->posts as $post ) {
              $values[$post->ID] = get_the_title( $post->ID );
            }
        }
        return $values;
    }
   
} // end class

$gl_portfolio = new GL_Portfolio();