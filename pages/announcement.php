<?php
// Dependencies handled by index.php
$page_name = 'announcement';

// Analytics
if(function_exists('log_page_view')) {
    log_page_view($conn, 'announcement');
}

// Include helpers safely
$helpersPath = __DIR__ . '/../includes/announcement_helpers.php';
if (file_exists($helpersPath)) {
    require_once $helpersPath;
}

// Helper functions moved to includes/announcement_helpers.php

// Fetch all announcements from events table (single source of truth)
// Only show items where show_in_announcement = 1
if (!function_exists('fetchAdminAnnouncements')) {
    function fetchAdminAnnouncements(mysqli $conn, int $limit = 100): array
    {
        if (function_exists('fetchAnnouncementsFromEvents')) {
            return fetchAnnouncementsFromEvents($conn, $limit, false);
        }
        return [];
    }
}

// Fetch announcements from Admin → Announcements
$allItems = fetchAdminAnnouncements($conn);

// Fetch distinct authors for filter pills
$distinctAuthors = [];
if (function_exists('fetchDistinctAuthors')) {
    $distinctAuthors = fetchDistinctAuthors($conn);
}
?>

<main id="content">
    <!-- HERO SECTION -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-inner">
                <!-- Left Side: Text Content -->
                <div class="page-hero-text">
                    <p class="page-hero-label">CAMPUS UPDATES</p>
                    <h1 class="page-hero-title">Announcements <span class="page-hero-accent">&amp; Events</span></h1>
                    <p class="page-hero-description">Stay updated with the latest announcements and upcoming campus events at PUP Biñan Campus.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="announcements">
        <div class="container">
            <article class="announcements-page-card" aria-labelledby="annoHeading">
                <div class="announcements-page-header">
                    <span class="announcements-page-label">CAMPUS UPDATES</span>
                    <h2 id="annoHeading">All Announcements &amp; Events</h2>
                    <p class="announcements-page-subtitle">Stay updated with the latest campus announcements, academic reminders, and upcoming activities.</p>
                </div>

                <div class="announcements-filters">
                    <button class="filter-pill active" data-filter="all">All</button>
                    <?php foreach ($distinctAuthors as $author): ?>
                        <button class="filter-pill" data-filter="<?php echo htmlspecialchars($author); ?>"><?php echo htmlspecialchars($author); ?></button>
                    <?php endforeach; ?>
                </div>

                <div class="announcements-page-list">
                    <?php if (!empty($allItems)): ?>
                        <?php foreach ($allItems as $item): ?>
                            <?php
                            $author = $item['author'] ?? '';
                            $authorName = $item['author_name'] ?? ($item['author'] ?? '');
                            // Use the same logic as fetchDistinctAuthors for consistency
                            $authorDisplay = !empty($authorName) ? $authorName : $author;
                            $displaySource = !empty($authorName) ? $authorName : strtoupper(str_replace('_', ' ', $item['display_source'] ?? ''));
                            $categoryDisplay = strtoupper(str_replace('_', ' ', $item['category'] ?? ''));
                            ?>
                            <div class="announcement-page-card" id="ann-<?php echo htmlspecialchars($item['id']); ?>" data-author="<?php echo htmlspecialchars($authorDisplay); ?>">
                                <div class="announcement-page-card-accent"></div>
                                <div class="announcement-page-card-content">
                                    <div class="announcement-page-card-badges">
                                        <span class="announcement-page-badge-category"><?php echo htmlspecialchars($categoryDisplay); ?></span>
                                    </div>
                                    <h3 class="announcement-page-card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <div class="announcement-page-card-meta">
                                        <span class="announcement-page-card-date">
                                            Event: <?php echo htmlspecialchars(formatDate($item['start_date'])); ?>
                                            <?php if (!empty($item['location'])): ?>
                                                &middot; <?php echo htmlspecialchars($item['location']); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($item['description'])): ?>
                                        <?php
                                        // Show full description (not truncated) - render as HTML since it comes from rich text editor
                                        // Allow safe HTML tags for formatting
                                        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><div><span><blockquote>';
                                        $fullDescription = strip_tags($item['description'], $allowedTags);
                                        ?>
                                        <div class="announcement-page-card-description"><?php echo $fullDescription; ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($displaySource)): ?>
                                        <p class="announcement-page-card-author">Posted by: <?php echo htmlspecialchars($displaySource); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="announcements-page-empty">No announcements at the moment.</p>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterPills = document.querySelectorAll('.filter-pill');
    const announcementCards = document.querySelectorAll('.announcement-page-card');

    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            // Update active state
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            // Filter cards by author
            announcementCards.forEach(card => {
                const cardAuthor = card.getAttribute('data-author');
                if (filter === 'all' || cardAuthor === filter) {
                    card.classList.remove('filtered-out');
                } else {
                    card.classList.add('filtered-out');
                }
            });
        });
    });

    // Handle anchor scrolling when page loads with hash
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        const targetElement = document.getElementById(hash);
        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }
});
</script>
