<?php
// Include analytics and helpers
require_once __DIR__ . '/DATAANALYTICS/page_visits.php';
require_once __DIR__ . '/DATAANALYTICS/page_views.php';
require_once __DIR__ . '/includes/announcement_helpers.php';

// --- Analytics Logic ---
$ip = $_SERVER['REMOTE_ADDR'];

// Ensure visitors table exists and record visit
$conn->query("CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$stmt = $conn->prepare("INSERT INTO visitors (ip_address) VALUES (?)");
if ($stmt) {
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}

// Record homepage visit
$today = date('Y-m-d');
record_page_visit($conn, 'homepage.php', $ip, $today);
log_page_view($conn, 'homepage');

// --- Helper Functions from homepage.php ---

$settingDefaults = [
  'site_title' => 'POLYTECHNIC UNIVERSITY OF THE PHILIPPINES',
  'campus_name' => 'Biñan Campus',
  'hero_heading' => 'Serving the Nation through Quality Public Education',
  'hero_text' => 'Welcome to the PUP Biñan Campus homepage - your hub for announcements, admissions, academic programs, student services, and campus life.',
  'logo_path' => 'images/PUPLogo.png',
  'hero_image_path' => '',
  'hero_video_path' => '',
  'footer_about' => 'PUP Biñan Campus is part of the country\'s largest state university system, committed to accessible and excellent public higher education.',
  'footer_address' => "Sto. Tomas, Biñan, Laguna\nPhilippines 4024",
  'footer_email' => 'info.binan@pup.edu.ph',
  'footer_phone' => '(xxx) xxx xxxx'
];

function fetchSettings(mysqli $conn, array $keys): array
{
  if (empty($keys)) return [];
  
  // Check if site_settings table exists
  $check = $conn->query("SHOW TABLES LIKE 'site_settings'");
  if ($check->num_rows == 0) return [];

  $placeholders = implode(',', array_fill(0, count($keys), '?'));
  $types = str_repeat('s', count($keys));
  $sql = "SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ($placeholders)";

  $stmt = $conn->prepare($sql);
  if (!$stmt) return [];

  $stmt->bind_param($types, ...$keys);
  $stmt->execute();
  $result = $stmt->get_result();

  $settings = [];
  while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
  }
  $stmt->close();

  return $settings;
}

function fetchLatestArchives(mysqli $conn, int $limit = 4): array
{
  $sql = "SELECT a.*, CONCAT(s.firstname, ' ', s.lastname) as student_name 
          FROM archive_list a 
          LEFT JOIN student_list s ON a.student_id = s.id 
          WHERE a.status = 1 
          ORDER BY a.date_created DESC 
          LIMIT ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) return [];
  $stmt->bind_param('i', $limit);
  $stmt->execute();
  $result = $stmt->get_result();
  $items = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $items;
}

function fetchAnnouncements(mysqli $conn, int $limit = 3): array
{
  return fetchAnnouncementsFromEvents($conn, $limit, true);
}

function fetchEventsForMonth(mysqli $conn, int $year, int $month): array
{
  $startDate = sprintf('%04d-%02d-01', $year, $month);
  $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
  $endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);

  $sql = "SELECT id, title, description, start_date, end_date, location, category, show_on_homepage
            FROM events
            WHERE (start_date <= ? AND end_date >= ?)
               OR (start_date >= ? AND start_date <= ?)
            ORDER BY start_date ASC";

  $stmt = $conn->prepare($sql);
  if (!$stmt) return [];

  $stmt->bind_param('ssss', $endDate, $startDate, $startDate, $endDate);
  $stmt->execute();
  $result = $stmt->get_result();
  $items = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  return $items;
}

