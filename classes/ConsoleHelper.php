<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15/02/2019
 * Time: 15:33
 */
namespace app;


use interfaces\InOutHelper;

class ConsoleHelper implements InOutHelper
{
    public function readPosition()
    {
        echo "Введите номер клетки:\n";
        fscanf(STDIN, "%d\n", $position);
        if(is_null($position)){
            throw new \Exception("Ошибка ввода");
        }
        return $position;
    }

    public function readValue()
    {
        echo "Введите число:\n1)O\n2)X\n";
        fscanf(STDIN, "%d\n", $value);
        if(is_null($value)){
            throw new \Exception("Ошибка ввода");
        }
        return ($value == 1) ? "O" : "X";
    }

    public function draw($field)
    {
        echo "__________________________\n";
        for($i=0; $i < count($field); $i++){
            if($field[$i] === "X"){
                echo "|   X   ";
            } else if($field[$i] === "O"){
                echo "|   O   ";
            } else{
                $index = $i + 1;
                echo "|#{$index}     ";
            }
            if(($i+1) % 3 == 0 && (($i+1) / 3) >= 1){
                echo "|\n__________________________\n";
            }
        }
    }

    public function readRestart()
    {
        echo "Предыдущая игра не закончена.\n1)Продолжить.\n2)Начать сначала.\n";
        fscanf(STDIN, "%d\n", $value);
        return ($value == 1) ? true : false;
    }

    /**
     * @param int $winner
     */
    public function drawResult($winner)
    {
        if($winner == -1){
            echo "Ничья!\n";
        } else if($winner == 0){
            echo "Победили нолики!\n";
        } else if($winner == 1){
            echo "Победили крестики!\n";
        }
    }
}