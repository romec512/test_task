<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15/02/2019
 * Time: 13:50
 */
require "classes/Game.php";
require "interfaces/InOutHelper.php";
require "classes/ConsoleHelper.php";
$game = new \app\Game();
while(true) {
    echo "1)Начать игру\n2)Сделать ход\n3)Информация о разработчике\n4)Показать поле\n5)Выход\n";
    fscanf(STDIN, "%d\n", $command);
    try{
    switch ($command) {
        case 1 :
            $game->start();
            break;
        case 2: $game->execute();
            break;
        case 3 :
//            $game->about();
            $game->UserComp();
            break;
        case 4 :
            $game->draw();
            break;
        case 5 :
            exit();
            break;
    }} catch (Exception $e){
        echo $e->getMessage() . "\n";
    }
}