function buildMonthCalendar(int $year, int $month, array $events): string
{
  $today = date('Y-m-d');
  $currentYear = (int)date('Y');
  $currentMonth = (int)date('m');
  $currentDay = (int)date('d');

  $firstDay = mktime(0, 0, 0, $month, 1, $year);
  $daysInMonth = date('t', $firstDay);
  $dayOfWeek = date('w', $firstDay);

  $eventsByDay = [];
  foreach ($events as $event) {
    $eventStart = strtotime($event['start_date']);
    $eventEnd = strtotime($event['end_date'] ?: $event['start_date']);
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
      $dayTimestamp = mktime(0, 0, 0, $month, $day, $year);
      if ($dayTimestamp >= $eventStart && $dayTimestamp <= $eventEnd) {
        $dayKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
        if (!isset($eventsByDay[$dayKey])) {
          $eventsByDay[$dayKey] = [];
        }
        $eventsByDay[$dayKey][] = $event;
      }
    }
  }

  $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  
  $html = '<div class="calendar-grid">';
  $html .= '<div class="calendar-header">';
  foreach ($dayNames as $dayName) {
    $html .= '<div class="calendar-day-name">' . htmlspecialchars($dayName) . '</div>';
  }
  $html .= '</div>';
  $html .= '<div class="calendar-days">';

  for ($i = 0; $i < $dayOfWeek; $i++) {
    $html .= '<div class="calendar-day calendar-day-empty"></div>';
  }

  for ($day = 1; $day <= $daysInMonth; $day++) {
    $dayKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $isToday = ($year == $currentYear && $month == $currentMonth && $day == $currentDay);
    $hasEvents = isset($eventsByDay[$dayKey]) && !empty($eventsByDay[$dayKey]);

    $classes = ['calendar-day'];
    if ($isToday) $classes[] = 'calendar-day-today';
    if ($hasEvents) $classes[] = 'calendar-day-has-events';

    $html .= '<div class="' . implode(' ', $classes) . '">';
    $html .= '<div class="calendar-day-number">' . $day . '</div>';
    
    if ($hasEvents) {
      $html .= '<div class="calendar-day-events">';
      foreach ($eventsByDay[$dayKey] as $event) {
        $title = htmlspecialchars($event['title']);
        if (mb_strlen($title) > 20) $title = mb_substr($title, 0, 17) . '...';
        $categoryClass = 'cat-' . strtolower(str_replace(' & ', '-', str_replace(' ', '-', $event['category'] ?? 'Events')));
        $eventId = isset($event['id']) ? (int)$event['id'] : 0;
        $html .= '<a href="./?page=pages/announcement&id=' . $eventId . '#ann-' . $eventId . '" class="calendar-event-link">';
        $html .= '<div class="calendar-event ' . htmlspecialchars($categoryClass) . '" title="' . htmlspecialchars($event['title']) . '">' . $title . '</div>';
        $html .= '</a>';
      }
      $html .= '</div>';
    }
    
    $html .= '</div>';
  }

  $totalCells = $dayOfWeek + $daysInMonth;
  $remainingCells = 7 - ($totalCells % 7);
  if ($remainingCells < 7) {
    for ($i = 0; $i < $remainingCells; $i++) {
      $html .= '<div class="calendar-day calendar-day-empty"></div>';
    }
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}

function formatEventDateForHighlight(?string $startDate): string
{
  if (!$startDate) return '';
  $timestamp = strtotime($startDate);
  if (!$timestamp) return '';
  return date('j', $timestamp) . ' (' . strtoupper(date('D', $timestamp)) . ')';
}

function getEventCategoryClass(?string $category): string
{
  if (!$category) return 'cat-default';
  $categoryLower = strtolower($category);
  if (strpos($categoryLower, 'midterm') !== false || strpos($categoryLower, 'exam') !== false) return 'cat-exam';
  elseif (strpos($categoryLower, 'holiday') !== false || strpos($categoryLower, 'no class') !== false) return 'cat-holiday';
  elseif (strpos($categoryLower, 'academic') !== false) return 'cat-academic';
  elseif (strpos($categoryLower, 'campus') !== false || strpos($categoryLower, 'life') !== false) return 'cat-campus';
  return 'cat-default';
}

