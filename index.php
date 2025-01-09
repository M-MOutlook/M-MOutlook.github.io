<?php

session_start();
// Liczba wpisów na jednej stronie
$posts_per_page = 3;

// Przykładowe dane (normalnie byłyby one pobierane z bazy danych)
$all_posts = [
    "Wpis 1 - Podstawy fotografii",
    "Wpis 2 - Jak ustawić aparat?",
    "Wpis 3 - Kompozycja w fotografii",
    "Wpis 4 - Ustawienia kamery w trudnych warunkach",
    "Wpis 5 - Jak fotografować portrety",
    "Wpis 6 - Fotografia krajobrazowa",
    "Wpis 7 - Jak edytować zdjęcia?",
    "Wpis 8 - Makrofotografia",
    "Wpis 9 - Wybór najlepszego obiektywu",
];

// Sprawdzamy, czy parametr 'str' jest ustawiony
if (isset($_GET['str']) && is_numeric($_GET['str'])) {
    $current_page = (int)$_GET['str'];
} else {
    // Domyślnie pierwsza strona
    $current_page = 1;
}

// Obliczamy indeksy dla paginacji
$total_posts = count($all_posts);
$total_pages = ceil($total_posts / $posts_per_page);

// Upewniamy się, że numer strony jest w dozwolonym zakresie
if ($current_page < 1) {
    $current_page = 1;
} elseif ($current_page > $total_pages) {
    $current_page = $total_pages;
}

// Wyliczamy początkowy i końcowy indeks dla obecnej strony
$start_index = ($current_page - 1) * $posts_per_page;
$end_index = min($start_index + $posts_per_page, $total_posts);

// Pobieramy odpowiednie wpisy na podstawie obliczonych indeksów
$current_posts = array_slice($all_posts, $start_index, $posts_per_page);

function convertBBCodeToHTML($text) {
    // Zamiana BBCode na HTML
    $text = preg_replace("/\[b\](.*?)\[\/b\]/is", "<strong>$1</strong>", $text);
    $text = preg_replace("/\[i\](.*?)\[\/i\]/is", "<em>$1</em>", $text);
    $text = preg_replace("/\[u\](.*?)\[\/u\]/is", "<u>$1</u>", $text);
    $text = preg_replace("/\[url\](.*?)\[\/url\]/is", "<a href=\"$1\" target=\"_blank\">$1</a>", $text);
    
    return $text;
}

// Sprawdzamy, czy formularz został wysłany
if (isset($_POST['submit'])) {
    // Zapisujemy treść posta w sesji
    $_SESSION['post'] = $_POST['post_text'];
}

// Sprawdzamy, czy użytkownik chce edytować post
if (isset($_POST['edit'])) {
    // Przywracamy treść posta do edycji
    $post_text = $_SESSION['post'];
} else {
    $post_text = isset($_SESSION['post']) ? $_SESSION['post'] : '';
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google-site-verification" content="FoaoInpMSa1GddVo36aaINkZ_lJibakhdbmM7lEzmbM" />
    <title>Blog Fotograficzny</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Nagłówek -->
    <header>
        <div class="logo">
            <img src="logo.png" alt="Logo bloga" />
        </div>
        <h1>Blog Fotograficzny</h1>
        <p><a href="/">www.blogfotograficzny.pl</a></p>
    </header>

    <!-- Główna część strony -->
    <div class="container">
        <!-- Pasek boczny -->
        <aside class="sidebar">
            <section class="about-me">
                <h2>O mnie</h2>
                <p>Cześć! Jestem pasjonatem fotografii. Na tym blogu dzielę się swoimi doświadczeniami i poradami fotograficznymi.</p>
            </section>
            <section class="categories">
                <h2>Kategorie</h2>
                <ul>
                    <li><a href="#">Porady fotograficzne</a></li>
                    <li><a href="#">Recenzje sprzętu</a></li>
                    <li><a href="#">Fotografia przyrody</a></li>
                </ul>
            </section>
        </aside>

        <!-- Główna treść wpisów -->
        <main class="content">
            <h2>Wpisy na blogu</h2>

            <?php
            // Wyświetlanie wpisów na podstawie paginacji
            foreach ($current_posts as $post) {
                echo "<article class='post'><p>$post</p></article>";
            }
            ?>

            <!-- Paginacja -->
            <div class="pagination">
                <?php
                // Linki do poprzedniej strony
                if ($current_page > 1) {
                    echo "<a href='?str=" . ($current_page - 1) . "'>&laquo; Poprzednia</a>";
                }

                // Linki do następnej strony
                if ($current_page < $total_pages) {
                    echo "<a href='?str=" . ($current_page + 1) . "'>Następna &raquo;</a>";
                }
                ?>
            </div>

            <section class="post-form">
                <h2>Dodaj nowy post</h2>

                <!-- Formularz dodawania postu -->
                <form method="POST" action="">
                    <label for="post_text">Treść posta:</label><br>
                    <textarea name="post_text" id="post_text" rows="10" cols="50"><?= htmlspecialchars($post_text) ?></textarea><br><br>

                    <p><strong>BBCode:</strong></p>
                    <p>[b]pogrubienie[/b], [i]kursywa[/i], [u]podkreślenie[/u], [url]link[/url]</p>

                    <button type="submit" name="submit">Zatwierdź post</button>
                    <button type="submit" name="edit">Edytuj post</button>
                </form>
            </section>

            <section class="post-preview">
                <h2>Podgląd posta:</h2>
                <?php
                // Pokazujemy podgląd sformatowanego posta
                if (isset($_SESSION['post']) && !empty($_SESSION['post'])) {
                    $formatted_post = convertBBCodeToHTML($_SESSION['post']);
                    echo "<div class='formatted-post'>$formatted_post</div>";
                }
                ?>
            </section>
        </main>
    </div>

    <!-- Stopka -->
    <footer>
        <p>Michał Muszyński</p>
    </footer>
</body>
</html>
