<?php
// ------------------------------------------------------------
// (1) Routing Logic
// ------------------------------------------------------------
$allowed_routes = [
    'openai' => 'index_gpt.html',      // For backward compatibility
    'deepseek' => 'index_deepseek.html', // For backward compatibility
    '/' => 'index_deepseek.html'
];

$request_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path_segment = explode('/', $request_path)[0] ?? '';
$page = $allowed_routes[strtolower($path_segment)] ?? $allowed_routes['/'];
if (!in_array($page, $allowed_routes)) {
    $page = $allowed_routes['/'];
}

// ------------------------------------------------------------
// (2) Load JSON and build dynamic structure
// ------------------------------------------------------------
$jsonFile = __DIR__ . '/questions.json';
$jsonData = [];
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $jsonData = json_decode($content, true);
}

if (!isset($jsonData['categories'])) {
    $jsonData['categories'] = [];
}

$currentCategory = null;
foreach ($jsonData['categories'] as $cat) {
    if (isset($cat['id']) && strtolower($cat['id']) === strtolower($path_segment)) {
        $currentCategory = $cat;
        break;
    }
}

if (!$currentCategory && count($jsonData['categories']) > 0) {
    $currentCategory = $jsonData['categories'][0];
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>PRG1 Lernaufgaben</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Example of icons, optional -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Basic styling -->
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <!-- Dark Mode Toggle -->
    <button class="dark-mode-toggle" id="darkModeBtn">Dark Mode</button>

    <!-- Header -->
    <header>
        <h1>PRG1 Lernaufgaben</h1>
    </header>

    <!-- Navigation built from JSON categories -->
    <nav>
        <?php
        foreach ($jsonData['categories'] as $cat) {
            $isActive = (strtolower($cat['id']) === strtolower($path_segment)) ? 'active' : '';
            echo '<a href="/' . htmlspecialchars($cat['id']) . '" class="' . $isActive . '">';
            echo htmlspecialchars($cat['title']);
            echo '</a>';
        }
        ?>
    </nav>

    <div class="page-container">
        <!-- Main Content -->
        <div class="content">
            <div class="disclaimer">
                <strong>Hinweis:</strong> Die Antworten wurden automatisch mit KI generiert. Keine Gewähr auf
                Richtigkeit.
            </div>

            <?php
            if ($currentCategory) {
                if (!isset($currentCategory['areas'])) {
                    $currentCategory['areas'] = [];
                }

                $areaIndex = 1;
                foreach ($currentCategory['areas'] as $area) {
                    $accordionId = "accordion_" . strtolower($currentCategory['id']) . $areaIndex;
                    $areaTitle = isset($area['areaTitle']) ? $area['areaTitle'] : ("Bereich " . $areaIndex);

                    echo '<div class="accordion-container">';
                    echo '  <button class="accordion-button" data-accordion-id="' . $accordionId . '" aria-expanded="false" aria-controls="' . $accordionId . '">';
                    echo htmlspecialchars($areaTitle);
                    echo '  </button>';
                    echo '  <div class="accordion-content" id="' . $accordionId . '" role="region">';

                    echo '    <div class="section">';

                    if (!isset($area['questions'])) {
                        $area['questions'] = [];
                    }

                    $qIndex = 1;
                    foreach ($area['questions'] as $q) {
                        $questionText = isset($q['question']) ? $q['question'] : "Frage #" . $qIndex;
                        $answerText = isset($q['answer']) ? $q['answer'] : "...";

                        $questionId = "q_" . $accordionId . "_" . $qIndex;
                        $answerId = "ans_" . $accordionId . "_" . $qIndex;

                        echo '<div class="question" id="' . $questionId . '">';
                        echo '  <div class="question-text">' . htmlspecialchars($qIndex . '. ' . $questionText) . '</div>';
                        echo '  <button type="button" class="question-button" data-answer-id="' . $answerId . '" aria-expanded="false" aria-controls="' . $answerId . '">Antwort anzeigen</button>';
                        echo '  <div class="answer" id="' . $answerId . '" role="region">';
                        echo nl2br(htmlspecialchars($answerText));
                        echo '  </div>';
                        echo '</div>';
                        $qIndex++;
                    }

                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';

                    $areaIndex++;
                }
            } else {
                echo '<p>Keine Daten vorhanden oder Kategorie nicht gefunden.</p>';
            }
            ?>
        </div>
    </div>

    <footer>
        <div class="copyright">
            &copy; <?php echo date("Y"); ?> Simon Wessel. Alle Rechte vorbehalten.
        </div>
    </footer>

    <script src="main.js"></script>
</body>

</html>