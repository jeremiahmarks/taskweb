<?php
include 'Renderer.php';

class JSONRenderer implements Renderer {

    /** The JSON file exported from taskwarrior. */
    private $f;

    function __construct($f) {
        $this->f = $f;
    }

    function render() {
        echo '<div class="tasks">';
        while (($line = fgets($this->f)) !== FALSE) {
            // remove whitespace/line endings
            $line = trim($line);
            // remove comma if exists
            if ($line[strlen($line)-1] == ',') {
                $line = rtrim($line, ',');
            }
            $task = json_decode($line);
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    echo ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                case JSON_ERROR_NONE:
                default:
                break;
            }
            echo '<div class="task">';
            echo '<span class="status">' . $task->{'status'} . '</span>';
            echo '<span class="description">' . $task->{'description'} . '</span>';
            echo '<div class="meta">';
            if (isset($task->{'project'})) {
                $project = $task->{'project'};
                echo '<span class="project">' . $project . '</span>';
            }
            if (isset($task->{'priority'})) {
                $priority = $task->{'priority'};
                echo '<span class="priority">' . $priority . '</span>';
            }
            if (isset($task->{'tags'})) {
                $tags = $task->{'tags'};
                echo '<div class="tags">';
                foreach ($tags as $tag) {
                    echo '<span class="tag">' . $tag . '</span>';
                }
                echo '</div>';
            }
            if (isset($task->{'annotations'})) {
                $annotations = $task->{'annotations'};
                echo '<div class="annotations">';
                foreach ($annotations as $entry => $annotation) {
                    echo '<span class="annotation">' . $annotation->{'description'} . '</span>';
                }
                echo '</div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    }
}
