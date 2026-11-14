# PHP Developer Portfolio

## Requirements

- PHP 8.0+ recommended
- Any local PHP server (XAMPP, Laragon, MAMP, WAMP, or PHP built-in server)

## Run locally

1. Extract this folder into your local server's web root (for XAMPP, usually `htdocs`).
2. Open `data.php` and replace the sample information with yours.
3. Start Apache / PHP.
4. Visit `http://localhost/portfolio/`.

Or, from this directory, run:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000`.

## Where to edit your information

Everything intended for personal editing is in **`data.php`**:

- `$profile`: name, callsign (default `Arkhive`), role, short introduction, location, status, email, portrait, CV link
- `$projects`: project title, category, year, description, technology stack, image, demo and source links
- `$skills`: grouped technologies
- `$timeline`: education, work experience, internships, awards or certifications
- `$socials`: GitHub, LinkedIn and other profile links

Add more projects by copying one project array entry. The category filter is generated automatically from the project categories.

The Profile tab's educational panel reads education entries from `$timeline`. Its programming-language carousel reads `$skills['LANGUAGES']`; the corresponding local logo paths are mapped in `index.php`. The profile-card artwork uses `assets/Images/RasBG.png` as a full-card background with faded horizontal edges. Moving the pointer left or right over the specialty carousel scrolls it in that direction; keyboard arrows and native touch scrolling are also supported without a visible scrollbar. The layout reference is `assets/Images/profile-card-reference-v2.svg`.

Each Works card opens a shareable project dossier at `#works/<project-slug>`. The detail pages use the existing project fields in `data.php`; missing information is shown as unavailable. Project galleries load screenshots from `assets/Images/FurrpectChoiceProject/`, `assets/Images/LibraryMSProject/`, and `assets/Images/VoluntierProject/`. Their carousels autoplay, pause while hovered or focused, support keyboard and touch navigation, and respect reduced-motion preferences.

The topbar renders the `Arkhive` callsign as `ARKHIVE`, with a white/black `ARK` and a `HIVE` gradient that follows the active theme: azure and medical blues on dark backgrounds, and orange/fire tones on light backgrounds.

## Add your images and resume

Create these optional folders if needed:

- `assets/Images/` — profile picture and project screenshots
- `assets/files/` — resume PDF

Then set paths in `data.php`, for example:

```php
'portrait' => 'assets/Images/profile.jpg',
'image' => 'assets/Images/library-system.png',
'resume_url' => 'assets/files/resume.pdf',
```

Use images you own or have permission to publish. Recommended project screenshot ratio: 16:9.

## Customize the look

Edit `assets/css/style.css`. The main colors are CSS variables at the top:

- `--bg`: page background
- `--panel`: card background
- `--cyan`, `--blue`, and `--amber`: theme-specific accent colors
- `--accent-rgb`: matching RGB values used for translucent accent effects

## Notes

- The email action opens the visitor's email app using `mailto:`. It is not a server-side contact form and does not store messages.
- External social and project links should be replaced with your actual URLs.
- The Google Fonts import requires an internet connection; system fallback fonts are included.
- The design is responsive and uses a fixed, HUD-inspired navigation bar, tab panels, subtle interface effects, and project category filters.

## Portfolio content checklist

For each project, try to include:
1. What problem it solves
2. What you personally built / your role
3. The important features or technical decisions
4. The technologies used
5. A live demo and source-code link, when public

A few complete, well-explained projects are more convincing than a large list of project titles.
