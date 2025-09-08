<?php
/**
 * Template: Course Category (taxonomy)
 * URL: /course-category/{term}/
 */
get_header();

$uid  = uniqid('bc_');
$term = get_queried_object(); // WP_Term
?>
<div id="<?php echo esc_attr($uid); ?>" class="bc-wrap"
    data-archive-url="<?php echo esc_url( get_post_type_archive_link('course') ); ?>"
    data-current-category-name="<?php echo esc_attr( $term->name ); ?>"
    data-current-category-slug="<?php echo esc_attr( $term->slug ); ?>">
    <div class="bc-container">
        <img src="<?= get_theme_media_url('background.svg') ?>" alt="background" class="bg">

        <div class="bc-head">
            <h3 class="bc-heading">
                <?php
                echo sprintf( esc_html__('Courses in “%s”', 'textdomain'), esc_html($term->name) );
                ?>
            </h3>
            <div class="bc-searchbar">
                <?php echo do_shortcode('[course_search_box]'); ?>
            </div>
        </div>

        <div class="bc-grid">
            <aside class="bc-sidebar">
                <h4 class="bc-sidebar-heading">
                    <span>
                        <img src="<?php echo get_theme_icon_url('filter-icon.svg'); ?>" alt="Filter icon" /> Filter by
                    </span>
                    <button data-action="clear">Clear filters</button>
                </h4>

                <div class="bc-filter-container">
                    <div class="bc-fgroup" data-group="ratings">
                        <div class="bc-fhead">Ratings <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="rating" value="4.5">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label">
                                    <img src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt=""
                                        aria-hidden="true" />
                                    4.5 &amp; up
                                </span>
                            </label>

                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="rating" value="4">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label">
                                    <img src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt=""
                                        aria-hidden="true" />
                                    4.0 &amp; up
                                </span>
                            </label>

                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="rating" value="3.5">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label">
                                    <img src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt=""
                                        aria-hidden="true" />
                                    3.5 &amp; up
                                </span>
                            </label>

                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="rating" value="3">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label">
                                    <img src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt=""
                                        aria-hidden="true" />
                                    3.0 &amp; up
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="category">
                        <div class="bc-fhead">Category <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open" data-role="category-options">

                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="outcomes">
                        <div class="bc-fhead">Student outcomes <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <?php
                                $outcomes = [
                                "Improved Skill" => "Improved skill",
                                "Built Project"  => "Built project",
                                "Career Boost"   => "Career boost",
                                "Gained Confidence" => "Gained confidence",
                                "Earned Income"  => "Earned income",
                                "No Impact"      => "No impact",
                                ];
                                foreach ($outcomes as $val => $label) :
                            ?>
                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="outcomes"
                                    value="<?php echo esc_attr($val); ?>">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label"><?php echo esc_html($label); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="level">
                        <div class="bc-fhead">Level <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <?php foreach (['Beginner','Intermediate','Advanced'] as $lvl) : ?>
                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="level"
                                    value="<?php echo esc_attr($lvl); ?>">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label"><?php echo esc_html($lvl); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="price">
                        <div class="bc-fhead">Price <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <?php foreach (['Paid','Free'] as $price) : ?>
                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="price"
                                    value="<?php echo esc_attr($price); ?>">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label"><?php echo esc_html($price); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="duration">
                        <div class="bc-fhead">Duration <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <?php
                                $durations = [
                                '0-1'  => '0–1 Hour',
                                '1-3'  => '1–3 Hours',
                                '3-6'  => '3–6 Hours',
                                '6-17' => '6–17 Hours',
                                '17+'  => '17+ Hours',
                                ];
                                foreach ($durations as $val => $label) :
                            ?>
                            <label class="bc-check">
                                <input class="cb-input" type="checkbox" name="duration"
                                    value="<?php echo esc_attr($val); ?>">
                                <span class="cb-box" aria-hidden="true">
                                    <svg class="cb-icon" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 8l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="cb-label"><?php echo esc_html($label); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="bc-main">
                <div class="bc-toolbar">
                    <div class="bc-count-wrapper">
                        <button class="bc-filter-toggle" aria-label="Toggle filters">
                            <img src="<?php echo get_theme_icon_url('filter-icon.svg'); ?>" alt="Filter icon" />
                        </button>
                        <div class="bc-count">0 results</div>
                    </div>
                    <div class="bc-sort-wrapper">
                        <label for="<?php echo esc_attr($uid); ?>_sort" class="bc-muted"
                            style="margin-right:8px;font-size:14px;">Sort by</label>
                        <div class="bc-select">
                            <select id="<?php echo esc_attr($uid); ?>_sort" class="bc-sort">
                                <option value="relevance">relevancy</option>
                                <option value="rating_desc">highest rated</option>
                                <option value="newest">most recently added</option>
                            </select>
                            <ion-icon name="chevron-down-outline" class="bc-select-icon"></ion-icon>
                        </div>
                    </div>
                </div>

                <div id="<?php echo esc_attr($uid); ?>_results"></div>

                <nav class="bc-pagination" id="<?php echo esc_attr($uid); ?>_pagination"></nav>
            </main>
        </div>
    </div>

    <div class="bc-backdrop"></div>
</div>
<?php get_footer(); ?>