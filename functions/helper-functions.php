<?php

/**
 * Icon Helper
 */
function get_theme_icon_url($icon_name) {
    $icon_path = get_template_directory() . '/assets/media/' . $icon_name;
    $icon_url  = get_template_directory_uri() . '/assets/media/' . $icon_name;
    $icon_fallback  = get_template_directory_uri() . '/assets/media/icon-fallback.svg';

    return file_exists($icon_path) ? $icon_url : $icon_fallback;
}

/**
 * Media Helper
 */
function get_theme_media_url($media_name) {
    $media_path = get_template_directory() . '/assets/media/' . $media_name;
    $media_url  = get_template_directory_uri() . '/assets/media/' . $media_name;
    $media_fallback  = get_template_directory_uri() . '/assets/media/media-fallback.svg';

    return file_exists($media_path) ? $media_url : $media_fallback;
}

/**
 * Helper function to display rating stars
 */
function get_rating_stars($rating) {
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $stars .= '<span class="r-star active"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        } elseif ($rating >= $i - 0.5) {
            $stars .= '<span class="r-star half active"><ion-icon name="star-half" aria-hidden="true"></ion-icon></span>';
        } else {
            $stars .= '<span class="r-star"><ion-icon name="star" aria-hidden="true"></ion-icon></span>';
        }
    }
    return $stars;
}

// Helper function to get rating data
function reviewmvp_get_course_rating_data($course_id) {
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
    ", '_review_rating', 'course_review', '_review_course', $course_id);

    $ratings = $wpdb->get_col($query);

    if (empty($ratings)) {
        return [
            'average'   => 0,
            'count'     => 0,
            'breakdown' => [5=>0,4=>0,3=>0,2=>0,1=>0],
        ];
    }

    $ratings = array_map('intval', $ratings);
    $count   = count($ratings);
    $average = round(array_sum($ratings) / $count, 1);

    // Count each star
    $counts = array_count_values($ratings);

    // Initialize breakdown (percentages)
    $breakdown = [];
    for ($i = 5; $i >= 1; $i--) {
        $starCount = $counts[$i] ?? 0;
        $breakdown[$i] = $count > 0 ? round(($starCount / $count) * 100) : 0;
    }

    return [
        'average'   => $average,
        'count'     => $count,
        'breakdown' => $breakdown,
    ];
}

// Helper function to get overall outcomes data
function reviewmvp_get_course_outcomes($course_id) {
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

    $reviewCount = count($rawOutcomes); // number of reviews for denominator

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
        // percentage is outcome count divided by total reviews
        $percent = $reviewCount > 0 ? round(($count / $reviewCount) * 100) : 0;
        $key = sprintf("%s (%d%%)", $label, $percent);
        $overall[$key] = $icons[$label] ?? 'icon-default.svg';
    }

    return $overall;
}

// Helper function to get overall recommendation & worth data
function reviewmvp_get_course_review_stats($course_id) {
    global $wpdb;

    // Get recommendations
    $recommendQuery = $wpdb->prepare("
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
    ", '_review_recommend', 'course_review', '_review_course', $course_id);

    $recommendations = $wpdb->get_col($recommendQuery);

    $recommendPercent = 0;
    if (!empty($recommendations)) {
        $totalReviews = count($recommendations);
        $yesCount = 0;
        foreach ($recommendations as $val) {
            if (trim($val) === "Yes, I’d recommend it") {
                $yesCount++;
            }
        }
        $recommendPercent = $totalReviews > 0 ? round(($yesCount / $totalReviews) * 100) : 0;
    }

    // Get worth responses
    $worthQuery = $wpdb->prepare("
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
    ", '_review_worth', 'course_review', '_review_course', $course_id);

    $worthResponses = $wpdb->get_col($worthQuery);

    $worthPercent = 0;
    if (!empty($worthResponses)) {
        $totalReviews = count($worthResponses);
        $yesCount = 0;
        foreach ($worthResponses as $val) {
            if (trim($val) === "Yes, good value") {
                $yesCount++;
            }
        }
        $worthPercent = $totalReviews > 0 ? round(($yesCount / $totalReviews) * 100) : 0;
    }

    return [
        'recommend' => $recommendPercent,
        'worth'     => $worthPercent,
    ];
}