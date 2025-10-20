<?php
/**
 * Archive template for recipes
 */

get_header(); ?>

<div class="recipe-archive-container">
    <div class="container">
        <header class="page-header">
            <?php if (is_tax('recipe_category')) : ?>
                <h1 class="page-title">
                    <?php 
                    $current_term = get_queried_object();
                    printf(__('Receitas da categoria: %s', 'recipe-manager'), $current_term->name);
                    ?>
                </h1>
                <?php if ($current_term->description) : ?>
                    <div class="archive-description"><?php echo wp_kses_post($current_term->description); ?></div>
                <?php endif; ?>
            <?php else : ?>
                <h1 class="page-title"><?php _e('Receitas', 'recipe-manager'); ?></h1>
                <?php
                $description = get_the_archive_description();
                if ($description) {
                    echo '<div class="archive-description">' . $description . '</div>';
                }
                ?>
            <?php endif; ?>
        </header>

        <?php
        // Display category navigation
        $categories = get_terms(array(
            'taxonomy' => 'recipe_category',
            'hide_empty' => true,
        ));
        
        if ($categories && !is_wp_error($categories)) :
        ?>
            <div class="category-navigation">
                <h3><?php _e('Categorias:', 'recipe-manager'); ?></h3>
                <div class="category-links">
                    <a href="<?php echo get_post_type_archive_link('recipe'); ?>" 
                       class="category-link <?php echo !is_tax('recipe_category') ? 'active' : ''; ?>">
                        <?php _e('Todas', 'recipe-manager'); ?>
                    </a>
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo get_term_link($category); ?>" 
                           class="category-link <?php echo (is_tax('recipe_category', $category->slug)) ? 'active' : ''; ?>">
                            <?php echo esc_html($category->name); ?>
                            <span class="category-count">(<?php echo $category->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <div class="recipes-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="recipe-<?php the_ID(); ?>" <?php post_class('recipe-card'); ?>>
                        <div class="recipe-card-inner">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="recipe-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="recipe-content">
                                <header class="recipe-header">
                                    <h2 class="recipe-title">
                                        <a href="<?php the_permalink(); ?>" rel="bookmark">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>
                                    
                                    <?php
                                    $prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
                                    if ($prep_time) :
                                    ?>
                                        <div class="recipe-prep-time">
                                            <span class="prep-time-icon">⏱️</span>
                                            <span class="prep-time-text">
                                                <?php printf(__('%d minutos', 'recipe-manager'), intval($prep_time)); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </header>
                                
                                <?php if (has_excerpt()) : ?>
                                    <div class="recipe-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <footer class="recipe-footer">
                                    <a href="<?php the_permalink(); ?>" class="recipe-read-more">
                                        <?php _e('Ver receita', 'recipe-manager'); ?>
                                    </a>
                                </footer>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('Anterior', 'recipe-manager'),
                'next_text' => __('Próximo', 'recipe-manager'),
            ));
            ?>
            
        <?php else : ?>
            <div class="no-recipes-found">
                <h2><?php _e('Nenhuma receita encontrada', 'recipe-manager'); ?></h2>
                <p><?php _e('Desculpe, nenhuma receita foi encontrada. Por favor, tente novamente mais tarde.', 'recipe-manager'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
