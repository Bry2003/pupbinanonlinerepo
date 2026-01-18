<?php
// Dependencies are handled by index.php
$page_name = 'news';

// Analytics
if(function_exists('log_page_view')) {
    log_page_view($conn, 'news');
}

// Function to fetch all published news
if (!function_exists('fetchAllNews')) {
    function fetchAllNews(mysqli $conn): array {
        $sql = "SELECT n.id, n.title, n.summary, n.image_path, n.publish_date, n.body,
                     a.full_name as author_name
                FROM news n
                LEFT JOIN admins a ON n.created_by = a.id
                WHERE n.is_published = 1
                ORDER BY COALESCE(n.publish_date, n.created_at) DESC";

        $result = $conn->query($sql);
        if (!$result) {
            return [];
        }

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $result->free();

        return $items;
    }
}

// Function to format date
if (!function_exists('formatDate')) {
    function formatDate(?string $date): string {
        if (!$date) {
            return 'Draft';
        }

        $timestamp = strtotime($date);
        return $timestamp ? date('M j, Y', $timestamp) : $date;
    }
}

// Function to create excerpt
if (!function_exists('excerpt')) {
    function excerpt(string $text, int $limit = 150): string {
        $clean = trim(strip_tags($text));
        if ($clean === '') {
            return '';
        }

        if (function_exists('mb_strlen')) {
            if (mb_strlen($clean) <= $limit) {
                return $clean;
            }
            return rtrim(mb_substr($clean, 0, $limit - 3)) . '...';
        }

        if (strlen($clean) <= $limit) {
            return $clean;
        }

        return rtrim(substr($clean, 0, $limit - 3)) . '...';
    }
}

$allNews = fetchAllNews($conn);

// Pagination settings - 9 items per page (3 rows x 3 columns)
$itemsPerPage = 9;
$totalItems = count($allNews);
$totalPages = ceil($totalItems / $itemsPerPage);

// Get current page from URL, default to 1
// Use 'p' parameter to avoid conflict with 'page' parameter used by router
$currentPage = isset($_GET['p']) ? max(1, min((int)$_GET['p'], $totalPages)) : 1;

// Calculate offset and get items for current page
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedNews = array_slice($allNews, $offset, $itemsPerPage);

// Calculate display range
$startItem = $offset + 1;
$endItem = min($offset + $itemsPerPage, $totalItems);
?>

