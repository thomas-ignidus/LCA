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
$leftSide->drawSide($box_height, 'u', ['edge_type'=>'hill', 'start'=>0, 'end'=>$front_height], [0,0]);
$leftSide->drawSide($box_depth, 'r', ['edge_type'=>'valley' ]);
$leftSide->drawSide($box_height, 'd', ['edge_type'=>'valley']);
$leftSide->drawSide($box_depth, 'l', ['edge_type'=>'hill']);
$box_commands = $leftSide->commands;

$back = new Piece();
$back->drawSide($box_height, 'u', ['edge_type'=>'hill'], [$leftSide->max_x + 10,0]);
$back->drawSide($box_width, 'r', ['edge_type'=>'hill']);
$back->drawSide($box_height, 'd', ['edge_type'=>'hill']);
$back->drawSide($box_width, 'l', ['edge_type'=>'hill']);
$box_commands .= $back->commands;

$rightSide = new Piece();
$rightSide->drawSide($box_height, 'u', ['edge_type'=>'hill','start'=>0, 'end'=>$front_height], [$back->max_x + 10,0]);
$rightSide->drawSide($box_depth, 'r', ['edge_type'=>'valley' ]);
$rightSide->drawSide($box_height, 'd', ['edge_type'=>'valley']);
$rightSide->drawSide($box_depth, 'l', ['edge_type'=>'hill']);
$box_commands .= $rightSide->commands;

$bottom = new Piece();
$bottom->drawSide($box_depth, 'u', ['edge_type'=>'valley'], [$back->min_x, $back->max_y + 10]);
$bottom->drawSide($box_width, 'r', ['edge_type'=>'valley']);
$bottom->drawSide($box_depth, 'd', ['edge_type'=>'valley']);
$bottom->drawSide($box_width, 'l', ['edge_type'=>'hill']);
$box_commands .= $bottom->commands;


$top = new Piece();
$top->drawSide($box_depth, 'u', ['edge_type'=>'hill'], [$back->min_x, $back->min_y - $box_depth - 10]);
$top->drawSide($box_width, 'r', ['edge_type'=>'valley' ]);
$top->drawSide($box_depth, 'd', ['edge_type'=>'hill']);
$top->drawSide($box_width, 'l');
$top->drawSide($box_width - (2*WOOD_THICKNESS + KERF_WIDTH), 'r', false, [$top->min_x + WOOD_THICKNESS + KERF_WIDTH, $top->min_y + (2*WOOD_THICKNESS/3)]);
$top->drawSide(ACRYLIC_THICKNESS, 'u', false);
$top->drawSide($box_width - (2*WOOD_THICKNESS + KERF_WIDTH), 'l', false);
$top->drawSide(ACRYLIC_THICKNESS, 'd', false);


$box_commands .= $top->commands;


$front = new Piece();
$front->drawSide($front_height, 'u', ['edge_type'=>'valley','start'=>0, 'end'=>$front_height], [$bottom->max_x + 10, -$front_height-10]);
$front->drawSide($box_width, 'r');
$front->drawSide($front_height, 'd', ['edge_type'=>'valley']);
$front->drawSide($box_width, 'l', ['edge_type'=>'valley']);
$box_commands .= $front->commands;

$box_commands .= "\n";
$myfile = fopen("output/califinobox.lct", "w") or die("Unable to open file!");
fwrite($myfile, $box_commands);
fclose($myfile);

echo "Box Commands\n";
echo $box_commands;

$acrylic = new Piece();
$acrylic->drawSide($box_height-WOOD_THICKNESS, 'u', false, [0,0]);
$acrylic->drawSide($box_width - 2*WOOD_THICKNESS, 'r');
$acrylic->drawSide($box_height-WOOD_THICKNESS, 'd');
$acrylic->drawSide($box_width - 2*WOOD_THICKNESS, 'l');
echo "\n\nAcrylic Commands\n";
echo $acrylic->commands;
$myfile = fopen("output/califinobox_acrylic.lct", "w") or die("Unable to open file!");
fwrite($myfile, $acrylic->commands);
fclose($myfile);


echo "</pre>";
