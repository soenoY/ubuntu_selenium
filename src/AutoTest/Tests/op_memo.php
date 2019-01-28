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

    //メニュー切り替え時
    public function changeManu(){
        static const tes = WebDriverBy::className('dtTextBtn');
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextBtn'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('dtTextBtn'))->click();
    }

    //カラーチップ取得時
    public function colorTip(){
        $colorTips =  $this->webDriver->wait()->until(
            //引数に指定した locatorが, ページ上に少なくとも一つ存在するまで
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('.itemColorChip'))
        );
        foreach ($colorTips as $colorTip) {
            $colorTip->click();
        }

//        実際のコード抜粋　具体名な色名の指定でclickイベントをだすのは難しいかもしれない。
//        <li class="itemColorChip">
//            <span style="background-color:#767676" title="グレー" data-href="https://test.originalprint.jp/sp.php/dtoolapi/changeItemColor?si_id=6&ic_id=1077"></span>
//        </li>
    }

    //加工箇所の変更
    public function woiTip(){
        $this->webDriver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector('.areaBox'))
        );
        //加工箇所と加工箇所名で指定するとき
        $this->webDriver->findElement(WebDriverBy::xpath("//div[@class='areaBox'][@data-print_type_name='フルカラーカッティング '][@data-pos_name='胸中央']"))->click();

        //woi-idで指定するとき。
        $this->webDriver->findElement(WebDriverBy::xpath("//div[@class='areaBox'][@data-woi-id='121778']"))->click();
    }

    //文字の入力（メニューがテキストになった状態で）
    public function textInput(){
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextText'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('dtTextText '))->click();
        $this->webDriver->findElement(WebDriverBy::className('dtTextText '))->sendKeys("dddddd");
    }

    //日本語、アルファベットの切り替え。（inputは非表示なので表示させてからイベントを送出する必要がある。）
    public function selectFontCategory(){
        $this->clickableElement("dtTextFontTypeKanji");
        $this->clickableElement("dtTextFontTypeAlphabet");

        //ここでの待ちの指定はうまくいっていない。
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('dtTextFontTypeAlphabet'))
        );
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('dtTextFontTypeKanji'))
        );

        //チェックボックスの選択はできるがセレクトボックスにイベントが伝わらないので１sec待たせる。
        sleep(1);//スクリプト内部のタイミングなのかここは待ちがないとうまく来なかった。
        $this->webDriver->findElement(WebDriverBy::id('dtTextFontTypeAlphabet'))->click();
        sleep(2);//実際はどちらかを選ぶだろうから待ちはいらない。
        $this->webDriver->findElement(WebDriverBy::id('dtTextFontTypeKanji'))->click();
    }


    //イベント送出のためにdisplay:noneの非表示項目を表示させる。（＋表示形式を他の要素に関係ないfixedに設定。+opacityで見えないようにする。）
    function clickableElement($id){
        $jsFunc ="var target = document.getElementById('". $id."');target.style.display='block';target.style.display='block';target.style.opacity='0';";
        var_dump($jsFunc);
        $this->webDriver->executeScript($jsFunc);
    }

    /** @test  */
    public function test_op()
    {

        $this->webDriver->get($this->url);

        //動作テスト
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('areaBox'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('areaBox'))->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('itemAreaChanged'))
            )
        );


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

    //新しく開いたウィンドウのハンドルを返すメソッド。
//$handleBeforeOpen = $this->webDriver->getWindowHandles();
//$newWindowHundle = $this->getNewWindowHandle($handleBeforeOpen, $handleAfterOpen);
//    public function getNewWindowHandle($handleBeforeOpen, $handleAfterOpen)
//    {
//        //$handleAfterOpenから$handleBeforeOpenに含まれるものを除外
//        $handles = $handleAfterOpen;
//        $handles->removeAll($handleBeforeOpen);//要書き換え
////        $handles = array_diff($handleAfterOpen,$handleBeforeOpen);
//
//        if(count($handles) == 0){
//            throwException("新しいウインドウが見つかりません");
//        }else if(count($handles)>1){
//            throwException("新しいウインドウが複数あります");
//        }else{
//            return $handles[1];
//        }
//    }
}
