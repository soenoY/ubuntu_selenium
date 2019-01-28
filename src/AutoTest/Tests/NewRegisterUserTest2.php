<?php

namespace AutoTest\Tests;

use AutoTest\Base\Base_TestCase;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class NewRegisterUserTest2 extends Base_TestCase
{
    //※画面の性質上一度通ったアドレスと名前は変更しないと通らなくなる。
    const EMAIL_ADDRESS = 'iimmaaggee22222@gmail.com';
    const USERNAME = 'iimmaaggee22222';
    const PASSWORD = 'hogehoge123';

    /** @test */
    public function test_registerUser()
    {
        // トップへのアクセス
        $this->webDriver->get(self::BASE_URL);//baseurlはmmgのトップ画面
        var_dump($this->webDriver->getCurrentURL());

        // ログインのクリック
        $this->webDriver->findElement(WebDriverBy::className('js-login'))->click();

        // ログインモーダルが表示されたらはじめてご利用の方ボタンをクリック
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("wa-signup-link")))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-link"))->click();

        // 会員登録のemailが表示されるまで待ち、メールアドレスを入力
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#wa-signup-email'))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->sendKeys(self::EMAIL_ADDRESS);

        // JavaScriptによるユーザ名作成（既存の入力を空にして、取得したエレメントにユーザーネームを打ち込む）
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-username"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-username"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-username"))->sendKeys(self::USERNAME);

        //次へボタンを押す。
        $this->webDriver->findElement(
            WebDriverBy::xpath("//input[@type='submit' and @id='js-signUpNextButton' and @value='次へ']")
        )->click();

        // パスワード設定モーダル内のパスワードを入力エリアが表示されたらパスワードを入力
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))
            )
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->sendKeys(self::PASSWORD);
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->sendKeys(self::PASSWORD);

        // 値をとりたいチェックボックスが非表示時で取れないので表示させたのち　選択状態にする。
        $this->webDriver->executeScript("document.getElementById('wa-signup-password-agree').style.display='block';");
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-agree"))
            )
        );
        if (!$this->webDriver->findElement(WebDriverBy::id("wa-signup-password-agree"))->isSelected()) {
            $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-agree"))->click();
        }

        $this->webDriver->findElement(WebDriverBy::id('js-signUpComplete'))->click();
    }
}