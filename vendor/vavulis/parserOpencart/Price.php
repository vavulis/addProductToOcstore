<?php
require_once __ROOT__ . '/vendor/vavulis/errors/MyException.php';

class Price
{

    private $price; // цена. тип данных int
    private $markup; // наценка в процентах
    private $error = 0; // есть ли ошибка или все хорошо
    private $error_msg = ''; // текст ошибки

    private function makeValidPrice($price)
    {
        if ($price === '') {
            $this->error = 1;
            $this->error_msg = 'Не задана цена!';
            return 0;
        }
        if (!is_string($price)) {
            $this->error = 1;
            $this->error_msg = 'Параметр PRICE должен быть типа STRING!';
            return 0;
        }
        $price = str_replace(" ", "", $price);
        $price_array = str_split($price);
        $buf = '';
        foreach ($price_array as $pa) {
            if (in_array($pa, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], true)) {
                $buf .= $pa;
            } else {
                $this->error = 1;
                $this->error_msg = 'В цене есть недопустимые символы!' . 'price = ' . serialize($price);
                return 0;
            }
        }
        return intval($buf);
    }

    public function setPrice($price)
    {
        $valid_price = $this->makeValidPrice($price);
        if (!$this->error) {
            $this->price = $valid_price;
        }
    }

    public function getPriceToInt()
    {
        if (!$this->error) {
            if ($this->markup == 0) {
                return $this->price;
            } elseif ($this->markup > 0) {
                return $this->price + ($this->price / 100) * $this->markup;
            } else {
                throw new MyException('Ошибка в логике в getPrice()!');
            }
        }
    }

    public function getPriceToString()
    {
        if (!$this->error) {
            return $this->getPriceToInt() . '.0000';
        }
    }

    public function __toString()
    {
        if ($this->error) {
            return $this->error_msg;
        } else {
            return $this->getPriceToString();
        }
    }

    public function __construct($price, $markup = 0)
    {
        $this->markup = intval($markup);
        $this->setPrice($price);
    }

    public function getPrice()
    {
        if ($this->error) {
            return [
                'status' => 'error',
                'msg' => $this->error_msg
            ];
        } else {
            $price_str = $this->getPriceToString();
        }
        if ($this->error) {
            return [
                'status' => 'error',
                'msg' => $this->error_msg
            ];
        } else {
            return [
                'status' => 'ok',
                'correct_price' => $price_str
            ];
        }
    }
}
