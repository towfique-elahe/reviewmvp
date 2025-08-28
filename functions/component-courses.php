<?php
/**
 * Function File Name: Component Courses
 * 
 * The file for custom courses list section.
 */

// Shortcode: [browse_courses]

function browse_courses_shortcode() {
  $uid = uniqid('bc_'); // keep multiple shortcodes on one page from colliding
  ob_start(); ?>
<div id="<?php echo esc_attr($uid); ?>" class="bc-wrap">
    <div class="bc-container">
        <h3 class="bc-heading">Browse all courses</h3>
        <div class="bc-grid">
            <!-- Sidebar -->
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
                            <!-- JS will inject categories here -->
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

            <!-- Main -->
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
<?php
  return ob_get_clean();
}
add_shortcode('browse_courses','browse_courses_shortcode');

/**
 * Helper: Get average rating + count for a course
 */
function reviewmvp_get_course_overall_rating_data( $course_id ) {
    global $wpdb;

    $query = $wpdb->prepare("
        SELECT pm.meta_value
        FROM {$wpdb->postmeta} pm
        INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
          AND p.post_type = %s
          AND p.post_status = 'publish'
          AND EXISTS (
              SELECT 1 FROM {$wpdb->postmeta} pm2
              WHERE pm2.post_id = p.ID
              AND pm2.meta_key = %s
              AND pm2.meta_value = %d
          )
    ", '_review_rating', 'course_review', '_review_course', $course_id );

    $ratings = $wpdb->get_col( $query );

    if ( empty( $ratings ) ) {
        return [
            'average' => 0,
            'count'   => 0,
        ];
    }

    $ratings = array_map( 'intval', $ratings );
    $count   = count( $ratings );
    $average = round( array_sum( $ratings ) / $count, 1 );

    return [
        'average' => $average,
        'count'   => $count,
    ];
}

/**
 * Add custom meta fields to REST API for 'course'
 */
function reviewmvp_register_course_rest_fields() {
    $fields = [
        'course_provider'    => '_course_provider',
        'course_short_desc'  => '_course_short_desc',
        'course_price'       => '_course_price',
        'course_duration'    => '_course_duration',
        'course_level'       => '_course_level',
        'course_instructor'  => '_course_instructor',
    ];

    foreach ($fields as $key => $meta_key) {
        register_rest_field('course', $key, [
            'get_callback' => function($object) use ($meta_key) {
                return get_post_meta($object['id'], $meta_key, true);
            },
            'schema' => null,
        ]);
    }
}
add_action('rest_api_init', 'reviewmvp_register_course_rest_fields');

add_action('rest_api_init', function () {
    // Add rating data in REST API for 'course'
    register_rest_field('course', 'rating_data', [
        'get_callback' => function ($object) {
            $course_id = $object['id'];
            $rating = reviewmvp_get_course_overall_rating_data($course_id);
            return [
                'average' => $rating['average'],
                'count'   => $rating['count'],
            ];
        },
        'schema' => [
            'description' => 'Course rating data',
            'type'        => 'object',
        ],
    ]);
    // Add outcomes data in REST API for 'course'
    register_rest_field('course', 'outcomes_data', [
        'get_callback' => function ($object) {
            $course_id = $object['id'];
            $outcomes = reviewmvp_get_course_outcomes($course_id);

            return $outcomes; // already formatted as ["Improved skill (20%)" => "icon.svg", ...]
        },
        'schema' => [
            'description' => 'Course outcomes data',
            'type'        => 'object',
        ],
    ]);
    // Add worth % and recommend % to REST API for 'course'
    register_rest_field('course', 'review_stats', [
        'get_callback' => function ($object) {
            $course_id = $object['id'];
            $stats = reviewmvp_get_course_review_stats($course_id);
            return [
                'worth'      => $stats['worth'] ?? 0,
                'recommend'  => $stats['recommend'] ?? 0,
            ];
        },
        'schema' => [
            'description' => 'Worth and recommendation stats',
            'type'        => 'object',
        ],
    ]);
    // Add categories to REST API for 'course'
    register_rest_field('course', 'course_categories', [
        'get_callback' => function ($object) {
            $terms = get_the_terms($object['id'], 'course_category');
            if (empty($terms) || is_wp_error($terms)) {
                return [];
            }
            return array_map(function ($t) {
                return [
                    'id'   => $t->term_id,
                    'name' => $t->name,
                    'slug' => $t->slug,
                ];
            }, $terms);
        },
        'schema' => [
            'description' => 'Categories for the course',
            'type'        => 'array',
        ],
    ]);
    // Add rating stars to REST API for 'course'
    register_rest_field('course', 'rating_html', [
        'get_callback' => function ($object) {
            $course_id = $object['id'];
            $rating = reviewmvp_get_course_overall_rating_data($course_id);
            return get_rating_stars($rating['average']);
        },
        'schema' => [
            'description' => 'Course rating stars HTML',
            'type'        => 'string',
        ],
    ]);
});