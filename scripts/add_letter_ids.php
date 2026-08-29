<?php

$file = __DIR__ . '/../hb15_index_content.php';
$content = file_get_contents($file);
foreach (range('A', 'Z') as $letter) {
    $content = str_replace('<h4>' . $letter . '</h4>', '<h4 id="letter-' . $letter . '">' . $letter . '</h4>', $content);
}
file_put_contents($file, $content);
echo "Updated letter IDs in $file\n";
