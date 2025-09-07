## Review MVP

- Theme Name: Review MVP
- Author: Towfique Elahe
- Author URI: https://towfique-elahe.framer.website/
- Description: A custom WordPress theme for course review system, compatible with Elementor and WooCommerce.
- Version: 1.2.6
- License: GNU General Public License v3 or later
- License URI: http://www.gnu.org/licenses/gpl-3.0.html
- Text Domain: review-mvp
- Tags: review, course review, review system, course rating, course review theme, review theme,

## Version History

## V1.2.6

- Added: Pending-count badges on Courses and Reviews admin menus.
- Added: Reject workflow for both CPTs (custom status + row/bulk actions).

## V1.2.5

- Fixed: The Add Review page now clears saved form drafts when the session ends (e.g., logout or account switch).

## V1.2.4

- Added: Course archive template with the integrated [course_search_box] shortcode, replacing the previous page + shortcode flow.
- Added: Course category taxonomy template mirroring the archive UI, with the current category auto-selected in the sidebar filters.

## V1.2.3

- Fixed: LinkedIn connect on the Add Review page no longer switches accounts when an existing user with the same email is found. The entire connection flow has been reworked to handle all scenarios correctly.

## V1.2.2

- **Release: General availability (GA) build.**

## V1.2.1

- Updated: Write a Review — revamped LinkedIn connect flow with auto-login on connect; form inputs now persist via local storage and are restored after reload.
- Updated: Login & Register — LinkedIn sign-in supported; users are flagged as “LinkedIn connected.”
- Fixed: Course Archive — layout and styling tweaks.

## V1.2.0

- Added: New WP Admin page – “API Credentials” for storing LinkedIn & Google API keys.
- Updated: Write a Review page – fixed LinkedIn profile connect data storage issue and automatically sets verified status.
- Updated: Improved design and responsiveness for Reviewer Dashboard, Profile, and Leaderboard pages.
- Fixed: Font overwrite issue by adding font CDN via WordPress enqueue script.

## V1.1.9

- Updated: Write a Review page (course title search updated with add missing course option).
- Updated: Course search (improved behaviour with enter key, integrated search in browse courses page).
- Updated: Add Course page (frontend shortcode collects reviewer data, WP Admin add course page with reviewer option, CPT updated with reviewer meta field).
- Updated: Course & Review flow (guest can add review for their submitted course, reviewer can directly review their submitted course).
- Updated: Reviewer Dashboard (added stats cards for reviews and courses).
- Added: Reviewer Profile management page (update display name and change password).
- Added: Reviewer Leaderboard page (rankings with reviews and course suggestions).

## V1.1.8

- Added: Custom shortcode for the Reviewer Dashboard.
- Updated: Custom shortcodes for authentication forms (Login, Signup, Forgot Password, and Reset Password) with full functionality.

## V1.1.7

- Updated: Completed Write a Review page with full functionality and final design.
- Added: Custom shortcodes for authentication forms — Login, Signup, Forgot Password, and Reset Password.
- Note: These forms are fully functional without LinkedIn and Google login integration.

## V1.1.6

- Fixed: CSS overwrite issues (font, color, etc.) in course archive, single course page, and add course form.
- Updated: Write a Review page (Level input functional, platform field removed, added search in course title selection, changed options UI from radio to background select, added & functional "Review Anonymously", fixed draft saving to use course title instead of ID, added label "Your overall feedback", field-level validation with scroll-to-error, submit button style update, LinkedIn profile connect).
- Updated: Single course page to show level in reviews.
- Updated: WP Admin Add Review page (recommend & worth changed to select, added select for level, added placeholders).

## V1.1.5

- Updated: Single course page (Platform name dynamic, condition-based components, scroll to top for long reviews, hover bg color #E94C89, SVG real review badge, font/letter spacing/weight fixes, outcomes alignment, icon radius/margin fixes, smooth "see more" button, responsiveness).
- Updated: WP Admin Add Course Page (Changed provider label to platform, added short description field, added placeholders).
- Updated: Browse courses page (Course description replaced with short description).

## V1.1.4

- Added: **Short Description** meta field for CPT `course`.
- Added: Short Description field to Add/Edit Course screen in WP Admin.
- Updated: Course archive page to display the Short Description under each course listing.

## V1.1.3

- Added: Custom taxonomy **course_category** for CPT `course`.
- Updated: Course archive page with repositioned Clear Filters button, fixed sidebar dropdown icon toggle issue, simplified Sort by options (Relevancy, Highest rated, Most recently added), dynamic Worth % and Recommend % stats, corrected course outcomes percentage display, dynamic course category filter options, and fully functional filtering for ratings, outcomes, level, price, duration, and categories.

## V1.1.2

- Added: Add review page completed, fully functional.

## V1.1.1

- Added: Add missing course page completed with design and functionality.
- Added: Add review page structuring completed.

## V1.1.0

- Updated: Courses shortcode template updated with dynamic data.

## V1.0.9

- Updated: Single course template completed with dynamic data.

## V1.0.8

- Added: CPT for course reviews, now admin can add reviews under course.
- Updated: Single course template now shows real views partially.

## V1.0.7

- Updated: Single course details view shortcode converted to single course template and made it dynamic partially.
- Updated: Courses list view shortcode updated, dynamic partially.

## V1.0.6

- Updated: Single course details view completed.

## V1.0.5.2

- Added: Single course details view (Initial).

## V1.0.5.1

- Updated: Courses list view.

## V1.0.5

- Updated: Courses list view.

## V1.0.4

- Updated: Course search bar.
- Added: Courses list view (Initial).

## V1.0.3

- 404 page desing completed.

## V1.0.2

- Added new function file 'featured-reviews.php', added featured reviews card (desing only) via shortcode.

## V1.0.1

- Added new function file 'role-management', removed all wp users role except administrator and added new role reviewer.
- Added new function file 'course-search', added course search input via shortcode for now, will be updated with search functionality.
- Added css file for course search.
- Updated root css file to include theme global colors, fonts etc

## V1.0.0

- Added custom post type course.
- Added course management in wp dashboard.
- Added single course page template.
