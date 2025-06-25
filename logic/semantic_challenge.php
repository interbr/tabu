<?php
function generateSemanticChallenge(int $stage = 1): array {
    $objects = ['Äpfel', 'Autos', 'Bücher', 'Türen', 'Uhren', 'Steine', 'Bananen', 'Flaschen', 'Stühle', 'Kisten'];
    $quantities = [100, 250, 500, 750, 1000];

    shuffle($objects);
    shuffle($quantities);

    $thing1 = $objects[0];
    $thing2 = $objects[1];

    $amount1 = rand(1, 3);
    $amount2 = rand(2, 6);
    $number_in = $quantities[0];

    // Transformation je Stufe
    $amount1_out = $amount1 + rand(1, $stage);
    $amount2_out = $amount2 + rand(1, $stage + 1);
    $number_out  = $number_in + (rand(1, 2) * 50); // z. B. 500 → 600

    $input = [
        "{$amount1} {$thing1}",
        (string)$number_in,
        "{$amount2} {$thing2}"
    ];

    $output = [
        "{$amount1_out} {$thing1}",
        (string)$number_out,
        "{$amount2_out} {$thing2}"
    ];

    return [
        'input' => $input,
        'output' => $output
    ];
}
