<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15/02/2019
 * Time: 13:58
 */

namespace app;

class Game
{
    const GAME_IS_NOT_STARTED = 0;
    const GAME_IS_STARTED = 1;
    const JSON_FILE = "/../game.json";
    public $gameStatus;
    //объект класса ввода - вывода
    private $inOut;
    private $elementsCount = 0;
    //последний совершенный ход
    private $lastMove = "O";
    public $board = [0,1,2,3,4,5,6,7,8];
    public $huPlayer = "O";
    public $aiPlayer = "X";
    public function __construct()
    {
        $json = file_get_contents(__DIR__ . self::JSON_FILE);
        if($json) {
            $current_game = json_decode($json);
            if (!empty($current_game)) {
                foreach ($current_game as $mark) {
                    $this->board[$mark->pos] = $mark->value;
                    $this->lastMove = $mark->value;
                    $this->elementsCount++;
                }
                $this->gameStatus = self::GAME_IS_STARTED;
            } else {
                $this->gameStatus = self::GAME_IS_NOT_STARTED;
            }
        }
        $this->inOut = new ConsoleHelper();
    }

    public function start(){
        //если игра не начата, то начинаем
        if($this->gameStatus == self::GAME_IS_NOT_STARTED){
            $this->execute();
        } else {
            //предлагаем пользователю начать игру сначала
            $choice = $this->inOut->readRestart();
            if($choice){
                //продолжаем
                $this->execute();
            } else {
                $this->reset();
                $this->execute();
            }
        }
    }

    public function end(){

    }

    public function draw(){
        $this->inOut->draw($this->board);
    }

    public function about(){
        echo "Разработчик: Роман Асадов, roma-asadov@mail.ru, 89991563301\n";
    }

    private function save($position){
        $file = fopen(__DIR__ . self::JSON_FILE, "r+");
        fseek($file, -1, SEEK_END);
        if($this->gameStatus == self::GAME_IS_NOT_STARTED){
            fprintf($file, '{"pos" : %d, "value" : \"%s\"}]', $position, $this->board[$position]);
        } else {
            fprintf($file, ',{"pos" : %d, "value" : \"%s\"}]', $position, $this->board[$position]);
        }
        fclose($file);
    }

    public function execute(){
        $this->draw();
        $position = $this->inOut->readPosition();
        if($this->board[$position - 1] === "X" || $this->board[$position - 1] === "O"){
            throw new \Exception("Эта клетка уже занята");
        } else {
            $value = $this->inOut->readValue();
            if($value === $this->lastMove){
                throw new \Exception("Вы уже совершили свой ход. Передайте ход сопернику.");
            } else {
                $this->board[$position - 1] = $value;
                $this->lastMove = $value;
                $this->save($position - 1);
                $this->draw();
                $this->gameStatus = self::GAME_IS_STARTED;
                $this->elementsCount++;
                if($this->isGameEnded()){
                    $this->reset();
                }
            }
        }
    }

    private function reset(){
        $file = fopen(__DIR__ . self::JSON_FILE, "w");
        fprintf($file, "[]");
        fclose($file);
        $this->board = [0, 1, 2, 3, 4, 5, 6, 7, 8];
        $this->gameStatus = self::GAME_IS_NOT_STARTED;
        $this->lastMove = "O";
        $this->elementsCount = 0;
    }

    private function isGameEnded(){
        if($this->elementsCount >= 9){
            $this->inOut->drawResult(-1);
            return true;
        } else {
            if($this->getWinner($this->board, $this->aiPlayer)){
                if($this->aiPlayer === "X") {
                    $this->inOut->drawResult(1);
                } else {
                    $this->inOut->drawResult(0);
                }
                return true;
            } else if($this->getWinner($this->board, $this->huPlayer)) {
                if($this->huPlayer === "X") {
                    $this->inOut->drawResult(1);
                } else {
                    $this->inOut->drawResult(0);
                }
                return true;
            }
        }
        return false;
    }

    public function getEmptyCells($board){
        $emptyCells = [];
        for($i = 0; $i < count($board); $i++){
            if($board[$i] !== "O" && $board[$i] !== "X") {
                $emptyCells[] = $board[$i];
            }
        }
        return $emptyCells;
    }

