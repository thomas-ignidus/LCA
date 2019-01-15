<?php


class Piece
{
    public $height;
    public $width;
    public $commands;
    private $notch_depth;
    private $notch_width;
    private $current_coordinates;
    public $evalation;
    public $min_x;
    public $min_y;
    public $max_x;
    public $max_y;


    function __construct()
    {
        $this->commands = '';
        $this->notch_depth = WOOD_THICKNESS;
        $this->notch_width = DEFAULT_NOTCH_WIDTH;
        $this->elevation = 'hill';
    }

    public function drawSide($length, $direction = 'u', $connector = false, $start = false)
    {
        if ($start != false) {
            $this->commands .= "l\n" . implode(',', $start) . "\n";
//			$start = explode($start, ',');
            $this->current_coordinates = ['x' => $start[0], 'y' => $start[1]];
        }
        if ($direction == 'u') {
            $this->current_coordinates['y'] += $length;
        } else if ($direction == 'd') {
            $this->current_coordinates['y'] -= $length;
        } else if ($direction == 'l') {
            $this->current_coordinates['x'] -= $length;
        } else if ($direction == 'r') {
            $this->current_coordinates['x'] += $length;
        }

        if (!isset($this->min_x) || $this->current_coordinates['x'] < $this->min_x) {
            $this->min_x = $this->current_coordinates['x'];
        }
        if (!isset($this->max_x) || $this->current_coordinates['x'] > $this->max_x) {
            $this->max_x = $this->current_coordinates['x'];
        }
        if (!isset($this->min_y) || $this->current_coordinates['y'] < $this->min_y) {
            $this->min_y = $this->current_coordinates['y'];
        }
        if (!isset($this->max_y) || $this->current_coordinates['y'] > $this->max_y) {
            $this->max_y = $this->current_coordinates['y'];
        }
        $this->height = $this->max_y - $this->min_y;
        $this->height = $this->max_y - $this->min_y;

        if ($connector == false) {
            $this->commands .= $this->current_coordinates['x'] . ',' . $this->current_coordinates['y'] . "\n";
        } else {
            if(isset($connector['start']) && isset($connector['end'])){
                $remainingLength = $connector['end'];
            }
            $edge_notch_width = 0;
            while ($edge_notch_width < MIN_NOTCH_WIDTH * 2) {
                if (isset($default_notch_count)) {
                    $default_notch_count -= 2;
                } else {
                    if(isset($remainingLength)){
                        $default_notch_count = $this->calculateDefaultNotchCount($remainingLength, $this->notch_width);
                    }
                    else{
                        $default_notch_count = $this->calculateDefaultNotchCount($length, $this->notch_width);
                    }
                }
                if(isset($remainingLength)){
                    $edge_notch_width = $this->calculateEdgeNotchLength($remainingLength, $this->notch_width, $default_notch_count, $connector['edge_type']);
                }
                else{
                    $edge_notch_width = $this->calculateEdgeNotchLength($length, $this->notch_width, $default_notch_count, $connector['edge_type']);
                }
            }

            $hill_notch_width = $this->notch_width + KERF_WIDTH;
            $valley_notch_width = $this->notch_width - KERF_WIDTH;
            if ($connector['edge_type'] == 'hill') {
//start with hill
                if ($direction == 'u') {
                    $this->commands .= Helper::getCommand('up', $edge_notch_width);
                    $this->commands .= Helper::getCommand('right', $this->notch_depth);
                    $this->commands .= Helper::getCommand('up', $valley_notch_width);
                    $this->commands .= Helper::getCommand('left', $this->notch_depth);
                } else if ($direction == 'd') {
                    $this->commands .= Helper::getCommand('down', $edge_notch_width);
                    $this->commands .= Helper::getCommand('left', $this->notch_depth);
                    $this->commands .= Helper::getCommand('down', $valley_notch_width);
                    $this->commands .= Helper::getCommand('right', $this->notch_depth);
                } else if ($direction == 'l') {
                    $this->commands .= Helper::getCommand('left', $edge_notch_width);
                    $this->commands .= Helper::getCommand('up', $this->notch_depth);
                    $this->commands .= Helper::getCommand('left', $valley_notch_width);
                    $this->commands .= Helper::getCommand('down', $this->notch_depth);
                } else if ($direction == 'r') {
                    $this->commands .= Helper::getCommand('right', $edge_notch_width);
                    $this->commands .= Helper::getCommand('down', $this->notch_depth);
                    $this->commands .= Helper::getCommand('right', $valley_notch_width);
                    $this->commands .= Helper::getCommand('up', $this->notch_depth);
                }
                $default_notch_count--;
            } else {
//start with valley
                if ($direction == 'u') {
                    $this->commands .= "l\n";
                    $this->commands .= Helper::getCommand('right', $this->notch_depth);
                    $this->commands .= Helper::getCommand('up', $edge_notch_width);
                    $this->commands .= Helper::getCommand('left', $this->notch_depth);
                } else if ($direction == 'd') {
                    $this->commands .= "l\n";
                    $this->commands .= Helper::getCommand('left', $this->notch_depth);
                    $this->commands .= Helper::getCommand('down', $edge_notch_width);
                    $this->commands .= Helper::getCommand('right', $this->notch_depth);
                } else if ($direction == 'l') {
                    $this->commands .= "l\n";
                    $this->commands .= Helper::getCommand('up', $this->notch_depth);
                    $this->commands .= Helper::getCommand('left', $edge_notch_width);
                    $this->commands .= Helper::getCommand('down', $this->notch_depth);
                } else if ($direction == 'r') {
                    $this->commands .= "l\n";
                    $this->commands .= Helper::getCommand('down', $this->notch_depth);
                    $this->commands .= Helper::getCommand('right', $edge_notch_width);
                    $this->commands .= Helper::getCommand('up', $this->notch_depth);
                }
            }
            for ($i = 0; $i < $default_notch_count; $i += 2) {
                if ($direction == 'u') {
                    $this->commands .= Helper::getCommand('up', $hill_notch_width);
                    $this->commands .= Helper::getCommand('right', $this->notch_depth);
                    if ($i + 1 < $default_notch_count) {
                        $this->commands .= Helper::getCommand('up', $valley_notch_width);
                        $this->commands .= Helper::getCommand('left', $this->notch_depth);
                    }
                } else if ($direction == 'd') {
                    $this->commands .= Helper::getCommand('down', $hill_notch_width);
                    $this->commands .= Helper::getCommand('left', $this->notch_depth);
                    if ($i + 1 < $default_notch_count) {
                        $this->commands .= Helper::getCommand('down', $valley_notch_width);
                        $this->commands .= Helper::getCommand('right', $this->notch_depth);
                    }
                } else if ($direction == 'l') {
                    $this->commands .= Helper::getCommand('left', $hill_notch_width);
                    $this->commands .= Helper::getCommand('up', $this->notch_depth);
                    if ($i + 1 < $default_notch_count) {
                        $this->commands .= Helper::getCommand('left', $valley_notch_width);
                        $this->commands .= Helper::getCommand('down', $this->notch_depth);
                    }
                } else if ($direction == 'r') {
                    $this->commands .= Helper::getCommand('right', $hill_notch_width);
                    $this->commands .= Helper::getCommand('down', $this->notch_depth);
                    if ($i + 1 < $default_notch_count) {
                        $this->commands .= Helper::getCommand('right', $valley_notch_width);
                        $this->commands .= Helper::getCommand('up', $this->notch_depth);
                    }
                }
            }
            if ($connector['edge_type'] == 'hill') {
//end with hill
                if ($direction == 'u') {
                    $this->commands .= Helper::getCommand('up', $edge_notch_width);
                } else if ($direction == 'd') {
                    $this->commands .= Helper::getCommand('down', $edge_notch_width);
                } else if ($direction == 'l') {
                    $this->commands .= Helper::getCommand('left', $edge_notch_width);
                } else if ($direction == 'r') {
                    $this->commands .= Helper::getCommand('right', $edge_notch_width);
                }
                $default_notch_count--;
                $this->elevation = 'hill';
            } else {
//end with valley
                if ($direction == 'u') {
                    $this->commands .= Helper::getCommand('up', $edge_notch_width);
                } else if ($direction == 'd') {
                    $this->commands .= Helper::getCommand('down', $edge_notch_width);
                } else if ($direction == 'l') {
                    $this->commands .= Helper::getCommand('left', $edge_notch_width);
                } else if ($direction == 'r') {
                    $this->commands .= Helper::getCommand('right', $edge_notch_width);
                }
                $this->elevation = 'valley';
            }
            if(isset($remainingLength)){
                if ($direction == 'u') {
                    $this->commands .= Helper::getCommand('up', $length - $remainingLength);
                } else if ($direction == 'd') {
                    $this->commands .= Helper::getCommand('down', $length - $remainingLength);
                } else if ($direction == 'l') {
                    $this->commands .= Helper::getCommand('left', $length - $remainingLength);
                } else if ($direction == 'r') {
                    $this->commands .= Helper::getCommand('right', $length - $remainingLength);
                }
            }
        }

        $this->commands .= "l\n" . $this->current_coordinates['x'] . "," . $this->current_coordinates['y'] . "\n";
    }

    private function calculateDefaultNotchCount($connection_length, $notch_width)
    {
        $default_notch_count = floor($connection_length / $notch_width);
        if ($default_notch_count % 2 == 0) {
            $default_notch_count -= 1;
        }
        return $default_notch_count;
    }

    private function calculateEdgeNotchLength($connection_length, $default_notch_width, $default_notch_count, $edge_type)
    {
        $edge_notch_width = (($connection_length - ($default_notch_width * $default_notch_count)) / 2);
        if ($edge_type == 'hill') {
            $edge_notch_width += KERF_WIDTH / 2;
        } else if ($edge_type == 'valley') {
            $edge_notch_width -= KERF_WIDTH / 2;
        }
        return $edge_notch_width;
    }
}