function buildMonthlyEventsList(array $events): string
{
  $highlightEvents = array_slice($events, 0, 5);
  if (empty($highlightEvents)) {
    return '<div class="event-highlight-item"><p class="no-events-message">No events scheduled for this month.</p></div>';
  }
  
  $html = '';
  foreach ($highlightEvents as $event) {
    $eventDate = formatEventDateForHighlight($event['start_date']);
    $categoryClass = getEventCategoryClass($event['category']);
    
    $html .= '<div class="event-highlight-item">';
    $html .= '<div class="event-date-box ' . htmlspecialchars($categoryClass) . '">';
    $html .= htmlspecialchars($eventDate);
    $html .= '</div>';
    $html .= '<div class="event-highlight-content">';
    $html .= '<h4 class="event-highlight-title">' . htmlspecialchars($event['title']) . '</h4>';
    if (!empty($event['location'])) {
      $html .= '<p class="event-highlight-location">' . htmlspecialchars($event['location']) . '</p>';
    }
    if (!empty($event['description'])) {
      $html .= '<p class="event-highlight-description">' . htmlspecialchars(excerpt($event['description'], 80)) . '</p>';
    }
    $html .= '<span class="event-category-tag ' . htmlspecialchars($categoryClass) . '">';
    $html .= htmlspecialchars($event['category'] ?: 'Event');
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
  }
  return $html;
}

// --- Fetch Data ---
$settings = array_merge(
  $settingDefaults,
  fetchSettings($conn, array_keys($settingDefaults))
);

$latestArchives = fetchLatestArchives($conn, 6);

// Calendar Logic (removed as section is deleted)
$heroVideoPath = !empty($settings['hero_video_path']) ? $settings['hero_video_path'] : 'videos/homepagevid.mp4';

// Check for system cover image or video from settings
$systemCover = $_settings->info('cover');

// Manual fallback search if setting is empty or default
if (empty($systemCover) || strpos($systemCover, 'no-image') !== false) {
    $manualCoverSearch = glob(base_app . 'uploads/cover-*.*');
    if (!empty($manualCoverSearch)) {
        // Prefer the most recent one if multiple exist, or just the first one
        $systemCover = 'uploads/' . basename($manualCoverSearch[0]);
    }
}

// Determine if the cover is a video or an image
$isVideoCover = false;
$finalVideoPath = '';
$finalCoverImage = '';

if (!empty($systemCover)) {
    // Check for video extensions
    if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $systemCover)) {
        $isVideoCover = true;
        
        // Use validate_image to handle S3 presigning if applicable
        // validate_image handles both remote URLs (signing them) and local paths (prepending base_url)
        $potentialVideoPath = validate_image($systemCover);
        
        // Check if validate_image returned the error placeholder
        if (strpos($potentialVideoPath, 'no-image-available.png') === false) {
            $finalVideoPath = $potentialVideoPath;
        }
    } else {
        // It's an image
        $finalCoverImage = validate_image($systemCover);
    }
}

// If no video cover from settings, try the hero_video_path setting as fallback
if (empty($finalVideoPath)) {
    $fallbackVideo = !empty($settings['hero_video_path']) ? $settings['hero_video_path'] : 'videos/homepagevid.mp4';
    if (is_file(base_app . $fallbackVideo)) {
        $finalVideoPath = $fallbackVideo;
    }
}

// If we have a video, we don't necessarily need a cover image background, 
// BUT having one as a poster is good practice. 
// If the user provided an image cover, use it. 
?>

<!-- Hero Section -->
<?php
$heroContent = '<div class="container hero-inner hero-text-center">
  <div>
    <h1 class="text-white font-weight-bold" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">Maligayang pagdating sa Politeknikong Unibersidad ng Pilipinas – Biñan</h1>
    <p class="lead text-white mt-3 mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8); font-size: 1.25rem;">
        Access our Institutional Repository & Archives
    </p>
    <form action="./" method="GET" class="mx-auto mt-4" style="max-width: 600px;">
        <input type="hidden" name="page" value="projects">
        <div class="input-group input-group-lg shadow-lg" style="border-radius: 50px; overflow: hidden;">
            <input type="search" name="q" class="form-control border-0 px-4" placeholder="Search for research, theses, or capstone projects..." aria-label="Search" style="height: 60px; font-size: 1.1rem;">
            <div class="input-group-append">
                <button class="btn btn-warning text-dark font-weight-bold px-4" type="submit" style="background-color: var(--pup-gold, #ffc107); border: none;">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </div>
    </form>
  </div>
</div>';

$heroOptions = [
  'height' => 'auto',
  'minHeight' => '600px',
  'maxHeight' => 'none',
  'brightness' => 0.6,
  'showOverlay' => true,
  'autoplay' => true,
  'muted' => true,
  'loop' => true,
  'coverImage' => $finalCoverImage,
];

