<?php

//tight fit(no glue) define('KERF_WIDTH', .26);
define('KERF_WIDTH', .25);
define('WOOD_THICKNESS', 4.5);

define('DEFAULT_NOTCH_WIDTH', 10);
define('MIN_NOTCH_WIDTH', 4);

$box_height = 160;
$box_width = 170;
$box_depth = 70;
$front_height = 50;

echo "<pre>";

$leftSide = new Piece();
$leftSide->drawSide($box_height, 'u', false, [0,0]);
$leftSide->drawSide($box_depth, 'r', ['edge_type'=>'hill' ]);
$leftSide->drawSide($box_height, 'd', ['edge_type'=>'valley']);
$leftSide->drawSide($box_depth, 'l', ['edge_type'=>'hill']);
$commands = $leftSide->commands;

$back = new Piece();
$back->drawSide($box_height, 'u', ['edge_type'=>'hill', 'start'=>0, 'end'=>$front_height], [$leftSide->max_x + 10,0]);
$back->drawSide($box_width, 'r', ['edge_type'=>'hill']);
$back->drawSide($box_height, 'd', ['edge_type'=>'hill']);
$back->drawSide($box_width, 'l', ['edge_type'=>'hill']);
$commands .= $back->commands;

$rightSide = new Piece();
$rightSide->drawSide($box_height, 'u', ['edge_type'=>'valley','start'=>0, 'end'=>$front_height], [$back->max_x + 10,0]);
$rightSide->drawSide($box_depth, 'r', ['edge_type'=>'hill' ]);
$rightSide->drawSide($box_height, 'd');
$rightSide->drawSide($box_depth, 'l', ['edge_type'=>'hill']);
$commands .= $rightSide->commands;

$bottom = new Piece();
$bottom->drawSide($box_depth, 'u', ['edge_type'=>'valley','start'=>0, 'end'=>$front_height], [$back->min_x, $back->max_y + 10]);
$bottom->drawSide($box_width, 'r', ['edge_type'=>'valley' ]);
$bottom->drawSide($box_depth, 'd', ['edge_type'=>'valley']);
$bottom->drawSide($box_width, 'l');
$commands .= $bottom->commands;


$top = new Piece();
$top->drawSide($box_depth, 'u', ['edge_type'=>'valley','start'=>0, 'end'=>$front_height], [$back->min_x, $back->min_y - $box_depth - 10]);
$top->drawSide($box_width, 'r', ['edge_type'=>'valley' ]);
$top->drawSide($box_depth, 'd', ['edge_type'=>'valley']);
$top->drawSide($box_width, 'l');
$commands .= $top->commands;

$myfile = fopen("output.lct", "w") or die("Unable to open file!");
fwrite($myfile, $commands);
fclose($myfile);

echo $commands;
echo "</pre>";

class Piece {
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


