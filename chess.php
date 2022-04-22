<?php
function printboard($board){
    $piece = [
        "em"=>"▄▀",//██
        "bk"=>"♔ ",
        "bq"=>"♕ ",
        "br"=>"♖ ",
        "bb"=>"♗ ",
        "bh"=>"♘ ",
        "bp"=>"♙ ",
        "wk"=>"♚ ",
        "wq"=>"♛ ",
        "wr"=>"♜ ",
        "wb"=>"♝ ",
        "wh"=>"♞ ",
        "wp"=>"♟ "
    ];
    echo "  ▄▄▄▄    ▄▄▄▄    ▄▄▄▄    ▄▄▄▄    \n";
    for ($i=0; $i < 8; $i++) {
        $line = abs($i-8)." ";
        for ($j=0; $j < 8; $j++) {
            if (($i+$j)%2==0) {
                if ($board[$i][$j]=="em") {
                    $line.="████";
                } else {
                    $line.= "█";
                    $line.= $piece[$board[$i][$j]];
                    $line.= "█";
                }
            } else {
                if ($board[$i][$j]=="em") {
                    $line.="    ";
                } else {
                    $line.= " ";
                    $line.= $piece[$board[$i][$j]];
                    $line.= " ";
                }
            }
        }
        echo $line."\n";
        if (!($i==7)) {
            if ($i%2==0) {
                echo "  ▀▀▀▀▄▄▄▄▀▀▀▀▄▄▄▄▀▀▀▀▄▄▄▄▀▀▀▀▄▄▄▄\n";
            } else {
                echo "  ▄▄▄▄▀▀▀▀▄▄▄▄▀▀▀▀▄▄▄▄▀▀▀▀▄▄▄▄▀▀▀▀\n";
            }
        } else {
            echo "      ▀▀▀▀    ▀▀▀▀    ▀▀▀▀    ▀▀▀▀\n";
        }
    }
    echo "   a   b   c   d   e   f   g   h  \n";
}

function makeboard(){
    $board = [
        ["br","bh","bb","bq","bk","bb","bh","br"],
        ["bp","bp","bp","bp","bp","bp","bp","bp"],
        ["em","em","em","em","em","em","em","em"],
        ["em","em","em","em","em","em","wp","em"],
        ["em","em","em","em","em","em","em","em"],
        ["em","em","em","em","em","em","em","em"],
        ["wp","wp","wp","em","wp","wp","wp","em"],
        ["wr","wh","wb","wq","wk","wb","wh","wr"]
    ];
    return $board;
}

function readin($in){
    if ($in == "die") {
        die;
    }
    $letters =[
        "a"=>"0",
        "b"=>"1",
        "c"=>"2",
        "d"=>"3",
        "e"=>"4",
        "f"=>"5",
        "g"=>"6",
        "h"=>"7"
    ];
    if (strlen($in)!=2) { return FALSE; }
    $string = str_split($in,1);
    if (array_key_exists($string[0],$letters) && $string[1] >= 1 && $string[1] <= 8) {
        $out[0] = abs($string[1]-8); 
        $out[1] = $letters[$string[0]];
        return $out;
    } else { return FALSE; }
}

