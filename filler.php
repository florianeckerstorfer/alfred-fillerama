<?php

/**
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */

$options = getOptions($_SERVER['argv']);
echo generateText($options['length'], $options['show'], $options['type']);

/**
 * @param string[] $args
 *
 * @return array<string,string>
 */
function getOptions(array $args) {
    mt_srand();
    $options = [
        'length' => empty($args[1]) || $args[1] === 'db' ? mt_rand(5, 10) : 1,
        'show'   => 'futurama',
        'type'   => 'db'
    ];
    if (!empty($args[1])) $options['type']   = $args[1];
    if (!empty($args[2])) $options['show']   = $args[2];
    if (!empty($args[3])) $options['length'] = $args[3];

    return $options;
}

/**
 * @param string $show Show to retrieve quotes from
 *
 * @return array
 */
function executeRequest($show)
{
    $url = sprintf('http://api.chrisvalleskey.com/fillerama/get.php?count=ALL&format=json&show=%s', $show);

    return json_decode(file_get_contents($url), true);
}

/**
 * @param string $show Show to retrieve quotes from
 * @param string $type Type of sentence; `db` (default) or `header`
 *
 * @return string[]
 */
function getDb($length, $show, $type)
{
    $key = $type == 'headers' ? 'header' : 'quote';
    $response = executeRequest($show);

    return array_map(function ($elem) use ($key) { return trim($elem[$key]); }, $response[$type]);
}

/**
 * @param int    $length Number of sentences
 * @param string $show   Show to take quotes from
 * @param string $type   Type of sentence; `db` (default) or `header`
 *
 * @return string
 */
function generateText($length, $show, $type)
{
    $db = getDb($length, $show, $type);
    shuffle($db);

    return html_entity_decode(implode(' ', array_slice($db, 0, $length)));
}