$content = $heroContent;
$videoPath = $finalVideoPath;
$customClass = 'hero';
$options = $heroOptions;
include 'components/hero-video.php';
?>

<!-- Latest Archives Section -->
<section class="section latest-archives-section py-5" id="latest-archives" style="background-color: #f9f9f9;">
  <div class="container">
    <div class="text-center mb-5">
        <h2 class="font-weight-bold" style="color: var(--pup-maroon, #800000);">Latest Research Submissions</h2>
        <div style="width: 50px; height: 3px; background: var(--pup-gold, #ffc107); margin: 10px auto;"></div>
        <p class="text-muted w-75 mx-auto">Discover the most recent academic contributions from our students.</p>
    </div>

    <div class="row">
        <?php if (!empty($latestArchives)): ?>
            <?php foreach ($latestArchives as $row): ?>
                <div class="col-md-4 mb-4">
                    <a href="./?page=view_archive&id=<?php echo $row['id']; ?>" class="card h-100 shadow-sm text-decoration-none text-dark hover-lift" style="border-radius: 15px; overflow: hidden; transition: transform 0.3s;">
                        <div class="position-relative" style="height: 200px; overflow: hidden;">
                            <img src="<?php echo validate_image($row['banner_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="w-100 h-100" style="object-fit: cover;">
                            <div class="position-absolute" style="bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 20px 15px 10px;">
                                <small class="text-white"><i class="far fa-calendar-alt mr-1"></i> <?php echo date('M d, Y', strtotime($row['date_created'])); ?></small>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="font-weight-bold mb-2" style="color: var(--pup-maroon, #800000);"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="text-muted small mb-2">By <?php echo htmlspecialchars($row['student_name'] ?? 'N/A'); ?></p>
                            <p class="card-text text-secondary" style="font-size: 0.9rem;">
                                <?php 
                                    $abstract = strip_tags(html_entity_decode($row['abstract']));
                                    echo strlen($abstract) > 100 ? substr($abstract, 0, 100) . '...' : $abstract;
                                ?>
                            </p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No archives found.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="./?page=projects" class="btn btn-lg rounded-pill px-5 text-white" style="background: var(--pup-maroon, #800000);">View All Archives</a>
    </div>
  </div>
</section>

