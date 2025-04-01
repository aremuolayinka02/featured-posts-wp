<?php
class Featured_Posts_Settings
{
    private static $instance = null;
    private $option_name = 'featured_posts_settings';

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {
        // Remove the settings menu hook from constructor
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'set_default_roles'));
    }

    public function init_menu() {
        add_action('admin_menu', array($this, 'add_settings_menu'), 20);
    }

    public function set_default_roles()
    {
        $options = get_option($this->option_name);
        if (!$options) {
            // Set default roles (editor and administrator)
            $default_roles = array('editor');
            update_option($this->option_name, array('allowed_roles' => $default_roles));
        }
    }

    public function add_settings_menu() {
        add_submenu_page(
            'edit.php?post_type=featured-section',
            __('Featured Posts Settings', 'featured-posts'),
            __('Settings', 'featured-posts'),
            'manage_options',
            'featured-posts-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting(
            'featured_posts_settings',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
        
        add_settings_section(
            'featured_posts_main_section',
            __('Role Permissions', 'featured-posts'),
            array($this, 'render_section_description'),
            'featured-posts-settings'
        );
        
        add_settings_field(
            'allowed_roles',
            __('Allowed Roles', 'featured-posts'),
            array($this, 'render_roles_field'),
            'featured-posts-settings',
            'featured_posts_main_section'
        );
    }

    public function get_allowed_roles() {
        $options = get_option($this->option_name);
        return isset($options['allowed_roles']) ? $options['allowed_roles'] : array('administrator', 'editor');
    }

    public function render_section_description()
    {
        echo '<p>' . __('Select which user roles can manage featured sections.', 'featured-posts') . '</p>';
    }

    public function render_roles_field()
    {
        $options = get_option($this->option_name);
        $allowed_roles = isset($options['allowed_roles']) ? $options['allowed_roles'] : array();

        // Get all roles except administrator (admin always has access)
        $roles = wp_roles()->roles;
        unset($roles['administrator']);

        echo '<fieldset>';
        foreach ($roles as $role_id => $role) {
            $checked = in_array($role_id, $allowed_roles) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 8px;">';
            echo '<input type="checkbox" name="' . $this->option_name . '[allowed_roles][]" value="' . esc_attr($role_id) . '" ' . $checked . '>';
            echo ' ' . esc_html($role['name']);
            echo '</label>';
        }
        echo '</fieldset>';
        echo '<p class="description">' . __('Administrators always have full access.', 'featured-posts') . '</p>';
    }

    public function sanitize_settings($input)
    {
        $sanitized_input = array();

        if (isset($input['allowed_roles']) && is_array($input['allowed_roles'])) {
            $sanitized_input['allowed_roles'] = array_map('sanitize_text_field', $input['allowed_roles']);
        } else {
            $sanitized_input['allowed_roles'] = array();
        }

        return $sanitized_input;
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('featured_posts_settings');
                do_settings_sections('featured-posts-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
