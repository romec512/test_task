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
    echo "1)Начать игру\n2)Сделать ход\n3)Играть против компьютера\n4)Информация о разработчике\n5)Показать поле\n6)Выход\n";
    fscanf(STDIN, "%d\n", $command);
    try{
    switch ($command) {
        case 1 :
            $game->start();
            break;
        case 2: $game->execute();
            break;
        case 3 :
            $game->UserComp();
            break;
        case 4:
            $game->about();
            break;
        case 5 :
            $game->draw();
            break;
        case 6 :
            exit();
            break;
    }} catch (Exception $e){
        echo $e->getMessage() . "\n";
    }
}