<?php

namespace AutoTest\Tests;

use AutoTest\Base\OpBase_TestCase;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

//ファイルの保存
use Facebook\WebDriver\Remote\LocalFileDetector;

class opTest extends OpBase_TestCase
{
    const BASE_URL = 'https://test.originalprint.jp';
    const EMAIL_ADDRESS = 'soeno@imagemagic.co.jp';
    const PASSWORD = '11111111';
    const DOWNLOADPATH = __DIR__.'/../../../tmp/op/download/';
    const UPLOADPATH = __DIR__.'/../../../tmp/op/upload/';
//    protected $url = "https://test.originalprint.jp/sp.php/dtool/run/?si_id=0&woi_ids=38771&ic_id=1076";
//    protected $url = "https://test.originalprint.jp/sp.php/dtool/run/?si_id=1&woi_ids=121778&ic_id=1076";
    protected $url = "https://test.originalprint.jp/sp.php/dtool/run/?si_id=0&woi_ids=8423,966,121778,82300&ic_id=1076";

    /** @test  */
    public function test_op()
    {
        //トップページへ遷移
//        $this->webDriver->get(self::BASE_URL);
//        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認
//
//        //トップページが表示されたらログインボタンをクリック
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::id("loginLink")))
//        );
//        $this->webDriver->findElement(WebDriverBy::id("loginLink"))->click();
//
//        //タイトルがログインページに指定されているテキストを含むとき。
//        $this->webDriver->wait()->until(
//            WebDriverExpectedCondition::titleContains('Tシャツ、バッグなどにオンデマンドでオリジナルプリント')
//        );
//
//        // ログインページが表示されたらアドレスを入力
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='loginid']"))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='loginid']"))->click();
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='loginid']"))->clear();
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='loginid']"))->sendKeys(self::EMAIL_ADDRESS);
//
//        // ログインページが表示されたらパスワードを入力
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='password']"))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='password']"))->click();
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='password']"))->clear();
//        $this->webDriver->findElement(WebDriverBy::cssSelector("input[name='password']"))->sendKeys(self::PASSWORD);
//
//        //ログインをクリック
//        $this->webDriver->findElement(WebDriverBy::linkText('メールアドレスでログイン'))->click();

        //トップページへ遷移
//        $this->webDriver->findElement(WebDriverBy::id('logo_layout2'))->click();
        //$this->webDriver->get(self::BASE_URL);

//        // 左のメニューエリアからtシャツのリンクをクリック
//        $this->webDriver->findElement(WebDriverBy::className('Tshirts'))->click();
//
//        //GLIMMER ドライTシャツを選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::linkText('GLIMMER ドライTシャツ'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::linkText('GLIMMER ドライTシャツ'))->click();
//
//        //デザイン・見積へ
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::className('design_link'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::className('design_link'))->click();
//
//        //数量、カラー選択エリアが開かれたのを確認。
//        $this->webDriver->wait(30, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::id("inputColorSizeSuccess"))
//            )
//        );
//
//        //適当なサイズに数量１を設定
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.quantityInput.ui-spinner-input'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.quantityInput.ui-spinner-input'))->click();
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.quantityInput.ui-spinner-input'))->sendKeys(1);
//
//        //次へ
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.submitBtn .nextLink'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.submitBtn .nextLink'))->click();
//
//        //Im転写を選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('#ui-id-5'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('#ui-id-5'))->click();
//
//        //適当な加工位置を設定
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::xpath("//div[@aria-labelledby='ui-id-5']//ul"))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::xpath("//div[@aria-labelledby='ui-id-5']//ul"))->click();
//
//        //注文前にデザインするを選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.doDesignLink'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.doDesignLink'))->click();
//
//        //新デザインツールで入稿を選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::id('js-dt-btn'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::id('js-dt-btn'))->click();
        // phantomjsでは動かないかもしれない。　https://teratail.com/questions/113048
        //    だめだった試み
        //        $this->webDriver->executeScript('void(0);');//jsのイベントを自分で送ってみる。
        //        sleep(50); // 読み込みを待つために５秒間処理を止める
        //        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認

        $this->webDriver->get($this->url);

        //動作テスト
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextBtn'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('dtTextBtn '))->click();


        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextText'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('dtTextText '))->click();
        $this->webDriver->findElement(WebDriverBy::className('dtTextText '))->sendKeys("dddddd");



        //表示にすることで位置が変わってまずいときようにfixed指定。無い方がいい？

