<?php

return [
    'plugin' => [
        'name' => 'Catalogue',
        'description' => 'Teljeskörű catalogue alkalmazás.'
    ],
    'catalogue' => [
        'menu_label' => 'Catalogue',
        'menu_description' => 'Catalogue bejegyzések kezelése',
        'posts' => 'Bejegyzések',
        'create_post' => 'catalogue bejegyzés',
        'categories' => 'Kategóriák',
        'create_category' => 'catalogue kategória',
        'tab' => 'Catalogue',
        'access_posts' => 'Catalogue bejegyzések kezelése',
        'access_categories' => 'Catalogue kategóriák kezelése',
        'access_other_posts' => 'Más felhasználók bejegyzéseinek kezelése',
        'access_import_export' => 'Bejegyzések importálása és exportálása',
        'access_publish' => 'Catalogue bejegyzések közzététele',
        'manage_settings' => 'Catalogue beállítások kezelése',
        'delete_confirm' => 'Törölni akarja a kijelölt bejegyzéseket?',
        'chart_published' => 'Közzétéve',
        'chart_drafts' => 'Piszkozatok',
        'chart_total' => 'Összesen',
        'settings_description' => 'Beállítási lehetőségek.',
        'show_all_posts_label' => 'Az összes bejegyzés mutatása az adminisztrátorok számára',
        'show_all_posts_comment' => 'A közzétett és a még nem publikált bejegyzések is egyaránt meg fognak jelenni az oldal szerkesztőinek.',
        'tab_general' => 'Általános'
    ],
    'posts' => [
        'list_title' => 'Catalogue bejegyzések',
        'filter_category' => 'Kategória',
        'filter_published' => 'Közzétéve',
        'filter_date' => 'Létrehozva',
        'new_post' => 'Új bejegyzés',
        'export_post' => 'Exportálás',
        'import_post' => 'Importálás'
    ],
    'post' => [
        'title' => 'Cím',
        'title_placeholder' => 'Új bejegyzés címe',
        'content' => 'Szöveges tartalom',
        'content_html' => 'HTML tartalom',
        'slug' => 'Keresőbarát cím',
        'slug_placeholder' => 'uj-bejegyzes-cime',
        'categories' => 'Kategóriák',
        'author_email' => 'Szerző e-mail címe',
        'created' => 'Létrehozva',
        'created_date' => 'Létrehozás dátuma',
        'updated' => 'Módosítva',
        'updated_date' => 'Módosítás dátuma',
        'published' => 'Közzétéve',
        'published_by' => 'Szerző:',
        'current_user' => 'Felhasználó',
        'published_date' => 'Közzététel dátuma',
        'published_validation' => 'Adja meg a közzététel dátumát',
        'tab_edit' => 'Szerkesztés',
        'tab_categories' => 'Kategóriák',
        'categories_comment' => 'Jelölje be azokat a kategóriákat, melyekbe be akarja sorolni a bejegyzést',
        'categories_placeholder' => 'Nincsenek kategóriák, előbb létre kell hoznia egyet!',
        'tab_manage' => 'Kezelés',
        'published_on' => 'Közzététel dátuma',
        'excerpt' => 'Kivonat',
        'summary' => 'Összegzés',
        'featured_images' => 'Kiemelt képek',
        'delete_confirm' => 'Valóban törölni akarja ezt a bejegyzést?',
        'delete_success' => 'Sikeresen törölve lettek a bejegyzések.',
        'close_confirm' => 'A bejegyzés nem került mentésre.',
        'return_to_posts' => 'Vissza a bejegyzésekhez',
        'posted_byline' => 'Publikálva: :date, itt: :categories',
        'posted_byline_no_categories' => 'Publikálva: :date.',
        'date_format' => 'Y.M.d.',
    ],
    'categories' => [
        'list_title' => 'Catalogue kategóriák',
        'new_category' => 'Új kategória',
        'uncategorized' => 'Nincs kategorizálva'
    ],
    'category' => [
        'name' => 'Név',
        'name_placeholder' => 'Új kategória neve',
        'description' => 'Leírás',
        'slug' => 'Keresőbarát cím',
        'slug_placeholder' => 'uj-kategoria-neve',
        'posts' => 'Bejegyzések',
        'delete_confirm' => 'Valóban törölni akarja ezt a kategóriát?',
        'delete_success' => 'Sikeresen törölve lettek a kategóriák.',
        'return_to_categories' => 'Vissza a kategóriákhoz',
        'reorder' => 'Kategóriák sorrendje'
    ],
    'menuitem' => [
        'catalogue_category' => 'Catalogue kategória',
        'all_catalogue_categories' => 'Összes catalogue kategória',
        'catalogue_post' => 'Catalogue bejegyzés',
        'all_catalogue_posts' => 'Összes catalogue bejegyzés',
        'category_catalogue_posts' => 'Catalogue kategória bejegyzések'
    ],
    'settings' => [
        'category_title' => 'Catalogue kategória lista',
        'category_description' => 'A catalogue kategóriákat listázza ki a lapon.',
        'category_slug' => 'Cím paraméter neve',
        'category_slug_description' => 'A webcím útvonal paramétere a jelenlegi kategória keresőbarát címe alapján való kereséséhez. Az alapértelmezett komponensrész ezt a tulajdonságot használja a jelenleg aktív kategória megjelöléséhez.',
        'category_display_empty' => 'Üres kategóriák kijelzése',
        'category_display_empty_description' => 'Azon kategóriák megjelenítése, melyekben nincs egy bejegyzés sem.',
        'category_page' => 'Kategória lap',
        'category_page_description' => 'A kategória hivatkozások kategória lap fájljának neve. Az alapértelmezett komponensrész használja ezt a tulajdonságot.',
        'post_title' => 'Catalogue bejegyzés',
        'post_description' => 'Egy catalogue bejegyzést jelez ki a lapon.',
        'post_slug' => 'Cím paraméter neve',
        'post_slug_description' => 'A webcím útvonal paramétere a bejegyzés keresőbarát címe alapján való kereséséhez.',
        'post_category' => 'Kategória lap',
        'post_category_description' => 'A kategória hivatkozások kategória lap fájljának neve. Az alapértelmezett komponensrész használja ezt a tulajdonságot.',
        'posts_title' => 'Catalogue bejegyzések',
        'posts_description' => 'A közzétett catalogue bejegyzések listázása a honlapon.',
        'posts_pagination' => 'Lapozósáv paraméter neve',
        'posts_pagination_description' => 'A lapozósáv lapjai által használt, várt paraméter neve.',
        'posts_filter' => 'Kategória szűrő',
        'posts_filter_description' => 'Adja meg egy kategória keresőbarát címét vagy webcím paraméterét a bejegyzések szűréséhez. Hagyja üresen az összes bejegyzés megjelenítéséhez.',
        'posts_per_page' => 'Bejegyzések laponként',
        'posts_per_page_validation' => 'A laponkénti bejegyzések értéke érvénytelen formátumú',
        'posts_no_posts' => 'Üzenet ha nincs bejegyzés',
        'posts_no_posts_description' => 'A catalogue bejegyzés listában kijelezendő üzenet abban az esetben, ha nincsenek bejegyzések. Az alapértelmezett komponensrész használja ezt a tulajdonságot.',
        'posts_no_posts_default' => 'Nem található bejegyzés',
        'posts_order' => 'Bejegyzések sorrendje',
        'posts_order_description' => 'Jellemző, ami alapján rendezni kell a bejegyzéseket',
        'posts_category' => 'Kategória lap',
        'posts_category_description' => 'A "Kategória" kategória hivatkozások kategória lap fájljának neve. Az alapértelmezett komponensrész használja ezt a tulajdonságot.',
        'posts_post' => 'Bejegyzéslap',
        'posts_post_description' => 'A "Tovább olvasom" hivatkozások catalogue bejegyzéslap fájljának neve. Az alapértelmezett komponensrész használja ezt a tulajdonságot.',
        'posts_except_post' => 'Bejegyzés kizárása',
        'posts_except_post_description' => 'Adja meg annak a bejegyzésnek az azonosítóját vagy webcímét, amit nem akar megjeleníteni a listázáskor.',
        'posts_except_post_validation' => 'A kivételnek webcímnek, illetve azonosítónak, vagy pedig ezeknek a vesszővel elválasztott felsorolásának kell lennie.',
        'posts_except_categories' => 'Kategória kizárása',
        'posts_except_categories_description' => 'Adja meg azoknak a kategóriáknak a webcímét vesszővel elválasztva, amiket nem akar megjeleníteni a listázáskor.',
        'posts_except_categories_validation' => 'A kivételnek webcímnek, vagy pedig ezeknek a vesszővel elválasztott felsorolásának kell lennie.',
        'rssfeed_catalogue' => 'Catalogue oldal',
        'rssfeed_catalogue_description' => 'Annak a lapnak a neve, ahol listázódnak a catalogue bejegyzések. Ezt a beállítást használja alapértelmezetten a catalogue komponens is.',
        'rssfeed_title' => 'RSS hírfolyam',
        'rssfeed_description' => 'A cataloguehoz tartozó RSS hírfolyam generálása.',
        'group_links' => 'Hivatkozások',
        'group_exceptions' => 'Kivételek'
    ],
    'sorting' => [
        'title_asc' => 'Név (növekvő)',
        'title_desc' => 'Név (csökkenő)',
        'created_asc' => 'Létrehozva (növekvő)',
        'created_desc' => 'Létrehozva (csökkenő)',
        'updated_asc' => 'Frissítve (növekvő)',
        'updated_desc' => 'Frissítve (csökkenő)',
        'published_asc' => 'Publikálva (növekvő)',
        'published_desc' => 'Publikálva (csökkenő)',
        'random' => 'Véletlenszerű'
    ],
    'import' => [
        'update_existing_label' => 'Meglévő bejegyzések frissítése',
        'update_existing_comment' => 'Két bejegyzés akkor számít ugyanannak, ha megegyezik az azonosító számuk, a címük vagy a webcímük.',
        'auto_create_categories_label' => 'Az import fájlban megadott kategóriák létrehozása',
        'auto_create_categories_comment' => 'A funkció használatához meg kell felelnie a Kategóriák oszlopnak, különben az alábbi elemekből válassza ki az alapértelmezett kategóriákat.',
        'categories_label' => 'Kategóriák',
        'categories_comment' => 'Válassza ki azokat a kategóriákat, amelyekhez az importált bejegyzések tartoznak (nem kötelező).',
        'default_author_label' => 'Alapértelmezett szerző (nem kötelező)',
        'default_author_comment' => 'A rendszer megpróbál egy meglévő felhasználót társítani a bejegyzéshez az Email oszlop alapján. Amennyiben ez nem sikerül, az itt megadott szerzőt fogja alapul venni.',
        'default_author_placeholder' => '-- válasszon felhasználót --'
    ]
];
