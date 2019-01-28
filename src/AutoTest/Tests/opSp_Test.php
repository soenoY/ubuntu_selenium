<?php

namespace AutoTest\Tests;

use AutoTest\Base\Op_Base_TestCase;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class opSp_Test extends Op_Base_TestCase
{
    const BASE_URL = 'https://test.originalprint.jp';
    const EMAIL_ADDRESS = 'soeno@imagemagic.co.jp';
    const PASSWORD = '11111111';
    const DOWNLOADPATH = __DIR__.'/../../../tmp/op/download/';
    const UPLOADPATH = __DIR__.'/../../../tmp/op/upload/';

    //iPhone X 手帳型ケース
    protected $url = "https://test.originalprint.jp/sp.php/dtool/run/?si_id=0&woi_ids=167442,167443&ic_id=26103";
    protected $returnUrl = "https://test.originalprint.jp/sp.php/item/itemDetail?i_id=5944";

    /** @test */
    public function test_header_return()
    {
        //トップページへ遷移
        $this->webDriver->get($this->url);
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('スマホ専用サイト')
        );
        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認


        //トップページが表示されたらバックボタンをクリック
        $backBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("dtBackBtn")))
        );
        $backBtn->click();
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('製作ならオリジナルプリントで！')
        );

        if ($this->returnUrl === $this->webDriver->getCurrentURL()) {
            var_dump("test_header_return : OK");
        } else {
            var_dump("test_header_return : NG");
        }
    }
}