//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::elementToBeClickable(
//                $this->webDriver->findElement(WebDriverBy::id('dtTextFontTypeAlphabet'))
//            )
//        );




//        $this->webDriver->findElement(WebDriverBy::className('stocklink'))->click();
//        sleep(2);
//        $this->webDriver->findElement(WebDriverBy::className('modalCloseBtn'))->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('itemAreaChanged'))
            )
        );
//        $colorTips =  $this->webDriver->wait()->until(
//            //引数に指定した locatorが, ページ上に少なくとも一つ存在するまで
//            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('.itemColorChip'))
//        );
//
//        $counter = 0;
//        foreach ($colorTips as $colorTip) {
//            $colorTips[$counter]->click();
//            $counter++;
////            var_dump($colorTip->getText());
//        }
//        var_dump($colorTips.length);//->click();

        //動作テスト
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.itemColorChipy'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.itemColorChip'))->click();

        //メニューからイメージを選択
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtImageBtn'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtImageBtn'))->click();
        $this->uploadImage();

        //イメージの配置を確認
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtCanvasMask .dtParts'))
            )
        );

        // キャプチャ
        $imgName = "tes";
        $file = self::DOWNLOADPATH . "_{$imgName}".time().".png";
        $this->webDriver->takeScreenshot($file);

        //メニューからスタンプを選択
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampBtn'))
            )
        );
        $this->url = $this->webDriver->getCurrentURL();
        $this->webDriver->get($this->url);
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampBtn'))->click();

        //適当なスタンプを選択
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampImage'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampImage'))->click();

        //デザインを確認を選択
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtConfirmBtn'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtConfirmBtn'))->click();

        //エリアが開かれたのを確認しエリア内のiframeにフォーカスを当てる。frameToBeAvailableAndSwitchToItでフレームがあればアクティブにする。
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt(
                $this->webDriver->findElement(WebDriverBy::cssSelector("#js-iframe"))
            )
        );

        //適当なサイズに数量１を設定 （urlからデザインツールへ移動したとき。）
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::cssSelector("#simuStock input[type='text']")))
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector("#simuStock input[type='text']"))->clear();
//        $this->webDriver->findElement(WebDriverBy::cssSelector("#simuStock input[type='text']"))->sendKeys(1);

//        //カートに入れるを選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.addCartLink'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.addCartLink'))->click();
//
//        //カートへ進むを選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.dialogToCartButton'))
//            )
//        );
//        $this->webDriver->findElement(WebDriverBy::cssSelector('.dialogToCartButton'))->click();
    }

    //fileupload https://github.com/facebook/php-webdriver/wiki/Upload-a-file
//    function uploadImage(){
//        $remote_image =self::UPLOADPATH . 'pict1.jpg';
//
//        // copy file to your local box
//        copy('https://www.google.co.jp/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png', $remote_image);
//
//        // getting the input element
//        $fileInput = $this->webDriver->findElement(WebDriverBy::id('dtImageUploadFile'));
//
//        // set the file detector
//        $fileInput->setFileDetector(new LocalFileDetector());
//
//        // upload the file and submit the form
////        $fileInput->sendKeys($remote_image)->submit();
//        var_dump($remote_image);
//        $fileInput->sendKeys($remote_image)->submit();
//
//        // clean up by removing the tmp save image
//        unlink($remote_image);
//    }
    function uploadImage(){
        // getting the input element
        $fileInput = $this->webDriver->findElement(WebDriverBy::id('dtImageUploadFile'));

        // set the file detector
        $fileInput->setFileDetector(new LocalFileDetector());

        var_dump("ssss");
        // upload the file and submit the form
        $fileInput->sendKeys(self::UPLOADPATH . 'pict1.png');
    }

    function clickableElement($id){
        $jsFunc ="var target = document.getElementById('". $id."');target.style.display='block';target.style.position='fixed';target.style.opacity='0.5';";
        var_dump($jsFunc);
        $this->webDriver->executeScript($jsFunc);
    }

//    public function memo()
//    {
//        //HowTo Wait https://github.com/facebook/php-webdriver/wiki/HowTo-Wait
//        // デフォルト待機（= 30秒）
//        $this->webDriver->wait()->until(
//            WebDriverExpectedCondition::titleIs('My Page')
//        );
//        //最大10秒間待ってから、タイトルが正しくない場合は500msごとに再試行してください。
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::titleIs('My Page')
//        );
//    }
}
