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
                            <label class="bc-check"><input type="checkbox" name="rating" value="4.5"><img
                                    src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt="Filter icon" />
                                4.5
                                &amp;
                                up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="4"><img
                                    src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt="Filter icon" />
                                4.0
                                &amp; up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="3.5"><img
                                    src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt="Filter icon" />
                                3.5
                                &amp;
                                up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="3"><img
                                    src="<?php echo get_theme_icon_url('filter-star-icon.svg'); ?>" alt="Filter icon" />
                                3.0
                                &amp; up</label>
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
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Improved Skill">
                                Improved
                                skill</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Built Project"> Built
                                project</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Career Boost"> Career
                                boost</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Gained Confidence">
                                Gained
                                confidence</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Earned Income"> Earned
                                income</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="No Impact"> No
                                impact</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="level">
                        <div class="bc-fhead">Level <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <label class="bc-check"><input type="checkbox" name="level" value="Beginner">
                                Beginner</label>
                            <label class="bc-check"><input type="checkbox" name="level" value="Intermediate">
                                Intermediate</label>
                            <label class="bc-check"><input type="checkbox" name="level" value="Advanced">
                                Advanced</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="price">
                        <div class="bc-fhead">Price <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <label class="bc-check"><input type="checkbox" name="price" value="Paid"> Paid</label>
                            <label class="bc-check"><input type="checkbox" name="price" value="Free"> Free</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="duration">
                        <div class="bc-fhead">Duration <span class="bc-muted">
                                <ion-icon name="chevron-down-outline" class="open"></ion-icon>
                            </span></div>
                        <div class="bc-fbody open">
                            <label class="bc-check"><input type="checkbox" name="duration" value="0-1"> 0–1 Hour</label>
                            <label class="bc-check"><input type="checkbox" name="duration" value="1-3"> 1–3
                                Hours</label>
                            <label class="bc-check"><input type="checkbox" name="duration" value="3-6"> 3–6
                                Hours</label>
                            <label class="bc-check"><input type="checkbox" name="duration" value="6-17"> 6–17
                                Hours</label>
                            <label class="bc-check"><input type="checkbox" name="duration" value="17+"> 17+
                                Hours</label>
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