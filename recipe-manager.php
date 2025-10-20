<?php
/**
 * Plugin Name: Gerenciador de Receitas
 * Description: Gerenciador de receitas com custom post type, archive e single templates.
 * Version: 1.0.0
 * Author: Tiago Reis
 * Text Domain: recipe-manager
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RECIPE_MANAGER_VERSION', '1.0.0');
define('RECIPE_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RECIPE_MANAGER_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main Recipe Manager class
 */
class RecipeManager {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('template_include', array($this, 'template_include'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivate'));
    }
    
    /**
     * Register custom post type for recipes
     */
    private static function register_post_type() {
        $labels = array(
            'name' => __('Receitas', 'recipe-manager'),
            'singular_name' => __('Receita', 'recipe-manager'),
            'menu_name' => __('Receitas', 'recipe-manager'),
            'add_new' => __('Adicionar nova receita', 'recipe-manager'),
            'add_new_item' => __('Adicionar nova receita', 'recipe-manager'),
            'edit_item' => __('Editar receita', 'recipe-manager'),
            'new_item' => __('Nova receita', 'recipe-manager'),
            'view_item' => __('Ver receita', 'recipe-manager'),
            'search_items' => __('Buscar receitas', 'recipe-manager'),
            'not_found' => __('Nenhuma receita encontrada', 'recipe-manager'),
            'not_found_in_trash' => __('Nenhuma receita encontrada na lixeira', 'recipe-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'receitas'),
            'capability_type' => 'post',
            'has_archive' => 'receitas',
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-food',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
        );
        
        register_post_type('recipe', $args);
    }
    
    /**
     * Register taxonomies for recipes
     */
    private static function register_taxonomies() {
        $category_labels = array(
            'name' => __('Categorias de receitas', 'recipe-manager'),
            'singular_name' => __('Categoria de receita', 'recipe-manager'),
        );
        
        register_taxonomy('recipe_category', 'recipe', array(
            'labels' => $category_labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'tipo-de-receita'),
            'show_in_rest' => true,
        ));
    }
    
    /**
     * Add meta boxes for recipe fields
     */
    public function add_meta_boxes() {
        add_meta_box(
            'recipe_details',
            __('Detalhes da receita', 'recipe-manager'),
            array($this, 'recipe_details_meta_box'),
            'recipe',
            'normal',
            'high'
        );
    }
    
    /**
     * Recipe details meta box callback
     */
    public function recipe_details_meta_box($post) {
        wp_nonce_field('recipe_details_nonce', 'recipe_details_nonce');
        
        $prep_time = get_post_meta($post->ID, '_recipe_prep_time', true);
        $ingredients = get_post_meta($post->ID, '_recipe_ingredients', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="recipe_prep_time"><?php _e('Tempo de preparo (minutos)', 'recipe-manager'); ?></label>
                </th>
                <td>
                    <input type="number" id="recipe_prep_time" name="recipe_prep_time" value="<?php echo esc_attr($prep_time); ?>" min="1" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="recipe_ingredients"><?php _e('Ingredientes', 'recipe-manager'); ?></label>
                </th>
                <td>
                    <?php
                    wp_editor(
                        $ingredients,
                        'recipe_ingredients_simple',
                        array(
                            'textarea_name' => 'recipe_ingredients',
                            'media_buttons' => false,
                            'teeny' => true,
                            'quicktags' => true,
                            'tinymce' => false
                        )
                    );
                    ?>
                    <p class="description"><?php _e('Use o editor para formatar os ingredientes. Você pode usar listas, negrito, itálico, etc.', 'recipe-manager'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['recipe_details_nonce']) || !wp_verify_nonce($_POST['recipe_details_nonce'], 'recipe_details_nonce')) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if (isset($_POST['recipe_prep_time'])) {
            update_post_meta($post_id, '_recipe_prep_time', sanitize_text_field($_POST['recipe_prep_time']));
        }
        
        if (isset($_POST['recipe_ingredients'])) {
            update_post_meta($post_id, '_recipe_ingredients', wp_kses_post($_POST['recipe_ingredients']));
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('recipe-manager-style', RECIPE_MANAGER_PLUGIN_URL . 'assets/css/style.css', array(), RECIPE_MANAGER_VERSION);
    }
    
    /**
     * Include custom templates
     */
    public function template_include($template) {
        if (is_post_type_archive('recipe') || is_tax('recipe_category')) {
            $archive_template = RECIPE_MANAGER_PLUGIN_PATH . 'templates/archive-recipe.php';
            if (file_exists($archive_template)) {
                return $archive_template;
            }
        }
        
        if (is_singular('recipe')) {
            $single_template = RECIPE_MANAGER_PLUGIN_PATH . 'templates/single-recipe.php';
            if (file_exists($single_template)) {
                return $single_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Plugin activation hook
     */
    public static function activate() {
        self::register_post_type();
        self::register_taxonomies();
        
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new RecipeManager();
