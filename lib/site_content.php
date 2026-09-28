<?php
/* PHP 5.4 / MariaDB 5.5.50. This file does not load common.php. */
require_once __DIR__.'/site_content_defaults.php';

function wc_h($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function wc_query($db, $sql, $params)
{
    // The legacy pdo_query() exits on errors; use a checked statement so missing
    // migration tables can fall back without printing SQL in the public page.
    $ps = $db->prepare($sql);
    if (!$ps || !$ps->execute($params)) { throw new RuntimeException('Content query failed.'); }
    return $ps;
}

function wc_load_texts($db, $page, $strict = false)
{
    $definitions = wc_text_defaults();
    if (!isset($definitions[$page])) { throw new InvalidArgumentException('Invalid content page.'); }
    $rows = array();
    foreach ($definitions[$page] as $key => $definition) {
        $rows[$key] = array('text_value' => $definition['text'], 'revision' => 0);
    }
    try {
        $ps = wc_query($db, 'SELECT content_key, text_value, revision FROM site_content_texts WHERE page_key = ?', array($page));
        while ($row = $ps->fetch(PDO::FETCH_ASSOC)) {
            if (isset($rows[$row['content_key']])) { $rows[$row['content_key']] = $row; }
        }
        if ($strict) {
            foreach ($rows as $row) { if (!$row['revision']) { throw new RuntimeException('Content seed required.'); } }
        }
    } catch (Exception $e) {
        if ($strict) { throw $e; }
        error_log('site_content: text defaults used ('.$page.').');
    }
    return $rows;
}

function wc_text($rows, $key)
{
    return isset($rows[$key]) ? $rows[$key]['text_value'] : '';
}

function wc_safe_image_path($path)
{
    if (!is_string($path)) { return ''; }
    $path = '/'.ltrim($path, '/');
    // Static defaults and old/new upload roots are supported. No URL/CSS syntax.
    if (!preg_match('~^/(?:@resource/images|uploads|upload)/[A-Za-z0-9_./-]+\.(?:jpg|jpeg|png|gif|webp)$~iD', $path)
        || strpos($path, '..') !== false || strpos($path, '//') !== false) { return ''; }
    return $path;
}

function wc_load_images($db, $group, $strict = false)
{
    $definitions = wc_image_defaults(); $rows = array();
    foreach ($definitions as $key => $definition) {
        if ($definition['group'] === $group) {
            $rows[$key] = array('storage_key' => $definition['storage_key'], 'alt_text' => $definition['alt_text'], 'revision' => 0);
        }
    }
    if (!$rows) { return $rows; }
    try {
        $ps = wc_query($db, 'SELECT slot_key, storage_key, alt_text, revision FROM site_content_images WHERE slot_key IN ('.implode(',', array_fill(0, count($rows), '?')).')', array_keys($rows));
        while ($row = $ps->fetch(PDO::FETCH_ASSOC)) {
            if (isset($rows[$row['slot_key']])) {
                $row['storage_key'] = wc_safe_image_path($row['storage_key']);
                if ($row['storage_key'] === '') { $row['storage_key'] = $definitions[$row['slot_key']]['storage_key']; }
                $rows[$row['slot_key']] = $row;
            }
        }
        if ($strict) {
            foreach ($rows as $row) { if (!$row['revision']) { throw new RuntimeException('Image seed required.'); } }
        }
    } catch (Exception $e) {
        if ($strict) { throw $e; }
        error_log('site_content: image defaults used ('.$group.').');
    }
    return $rows;
}

function wc_image($rows, $key, $field)
{
    return isset($rows[$key][$field]) ? $rows[$key][$field] : '';
}

function wc_valid_text($value, $max)
{
    if (!is_string($value) || strlen($value) > $max * 4 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)) { return false; }
    $chars = array();
    return preg_match_all('/./us', $value, $chars) !== false && count($chars[0]) <= $max;
}

function wc_valid_revision($value)
{
    return is_string($value) && preg_match('/^[1-9][0-9]{0,9}$/D', $value) && (float)$value < 4294967295;
}

function wc_token_equal($expected, $actual)
{
    if (!is_string($expected) || !is_string($actual) || $expected === '' || strlen($expected) !== strlen($actual)) { return false; }
    $diff = 0;
    for ($i = 0; $i < strlen($expected); $i++) { $diff |= ord($expected[$i]) ^ ord($actual[$i]); }
    return $diff === 0;
}
