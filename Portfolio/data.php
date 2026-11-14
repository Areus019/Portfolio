<?php

$profile = [
    'name' => 'Yvan Herbito',
    'callsign' => 'Arkhive',
    'classification' => '',
    'role' => 'Developer',
    'tagline' => 'ARKHIVE',
    'intro' => 'A fun loving guy.',
    'about' => 'I am a skilled Python programmer who codes, plays, and sometimes sleeps. I enjoy building practical digital experiences and creating applications for the good of mankind.',
    'location' => 'Valenzuela, Philippines',
    'focus' => 'Programming and problem solving',
    'availability' => 'OPEN TO WORK',
    'spoken_languages' => '',
    'operator_id' => '',
    'portrait' => '',
    'resume_url' => '',
    'email' => 'iamyvanherbito@gmail.com',
    'contact_message' => 'Have a project, opportunity, or interesting problem in mind? Send me a message.',
];

$projects = [
    [
        'title' => 'Furrpect Choice',
        'short_label' => 'A website for a Pet Grooming Service business.',
        'category' => 'Web',
        'year' => '2025',
        'description' => '',
        'stack' => [],
        'image' => '',
        'demo' => '',
        'source' => '',
    ],
    [
        'title' => 'Library Management System',
        'short_label' => 'Library Management System Demo for PLV.',
        'category' => 'Software',
        'year' => '2026',
        'description' => '',
        'stack' => [],
        'image' => '',
        'demo' => '',
        'source' => '',
    ],
    [
        'title' => 'Volunteer',
        'short_label' => 'A Volunteering website prototype.',
        'category' => 'Web',
        'year' => '2025',
        'description' => '',
        'stack' => [],
        'image' => '',
        'demo' => '',
        'source' => '',
    ],
];

$skills = [
    'LANGUAGES' => ['Python', 'Java', 'HTML', 'CSS', 'PHP', 'GDScript', 'C++'],
    'SOFT SKILLS' => ['Music', 'Web Design'],
    'TOOLS & WORKFLOW' => ['Git', 'GitHub', 'VS Code'],
];

$timeline = [
    [
        'period' => 'YEAR 3',
        'type' => 'EDUCATION',
        'title' => 'Bachelor',
        'organization' => 'PLV',
        'description' => 'Relevant coursework: Programming.',
    ],
];

$socials = [
    ['label' => 'GITHUB', 'url' => 'https://github.com/Areus019'],
    ['label' => 'LINKEDIN', 'url' => 'Unavailable'],
    ['label' => 'FACEBOOK', 'url' => ''],
];

$projectCategories = array_values(array_unique(array_map(
    static fn(array $project): string => $project['category'],
    $projects
))); 