function allowmove($board,$selpiece,$moveto){
    $pieceatend = $board[$moveto[0]][$moveto[1]];
    $piecetomove = $board[$selpiece[0]][$selpiece[1]];
    if (substr($pieceatend,0,1)!=substr($piecetomove,0,1)) {
        switch (substr($piecetomove,1,1)) {
            case 'h'://horse
                $ydif = abs($moveto[0]-$selpiece[0]);
                if ($ydif < 3 && $ydif > 0) {
                    if (($ydif+abs($moveto[1]-$selpiece[1]))==3) {
                        return TRUE;
                    }
                }
                return FALSE;
                break;
            
            case 'k'://king
                if (abs($moveto[0]-$selpiece[0]) == 1 || abs($moveto[0]-$selpiece[0]) == 0) {
                    if (abs($moveto[1]-$selpiece[1]) == 1 || abs($moveto[1]-$selpiece[1]) == 0) {
                        return TRUE;
                    }
                }
                return FALSE;
                break;
            
            case 'p'://pawn
                if (substr($piecetomove,0,1)=="w") {
                    $dirc = "-";
                } else {
                    $dirc = "+";
                }
                if ($pieceatend!="em") {
                    if ($moveto[0]-$selpiece[0] == $dirc."1" && abs($moveto[1]-$selpiece[1]) == 1) {
                        return TRUE;
                    }
                    return FALSE;
                }
                if (abs($moveto[1]-$selpiece[1]) == 0) {
                    if ($moveto[0]-$selpiece[0] == $dirc."1") {
                        return TRUE;
                    } elseif ($moveto[0]-$selpiece[0] == $dirc."2" && ($selpiece[0] == 1 || $selpiece[0] == 6)) {
                        //pawn can jump over piece if it is in front of it at the start
                        return TRUE;                        
                    }
                    return FALSE;
                }
                return FALSE;
                break;

            case 'q'://queen
                if (($moveto[0]-$selpiece[0]) == 0 || ($moveto[1]-$selpiece[1]) == 0) {
                    if (($moveto[0]-$selpiece[0]) == 0) {
                        for ($i=min($moveto[1],$selpiece[1])+1; $i < max($moveto[1],$selpiece[1]); $i++) { 
                            if ($board[$moveto[0]][$i]!="em") {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    } else {
                        for ($i=min($moveto[0],$selpiece[0])+1; $i < max($moveto[0],$selpiece[0]); $i++) { 
                            if ($board[$i][$moveto[1]]!="em") {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    }
                }
                if (($moveto[0] + $moveto[1]) == ($selpiece[0] + $selpiece[1])) {
                    for ($i=min($moveto[0],$selpiece[0])+1,$j=max($moveto[1],$selpiece[1])-1; $i < max($moveto[0],$selpiece[0]); $i++, $j--) { 
                        if ($board[$i][$j]!="em") {
                            return FALSE;
                        }
                    }
                    return TRUE;
                } elseif (($moveto[0] + abs($moveto[1]-7)) == ($selpiece[0] + abs($selpiece[1]-7))) {
                    for ($i=min($moveto[0],$selpiece[0])+1,$j=max($moveto[1],$selpiece[1])+1; $i < max($moveto[0],$selpiece[0]); $i++, $j++) { 
                        if ($board[$i][$j]!="em") {
                            return FALSE;
                        }
                    }
                    return TRUE;
                }
                
                return FALSE;
                break;

            case 'r'://rook
                if (($moveto[0]-$selpiece[0]) == 0 || ($moveto[1]-$selpiece[1]) == 0) {
                    if (($moveto[0]-$selpiece[0]) == 0) {
                        for ($i=min($moveto[1],$selpiece[1])+1; $i < max($moveto[1],$selpiece[1]); $i++) { 
                            if ($board[$moveto[0]][$i]!="em") {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    } else {
                        for ($i=min($moveto[0],$selpiece[0])+1; $i < max($moveto[0],$selpiece[0]); $i++) { 
                            if ($board[$i][$moveto[1]]!="em") {
                                return FALSE;
                            }
                        }
                        return TRUE;
                    }
                }
                return FALSE;
                break;

            case 'b'://bishop
                if (($moveto[0] + $moveto[1]) == ($selpiece[0] + $selpiece[1])) {
                    for ($i=min($moveto[0],$selpiece[0])+1,$j=max($moveto[1],$selpiece[1])-1; $i < max($moveto[0],$selpiece[0]); $i++, $j--) { 
                        if ($board[$i][$j]!="em") {
                            return FALSE;
                        }
                    }
                    return TRUE;
                } elseif (($moveto[0] + abs($moveto[1]-7)) == ($selpiece[0] + abs($selpiece[1]-7))) {
                    for ($i=min($moveto[0],$selpiece[0])+1,$j=max($moveto[1],$selpiece[1])+1; $i < max($moveto[0],$selpiece[0]); $i++, $j++) { 
                        if ($board[$i][$j]!="em") {
                            return FALSE;
                        }
                    }
                    return TRUE;
                }
                
                return FALSE;
                break;

            default:
                echo "WTF!!\n";
                die;
                break;
        }
        return TRUE;
    } else {
        return FALSE;
    }
    
}

$pieces = [
    "em"=>"▄▀",//██
    "bk"=>"♔ ",
    "bq"=>"♕ ",
    "br"=>"♖ ",
    "bb"=>"♗ ",
    "bh"=>"♘ ",
    "bp"=>"♙ ",
    "wk"=>"♚ ",
    "wq"=>"♛ ",
    "wr"=>"♜ ",
    "wb"=>"♝ ",
    "wh"=>"♞ ",
    "wp"=>"♟ "
];
$board = makeboard();
printboard($board);
echo "\n";
while (1) {
    echo "Select piece to move!\n>";
    $selpiece = readin(readline(""));
    if ($selpiece==FALSE) {
        printboard($board);
        echo "Make a valid input!\n";
        continue;
    }
    $piece = $board[$selpiece[0]][$selpiece[1]];
    printboard($board);
    echo "You selected ".$pieces[$piece]."!\n";
    echo "Choose where it is going to move!\n>";
    $moveto = readin(readline(""));
    if ($moveto==FALSE) {
        printboard($board);
        echo "Make a valid input!\n";
        continue;
    }
    if (allowmove($board,$selpiece,$moveto)) {//if allowed
        //make move
        $board[$moveto[0]][$moveto[1]] = $piece;
        $board[$selpiece[0]][$selpiece[1]] = "em";
        printboard($board);
        echo "You made a move!\n";
    } else {
        printboard($board);
        echo "You can't make that move!\n";
    }
    
}
//echo "▄▄▄▄\n█♔ █\n▀▀▀▀\n"
?>