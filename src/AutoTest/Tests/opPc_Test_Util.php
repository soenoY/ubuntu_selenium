<?php

namespace AutoTest\Tests;

use AutoTest\Base\Op_Base_TestCase;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Interactions\WebDriverActions;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class opPc_Test_Util
{
    /**
     * @var RemoteWebDriver
     */
    private $webDriver;

    function __construct(RemoteWebDriver $myWebDriver)
    {
        var_dump("------------------- set web driver ");
        $this->webDriver = $myWebDriver;
    }

    function goDtCace1()
    {
        var_dump("-------");
        //デザインツールへ移動
        $this->webDriver->get($this->url);
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
        );
    }

    public function openUrl(string $url)
    {
        var_dump("------------------- $url ");
        var_dump("url :: ".$url);
        $this->$webDriver->get($url);
        $this->$webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
        );
        var_dump($this->webDriver->getCurrentURL());  //遷移後のアドレスの確認

        return true;
    }
//dd


    /** @var RemoteWebDriver */
    public function getPageHeader()
    {
        $this->webDriver->findElement(WebDriverBy::cssSelector('h1.title'));
    }

    public function login($userName)
    {
        $this->webDriver->findElement(WebDriverBy::name(['login']))
            ->sendKeys($userName)
            ->submit();
    }
}