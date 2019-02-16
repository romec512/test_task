<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 15/02/2019
 * Time: 15:48
 */

namespace interfaces;


interface InOutHelper
{
    public function readPosition();
    public function readValue();
    public function readRestart();
    public function draw($field);
    public function drawResult($winner);
}