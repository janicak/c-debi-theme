<?php

$base_url = get_site_url();

return array(
    'deep-life' => array(
        'title' => 'Deep Life',
        'caption' => 'We are exploring the frontier of the "deep biosphere" - microbial life in the rocks and sediments that makes up 70% of our planet\'s surface.',
        'link' => array(
            'url' => $base_url . '/research-activities/overview',
            'title' => 'About our research'
        ),
        'desc-background' => '#3c3528',
        'background' => array(
            'image' => array(
                'url' => $base_url . '/wp-content/uploads/home-page/2008_09_26_08_42_06.0566 - JASON-WHOI credit.jpg'
            ),
            'caption' => '<b>Background: </b> Microbes "rust the crust" at Loihi Seamount (courtesy ROV JASON / WHOI)'
        ),
    ),
    'novel-organisms' => array(
        'title' => 'Novel Organisms',
        'caption' => 'This previously unknown <em>bacillus</em> living 342 meters below the ocean floor is one of very few isolated from the subseafloor crust.<br><br>Studying these organisms will reveal important, novel, and basic insights into the unique metabolic features of microbes in the deep biosphere.',
        'link' => array(
            'url' => $base_url . '/research-activities/research-themes/',
            'title' => 'About our research themes'
        ),
        'desc-background' => '#1f403a',
        'background' => array(
            'image' => array(
                'url' => $base_url . '/wp-content/uploads/home-page/7_16F6-01.jpg'
            ),
            'caption' => '<b>Background: </b> Core samples from IODP Leg 206 (photo credit: IODP-USIO).'
        ),
        'inset' => array(
            'caption' => '<strong>Left: </strong>Novel <em>Bacillus</em> cells producing spores and <strong>right: </strong> actively growing (scanning electron microscope (SEM) image courtesy Lily Momper / USC).',
            'images' => array(
                $base_url . '/wp-content/uploads/home-page/Momper1.jpg',
                $base_url . '/wp-content/uploads/home-page/Momper2.jpg',
            )
        )
    ),
    'slow-growing' => array(
        'title' => 'It\'s Slow Growing: Novel Approaches',
        'caption' => 'Visualization methods like "BONCAT" reveal important, novel, and basic insights into the unique metabolic features of microbes in the deep biosphere, as in the slow-growing, archaeal-bacterial consortium anaerobically oxidizing methane in deep sea sediments pictured here.',
        'link' => array(
            'url' => 'http://www.pnas.org/content/113/28/E4069.abstract',
            'title' => 'Read the article'
        ),
        'desc-background' => '#331f13',
        'background' => array(
            'color' => '#000000',
            'caption' => '<b>Figure:</b> Hatzenpichler et al. 2016, Proceedings of the National Academy of Sciences.'
        ),
        'inset' => array(
            'caption' => '<b><span style="color:#0000ff;">DAPI (DNA)</span>&nbsp;&nbsp;&nbsp;<span style="color:#00ff00">BONCAT (new proteins)</span>&nbsp;&nbsp;&nbsp;<span style="color:#ff0000">FISH (16s RNA)</span>&nbsp;&nbsp;&nbsp;Bar: 10μM</b>',
            'images' => array(
                $base_url . '/wp-content/uploads/hatzenpichler_pnas_fig.jpg',
            )
        )
    ),
    'global-implications' => array(
        'title' => 'Global Implications',
        'caption' => 'About 0.6% of Earth\'s total living biomass is in subseafloor sediments. This finding updates previous estimates made in 1998.',
        'link' => array(
            'url' => 'http://www.pnas.org/content/109/40/16213',
            'title' => 'Read the article'
        ),
        'desc-background' => '#302c50',
        'background' => array(
            'color' => '#399cd6',
        ),
        'inset' => array(
            'caption' => '<b>Figure: </b> Kallmeyer et al. 2012, Proceedings of the National Academy of Sciences.',
            'images' => array(
                $base_url . '/wp-content/uploads/home-page/pnas.png',
            )
        )
    ),
    'science-and-technology' => array(
        'title' => 'Science & Technology',
        'caption' => 'We require specialized technologies to collect and analyze samples from the deep biosphere, including sensors, samplers, and platforms, deep-sea submersibles, scientific drilling research ships, and ROVs (remotely operated vehicles).',
        'link' => array(
            'url' => $base_url . '/research-activities/research-themes/',
            'title' => 'About our research themes'
        ),
        'desc-background' => '#00455a',
        'background' => array(
            'image' => array(
                'url' => $base_url . '/wp-content/uploads/JOIDES_Resolution_Exp344_036-Edit.jpg'
            ),
            'caption' => '<strong>Above: </strong>HOV Alvin samples a hydrothermal vent (photo credit: WHOI). <b>Background: </b> Scientific drilling ship <i>JOIDES Resolution</i> (photo credit: IODP).'
        ),
        'inset' => array(
            'images' => array(
                //$base_url . '/wp-content/uploads/deeplife_CORK_from-Science-News-article-copy.jpg',
                $base_url . '/wp-content/uploads/home-page/WHOIatlantis-alvin_3764-2002_01_30_12_17_32_60953.jpg',
            )
        )
    ),
    'training-and-outreach' => array(
        'title' => 'Training & Outreach',
        'caption' => 'Get involved with our education, outreach and diversity opportunities for teachers, K-12, undergraduates, graduate students, and postdoctorals in our quest to train and foster the next generation of deep biosphere researchers.',
        'link' => array(
            'url' => $base_url . '/education-diversity/education-diversity-goals/',
            'title' => 'Learn more'
        ),
        'desc-background' => '#33425d',
        'background' => array(
            'image' => array(
                'url' => $base_url . '/wp-content/uploads/home-page/IMG_0714.JPG',
            ),
            'caption' => '<b>Background:</b> C-DEBI\'s undergraduate summer GEM course (photo credit: Ann Close, USC).'
        )
    )
);