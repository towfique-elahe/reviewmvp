<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();

                // Custom fields
                $fields = [
                    'status'            => get_post_meta(get_the_ID(), '_course_status', true),
                    'instructor'        => get_post_meta(get_the_ID(), '_course_instructor', true),
                    'overview'          => get_post_meta(get_the_ID(), '_course_overview', true),
                    'about_instructor'  => get_post_meta(get_the_ID(), '_course_about_instructor', true),
                    'price'             => get_post_meta(get_the_ID(), '_course_price', true),
                    'duration'          => get_post_meta(get_the_ID(), '_course_duration', true),
                    'level'             => get_post_meta(get_the_ID(), '_course_level', true),
                    'certificate'       => get_post_meta(get_the_ID(), '_course_certificate', true),
                    'refundable'        => get_post_meta(get_the_ID(), '_course_refundable', true),
                    'language'          => get_post_meta(get_the_ID(), '_course_language', true),
                    'course_url'        => get_post_meta(get_the_ID(), '_course_course_url', true),
                ];
                ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail('large'); ?>
                </div>
                <?php endif; ?>
            </header>

            <div class="entry-meta">
                <span class="posted-on"><?php echo get_the_date(); ?></span>
                <span class="byline"><?php the_author_posts_link(); ?></span>
            </div>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <div class="course-details">
                <?php if ($fields['status']) : ?>
                <p><strong>Status:</strong> <?php echo esc_html($fields['status']); ?></p>
                <?php endif; ?>

                <?php if ($fields['instructor']) : ?>
                <p><strong>Instructor:</strong> <?php echo esc_html($fields['instructor']); ?></p>
                <?php endif; ?>

                <?php if ($fields['overview']) : ?>
                <h2>Overview</h2>
                <p><?php echo esc_html($fields['overview']); ?></p>
                <?php endif; ?>

                <?php if ($fields['about_instructor']) : ?>
                <h2>About Instructor</h2>
                <p><?php echo esc_html($fields['about_instructor']); ?></p>
                <?php endif; ?>

                <?php if ($fields['price']) : ?>
                <p><strong>Price:</strong> $<?php echo esc_html($fields['price']); ?></p>
                <?php endif; ?>

                <?php if ($fields['duration']) : ?>
                <p><strong>Duration:</strong> <?php echo esc_html($fields['duration']); ?></p>
                <?php endif; ?>

                <?php if ($fields['level']) : ?>
                <p><strong>Level:</strong> <?php echo esc_html($fields['level']); ?></p>
                <?php endif; ?>

                <p><strong>Certificate:</strong> <?php echo ($fields['certificate'] === '1') ? 'Yes' : 'No'; ?></p>
                <p><strong>Refundable:</strong> <?php echo ($fields['refundable'] === '1') ? 'Yes' : 'No'; ?></p>

                <?php if ($fields['language']) : ?>
                <p><strong>Language:</strong> <?php echo esc_html($fields['language']); ?></p>
                <?php endif; ?>

                <?php if ($fields['course_url']) : ?>
                <p><a href="<?php echo esc_url($fields['course_url']); ?>" target="_blank" class="course-link">Go to
                        Course</a></p>
                <?php endif; ?>
            </div>

            <footer class="entry-footer">
                <?php the_tags('<span class="tags-links">', ', ', '</span>'); ?>
                <?php edit_post_link(__('Edit', 'reviewmvp'), '<span class="edit-link">', '</span>'); ?>
            </footer>

        </article>

        <?php
            endwhile;
        else :
            echo '<p>' . __('Sorry, no course found.', 'reviewmvp') . '</p>';
        endif;
        ?>

    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>