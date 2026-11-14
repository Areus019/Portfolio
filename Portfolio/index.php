<?php
declare(strict_types=1);
require __DIR__ . '/data.php';

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function callsignMarkup(string $callsign): string {
    return '<span class="callsign-word"><span class="callsign-ark">' . e(substr($callsign, 0, 3)) . '</span><span class="callsign-hive">' . e(substr($callsign, 3)) . '</span></span>';
}

function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $out = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $first = function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
        $out .= function_exists('mb_strtoupper') ? mb_strtoupper($first) : strtoupper($first);
    }
    return $out ?: 'OP';
}

function projectSlug(string $title): string {
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($title)));
    return trim($slug ?? '', '-') ?: 'project';
}

$displayName = $profile['name'] ?: 'YOUR NAME';
$year = date('Y');
$languageIcons = [
    'Python' => 'assets/Images/python-logo-only.png',
    'Java' => 'assets/Images/java.png',
    'HTML' => 'assets/Images/HTML5_logo_and_wordmark.svg',
    'CSS' => 'assets/Images/Official_CSS_Logo.svg',
    'PHP' => 'assets/Images/new-php-logo.png',
    'GDScript' => 'assets/Images/logo_vertical_color_dark.png',
    'C++' => 'assets/Images/ISO_C++_Logo.svg',
];
$educationRecords = array_values(array_filter(
    $timeline,
    static fn(array $item): bool => strcasecmp($item['type'], 'EDUCATION') === 0
));
$projectRoutes = [];
$usedProjectSlugs = [];
$projectImageDirectories = [
    'furrpect-choice' => 'FurrpectChoiceProject',
    'library-management-system' => 'LibraryMSProject',
    'volunteer' => 'VoluntierProject',
    'volunteer-website' => 'VoluntierProject',
];
foreach ($projects as $projectIndex => $project) {
    $slug = projectSlug($project['title']);
    if (isset($usedProjectSlugs[$slug])) {
        $slug .= '-' . ($projectIndex + 1);
    }
    $usedProjectSlugs[$slug] = true;

    $galleryImages = [];
    $imageDirectory = $projectImageDirectories[$slug] ?? null;
    if ($imageDirectory !== null) {
        $galleryFiles = glob(__DIR__ . '/assets/Images/' . $imageDirectory . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: [];
        sort($galleryFiles, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($galleryFiles as $galleryFile) {
            $galleryImages[] = str_replace('\\', '/', substr($galleryFile, strlen(__DIR__) + 1));
        }
    }
    if (!$galleryImages && !empty($project['image'])) {
        $galleryImages[] = $project['image'];
    }

    $projectRoutes[$projectIndex] = [
        'slug' => $slug,
        'gallery' => $galleryImages,
        'preview' => !empty($project['image']) ? $project['image'] : ($galleryImages[0] ?? ''),
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101214">
  <meta name="description" content="<?= e($profile['tagline']) ?>">
  <title><?= e($displayName) ?> — Developer Portfolio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="scanline" aria-hidden="true"></div>
<header class="topbar">
  <a class="brand" href="#home" aria-label="Go to top">
    <span class="brand-mark"><?= e(initials($displayName)) ?></span>
    <span class="brand-copy"><strong><?= callsignMarkup($profile['callsign']) ?></strong><small>PERSONAL ARCHIVE / <?= e($profile['classification']) ?></small></span>
  </a>
  <div class="system-readout"><span></span> RHODES ISLAND <b>/ PERSONNEL TERMINAL</b></div>
  <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false"><span></span><span></span></button>
</header>
<nav class="nav" aria-label="Main navigation">
  <button type="button" class="tab-link active" data-tab="home"><i>⌂</i><span>HOME</span></button>
  <button type="button" class="tab-link" data-tab="about"><i>01</i><span>PROFILE</span></button>
  <button type="button" class="tab-link" data-tab="works"><i>02</i><span>PROJECTS</span></button>
  <button type="button" class="tab-link" data-tab="skills"><i>03</i><span>SKILLS</span></button>
  <button type="button" class="tab-link" data-tab="experience"><i>04</i><span>RECORD</span></button>
  <button type="button" class="tab-link nav-contact" data-tab="contact"><i>↗</i><span>CONTACT</span></button>
</nav>

<main>
  <section class="hero section-wrap tab-panel is-active" id="home" data-tab-panel="home">
    <div class="hero-grid" aria-hidden="true"></div>
    <div class="hero-content">
      <div class="eyebrow"><span class="pulse"></span> INDEPENDENT DEVELOPER <b>// AVAILABLE FOR OPPORTUNITIES</b></div>
      <p class="hero-pretitle">WELCOME TO MY OPERATIONS BASE</p>
      <h1><?= e($displayName) ?><span class="accent">.</span></h1>
      <h2><?= e($profile['role']) ?></h2>
      <p class="hero-description"><?= e($profile['tagline']) ?></p>
      <div class="hero-actions">
        <button class="button button-primary" type="button" data-tab="works">VIEW MY WORK <span>↘</span></button>
        <button class="button button-ghost" type="button" data-tab="contact">GET IN TOUCH <span>↗</span></button>
      </div>
      <div class="hero-commands" aria-label="Quick access">
        <button class="launch-card" type="button" data-tab="works"><small>01 / FIELD OPERATIONS</small><strong>PROJECTS <span>↗</span></strong><b><?= count($projects) ?> RECORDS</b></button>
        <button class="launch-card" type="button" data-tab="about"><small>02 / PERSONNEL DATA</small><strong>OPERATOR FILE <span>↗</span></strong><b>IDENTITY VERIFIED</b></button>
        <button class="launch-card" type="button" data-tab="experience"><small>03 / SERVICE LOG</small><strong>EDUCATION <span>↗</span></strong><b>VIEW RECORD</b></button>
      </div>
      <div class="hero-meta">
        <span><small>BASE OF OPERATIONS</small><?= e($profile['location']) ?></span>
        <span><small>FOCUS</small><?= e($profile['focus']) ?></span>
        <span><small>STATUS</small><b class="status"><?= e($profile['availability']) ?></b></span>
      </div>
    </div>
    <div class="hero-side" aria-label="Profile insignia">
      <div class="crosshair" aria-hidden="true">+</div>
      <div class="side-number">NO. <?= e($profile['operator_id']) ?></div>
      <div class="operator-frame">
        <?php if (!empty($profile['portrait'])): ?>
          <img src="<?= e($profile['portrait']) ?>" alt="Portrait of <?= e($displayName) ?>">
        <?php else: ?>
          <div class="portrait-placeholder"><span><?= e(initials($displayName)) ?></span><small>INSERT<br>PORTRAIT</small></div>
        <?php endif; ?>
        <div class="frame-corner top-left" aria-hidden="true"></div>
        <div class="frame-corner bottom-right" aria-hidden="true"></div>
      </div>
      <div class="side-caption"><span>PERSONNEL FILE</span><b><?= callsignMarkup($profile['callsign']) ?></b></div>
    </div>
    <div class="hero-index"><span>SCROLL TO EXPLORE</span><span class="index-line"></span><span>01 / 05</span></div>
  </section>

  <section class="section-wrap section-block profile-section tab-panel" id="about" data-tab-panel="about">
    <div class="profile-screen-head">
      <div class="profile-screen-title"><small>MY INFO / PERSONNEL RECORD</small><strong><?= e($profile['name'] ?: 'YOUR NAME') ?></strong></div>
      <span class="profile-screen-callsign"><?= callsignMarkup($profile['callsign']) ?></span>
    </div>

    <div class="operator-card">
      <aside class="profile-identity-rail">
        <div class="profile-status-row">
          <div class="profile-status"><span class="pulse" aria-hidden="true"></span><?= e($profile['availability']) ?></div>
          <span class="profile-status-note">PERSONNEL / ACTIVE</span>
        </div>
        <div class="profile-status-indicators" aria-hidden="true"><span></span><span></span><span>×</span></div>
        <span class="profile-label">PERSONNEL / <?= e(strtoupper($profile['role'])) ?></span>
        <h2><?= e($profile['name'] ?: 'Your name here') ?></h2>
        <p class="profile-role"><?= e($profile['role']) ?></p>
        <span class="profile-record-label">PERSONNEL RECORD / 01</span>
        <div class="profile-rail-rule"></div>
        <div class="profile-field"><small>BASE OF OPERATIONS</small><strong><?= e($profile['location']) ?></strong></div>
        <div class="profile-field"><small>FOCUS</small><strong><?= e($profile['focus']) ?></strong></div>
        <div class="profile-language-stat">
          <small>PROGRAMMING LANGUAGES</small>
          <div><strong><?= str_pad((string)count($skills['LANGUAGES']), 2, '0', STR_PAD_LEFT) ?></strong><span><b>SPECIALTIES</b><small>LOCAL SKILL SET</small></span></div>
        </div>
        <div class="profile-rail-diamonds" aria-hidden="true"><span>◇</span><span>◇</span><span>◇</span><span>◇</span><span>◇</span></div>
        <?php if (!empty($profile['spoken_languages'])): ?>
          <div class="profile-field"><small>SPOKEN LANGUAGES</small><strong><?= e($profile['spoken_languages']) ?></strong></div>
        <?php endif; ?>
        <?php if (!empty($profile['resume_url'])): ?>
          <a class="profile-resume" href="<?= e($profile['resume_url']) ?>" target="_blank" rel="noopener">ACCESS CV <span>↗</span></a>
        <?php endif; ?>
        <small class="profile-rail-footer">PROFILE / <?= callsignMarkup($profile['callsign']) ?></small>
      </aside>

      <div class="profile-art-space" aria-hidden="true"></div>

      <div class="profile-details">
        <div class="profile-detail-card profile-personnel-card">
          <div class="profile-avatar">
            <?php if (!empty($profile['portrait'])): ?>
              <img src="<?= e($profile['portrait']) ?>" alt="">
            <?php else: ?>
              <span><?= e(initials($displayName)) ?></span>
            <?php endif; ?>
          </div>
          <div class="profile-personnel-copy">
            <small>OPERATOR FILE</small>
            <strong><?= e($profile['name'] ?: 'YOUR NAME') ?></strong>
            <span><?= e($profile['role']) ?> / <?= callsignMarkup($profile['callsign']) ?></span>
          </div>
          <div class="profile-operator-id"><small>OPERATOR ID</small><strong><?= e($profile['operator_id'] ?: 'UNASSIGNED') ?></strong></div>
        </div>

        <div class="profile-detail-card profile-greeting">
          <span class="profile-greeting-mark" aria-hidden="true">+</span>
          <div><small>OPERATOR MESSAGE</small><p><?= e($profile['intro']) ?></p><p class="profile-message-about"><?= e($profile['about']) ?></p></div>
        </div>

        <section class="profile-detail-card profile-specialties" aria-labelledby="specialties-title">
          <div class="profile-module-heading">
            <div><h3 id="specialties-title">SPECIALTIES</h3><span>PROGRAMMING LANGUAGES</span></div>
            <span class="language-count" aria-live="polite">01 / <?= str_pad((string)count($skills['LANGUAGES']), 2, '0', STR_PAD_LEFT) ?></span>
          </div>
          <div class="language-carousel" role="region" aria-label="Programming language specialties; move the pointer left or right to browse" tabindex="0">
            <div class="language-track">
              <?php foreach ($skills['LANGUAGES'] as $language): ?>
                <article class="language-card">
                  <div class="language-logo<?= $language === 'GDScript' ? ' language-logo-godot' : '' ?>">
                    <?php if (isset($languageIcons[$language])): ?>
                      <img src="<?= e($languageIcons[$language]) ?>" alt="<?= e($language) ?> logo">
                    <?php else: ?>
                      <span aria-hidden="true"><?= e(strtoupper(substr($language, 0, 2))) ?></span>
                    <?php endif; ?>
                  </div>
                  <strong><?= e($language) ?></strong>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="language-controls">
            <span>HOVER LEFT / RIGHT TO BROWSE</span>
          </div>
        </section>

        <section class="profile-detail-card profile-education" aria-labelledby="education-title">
          <div class="profile-module-heading">
            <div><h3 id="education-title">EDUCATIONAL BACKGROUND</h3><span>ACADEMIC RECORD</span></div>
            <span class="education-mark" aria-hidden="true">◇</span>
          </div>
          <div class="education-list">
            <?php foreach ($educationRecords as $item): ?>
              <article class="education-entry">
                <span class="education-period"><?= e($item['period']) ?></span>
                <div><strong><?= e($item['title']) ?></strong><span><?= e($item['organization']) ?></span><p><?= e($item['description']) ?></p></div>
              </article>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </div>
  </section>

  <section class="section-wrap section-block works-section tab-panel" id="works" data-tab-panel="works">
    <div class="works-list-view">
    <div class="section-heading"><div><span class="section-kicker">02 / FIELD OPERATIONS</span><h2>SELECTED <em>WORKS</em></h2></div><span class="section-code"><?= count($projects) ?> RECORDS FOUND</span></div>
    <p class="section-intro">A selection of projects, experiments, and systems I have worked on. Open a record to inspect the details.</p>
    <p class="project-route-status" role="status" hidden></p>
    <div class="filter-row" aria-label="Filter projects">
      <button class="filter active" type="button" data-filter="all">ALL RECORDS <span><?= count($projects) ?></span></button>
      <?php foreach ($projectCategories as $category): ?>
        <button class="filter" type="button" data-filter="<?= e(strtolower($category)) ?>"><?= e(strtoupper($category)) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="project-grid">
      <?php foreach ($projects as $i => $project): ?>
        <?php $projectRoute = $projectRoutes[$i]; ?>
        <article class="project-card" data-category="<?= e(strtolower($project['category'])) ?>" data-project-slug="<?= e($projectRoute['slug']) ?>">
          <div class="project-visual">
            <?php if ($projectRoute['preview'] !== ''): ?>
              <img data-project-preview src="<?= e($projectRoute['preview']) ?>" alt="<?= e($project['title']) ?> preview" loading="lazy" decoding="async">
            <?php else: ?>
              <div class="project-placeholder"><span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><b><?= e($project['short_label']) ?></b><small>PROJECT VISUAL / PLACEHOLDER</small></div>
            <?php endif; ?>
            <span class="project-class"><?= e(strtoupper($project['category'])) ?></span>
          </div>
          <div class="project-info">
            <div class="project-topline"><span>OPERATION // <?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span><span><?= e($project['year']) ?></span></div>
            <h3><?= e($project['title']) ?></h3>
            <p><?= e($project['description']) ?></p>
            <div class="tag-list"><?php foreach ($project['stack'] as $tech): ?><span><?= e($tech) ?></span><?php endforeach; ?></div>
            <div class="project-links">
              <a class="project-open-link" href="#works/<?= e($projectRoute['slug']) ?>" data-project-route="<?= e($projectRoute['slug']) ?>">OPEN PROJECT FILE ↗</a>
              <?php if (!empty($project['demo'])): ?><a href="<?= e($project['demo']) ?>" target="_blank" rel="noopener">LIVE DEMO ↗</a><?php endif; ?>
              <?php if (!empty($project['source'])): ?><a href="<?= e($project['source']) ?>" target="_blank" rel="noopener">SOURCE CODE ↗</a><?php endif; ?>
              <?php if (empty($project['demo']) && empty($project['source'])): ?><span class="muted-link">LINKS TO BE ADDED</span><?php endif; ?>
            </div>
          </div>
          <div class="card-index">R-<?= str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT) ?></div>
        </article>
      <?php endforeach; ?>
    </div>
    </div>
    <?php foreach ($projects as $i => $project): ?>
      <?php
        $projectRoute = $projectRoutes[$i];
        $galleryImages = $projectRoute['gallery'];
      ?>
      <article class="project-detail-view" data-project-detail="<?= e($projectRoute['slug']) ?>" hidden aria-labelledby="project-title-<?= e($projectRoute['slug']) ?>">
        <div class="project-detail-toolbar">
          <a class="project-back-link" href="#works">← ALL PROJECT RECORDS</a>
          <span>OPERATION // <?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?> / PROJECT DOSSIER</span>
        </div>
        <div class="project-detail-frame">
          <div class="project-gallery<?= count($galleryImages) > 1 ? ' has-multiple-slides' : '' ?>" data-project-gallery role="region" aria-label="<?= e($project['title']) ?> project images">
            <?php if ($galleryImages): ?>
              <div class="project-gallery-viewport" tabindex="0" aria-label="Project image gallery. Use the arrow keys to browse.">
                <div class="project-gallery-track">
                  <?php foreach ($galleryImages as $imageIndex => $imagePath): ?>
                    <?php $imageLabel = pathinfo($imagePath, PATHINFO_FILENAME); ?>
                    <figure class="project-gallery-slide<?= $imageIndex === 0 ? ' is-active' : '' ?>" aria-hidden="<?= $imageIndex === 0 ? 'false' : 'true' ?>">
                      <img src="<?= e($imagePath) ?>" alt="<?= e($project['title'] . ' — ' . $imageLabel) ?>" loading="lazy" decoding="async">
                      <figcaption><?= e($imageLabel) ?></figcaption>
                    </figure>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php if (count($galleryImages) > 1): ?>
                <div class="project-gallery-controls" aria-label="Project image controls">
                  <button class="project-gallery-arrow" type="button" data-gallery-previous aria-label="Previous image">←</button>
                  <span class="project-gallery-count" aria-live="polite">01 / <?= str_pad((string)count($galleryImages), 2, '0', STR_PAD_LEFT) ?></span>
                  <div class="project-gallery-dots">
                    <?php foreach ($galleryImages as $imageIndex => $imagePath): ?>
                      <button type="button" data-gallery-slide-to="<?= $imageIndex ?>" aria-label="Show image <?= $imageIndex + 1 ?> of <?= count($galleryImages) ?>" aria-current="<?= $imageIndex === 0 ? 'true' : 'false' ?>"></button>
                    <?php endforeach; ?>
                  </div>
                  <button class="project-gallery-arrow" type="button" data-gallery-next aria-label="Next image">→</button>
                </div>
              <?php endif; ?>
              <span class="project-gallery-watermark" aria-hidden="true">PROJECT VISUAL ARCHIVE</span>
            <?php else: ?>
              <div class="project-gallery-empty">
                <span>VISUAL ARCHIVE / UNAVAILABLE</span>
                <strong>No project images provided</strong>
                <small>Images can be added to this project record later.</small>
              </div>
            <?php endif; ?>
          </div>

          <div class="project-detail-identity">
            <div class="project-detail-kicker"><span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span> PROJECT RECORD / <?= e(strtoupper($project['category'])) ?></div>
            <h2 id="project-title-<?= e($projectRoute['slug']) ?>"><?= e($project['title']) ?></h2>
            <p><?= trim($project['description']) !== '' ? e($project['description']) : 'Project overview not provided.' ?></p>
          </div>

          <aside class="project-detail-data" aria-label="Project information">
            <div class="project-detail-stat">
              <small>PROJECT TYPE</small>
              <strong><?= e($project['category']) ?></strong>
            </div>
            <div class="project-detail-stat">
              <small>YEAR</small>
              <strong><?= trim($project['year']) !== '' ? e($project['year']) : 'Not provided' ?></strong>
            </div>
            <section class="project-detail-module">
              <h3>TECHNOLOGIES</h3>
              <div class="project-detail-tags">
                <?php if ($project['stack']): ?>
                  <?php foreach ($project['stack'] as $technology): ?><span><?= e($technology) ?></span><?php endforeach; ?>
                <?php else: ?>
                  <span class="project-detail-unavailable">Not provided</span>
                <?php endif; ?>
              </div>
            </section>
            <section class="project-detail-module project-detail-actions">
              <h3>ACCESS LINKS</h3>
              <?php if (!empty($project['demo'])): ?><a href="<?= e($project['demo']) ?>" target="_blank" rel="noopener noreferrer">LIVE DEMO <span>↗</span></a><?php endif; ?>
              <?php if (!empty($project['source'])): ?><a href="<?= e($project['source']) ?>" target="_blank" rel="noopener noreferrer">SOURCE CODE <span>↗</span></a><?php endif; ?>
              <?php if (empty($project['demo']) && empty($project['source'])): ?><span class="project-detail-unavailable">Links not provided</span><?php endif; ?>
            </section>
          </aside>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <section class="section-wrap section-block tab-panel" id="skills" data-tab-panel="skills">
    <div class="section-heading"><div><span class="section-kicker">03 / EQUIPMENT &amp; CAPABILITIES</span><h2>TECHNICAL <em>ARSENAL</em></h2></div><span class="section-code">LOADOUT / ACTIVE</span></div>
    <div class="skills-layout">
      <?php foreach ($skills as $group => $items): ?>
        <div class="skill-panel"><div class="skill-panel-head"><span><?= e($group) ?></span><b aria-hidden="true">+</b></div><div class="skill-items"><?php foreach ($items as $item): ?><span><?= e($item) ?></span><?php endforeach; ?></div></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section-wrap section-block tab-panel" id="experience" data-tab-panel="experience">
    <div class="section-heading"><div><span class="section-kicker">04 / SERVICE RECORD</span><h2>EXPERIENCE &amp; <em>EDUCATION</em></h2></div><span class="section-code">CHRONOLOGICAL LOG</span></div>
    <div class="timeline">
      <?php foreach ($timeline as $item): ?>
        <article class="timeline-item"><div class="timeline-date"><?= e($item['period']) ?></div><div class="timeline-marker" aria-hidden="true"></div><div class="timeline-content"><span><?= e($item['type']) ?></span><h3><?= e($item['title']) ?></h3><h4><?= e($item['organization']) ?></h4><p><?= e($item['description']) ?></p></div></article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="contact-section tab-panel" id="contact" data-tab-panel="contact">
    <div class="section-wrap contact-inner">
      <div><span class="section-kicker">05 / OPEN CHANNEL</span><h2>LET'S BUILD<br><em>SOMETHING</em><br>USEFUL.</h2><p><?= e($profile['contact_message']) ?></p></div>
      <div class="contact-panel">
        <span class="contact-label">COMMUNICATION CHANNELS</span>
        <a class="contact-email" href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email'] ?: 'your@email.com') ?> <span>↗</span></a>
        <div class="social-links">
          <?php foreach ($socials as $social): ?><a href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e($social['label']) ?> <span>↗</span></a><?php endforeach; ?>
        </div>
        <div class="contact-status"><span class="pulse"></span> CHANNEL OPEN / AWAITING TRANSMISSION</div>
      </div>
    </div>
  </section>
</main>
<footer class="footer section-wrap"><a class="footer-brand" href="#home"><?= callsignMarkup($profile['callsign']) ?> <span>®</span></a><span>PERSONAL ARCHIVE // <?= e($year) ?></span></footer>
<script src="assets/js/main.js" defer></script>
</body>
</html>
