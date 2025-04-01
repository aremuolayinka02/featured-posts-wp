<?php
/**
 * Plugin Name: RSA Featured
 * Plugin URI: 
 * Description: Manage featured post sections with single post assignments
 * Version: 1.0.0
 * Author: Olayinka Aremu
 * Author URI: 
 * Text Domain: featured-posts
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Featured_Posts_Plugin {
    
    private static $instance = null;
    private $post_type = 'featured-section';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_featured_post_meta_box'));
        add_action('save_post', array($this, 'save_featured_post_meta'));
        add_filter('manage_featured-section_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_featured-section_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        
        // Safely initialize Elementor integration
        add_action('plugins_loaded', array($this, 'init_elementor_integration'));
    }
    
    public function init_elementor_integration() {
        // Check if Elementor is installed and activated
        if (did_action('elementor/loaded')) {
            // Register all featured sections as custom queries
            add_action('init', array($this, 'register_elementor_queries'), 20);
        }
    }
    
    public function register_elementor_queries() {
        // Get all featured sections
        $featured_sections = get_posts(array(
            'post_type' => $this->post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        foreach ($featured_sections as $section) {
            // Create a sanitized query ID from the section title
            $query_id = sanitize_title($section->post_title);
            
            // Register a custom query for each section
            add_action('elementor/query/' . $query_id, function($query) use ($section) {
                $assigned_post_id = get_post_meta($section->ID, '_assigned_post_id', true);
                if ($assigned_post_id) {
                    $query->set('post_type', 'post');
                    $query->set('post__in', array($assigned_post_id));
                    $query->set('posts_per_page', 1);
                }
            });
        }
    }
    
    public function register_post_type() {
        $labels = array(
            'name'               => _x('Featured Sections', 'post type general name', 'featured-posts'),
            'singular_name'      => _x('Featured Section', 'post type singular name', 'featured-posts'),
            'menu_name'          => _x('RSA Featured', 'admin menu', 'featured-posts'),
            'name_admin_bar'     => _x('Featured Section', 'add new on admin bar', 'featured-posts'),
            'add_new'            => _x('Add New', 'featured section', 'featured-posts'),
            'add_new_item'       => __('Add New Featured Section', 'featured-posts'),
            'new_item'           => __('New Featured Section', 'featured-posts'),
            'edit_item'          => __('Edit Featured Section', 'featured-posts'),
            'view_item'          => __('View Featured Section', 'featured-posts'),
            'all_items'          => __('All Featured Sections', 'featured-posts'),
            'search_items'       => __('Search Featured Sections', 'featured-posts'),
            'not_found'          => __('No featured sections found.', 'featured-posts'),
            'not_found_in_trash' => __('No featured sections found in Trash.', 'featured-posts')
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-star-filled',
            'supports'            => array('title'),
        );
        
        register_post_type($this->post_type, $args);
    }
    
    public function add_featured_post_meta_box() {
        add_meta_box(
            'featured_post_assignment',
            __('Assign Post', 'featured-posts'),
            array($this, 'render_featured_post_meta_box'),
            $this->post_type,
            'normal',
            'high'
        );
    }
    
    public function render_featured_post_meta_box($post) {
        wp_nonce_field('featured_post_assignment', 'featured_post_assignment_nonce');
        
        // Get currently assigned post
        $assigned_post_id = get_post_meta($post->ID, '_assigned_post_id', true);
        
        // Get all published posts
        $published_posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        echo '<p>';
        echo '<label for="assigned_post_id">' . __('Select Post:', 'featured-posts') . '</label><br />';
        echo '<select id="assigned_post_id" name="assigned_post_id" style="width: 100%; max-width: 400px;">';
        echo '<option value="">' . __('-- Select a Post --', 'featured-posts') . '</option>';
        
        foreach ($published_posts as $published_post) {
            echo '<option value="' . esc_attr($published_post->ID) . '" ' . 
                 selected($assigned_post_id, $published_post->ID, false) . '>';
            echo esc_html($published_post->post_title);
            echo '</option>';
        }
        
        echo '</select>';
        echo '</p>';
        
        // If a post is assigned, show a preview link and the query ID
        if ($assigned_post_id) {
            $preview_link = get_permalink($assigned_post_id);
            echo '<p><a href="' . esc_url($preview_link) . '" target="_blank">' . 
                 __('View Assigned Post', 'featured-posts') . '</a></p>';
            
            // Display the Query ID for Elementor
            if (did_action('elementor/loaded')) {
                $query_id = sanitize_title($post->post_title);
                echo '<div class="elementor-query-info" style="margin-top: 15px; padding: 10px; background: #f0f0f1; border-left: 4px solid #2271b1;">';
                echo '<p><strong>' . __('Elementor Query ID:', 'featured-posts') . '</strong><br>';
                echo '<code>' . esc_html($query_id) . '</code></p>';
                echo '<p class="description">' . __('Use this Query ID in Elementor\'s Loop Builder under "Custom Query ID"', 'featured-posts') . '</p>';
                echo '</div>';
            }
        }
    }
    
    public function save_featured_post_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!isset($_POST['featured_post_assignment_nonce']) || 
            !wp_verify_nonce($_POST['featured_post_assignment_nonce'], 'featured_post_assignment')) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['assigned_post_id'])) {
            $assigned_post_id = sanitize_text_field($_POST['assigned_post_id']);
            
            if ($assigned_post_id && get_post_status($assigned_post_id) === 'publish') {
                update_post_meta($post_id, '_assigned_post_id', $assigned_post_id);
            } else {
                delete_post_meta($post_id, '_assigned_post_id');
            }
        }
    }
    
    public function set_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = __('Section Name', 'featured-posts');
        $new_columns['assigned_post'] = __('Assigned Post', 'featured-posts');
        $new_columns['query_id'] = __('Query ID', 'featured-posts');
        $new_columns['date'] = $columns['date'];
        return $new_columns;
    }
    
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'assigned_post':
                $assigned_post_id = get_post_meta($post_id, '_assigned_post_id', true);
                if ($assigned_post_id) {
                    $post = get_post($assigned_post_id);
                    if ($post) {
                        echo '<a href="' . get_edit_post_link($assigned_post_id) . '">' . 
                             esc_html($post->post_title) . '</a>';
                    }
                } else {
                    echo '—';
                }
                break;
                
            case 'query_id':
                if (did_action('elementor/loaded')) {
                    $post = get_post($post_id);
                    echo '<code>' . sanitize_title($post->post_title) . '</code>';
                } else {
                    echo '—';
                }
                break;
        }
    }
}

// Initialize the plugin
Featured_Posts_Plugin::get_instance();