    function __construct(){
        echo "\n\nmaking new Piece\n";
        $this->commands = '';
        $this->notch_depth = WOOD_THICKNESS;
        $this->notch_width = DEFAULT_NOTCH_WIDTH;
        $this->elevation = 'hill';
    }
    public function drawSide($length, $direction='u', $connector=false, $start=false){
        if($start != false){
            $this->commands .= "l\n" . implode(',', $start) . "\n";
//			$start = explode($start, ',');
            $this->current_coordinates = ['x'=>$start[0], 'y'=>$start[1]];
        }
        if($direction == 'u'){
            $this->current_coordinates['y'] += $length;
        }
        else if($direction == 'd'){
            $this->current_coordinates['y'] -= $length;
        }
        else if($direction == 'l'){
            $this->current_coordinates['x'] -= $length;
        }
        else if($direction == 'r'){
            $this->current_coordinates['x'] += $length;
        }

        if(!isset($this->min_x) || $this->current_coordinates['x'] < $this->min_x){
            $this->min_x = $this->current_coordinates['x'];
        }
        if(!isset($this->max_x) || $this->current_coordinates['x'] > $this->max_x){
            $this->max_x = $this->current_coordinates['x'];
        }
        if(!isset($this->min_y) || $this->current_coordinates['y'] < $this->min_y){
            $this->min_y = $this->current_coordinates['y'];
        }
        if(!isset($this->max_y) || $this->current_coordinates['y'] > $this->max_y){
            $this->max_y = $this->current_coordinates['y'];
        }
        $this->height = $this->max_y - $this->min_y;
        $this->height = $this->max_y - $this->min_y;

        if($connector == false){
            $this->commands .= $this->current_coordinates['x'] . ',' . $this->current_coordinates['y'] . "\n";
        }
        else {
            $edge_notch_width = 0;
            while($edge_notch_width < MIN_NOTCH_WIDTH*2){
                if(isset($default_notch_count)){
                    $default_notch_count -= 2;
                }
                else{
                    $default_notch_count = $this->calculateDefaultNotchCount($length, $this->notch_width);
                }
                $edge_notch_width = $this->calculateEdgeNotchLength($length, $this->notch_width, $default_notch_count, $connector['edge_type']);
            }
            echo '$edge_notch_width - ' . $edge_notch_width . "\n";
            echo '$default_notch_count - ' . $default_notch_count . "\n";
            $hill_notch_width = $this->notch_width + KERF_WIDTH;
            $valley_notch_width = $this->notch_width - KERF_WIDTH;
            if ($connector['edge_type'] == 'hill') {
                //start with hill
                if ($direction == 'u') {
                    $this->commands .= $this->getCommand('up', $edge_notch_width);
                    $this->commands .= $this->getCommand('right', $this->notch_depth);
                    $this->commands .= $this->getCommand('up', $valley_notch_width);
                    $this->commands .= $this->getCommand('left', $this->notch_depth);
                } else if ($direction == 'd') {
                    $this->commands .= $this->getCommand('down', $edge_notch_width);
                    $this->commands .= $this->getCommand('left', $this->notch_depth);
                    $this->commands .= $this->getCommand('down', $valley_notch_width);
                    $this->commands .= $this->getCommand('right', $this->notch_depth);
                } else if ($direction == 'l') {
                    $this->commands .= $this->getCommand('left', $edge_notch_width);
                    $this->commands .= $this->getCommand('up', $this->notch_depth);
                    $this->commands .= $this->getCommand('left', $valley_notch_width);
                    $this->commands .= $this->getCommand('down', $this->notch_depth);
                } else if ($direction == 'r') {
                    $this->commands .= $this->getCommand('right', $edge_notch_width);
                    $this->commands .= $this->getCommand('down', $this->notch_depth);
                    $this->commands .= $this->getCommand('right', $valley_notch_width);
                    $this->commands .= $this->getCommand('up', $this->notch_depth);
                }
                $default_notch_count--;
            } else {
                //start with valley
                if ($direction == 'u') {
                    $this->commands .= "l\n";
                    $this->commands .= $this->getCommand('right', $this->notch_depth);
                    $this->commands .= $this->getCommand('up', $edge_notch_width);
                    $this->commands .= $this->getCommand('left', $this->notch_depth);
                } else if ($direction == 'd') {
                    $this->commands .= "l\n";
                    $this->commands .= $this->getCommand('left', $this->notch_depth);
                    $this->commands .= $this->getCommand('down', $edge_notch_width);
                    $this->commands .= $this->getCommand('right', $this->notch_depth);
                } else if ($direction == 'l') {
                    $this->commands .= "l\n";
                    $this->commands .= $this->getCommand('up', $this->notch_depth);
                    $this->commands .= $this->getCommand('left', $edge_notch_width);
                    $this->commands .= $this->getCommand('down', $this->notch_depth);
                } else if ($direction == 'r') {
                    $this->commands .= "l\n";
                    $this->commands .= $this->getCommand('down', $this->notch_depth);
                    $this->commands .= $this->getCommand('right', $edge_notch_width);
                    $this->commands .= $this->getCommand('up', $this->notch_depth);
                }
            }
            for($i=0;$i<$default_notch_count;$i+=2){
                if ($direction == 'u') {
                    $this->commands .= $this->getCommand('up', $hill_notch_width);
                    $this->commands .= $this->getCommand('right', $this->notch_depth);
                    if($i+1 <$default_notch_count){
                        $this->commands .= $this->getCommand('up', $valley_notch_width);
                        $this->commands .= $this->getCommand('left', $this->notch_depth);
                    }
                } else if ($direction == 'd') {
                    $this->commands .= $this->getCommand('down', $hill_notch_width);
                    $this->commands .= $this->getCommand('left', $this->notch_depth);
                    if($i+1 <$default_notch_count){
                        $this->commands .= $this->getCommand('down', $valley_notch_width);
                        $this->commands .= $this->getCommand('right', $this->notch_depth);
                    }
                } else if ($direction == 'l') {
                    $this->commands .= $this->getCommand('left', $hill_notch_width);
                    $this->commands .= $this->getCommand('up', $this->notch_depth);
                    if($i+1 <$default_notch_count) {
                        $this->commands .= $this->getCommand('left', $valley_notch_width);
                        $this->commands .= $this->getCommand('down', $this->notch_depth);
                    }
                } else if ($direction == 'r') {
                    $this->commands .= $this->getCommand('right', $hill_notch_width);
                    $this->commands .= $this->getCommand('down', $this->notch_depth);
                    if($i+1 <$default_notch_count) {
                        $this->commands .= $this->getCommand('right', $valley_notch_width);
                        $this->commands .= $this->getCommand('up', $this->notch_depth);
                    }
                }
            }
            if ($connector['edge_type'] == 'hill') {
                //end with hill
                if ($direction == 'u') {
                    $this->commands .= $this->getCommand('up', $edge_notch_width);
                }
                else if ($direction == 'd') {
                    $this->commands .= $this->getCommand('down', $edge_notch_width);
                }
                else if ($direction == 'l') {
                    $this->commands .= $this->getCommand('left', $edge_notch_width);
                } else if ($direction == 'r') {
                    $this->commands .= $this->getCommand('right', $edge_notch_width);
                }
                $default_notch_count--;
                $this->elevation = 'hill';
            } else {
                //end with valley
                if ($direction == 'u') {
                    $this->commands .= $this->getCommand('up', $edge_notch_width);
                } else if ($direction == 'd') {
                    $this->commands .= $this->getCommand('down', $edge_notch_width);
                } else if ($direction == 'l') {
                    $this->commands .= $this->getCommand('left', $edge_notch_width);
                } else if ($direction == 'r') {
                    $this->commands .= $this->getCommand('right', $edge_notch_width);
                }
                $this->elevation = 'valley';
            }
        }

        $this->commands .= "l\n" . $this->current_coordinates['x'] . "," . $this->current_coordinates['y'] . "\n";
    }
    private function calculateDefaultNotchCount($connection_length, $notch_width){
        $default_notch_count = floor($connection_length / $notch_width);
        if($default_notch_count % 2 == 0){
            $default_notch_count -= 1;
        }
        return $default_notch_count;
    }
    private function calculateEdgeNotchLength($connection_length, $default_notch_width, $default_notch_count, $edge_type){
        $edge_notch_width = (($connection_length - ($default_notch_width * $default_notch_count)) /2);
        if($edge_type == 'hill'){
            $edge_notch_width += KERF_WIDTH/2;
        }
        else if($edge_type == 'valley'){
            $edge_notch_width -= KERF_WIDTH/2;
        }
        return $edge_notch_width;
    }
    private function getCommand($direction, $length){
        if($direction == 'up'){
            return '@0,' . $length . "\n";
        }
        if($direction == 'down'){
            return '@0,-' . $length . "\n";
        }
        if($direction == 'left'){
            return '@-' . $length . ",0\n";
        }
        if($direction == 'right'){
            return '@' . $length . ",0\n";
        }
    }
}