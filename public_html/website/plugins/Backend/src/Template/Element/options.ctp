<?php
$level = isset($level) ? (int)$level + 1 : 1;
$labels = isset($labels) && is_array($labels) ? $labels : [];
$type = isset($type) ? $type : '';
$selected = isset($selected) ? $selected : false;
if (isset($option) && isset($key)) {
    if (is_array($option)) {
        echo '<optgroup class="opt-level-' . $level . '" label="' . $key . '">';
        foreach ($option as $k => $o) {
            echo $this -> element('Backend.options', ['key' => $k, 'option' => $o, 'level' => $level, 'type' => array_key_exists($key, $labels) ? $labels[$key] : '', 'labels' => $labels, 'selected' => $selected]);
        }
        echo '</optgroup>';
    } else {
        $attr = $selected == $key ? ' selected="selected"' : ' ';
        echo '<option' . $attr . 'value="' . $key . '" data-type="' . $type . '" class="opt-level-' . $level . '">' . $option . '</option>';
    }
}
?>