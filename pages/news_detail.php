<?php
// Dependencies handled by index.php
$page_name = 'news_detail';

// Analytics
if(function_exists('log_page_view')) {
    log_page_view($conn, 'news_detail');
}

// Get news ID from URL
$newsId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($newsId <= 0) {
    echo "<script>location.replace('./?page=pages/news');</script>";
    exit;
}

// Fetch single news item
if (!function_exists('fetchNewsItem')) {
    function fetchNewsItem(mysqli $conn, int $id): ?array {
        $stmt = $conn->prepare("SELECT n.id, n.title, n.summary, n.body, n.image_path, n.publish_date, n.created_by,
                                     a.full_name as author_name
                              FROM news n
                              LEFT JOIN admins a ON n.created_by = a.id
                              WHERE n.id = ? AND n.is_published = 1");
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
}

// Format date
if (!function_exists('formatDate')) {
    function formatDate(?string $date): string {
        if (!$date) return 'Draft';
        $timestamp = strtotime($date);
        return $timestamp ? date('F j, Y', $timestamp) : $date;
    }
}

/**
 * Render rich text safely.
 */
if (!function_exists('renderRichText')) {
    function renderRichText(?string $value): string {
        if ($value === null || $value === '') return '';

        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // remove script/style blocks
        $decoded = preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $decoded);

        // remove attributes from opening tags (keep tags)
        $decoded = preg_replace_callback('/<([a-z][a-z0-9]*)(\s+[^>]*)?>/i', function($m){
            $tag = strtolower($m[1]);
            if ($tag === 'a' && preg_match('/href\s*=\s*["\']([^"\']*)["\']/i', $m[2] ?? '', $h)) {
                $href = htmlspecialchars($h[1], ENT_QUOTES, 'UTF-8');
                return '<a href="'.$href.'">';
            }
            return '<'.$tag.'>';
        }, $decoded);

        // allowlist tags
        $allowed = '<p><br><strong><em><b><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote>';
        $decoded = strip_tags($decoded, $allowed);

        // extra cleanup
        $decoded = preg_replace('/\s+/', ' ', $decoded);
        $decoded = preg_replace('/>\s+</', '><', $decoded);

        return trim($decoded);
    }
}

$newsItem = fetchNewsItem($conn, $newsId);
if (!$newsItem) {
    echo "<script>location.replace('./?page=pages/news');</script>";
    exit;
}
?>

<section class="section" id="news-detail">
    <div class="container">
        <div class="back-link-wrapper">
            <a href="./?page=pages/news" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to News
            </a>
        </div>

        <article class="news-detail-wrapper">
            <div class="news-detail-header">
                <h1 class="news-detail-title"><?php echo htmlspecialchars($newsItem['title']); ?></h1>
                <div class="news-detail-meta">
                    <span class="news-detail-date">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo htmlspecialchars(formatDate($newsItem['publish_date'])); ?>
                    </span>
                    <?php if (!empty($newsItem['author_name'])): ?>
                        <span class="news-detail-author">
                            <i class="far fa-user"></i>
                            By <?php echo htmlspecialchars($newsItem['author_name']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="news-detail-featured-image">
                <img src="<?php echo base_url . (htmlspecialchars($newsItem['image_path'] ?: 'images/pupbackrgound.jpg')); ?>"
                     alt="<?php echo htmlspecialchars($newsItem['title']); ?>">
            </div>

            <div class="news-detail-content">
                <?php echo renderRichText($newsItem['body']); ?>
            </div>

            <div class="news-detail-footer">
                <div class="share-buttons">
                    <span>Share this story:</span>
                    <a href="#" class="share-btn facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="share-btn twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="share-btn linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </article>
    </div>
</section>