<section class="section" id="all-news">
    <div class="container">
        <article class="card news-wrapper" aria-labelledby="allNewsHeading">
            <div class="news-section-header">
                <span class="news-label">• ALL NEWS</span>
                <h2 class="news-heading" id="allNewsHeading">All News from PUP Biñan</h2>
                <p class="news-intro">Stay updated with the latest news and announcements from PUP Biñan Campus.</p>
            </div>
            <div class="body">
                <?php if (!empty($paginatedNews)): ?>
                    <div class="news-grid news-grid-all">
                        <?php foreach ($paginatedNews as $news): ?>
                            <article class="news-card news-card-all">
                                <div class="news-img">
                                    <img src="<?php echo base_url . (htmlspecialchars($news['image_path'] ?: 'images/pupbackrgound.jpg')); ?>"
                                         alt="<?php echo htmlspecialchars($news['title']); ?>">
                                </div>
                                <div class="news-content">
                                    <h3 class="news-title"><?php echo htmlspecialchars($news['title']); ?></h3>
                                    <div class="news-date-category">
                                        <span class="news-date"><?php echo htmlspecialchars(formatDate($news['publish_date'])); ?></span>
                                        <span class="news-date-separator">•</span>
                                        <span class="news-category-inline"><?php 
                                            // Extract category from title or use default
                                            $category = 'Campus update';
                                            $titleLower = strtolower($news['title']);
                                            if (strpos($titleLower, 'building') !== false || strpos($titleLower, 'campus') !== false || strpos($titleLower, 'facility') !== false || strpos($titleLower, 'inauguration') !== false) {
                                                $category = 'Campus development';
                                            } elseif (strpos($titleLower, 'research') !== false || strpos($titleLower, 'innovation') !== false || strpos($titleLower, 'grant') !== false || strpos($titleLower, 'forum') !== false) {
                                                $category = 'Research & innovation';
                                            } elseif (strpos($titleLower, 'student') !== false || strpos($titleLower, 'leadership') !== false || strpos($titleLower, 'training') !== false) {
                                                $category = 'Student affairs';
                                            } elseif (strpos($titleLower, 'library') !== false) {
                                                $category = 'Library services';
                                            } elseif (strpos($titleLower, 'career') !== false || strpos($titleLower, 'webinar') !== false) {
                                                $category = 'Career development';
                                            } elseif (strpos($titleLower, 'cultural') !== false || strpos($titleLower, 'event') !== false) {
                                                $category = 'Campus life';
                                            } elseif (strpos($titleLower, 'partnership') !== false || strpos($titleLower, 'visit') !== false || strpos($titleLower, 'house') !== false) {
                                                $category = 'Institutional partnership';
                                            }
                                            echo htmlspecialchars($category);
                                        ?></span>
                                    </div>
                                    <p class="news-summary"><?php echo htmlspecialchars(excerpt($news['summary'], 150)); ?></p>
                                    <div class="news-card-footer">
                                        <a href="./?page=pages/news_detail&id=<?php echo (int)$news['id']; ?>" class="news-read-more-btn">Read more</a>
                                        <span class="news-footer-tag"><?php 
                                            // Footer tag based on category
                                            $footerTag = 'Campus update';
                                            $titleLower = strtolower($news['title']);
                                            if (strpos($titleLower, 'building') !== false || strpos($titleLower, 'inauguration') !== false) {
                                                $footerTag = 'Featured';
                                            } elseif (strpos($titleLower, 'partnership') !== false || strpos($titleLower, 'visit') !== false) {
                                                $footerTag = 'Partnership';
                                            } elseif (strpos($titleLower, 'cultural') !== false || strpos($titleLower, 'event') !== false) {
                                                $footerTag = 'Campus life';
                                            } elseif (strpos($titleLower, 'library') !== false) {
                                                $footerTag = 'Student support';
                                            } elseif (strpos($titleLower, 'career') !== false || strpos($titleLower, 'webinar') !== false) {
                                                $footerTag = 'Career services';
                                            }
                                            echo htmlspecialchars($footerTag);
                                        ?></span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($totalPages > 1): ?>
                        <div class="news-pagination">
                            <span class="news-pagination-info">Showing <?php echo $startItem; ?>-<?php echo $endItem; ?> of <?php echo $totalItems; ?> news articles.</span>
                            <div class="news-pagination-controls">
                                <?php if ($currentPage > 1): ?>
                                    <a href="./?page=pages/news&p=<?php echo $currentPage - 1; ?>" class="news-pagination-btn">Prev</a>
                                <?php else: ?>
                                    <span class="news-pagination-btn" disabled>Prev</span>
                                <?php endif; ?>
                                
                                <?php
                                // Show up to 3 page numbers
                                $startPage = 1;
                                $endPage = min(3, $totalPages);
                                
                                // If current page is beyond first 3 pages, show pages around current
                                if ($currentPage > 2 && $totalPages > 3) {
                                    $startPage = $currentPage - 1;
                                    $endPage = min($currentPage + 1, $totalPages);
                                    
                                    // If we're near the end, adjust to show last 3 pages
                                    if ($endPage == $totalPages && $totalPages > 3) {
                                        $startPage = max(1, $totalPages - 2);
                                    }
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span class="news-pagination-btn news-pagination-active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="./?page=pages/news&p=<?php echo $i; ?>" class="news-pagination-btn"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="./?page=pages/news&p=<?php echo $currentPage + 1; ?>" class="news-pagination-btn">Next</a>
                                <?php else: ?>
                                    <span class="news-pagination-btn" disabled>Next</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>No news published yet. Please check back soon.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>
