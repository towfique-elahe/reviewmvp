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
                    <ion-icon name="options"></ion-icon> Filter by
                </h4>

                <div class="bc-filter-container">
                    <div class="bc-fgroup" data-group="ratings">
                        <div class="bc-fhead">Ratings <span class="bc-muted">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
                            <label class="bc-check"><input type="checkbox" name="rating" value="4.5"> 4.5 &amp;
                                up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="4"> 4.0 &amp; up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="3.5"> 3.5 &amp;
                                up</label>
                            <label class="bc-check"><input type="checkbox" name="rating" value="3"> 3.0 &amp; up</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="category">
                        <div class="bc-fhead">Category <span class="bc-muted">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
                            <label class="bc-check"><input type="checkbox" name="category" value="UI/UX"> UI/UX</label>
                            <label class="bc-check"><input type="checkbox" name="category" value="Dropshipping">
                                Dropshipping</label>
                            <label class="bc-check"><input type="checkbox" name="category" value="Software development">
                                Software development</label>
                            <label class="bc-check"><input type="checkbox" name="category" value="Data analytics"> Data
                                analytics</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="outcomes">
                        <div class="bc-fhead">Student outcomes <span class="bc-muted">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Improved skills">
                                Improved
                                skills</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Built project"> Built
                                project</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Career boost"> Career
                                boost</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Gained confidence">
                                Gained
                                confidence</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="Earned income"> Earned
                                income</label>
                            <label class="bc-check"><input type="checkbox" name="outcomes" value="No impact"> No
                                impact</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="level">
                        <div class="bc-fhead">Level <span class="bc-muted">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
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
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
                            <label class="bc-check"><input type="checkbox" name="price" value="Paid"> Paid</label>
                            <label class="bc-check"><input type="checkbox" name="price" value="Free"> Free</label>
                        </div>
                    </div>

                    <div class="bc-fgroup" data-group="duration">
                        <div class="bc-fhead">Duration <span class="bc-muted">
                                <ion-icon name="chevron-down-outline"></ion-icon>
                            </span></div>
                        <div class="bc-fbody">
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
                    <div class="bc-count">0 results</div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="bc-btn" data-action="clear">Clear filters</button>
                        <label for="<?php echo esc_attr($uid); ?>_sort" class="bc-muted"
                            style="margin-right:8px;font-size:14px;">Sort by</label>
                        <select id="<?php echo esc_attr($uid); ?>_sort" class="bc-sort">
                            <option value="relevance">relevancy</option>
                            <option value="rating_desc">rating (high → low)</option>
                            <option value="rating_asc">rating (low → high)</option>
                            <option value="duration_asc">duration (short → long)</option>
                            <option value="duration_desc">duration (long → short)</option>
                            <option value="price_asc">price (low → high)</option>
                            <option value="price_desc">price (high → low)</option>
                        </select>
                    </div>
                </div>

                <div id="<?php echo esc_attr($uid); ?>_results"></div>

                <nav class="bc-pagination" id="<?php echo esc_attr($uid); ?>_pagination"></nav>
            </main>
        </div>
    </div>
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
 * Helper: Get overall outcomes data for a course
 */
function reviewmvp_get_overall_course_outcomes($course_id) {
    global $wpdb;

    // fetch all outcomes for reviews of this course
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
    ", '_review_outcome', 'course_review', '_review_course', $course_id);

    $rawOutcomes = $wpdb->get_col($query);

    if (empty($rawOutcomes)) {
        return [];
    }

    // Flatten arrays (stored as serialized arrays in DB)
    $outcomes = [];
    foreach ($rawOutcomes as $val) {
        $arr = maybe_unserialize($val);
        if (is_array($arr)) {
            foreach ($arr as $item) {
                $outcomes[] = $item;
            }
        } else {
            $outcomes[] = $arr;
        }
    }

    if (empty($outcomes)) {
        return [];
    }

    $counts = array_count_values($outcomes);
    $total  = array_sum($counts);

    // map to icons
    $icons = [
        "Improved Skill"    => "icon-improved-skill.svg",
        "Built Project"     => "icon-built-project.svg",
        "No Impact"         => "icon-no-impact.svg",
        "Career Boost"      => "icon-career.svg",
        "Earned Income"     => "icon-income.svg",
        "Gained Confidence" => "icon-confidence.svg",
    ];

    $overall = [];
    foreach ($counts as $label => $count) {
        $percent = $total > 0 ? round(($count / $total) * 100) : 0;
        $key = sprintf("%s (%d%%)", $label, $percent);
        $overall[$key] = $icons[$label] ?? 'icon-default.svg';
    }

    return $overall;
}

/**
 * Add custom meta fields to REST API for 'course'
 */
function reviewmvp_register_course_rest_fields() {
    $fields = [
        'course_provider'  => '_course_provider',
        'course_duration'  => '_course_duration',
        'course_level'     => '_course_level',
        'course_instructor'=> '_course_instructor',
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

// Add rating data in REST API for 'course'
add_action('rest_api_init', function () {
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
});

// Add outcomes data in REST API for 'course'
add_action('rest_api_init', function () {
    register_rest_field('course', 'outcomes_data', [
        'get_callback' => function ($object) {
            $course_id = $object['id'];
            $outcomes = reviewmvp_get_overall_course_outcomes($course_id);

            return $outcomes; // already formatted as ["Improved skill (20%)" => "icon.svg", ...]
        },
        'schema' => [
            'description' => 'Course outcomes data',
            'type'        => 'object',
        ],
    ]);
});