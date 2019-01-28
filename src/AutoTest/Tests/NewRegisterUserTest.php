<?php

namespace AutoTest\Tests;

use AutoTest\Base\Base_TestCase;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class NewRegisterUserTest extends Base_TestCase
{
    const EMAIL_ADDRESS = 'moai.01.im+autotest@gmail.com';
    const USERNAME = 'moai_01_im_autotest';
    const PASSWORD = 'hogehoge123';

    public function test_registerUser()
    {
        // トップへのアクセス
        $this->webDriver->get(self::BASE_URL);

        // ログインのクリック
        $this->webDriver->findElement(WebDriverBy::xpath("//div[@id='header']//button[normalize-space(.)='ログイン']"))->click();

        // 会員登録ボタンが表示されたら会員登録ボタンをクリック
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("wa-signup-link")))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-link"))->click();

        // 会員登録のemailが表示されるまで待ち、メールアドレスを入力
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("wa-signup-email")))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-email"))->sendKeys(self::EMAIL_ADDRESS);
        // JavaScriptによるユーザ名作成を待つ
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::textToBePresentInElementValue(WebDriverBy::id("wa-signup-username"), self::USERNAME)
        );
        $this->webDriver->findElement(WebDriverBy::xpath("//form[@id='wa-signup-email-form']//button[.='会員登録']"))->click();

        // 会員登録ボタンクリック後にパスワード入力する
        $this->webDriver->wait(10, 5000)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("wa-signup-password")))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password"))->sendKeys(self::PASSWORD);
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->click();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->clear();
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-confirm"))->sendKeys(self::PASSWORD);
        if (!$this->webDriver->findElement(WebDriverBy::id("wa-signup-password-agree"))->isSelected()) {
            $this->webDriver->findElement(WebDriverBy::id("wa-signup-password-agree"))->click();
        }
        $this->webDriver->findElement(WebDriverBy::xpath("//form[@id='wa-signup-password-form']//button[.='会員登録']"))->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("wa-signup-finish-close-link")))
        );
        $this->webDriver->findElement(WebDriverBy::id("wa-signup-finish-close-link"))->click();
    }
}
