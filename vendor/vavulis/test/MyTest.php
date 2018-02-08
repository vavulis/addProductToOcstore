<?php
namespace vavulis;

// чтобы воспользоватья классом надо 
// 1. задать $bank_of_tests - массив аргументов для ф-ции test()
// 2. переопределить метод test()
// 3. ф-ция test должна возвращать строку и информацией о успехе или неуспехе теста. можно использовать html-теги 
// 
// примерный формат $bank_of_tests: [$param1, $param2, $correct_answer]
// пример ф-ции test()
// function test($param1, $param2, $corect_answer) {
//   if ($param1 + $param2 == $correct_answer) {
//     return '<li>Allok!</li>';
//   } else {
//     return '<li>Error!</li>;
//   }
// }
abstract class MyTest
{

    protected $bank_of_tests = []; // массив аргументов для ф-ции test()
    protected $messages_buffer = []; // массив сообщений о результатах теста

    public function setBankOfTests(array $bank_of_tests)
    {
        $this->bank_of_tests = $bank_of_tests;
    }

    // return string - возвращает строку типа: 'тест пройден' или '<p>тест провален</p>'
    abstract public function test(array $args);

    public function printArray(array $data)
    {
        echo "<ul>";
        foreach ($data as $line) {
            echo "<li>";
            echo $line;
            echo "</li>";
        }
        echo "</ul>\n";
    }

    public function __construct(array $bank_of_tests = [])
    {
        $this->setBankOfTests($bank_of_tests);
    }

    public function __invoke()
    {
        if (count($this->bank_of_tests) > 0) {
            foreach ($this->bank_of_tests as $tst) {
                $this->messages_buffer[] = $this->test($tst);
            }
            $this->printArray($this->messages_buffer);
        } elseif ($this->bank_of_tests === []) {
            echo "Не заданы параметры для теста!";
        }
    }
}