<!-- My Dashboard Section (Original) -->
<?php if($_settings->userdata('id') > 0): ?>
<?php
$__uid = $_settings->userdata('id');
$__my_total = 0;
$__my_published = 0;
$__my_pending = 0;
$__r = $conn->query("SELECT COUNT(*) c FROM archive_list WHERE student_id = '{$__uid}'");
if($__r && ($__x = $__r->fetch_assoc())) $__my_total = (int)$__x['c'];
$__r = $conn->query("SELECT COUNT(*) c FROM archive_list WHERE student_id = '{$__uid}' AND status = 1");
if($__r && ($__x = $__r->fetch_assoc())) $__my_published = (int)$__x['c'];
$__r = $conn->query("SELECT COUNT(*) c FROM archive_list WHERE student_id = '{$__uid}' AND status = 0");
if($__r && ($__x = $__r->fetch_assoc())) $__my_pending = (int)$__x['c'];
$__recent_my = $conn->query("SELECT a.*, c.name curriculum_name FROM archive_list a INNER JOIN curriculum_list c ON a.curriculum_id = c.id WHERE a.student_id = '{$__uid}' ORDER BY a.date_created DESC LIMIT 3");
?>
<div class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="font-weight-bold" style="color: var(--pup-maroon, #800000);">My Dashboard</h2>
            <div style="width: 50px; height: 3px; background: var(--pup-gold, #ffc107); margin: 10px auto;"></div>
        </div>
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm py-4 h-100" style="border-radius: 15px;">
                    <div class="card-body">
                        <i class="fas fa-folder-open fa-3x mb-3" style="color: var(--pup-maroon, #800000);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($__my_total); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">My Submissions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm py-4 h-100" style="border-radius: 15px;">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-3x mb-3" style="color: var(--pup-gold, #ffc107);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($__my_published); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Published</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm py-4 h-100" style="border-radius: 15px;">
                    <div class="card-body">
                        <i class="fas fa-hourglass-half fa-3x mb-3" style="color: var(--pup-maroon, #800000);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($__my_pending); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Pending</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <a href="./?page=submit-archive" class="btn btn-lg rounded-pill px-5 text-white" style="background: var(--pup-maroon, #800000); box-shadow: 0 4px 15px rgba(128,0,0,0.3);">Submit New</a>
        </div>
        <div class="mt-5">
            <h4 class="font-weight-bold mb-3" style="color: #444;">Recent Submissions</h4>
            <div class="row">
                <?php if($__recent_my && $__recent_my->num_rows > 0): ?>
                    <?php while($row = $__recent_my->fetch_assoc()): ?>
                        <?php $status_badge = $row['status'] == 1 ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Pending</span>'; ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                                <div class="position-relative" style="height: 160px; overflow: hidden;">
                                    <img src="<?php echo validate_image($row['banner_path']) ?>" alt="<?php echo $row['title'] ?>" class="w-100 h-100" style="object-fit: cover;">
                                    <div class="position-absolute" style="bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 12px 10px 8px;">
                                        <span class="badge badge-warning text-white"><?php echo $row['curriculum_name'] ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="font-weight-bold mb-2">
                                        <a href="./?page=view_archive&id=<?php echo $row['id'] ?>" class="text-dark text-decoration-none"><?php echo $row['title'] ?></a>
                                    </h6>
                                    <div><?php echo $status_badge ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-muted">No recent submissions.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Statistics Section (Original) -->
<?php
$projects_count = $conn->query("SELECT id FROM archive_list where status = 1")->num_rows;
$students_count = $conn->query("SELECT id FROM student_list where status = 1")->num_rows;
$depts_count = $conn->query("SELECT id FROM department_list where status = 1")->num_rows;

$recent_archives = $conn->query("SELECT a.*, c.name as curriculum_name, d.name as dept_name 
    FROM archive_list a 
    INNER JOIN curriculum_list c ON a.curriculum_id = c.id 
    INNER JOIN department_list d ON c.department_id = d.id 
    WHERE a.status = 1 
    ORDER BY a.date_created DESC 
    LIMIT 3");
?>

<div class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm py-4 h-100 animate__animated animate__fadeInUp" style="border-radius: 15px; transition: transform 0.3s;">
                    <div class="card-body">
                        <i class="fas fa-book-reader fa-3x mb-3" style="color: var(--pup-maroon, #800000);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($projects_count); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Published Projects</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm py-4 h-100 animate__animated animate__fadeInUp animate__delay-1s" style="border-radius: 15px; transition: transform 0.3s;">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3" style="color: var(--pup-gold, #ffc107);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($students_count); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Active Scholars</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm py-4 h-100 animate__animated animate__fadeInUp animate__delay-2s" style="border-radius: 15px; transition: transform 0.3s;">
                    <div class="card-body">
                        <i class="fas fa-university fa-3x mb-3" style="color: var(--pup-maroon, #800000);"></i>
                        <h1 class="display-4 font-weight-bold mb-0" style="color: #333;"><?php echo number_format($depts_count); ?></h1>
                        <p class="text-muted text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Departments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Projects Section (Removed) -->

<!-- CTA Section (Original) -->
<div class="py-5 text-white" style="background: linear-gradient(135deg, var(--pup-maroon, #800000) 0%, #4a0000 100%); position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
    
    <div class="container text-center position-relative" style="z-index: 2;">
        <h2 class="font-weight-bold mb-3 display-4 text-white">Ready to Contribute?</h2>
        <p class="lead mb-5 w-75 mx-auto text-white" style="opacity: 0.9;">Join the PUP Biñan community in preserving academic excellence. Submit your research today.</p>
        <?php if(!isset($_SESSION['userdata'])): ?>
            <a href="login.php" class="btn btn-lg btn-light rounded-pill px-5 text-maroon font-weight-bold" style="color: var(--pup-maroon);">Login to Submit</a>
        <?php else: ?>
            <a href="./?page=submit-archive" class="btn btn-lg btn-light rounded-pill px-5 text-maroon font-weight-bold" style="color: var(--pup-maroon);">Submit Research</a>
        <?php endif; ?>
    </div>
</div>