    public function getWinner($board, $player){
        if(
            ($board[0] === $player && $board[1] === $player && $board[2] === $player) ||
            ($board[3] === $player && $board[4] === $player && $board[5] === $player) ||
            ($board[6] === $player && $board[7] === $player && $board[8] === $player) ||
            ($board[0] === $player && $board[3] === $player && $board[6] === $player) ||
            ($board[1] === $player && $board[4] === $player && $board[7] === $player) ||
            ($board[2] === $player && $board[5] === $player && $board[8] === $player) ||
            ($board[0] === $player && $board[4] === $player && $board[8] === $player) ||
            ($board[2] === $player && $board[4] === $player && $board[6] === $player)
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function minimax($newBoard, $player){
        $emptyCells = $this->getEmptyCells($newBoard);
        if($this->getWinner($newBoard, $this->aiPlayer)){
            return ["score" => 10, "depth" => 1];
        } else if($this->getWinner($newBoard, $this->huPlayer)){
            return ["score" => -10, "depth" => 1];
        } else if(count($emptyCells) == 0){
            return ["score" => 0, "depth" => 1];
        }
        $moves = [];
        //просматриваем все свободные ячейки
        for ($i = 0; $i < count($emptyCells); $i++){
            $move = [];
            $move["depth"] = 1;
  	        $move["index"] = $newBoard[$emptyCells[$i]];
        //делаем ход игроком по очереди
        $newBoard[$emptyCells[$i]] = $player;
        //получаем очки ходом соперника текущего игрока
        if ($player == $this->aiPlayer){
            $result = $this->minimax($newBoard, $this->huPlayer);
            $move["score"] = $result["score"];
            //суммируем глубину для нахождения самых близких концов игры и предотвращения их
            $move["depth"] += $result["depth"];
        }
        else{
            $result = $this->minimax($newBoard, $this->aiPlayer);
            $move["score"] = $result["score"];
            $move["depth"] += $result["depth"];
        }
        //очищаем сделанный ход
        $newBoard[$emptyCells[$i]] = $move["index"];
        //сохраняем в массив
        $moves[] = $move;
        }
        $bestMove = [];
        if($player == $this->aiPlayer){
            $bestScore = -32000;
            $minDepth = 32000;
            for($i = 0; $i < count($moves); $i++){
                /*тут возможна ситуация, что проигрышный ход будет самым коротким по глубине,
                чтобы этого не допустить при наличии выигрышной комбинации, но с большим
                числом ходов, добавляем проверку явл-ся ли текущий элемент положительным
                это сработает только в том случае, если текущий элемент больше bestScore ,а bestScore меньше нуля,
                автоматически присваиваем minDepth глубине выигрышного хода
                и не сработает, если bestScore уже положительный*/
                if($moves[$i]["score"] >= $bestScore && $moves[$i]["depth"] < $minDepth || $moves[$i]["score"] >= $bestScore && $bestScore < 0){
                    $bestScore = $moves[$i]["score"];
                    $bestMove = $i;
                    $minDepth = $moves[$i]["depth"];
                }
            }
        }else{
            $bestScore = 32000;
            $minDepth = 32000;
            for($i = 0; $i < count($moves); $i++){
                if($moves[$i]["score"] <= $bestScore && $moves[$i]["depth"] < $minDepth || $moves[$i]["score"] <= $bestScore && $bestScore > 0){
                    $bestScore = $moves[$i]["score"];
                    $bestMove = $i;
                    $minDepth = $moves[$i]["depth"];
                }
            }
  }

    // возвращаем наилучший вариант для хода
        return $moves[$bestMove];
    }

    public function userComp(){
        $this->reset();
        while(!$this->isGameEnded()) {
            if ($this->elementsCount == 0) {
                $this->board[4] = $this->aiPlayer;
            } else {
                $move = $this->minimax($this->board, $this->aiPlayer);
                $this->board[$move["index"]] = $this->aiPlayer;
            }
            $this->elementsCount++;
            $this->draw();
            if ($this->isGameEnded()) {
                $this->reset();
                return;
            }
            $position = $this->inOut->readPosition();
            $this->board[$position - 1] = "O";
            $this->lastMove = "O";
            $this->draw();
            $this->gameStatus = self::GAME_IS_STARTED;
            $this->elementsCount++;
        }
        $this->reset();
    }
}