<?php

include('Helper.php');
include('Piece.php');
include('config.php');

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
