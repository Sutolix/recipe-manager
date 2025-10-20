<?php
/**
 * Single recipe template
 */

get_header(); ?>

<div class="single-recipe-container">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article id="recipe-<?php the_ID(); ?>" <?php post_class('single-recipe'); ?>>
                
                <header class="recipe-header">
                    <h1 class="recipe-title"><?php the_title(); ?></h1>
                    
                    <?php
                    $prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
                    if ($prep_time) :
                    ?>
                        <div class="recipe-meta">
                            <div class="prep-time">
                                <span class="meta-icon">⏱️</span>
                                <span class="meta-label"><?php _e('Tempo de preparo:', 'recipe-manager'); ?></span>
                                <span class="meta-value"><?php printf(__('%d minutos', 'recipe-manager'), intval($prep_time)); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="recipe-featured-image">
                        <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                    </div>
                <?php endif; ?>

                <?php
                    // Display recipe category
                    $categories = get_the_terms(get_the_ID(), 'recipe_category');
                    if ($categories && !is_wp_error($categories)) :
                    ?>
                        <div class="recipe-category-section">
                            <span class="category-label"><?php _e('Categoria:', 'recipe-manager'); ?></span>
                            <div class="category-links">
                                <?php
                                $category_names = array();
                                foreach ($categories as $category) {
                                    $category_names[] = '<a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a>';
                                }
                                echo implode(', ', $category_names);
                                ?>
                            </div>
                        </div>
                <?php endif; ?>

                <div class="recipe-content-wrapper">
                    <div class="recipe-main-content">
                        <!-- Recipe Description -->
                        <div class="recipe-description">
                            <h2><?php _e('Descrição', 'recipe-manager'); ?></h2>
                            <div class="description-content">
                                <?php the_content(); ?>
                            </div>
                        </div>

                        <?php
                        $ingredients = get_post_meta(get_the_ID(), '_recipe_ingredients', true);
                        if ($ingredients) :
                        ?>
                            <div class="recipe-ingredients">
                                <h2><?php _e('Ingredientes', 'recipe-manager'); ?></h2>
                                <div class="ingredients-content">
                                    <?php echo wp_kses_post($ingredients); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="related-recipes-section">
                    <h2><?php _e('Mais receitas', 'recipe-manager'); ?></h2>
                    <div class="related-recipes-grid">
                        <?php

                        $related_args = array(
                            'post_type' => 'recipe',
                            'posts_per_page' => 3,
                            'post__not_in' => array(get_the_ID()),
                            'orderby' => 'rand',
                            'meta_query' => array(
                                array(
                                    'key' => '_thumbnail_id',
                                    'compare' => 'EXISTS'
                                )
                            )
                        );
                        
                        $related_recipes = new WP_Query($related_args);
                        
                        if ($related_recipes->have_posts()) :
                            while ($related_recipes->have_posts()) : $related_recipes->the_post();
                        ?>
                            <div class="related-recipe-card">
                                <div class="related-recipe-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                    </a>
                                </div>
                                <div class="related-recipe-content">
                                    <h3 class="related-recipe-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <?php
                                    $related_prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
                                    if ($related_prep_time) :
                                    ?>
                                        <div class="related-recipe-prep-time">
                                            <span class="prep-time-icon">⏱️</span>
                                            <span class="prep-time-text">
                                                <?php printf(__('%d min', 'recipe-manager'), intval($related_prep_time)); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                        ?>
                            <p class="no-related-recipes"><?php _e('Mais nenhuma receita encontrada.', 'recipe-manager'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
