<?php

namespace AutoTest\Tests;

namespace AutoTest\Tests\opPc_Test_Util;

require_once dirname(__FILE__).'/../../../vendor/autoload.php';
require_once dirname(__FILE__).'/../Base/Op_Base_TestCase.php';

use AutoTest\Base\Op_Base_TestCase;
use AutoTest\Tests\opPc_Test_Util;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverSelect;

//ファイルの保存
class opPc_Test extends Op_Base_TestCase
{
    //ロケータ
    const BASE_URL = 'https://test.originalprint.jp';
    const DOWNLOADPATH = __DIR__.'/../../../tmp/op/download/';
    const UPLOADPATH = __DIR__.'/../../../tmp/op/upload/';

    //iPhone X 手帳型ケース
    protected $dtUrl = "https://test.originalprint.jp/sp.php/dtool/run/";
    protected $item_sp = "?si_id=0&woi_ids=167442,167443&ic_id=26103";
    protected $itemName = "iPhone X 手帳型ケース";
    protected $returnUrl = "https://test.originalprint.jp/store.php/original/index?i_id=5944";
    protected $usageUrl = "https://test.originalprint.jp/store.php/page/designToolOperation";

    const EMAIL_ADDRESS = 'soeno@imagemagic.co.jp';
    const PASSWORD = '11111111';

    /** util */
    public $opPc_util;

    /**
     * @Before
     */
    public function test_before()
    {
        //dbの状態を担保する処理を追加

//        $this->opPc_util = new opPc_Test_Util($this->webDriver);
        $this->assertTrue(true);
    }

    /**
     * @Test
     * @group header
     */
    public function test_header_return()
    {
        //デザインツールへ遷移(スマホケース)
        $this->open_dt_smartPhone();

        //トップページが表示されたらバックボタンをクリック
        $backBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::className("dtBackBtn")))
        );
        $backBtn->click();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('製作ならオリジナルプリントで！')
        );
        $this->assertTrue( ($this->returnUrl === $this->webDriver->getCurrentURL()) , '未デザイン時のアイテム詳細画面への戻るボタンが正しく動いていません。');
    }

    /**
     * @Test
     * @group header
     */
    public function test_header_input_return()
    {
        $this->open_dt_smartPhone();//デザインツールへ遷移(スマホケース)
        $this->dt_menu_stamp();//適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp_putStampThumb();//適当なスタンプパーツを作成

        //トップページが表示されたらバックボタンをクリック
        $backBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::className("dtBackBtn")))
        );
        $backBtn->click();

        //Confirmダイアログの選択
        $this->webDriver->switchTo()->alert()->accept();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('製作ならオリジナルプリントで！')
        );

        $this->assertTrue( ($this->returnUrl === $this->webDriver->getCurrentURL()) , 'アイテム詳細画面への戻るボタンが正しく動いていません。');
    }

    /**
     * @Test
     * @group header
     */
    public function test_header_usage()
    {
        $this->open_dt_smartPhone();//デザインツールへ移動

        //使い方ボタンを押す
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::cssSelector('.dtUsageBtn')))
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtUsageBtn'))->click();

        //新しいウインドウにフォーカスを移す。　https://saitodev.co/article/php-webdriverでtarget__blank_付きのアンカータグ対策
        $wins = $this->webDriver->getWindowHandles();
        $this->webDriver->switchTo()->window(end($wins));
        $checkUrl = $this->webDriver->getCurrentURL();

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('デザインツールの使い方')
        );
        $this->webDriver->close();

        $this->webDriver->switchTo()->window(array_shift($wins));
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
        );
        $this->webDriver->quit();

        //遷移後のアドレスの確認
        $this->assertSame($this->usageUrl, $checkUrl);
        //https://phpunit.de/manual/6.5/ja/appendixes.assertions.html#appendixes.assertions.assertThat

        $this->assertTrue( ($this->usageUrl === $checkUrl) , '使い方ボタンが正しく動いていません。');
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_confirm_notEntered()
    {
        $this->open_dt_smartPhone();//デザインツールへ移動

        //トップページが表示されたら確認ボタンをクリック
        $this->dt_confirm();

        //確認時のアラートが開かれたらOK
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.modalMain #descriptionAlertBtn'))
            )
        );
        $this->assertNotNull($btn, '未デザイン時の確認ポップアップの表示');
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_confirm_entered()
    {
        $this->open_dt_smartPhone();//デザインツールへ移動

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        //パーツが表示されたら確認ボタンをクリック
        $this->dt_confirm();

        //エリアが開かれたのを確認しエリア内のiframeにフォーカスを当てる。frameToBeAvailableAndSwitchToItでフレームがあればアクティブにする。
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt(
                $this->webDriver->findElement(WebDriverBy::cssSelector("#js-iframe"))
            )
        );

        //カート追加ボタンがあれば確認ボタン後のポップアップ表示とみなし　OK
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::className("addCartLink")))
        );
        $this->assertNotNull($btn, 'デザイン時の確認ポップアップの表示');
    }

    public function test_canvasArea_delete_ok(){
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        $this->dt_deleteAll();//すべて消すボタン
        $this->dt_modal_select_ok();//出てきたモーダルのＯＫを押す

        $elements = $this->webDriver->findElements(WebDriverBy::cssSelector('.dtParts'));

        //画面上にパーツが存在するかどうか
        $this->assertEquals(0, count($elements),"全消去できているか");
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_delete_cancel(){
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        $this->dt_deleteAll();//すべて消すボタン
        $this->dt_modal_select_ng();//出てきたモーダルのNGを押す

        $elements = $this->webDriver->findElements(WebDriverBy::cssSelector('.dtParts'));
        //存在するかどうか
        $this->assertNotEquals(0, count($elements),"消去せずにパーツが残っているか");
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_grid_onOff(){
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        $dtGrid = $this->dt_grid();
        $dtGrid->click();//グリットボタンを押す

        $elements = $this->webDriver->findElements(WebDriverBy::cssSelector('.canvas_grid.is-active'));
        $this->assertNotEquals(0, count($elements),"グリットボタンがオフの時にクリックしたときグリットはオン");

        $dtGrid->click();//グリットボタンを押す

        $elements2= $this->webDriver->findElements(WebDriverBy::cssSelector('.canvas_grid.is-active'));
        $this->assertEquals(0, count($elements2),"グリットボタンがオンの時にクリックしたときグリットはオフ");
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_print_info(){
        //解像度、表記
        $maxImageSize = new \ArrayObject();
        $maxImageSize->maxImageWigth = 3346;
        $maxImageSize->maxImageHeight =  2425;

        $dpi = 400;

        //デザインツールへ移動
        $this->open_dt_smartPhone();

        $textElement = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtDpiMessage.is-active'))
            )
        );
        $newImageSize = new \ArrayObject();
        $newImageSize->myWidth = $textElement->findElement(WebDriverBy::cssSelector('.workShapeHeight'))->getText();
        $newImageSize->myHeight = $textElement->findElement(WebDriverBy::cssSelector('.workShapeWidth'))->getText();

        $text = $textElement->getText();
        $this->assertEquals($maxImageSize, $newImageSize,"解像度表記 サイズの表記");
        $this->assertTrue(preg_match("/".$dpi."dpi$/",$text) >= 0,"解像度表記 dpiの表記");
    }

//    //ホイール　保留
//    public function test_canvasArea_mouseWheel_scroll(){
//        var_dump("start:test_canvasArea_mouseWheel_scroll");
//
//        //デザインツールへ移動
//        $this->webDriver->get($this->dtUrl.$this->item_sp);
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
//        );
//
//        $mainArea = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::id('dtMainArea'))
//            )
//        );
//        $mainAreaX = $mainArea->getLocation()->getX();
//        $mainAreaY = $mainArea->getLocation()->getY();
//        $mainAreaWidth = $mainArea->getSize()->getWidth();
//        $mainAreaHeight = $mainArea->getSize()->getHeight();
//
//        var_dump($mainAreaX." ".$mainAreaY." ".$mainAreaWidth." " .$mainAreaHeight);
//        $action = new WebDriverActions($this->getModule('WebDriver')->webDriver);
//        $action->moveByOffset($mainAreaX + $mainAreaWidth/2 , $mainAreaY + $mainAreaHeight/2);
//
//        //https://stackoverflow.com/questions/6735830/how-to-fire-mouse-wheel-event-in-firefox-with-javascript
////        mouseWheel
//        var_dump("end:test_canvasArea_mouseWheel_scroll");
//    }
//
////ホイールのクリック状態　保留
////    public function test_canvasArea_mouseWheel_clickMove(){
////        var_dump("start:test_canvasArea_mouseWheel_clickMove");
////
////        //デザインツールへ移動
////        $this->webDriver->get($this->dtUrl.$this->item_sp);
////        $this->webDriver->wait(10, 500)->until(
////            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
////        );
////
////        $mainArea = $this->webDriver->wait(10, 500)->until(
////            WebDriverExpectedCondition::visibilityOf(
////                $this->webDriver->findElement(WebDriverBy::id('dtMainArea'))
////            )
////        );
////        $mainAreaX = $mainArea->getLocation()->getX();
////        $mainAreaY = $mainArea->getLocation()->getY();
////        $mainAreaWidth = $mainArea->getSize()->getWidth();
////        $mainAreaHeight = $mainArea->getSize()->getHeight();
////
////        var_dump($mainAreaX." ".$mainAreaY." ".$mainAreaWidth." " .$mainAreaHeight);
////        $action = new WebDriverActions($this->getModule('WebDriver')->webDriver);
////        $action->moveByOffset($mainAreaX + $mainAreaWidth/2 , $mainAreaY + $mainAreaHeight/2);
////
////        var_dump("end:test_canvasArea_mouseWheel_clickMove");
////    }
//
////ホイールのダブルクリック　保留
////    public function test_canvasArea_mouseWheel_doubleClick(){}
//

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_selectStamp()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();
        //パーツの配置を確認
        $parts = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtParts'))
            )
        );

        //アイテムメニューにフォーカスを移動させる。
        $this->dt_menu_item();

        //パーツのある場所をクリックさせる。
        $this->dt_parts_center_click($parts);

        //スタンプメニューがアクティブになっているかを確認する。
        $stampBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampBtn.is-select'))
            )
        );
        $this->assertNotNull($stampBtn, 'キャンバスからの選択でメニューがスタンプに切り替わる');
    }

//    public function test_canvasArea_selectStamp_posTop()
//    {
//        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)
//
//        //適当なパーツの作成　（スタンプメニューを開く）
//        $this->dt_menu_stamp();
//        $firstStampThumb = $this->dt_menu_stamp_putStampThumb(0);
//        $firstPosY = $firstStampThumb->getLocation()->getY();
//        //パーツの配置を確認
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtParts'))
//            )
//        );
//
//        $stampThumb = $this->dt_menu_stamp_putStampThumb(14);
//
//        $posY = $stampThumb->getLocation()->getY();
//        var_dump($posY );
//        //パーツの配置を確認
//        $driver = $this->webDriver;
//        $num = 1;
//        $parts = $driver->wait()->until(
//            function () use ($driver,$num) {
//                $elements = $driver->findElements(WebDriverBy::cssSelector('.dtParts'));
//                if(count($elements) > $num){
//                    return $elements;
//                }
//            },
//            'Error locating more than five elements'
//        );
//
//        $this->dt_parts_center_click($parts[$num]);
//
//        //パーツのある場所をクリックさせる。
//        $this->dt_parts_center_click($parts[$num]);
//        $posY = $stampThumb->getLocation()->getY();
//
//        $driver->wait(300);
//        var_dump($firstPosY);
//        var_dump($posY );
//        if($firstPosY == $posY) {
//            var_dump("ok !!!");
//        }else{
//            var_dump("not ok !!!");
//        }
//        var_dump("OK:test_canvasArea_selectStamp_posTop");
//    }


//$posX = $parts->getLocation()->getX();
//$posY = $parts->getLocation()->getY();
//$width = $parts->getSize()->getWidth();
//$height = $parts->getSize()->getHeight();
//var_dump($posY );
//var_dump($posX );
//var_dump($width );
//var_dump($height);
//        $action->moveToElement($parts)->click();
//->click()->build()->perform();
//        $action->moveToElement($element_you_want)->perform();
//phpwebdriver caction
//        $action = new WebDriverActions($this->driver);
//        $action->moveToElement(WebDriverBy::tagName("body"))->click()->build()->perform();
//        $action->moveByOffset( $posX + $width/2 , $posY + $height/2).click().build().perform();
//        $action->moveByOffset( 959 , 300).click().build().perform();


//
//        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)
//
//        //適当なパーツの作成　（スタンプメニューを開く）
//        $this->dt_menu_stamp();
//        $this->dt_menu_stamp_setCategory("入学祝い");//カテゴリ選択
//        $selectThumb = $this->dt_menu_stamp_putStampThumb();
//
//        //エラー発生中　スタンプカテゴリ変更後にスタンプが置けない。
//
//        //アイテムメニューに選択を移す。
//        $this->dt_menu_item();
//        $elements = $this->webDriver->findElements(WebDriverBy::cssSelector('.dtParts'));
//        if(count($elements) <= 0){
//            var_dump("error:test_canvasArea_selectStamp　 :Make Parts Error");
//            return;
//        }
//        $elements[0]->click();
//
//
////        //適当なパーツの作成　（テキストメニューを開く）
////        $this->dt_menu_text();
////        $this->dt_menu_text_inputText("ddddd");
////
////        $elements = $this->webDriver->findElements(WebDriverBy::cssSelector('.dtParts'));
////
////        var_dump((count($elements) <= 1)."       ".count($elements));
////        //画面上にパーツが1個だけの時は終わる
////        if(count($elements) <= 1){
////            var_dump("error:test_canvasArea_selectStamp");
////            return;
////        }
////        $elements[0]->click();
////
////        //メニューからスタンプを選択
////        $this->webDriver->wait(10, 500)->until(
////            WebDriverExpectedCondition::visibilityOf(
////                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtItemBtn.is-select'))
////            )
////        );
////
//        $selectThumbX = $selectThumb->getLocation()->getX();
//        $selectThumbY = $selectThumb->getLocation()->getY();
//        var_dump($selectThumbX." ".$selectThumbY);
//        var_dump("start:test_canvasArea_selectStamp");
//
//    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_stamp_move()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        $elements = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );
        $this->dt_parts_move_by_keybord($elements);
        //        要追加
//        $this->dt_parts_move_by_mouse($elements);
    }

//    public function test_canvasArea_stamp_rotate()
//    {
//        var_dump("start:test_canvasArea_stamp_rotate");
//
//        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)
//
//        //適当なパーツの作成　（スタンプメニューを開く）
//        $this->dt_menu_stamp();
//        $this->dt_menu_stamp_putStampThumb();
//
//        $dtRotateBtn = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtRotateBtn'))
//            )
//        );
//        $dtRotateBtn.self::getObjectAttribute( transform);
//
//        $dtRotateBtn->clickAndHold();
//        $this->dt_parts_move_by_keybord($elements);
//
//
//        var_dump("OK:test_canvasArea_stamp_rotate");
//    }

////    public function test_canvasArea_stamp_rotate(){}
////    public function test_canvasArea_stamp_size(){}
////    public function test_canvasArea_stamp_transformWithRotate(){}
////    public function test_canvasArea_stamp_sizeWithRotate(){}
////    public function test_canvasArea_stamp_outCanvas_ok(){}
////    public function test_canvasArea_stamp_outCanvas_cancel(){}
////
////    public function test_canvasArea_stamp_guide_vertical(){}
////    public function test_canvasArea_stamp_guide_vertical_canvassize(){}
////    public function test_canvasArea_stamp_guide_horizontal(){}
////    public function test_canvasArea_stamp_guide_horizontal_canvassize(){}
////
////    public function test_canvasArea_stamp_otherParts_guide_vertical(){}
////    public function test_canvasArea_stamp_otherParts_guide_vertical_canvassize(){}
////    public function test_canvasArea_stamp_otherParts_guide_horizontal(){}
////    public function test_canvasArea_stamp_otherParts_guide_horizontal_canvassize(){}
////
////    public function test_canvasArea_selectText(){}
///

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_text_move()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（テキストメニューを開く）
        $myText = "ddddd";
        $this->dt_menu_text();
        $this->dt_menu_text_inputText($myText);

        $elements = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );
        $this->dt_parts_move_by_keybord($elements);

//        要追加
//        $this->dt_parts_move_by_mouse($elements);
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_selectImage()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なイメージの作成　（イメージメニューを開く）
        $imgName = 'pict1.png'; //tmp/op/upload内の画像を指定する。
        $this->dt_menu_image();
        $this->dt_menu_image_uploadImage($imgName);

        $parts = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );

        //アイテムメニューにフォーカスを移動させる。
        $this->dt_menu_item();

        //パーツのある場所をクリックさせる。
        $this->dt_parts_center_click($parts);

        //スタンプメニューがアクティブになっているかを確認する。
        $imageBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtImageBtn.is-select'))
            )
        );
        $this->assertNotNull($imageBtn, 'キャンバスからイメージパーツ選択でメニューがイメージに切り替わる');
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_image_move()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なイメージの作成　（イメージメニューを開く）
        $imgName = 'pict1.png'; //tmp/op/upload内の画像を指定する。
        $this->dt_menu_image();
        $this->dt_menu_image_uploadImage($imgName);

        $elements = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );
        $this->dt_parts_move_by_keybord($elements);

//要修正
//        $this->dt_parts_move_by_mouse($elements);
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_selectParts_activeBtns()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        $dtLayerTopBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtLayerTopBtn'))
            )
        );
        $this->assertNotNull($dtLayerTopBtn, '最前面ボタンが表示されている');

        $dtLayerUpBtn =$this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtLayerUpBtn'))
            )
        );
        $this->assertNotNull($dtLayerUpBtn, '一つ前面ボタンが表示されている');

        $dtLayerDownBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtLayerDownBtn'))
            )
        );
        $this->assertNotNull($dtLayerDownBtn, '一つ背面ボタンが表示されている');

        $dtLayerBottomBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtLayerBottomBtn'))
            )
        );
        $this->assertNotNull($dtLayerBottomBtn, '最背面ボタンが表示されている');

        $dtCloneBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtCloneBtn'))
            )
        );
        $this->assertNotNull($dtCloneBtn, '複製するボタンが表示されている');

        $alineBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('#dtAlignBtn'))
            )
        );
        $this->assertNotNull($alineBtn, '整列するボタンが表示されている');

//整列ボタンがオフの時はオンにして調べる。
        $this->ifOffCilckTarget($alineBtn, '.is-active');

        $dtAlignW_L = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignW_L'))
            )
        );
        $this->assertNotNull($dtAlignW_L, '左揃えボタンが表示されている');

        $dtAlignW_C = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignW_C'))
            )
        );
        $this->assertNotNull($dtAlignW_C, '横中央ボタンが表示されている');

        $dtAlignW_R = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignW_R'))
            )
        );
        $this->assertNotNull($dtAlignW_R, '右揃えボタンが表示されている');

        $dtAlignH_T = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignH_T'))
            )
        );
        $this->assertNotNull($dtAlignH_T, '上揃えボタンが表示されている');

        $dtAlignH_C = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignH_C'))
            )
        );
        $this->assertNotNull($dtAlignH_C, '縦中央ボタンが表示されている');

        $dtAlignH_B = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id('dtAlignH_B'))
            )
        );
        $this->assertNotNull($dtAlignH_B, '下揃えボタンが表示されている');
    }

    /**
     * @Test
     * @group canvasArea
     */
    public function test_canvasArea_gridView_base()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_stamp();
        $this->dt_menu_stamp_putStampThumb();

        $dtbasePos = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtbasePos.is-active'))
            )
        );
        $this->assertNotNull($dtbasePos, 'グリットアクティブ時に基点が表示されている。');
    }

    /**
     * @Test
     * @group menuArea
     */
    public function test_menuArea_text_selectTextArea()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（スタンプメニューを開く）
        $this->dt_menu_text();
        $this->dt_menu_text_inputText();

        $dtPartsBox = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBox'))
            )
        );
        $noImageSize = new \ArrayObject();
        $noImageSize->myWidth =  1 + 2;//テキストが未入力の時のサイズ width + (border * 2)
        $noImageSize->myHeight =  1 + 2;

        $newImageSize = new \ArrayObject();
        $newImageSize->myWidth = $dtPartsBox->getSize()->getWidth();//テキストが未入力の時のサイズ width + (border * 2)
        $newImageSize->myHeight = $dtPartsBox->getSize()->getHeight();

        $this->assertEquals($noImageSize, $newImageSize);
    }

    /**
     * @Test
     * @group menuArea
     */
    public function test_menuArea_text_inputTextArea()
    {
        var_dump("start:test_menuArea_text_inputTextArea");
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（テキストメニューを開く）
        $myText = "test_menuArea_text_inputTextArea";
        $this->dt_menu_text();
        $this->dt_menu_text_inputText($myText);

        $parts = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );
        $this->assertNotNull($parts, '入力された文字がパーツとして作成されている。');
    }

    /**
     * @Test
     * @group menuArea
     */
    public function test_menuArea_text_deleteTextArea()
    {
        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)

        //適当なパーツの作成　（テキストメニューを開く）
        $myText = "test_menuArea_text_inputTextArea";
        $this->dt_menu_text();
        $this->dt_menu_text_inputText($myText);

        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtPartsBtnArea.is-active'))
            )
        );

        $textArea = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextText'))
            )
        );

        $deleteTextNum = 3;
        $newText = substr($myText, 0, -1 * $deleteTextNum);//から3引いた数
        for ($i = 0; $i < $deleteTextNum; $i++) {
            $textArea->sendKeys(array(WebDriverKeys::DELETE));
        }

        $driver = $this->webDriver;
        $isMatch = $driver->wait()->until(
            function () use ($driver, $newText) {
                $imgElements = $this->webDriver->findElement(WebDriverBy::cssSelector('.dtCanvasMask > .selected img'));
                $src = $imgElements->getAttribute("src");
                $isGetImage = preg_match("/".$newText."/", $src);
                return $isGetImage == 1 ? true : false;
            },
            'Error test_menuArea_text_deleteTextArea'
        );
        $this->assertEquals(true, $isMatch);
    }

//要修正
//    public function test_menuArea_text_inputAlphabetInAlphabet()
//    {
//        $this->open_dt_smartPhone(); //デザインツールへ遷移(スマホケース)
//
//        $this->dt_menu_text();//テキストメニューを開く
//
//        $this->selectFontCategoryKanji(false);
//        $btn = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('#dtTextFontAlphabetArea.is-active'))
//            )
//        );
//        $this->assertNotNull($btn, 'アルファベットの入力が可能。');
//    }

//    public function test_menuArea_text_inputKanaInAlphabet()
//    {
//        var_dump("start:test_menuArea_text_inputKanaInAlphabet");
//
//    }

//    /**
//     * @Test
//     * @group menuArea
//     */
//    public function test_menuArea_item_name()
//    {
//        var_dump("start:test_menuArea_item_name");
//
//        //デザインツールへ遷移(スマホケース)
//        $this->open_dt_smartPhone();
//
//        //アイテムメニューを開く
//        $this->dt_menu_item();
//
//        //アイテム名が空でないことを確認する
//        $itemName =$this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('#dtItemName'))
//            )
//        );
//
//        //アイテム名の一致の確認
//        if ($this->itemName === $itemName->getText()) {
//            var_dump("end:test_menuArea_item_name : OK");
//        } else {
//            var_dump("end:test_menuArea_item_name : NG");
//        }
//    }
//
//    /**
//     * @Test
//     * @group menuArea
//     */
//    //トップから流れでとる?（戻るやアイテム変更では色は取れないため。）:編集中エラー発生
//    public function test_menuArea_item_color(){
//        var_dump("start:test_menuArea_item_color");
//
//        $this->open_op_top();//トップへのアクセス
//
//        // 左のメニューエリアからtシャツのリンクをクリック
//        $this->webDriver->findElement(WebDriverBy::className('Tshirts'))->click();
//
//        //GLIMMER ドライTシャツを選択
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::titleContains('Tシャツのプリント')
//        );
//        $this->webDriver->findElement(WebDriverBy::linkText('GLIMMER ドライTシャツ'))->click();
//
//        $colorTips = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf(
//                $this->webDriver->findElement(WebDriverBy::cssSelector('.item_color_link'))
//            )
//        );
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
//
//        // phantomjsでは動かないかもしれない。　https://teratail.com/questions/113048
//        //    だめだった試み
//        //        $this->webDriver->executeScript('void(0);');//jsのイベントを自分で送ってみる。
//        //        sleep(50); // 読み込みを待つために５秒間処理を止める
//        //        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認
//
//        $this->open_dt_smartPhone();//デザインツールへ遷移(スマホケース)
//        var_dump("end:test_menuArea_item_color");
//    }

//    public function test_menuArea_item_position()
//    {
//        var_dump("start:test_menuArea_item_position");
//
//
//    }

////    public function test_menuArea_item_addPosition(){}
////    public function test_menuArea_item_changePosition(){}
////    public function test_menuArea_item_sizeList(){}
////    public function test_menuArea_item_stockList(){}
////    public function test_menuArea_item_change(){}
////
////    public function test_menuArea_text_selectTextArea(){}
////    public function test_menuArea_text_inputTextArea(){}
////    public function test_menuArea_text_deleteTextArea(){}
////    public function test_menuArea_text_inputAlphabetInAlphabet(){}
////    public function test_menuArea_text_inputKanaInAlphabet(){}
////    public function test_menuArea_text_inputNumberInNumber(){}
////    public function test_menuArea_text_inputAlphabetInNumber(){}
////    public function test_menuArea_text_inputKanaInNumber(){}
////
////    public function test_menuArea_text_selectboxSelect(){}
////    public function test_menuArea_text_selectboxNoSelect(){}
////
////    public function test_menuArea_text_fontSelect(){}
////    public function test_menuArea_text_fontSelectEnAble(){}
////    public function test_menuArea_text_fontSelectChangeFont(){}
////
////    public function test_menuArea_text_changeColor(){}
////
////    public function test_menuArea_text_normal(){}
////    public function test_menuArea_text_verticallyPlaced(){}
////    public function test_menuArea_text_basicArch(){}
////    public function test_menuArea_text_basicArchStrong(){}
////    public function test_menuArea_text_reverseArch(){}
////    public function test_menuArea_text_strongArch(){}
////    public function test_menuArea_text_americanArch(){}
////    public function test_menuArea_text_bridge(){}
////
////    public function test_menuArea_text_sizePlus(){}
////    public function test_menuArea_text_sizeMinus(){}
////    public function test_menuArea_text_sizeReset(){}
////
////    public function test_menuArea_image_drop(){}
////    public function test_menuArea_image_select(){}
////    public function test_menuArea_image_uploadMany(){}
////
////    public function test_menuArea_image_resolutionLack(){}
////    public function test_menuArea_image_resolutionResolve(){}
////    public function test_menuArea_image_color(){}
////
////    public function test_menuArea_stamp_selectCat(){}
////    public function test_menuArea_stamp_thumbView(){}
////    public function test_menuArea_stamp_addStamp(){}
////    public function test_menuArea_stamp_colorChange(){}
////    public function test_menuArea_stamp_color(){}
////    public function test_menuArea_stamp_colorReturn(){}
////
////    public function test_menuArea_template_title(){}
////    public function test_menuArea_template_titleNotSubmit(){}
////    public function test_menuArea_template_save_noTitile(){}
////    public function test_menuArea_template_save_notDesign(){}
////    public function test_menuArea_template_save_notLogin_ok(){}
////    public function test_menuArea_template_save_notLogin_cancel(){}
////    public function test_menuArea_template_save_ok(){}
////
////    public function test_menuArea_template_recommend(){}
////    public function test_menuArea_template_recommend_select(){}
////    public function test_menuArea_template_recommendArea_infiniteScroll(){}
////
////    public function test_menuArea_template_saveArea_notLogin_ok(){}
////    public function test_menuArea_template_saveArea_notLogin_cancel(){}
////    public function test_menuArea_template_saveArea_show(){}
////    public function test_menuArea_template_saveArea_infiniteScroll(){}
////    public function test_menuArea_template_saveArea_select(){}
////    public function test_menuArea_template_saveArea_deleteTemplate(){}
////
////
////    /**
////     * @Test
////     * @group other
////     */
////    public function test_other_(){}
//
//
    /**
     * @After
     */
    public function test_after()
    {
        $this->assertTrue(true);
    }

/// page:top -------------------------------------------------------------------------------------------

    //デザインツールへ遷移(スマホケース)
    public function open_op_top()
    {
        $this->webDriver->get(self::BASE_URL);
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('【公式】ネットで印刷のオリジナルプリント.jp')
        );
//        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認
    }




/// page:dt -------------------------------------------------------------------------------------------
//    よく使いそうな関数

    //デザインツールへ遷移(スマホケース)
    public function open_dt_smartPhone()
    {
        $this->webDriver->get($this->dtUrl.$this->item_sp);
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::titleContains('作成ならオリジナルプリント')
        );
//        var_dump($this->webDriver->getCurrentURL());//遷移後のアドレスの確認
    }

//メニュー
    //メニュー：itemを選択
    public function dt_menu_item()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtItemBtn'))
            )
        );
        $this->assertNotNull($btn, 'itemメニューがアクティブになっている');
        $this->ifOffCilckTarget($btn, '.is-select');
    }

    //メニュー：textを選択
    public function dt_menu_text()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextBtn'))
            )
        );
        $this->assertNotNull($btn, 'textメニューがアクティブになっている');
        $this->ifOffCilckTarget($btn, '.is-select');
    }

    //メニュー：imageを選択
    public function dt_menu_image()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtImageBtn'))
            )
        );
        $this->assertNotNull($btn, 'imageメニューがアクティブになっている');
        $this->ifOffCilckTarget($btn, '.is-select');
    }

    //メニュー：stampを選択
    public function dt_menu_stamp()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtStampBtn'))
            )
        );
        $this->assertNotNull($btn, 'スタンプメニューがアクティブになっている');
        $this->ifOffCilckTarget($btn, '.is-select');
    }

    //メニュー：テンプレートを選択
    public function dt_menu_template()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtTemplateBtn'))
            )
        );
        $this->assertNotNull($btn, 'テンプレートメニューがアクティブになっている');
        $this->ifOffCilckTarget($btn, '.is-select');
    }

//メニュー内の操作
    //テキストメニュー内

    //テキストパーツの文字入力
    public function dt_menu_text_inputText($text="")
    {
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className('dtTextText'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::className('dtTextText'))->click();
        if($text != ""){
            $this->webDriver->findElement(WebDriverBy::className('dtTextText'))->sendKeys($text);
        }

        //Custom conditions  https://github.com/facebook/php-webdriver/wiki/HowTo-Wait
        //カスタムイベント　テキストのイメージのsrcに入力したテキストが含まれる時画像が生成済みとみなし次のイベントに進む。
        $driver = $this->webDriver;
        $driver->wait()->until(
            function () use ($driver, $text) {
                $imgElements = $this->webDriver->findElement(WebDriverBy::cssSelector('.dtCanvasMask > .selected img'));
                $src = $imgElements->getAttribute("src");
                $isGetImage = preg_match("/".$text."/", $src);
//                var_dump($src);
//                var_dump($isGetImage);
                return $isGetImage;
            },
            'Error locating more than five elements'
        );
    }

    //日本語、アルファベットの切り替え。（inputは非表示なので表示させてからイベントを送出する必要がある。）
    public function selectFontCategoryKanji($isKanji = true){
        $newFontCategory = "dtTextFontTypeKanji";
        if(!$isKanji){
            $newFontCategory = "dtTextFontTypeAlphabet";
        }
        $this->clickableElement($newFontCategory);

        $categoryBtn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::id($newFontCategory))
            )
        );
        $categoryBtn->click();
    }

    //イメージパーツ内
    public function dt_menu_image_uploadImage($imgName)
    {
        $this->uploadImage($imgName);

        //イメージの配置を確認
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtCanvasMask .dtParts'))
            )
        );
    }

    //スタンプ内
    //適当なスタンプパーツを作成
    public function dt_menu_stamp_putStampThumb($num = 0)
    {
        $driver = $this->webDriver;
        $stampThumb = $driver->wait()->until(
            function () use ($driver,$num) {
                $elements = $driver->findElements(WebDriverBy::cssSelector('.dtStampImage'));
                if( count($elements) > $num){
                    return $elements;
                }
            },
            'Error locating more than five elements'
        );
        //指定がなければ表示されている最初のサムネイルをクリック
        if ($stampThumb && $stampThumb[$num]) {
            $stampThumb[$num]->click();
            return $stampThumb[$num];
        } else {
            var_dump("dt_menu_stamp_putStampThumb error");
            return;
        }

//        $stampThumb = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::elementToBeClickable(
//                $this->webDriver->findElements(WebDriverBy::cssSelector('.dtStampImage'))
//            )
//        );
//        $element = $this->webDriver->findElement(WebDriverBy::id('first_name'));
//        $this->waitVisible($element);
    }

    //スタンプのカテゴリの選択
    public function dt_menu_stamp_setCategory($categoryName)
    {
        //select https://github.com/facebook/php-webdriver/wiki/Select,-checkboxes,-radio-buttons
        $selectElement = $this->webDriver->findElement(WebDriverBy::cssSelector('#dtStampCategory'));
        $select = new WebDriverSelect($selectElement);

// Select option
//        $select->selectByValue('fr'); // Will select "French"
//        $select->selectByIndex(1); // Will select "German"
        $select->selectByVisibleText($categoryName);
//        $select->selectByVisiblePartialText('UK'); // Will select "English (UK)"


        echo $select->getFirstSelectedOption()->getText();
        echo $select->getFirstSelectedOption()->getAttribute('value');
        $this->webDriver->wait(100);
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::elementTextMatches($selectElement,$categoryName)
//        );
//        $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::elementSelectionStateToBe($selectElement,$categoryName)
//        );
//
//        $selectCategory =$this->webDriver->findElement(WebDriverBy::cssSelector('#dtStampCategory'))->click();
//        //選択（sendKeyでよい）
//        $this->webDriver->getKeyboard()->sendKeys($categoryName);
//        $selectCategory-> click();
    }




//操作エリア
    //グリットボタンを押す。
    public function dt_grid()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtGrid'))
            )
        );
        return $btn;
    }

    //すべて消すボタンを押す。
    public function dt_deleteAll()
    {
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.dtDeleteAll'))
            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.dtDeleteAll'))->click();
    }

    //確認ボタンを押す。
    public function dt_confirm()
    {
        $btn = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::className("dtConfirmBtn"))
            )
        );
        $btn->click();
    }

    //パーツの移動 キーボード
    public function dt_parts_move_by_keybord($elements)
    {
        $body = $this->webDriver->findElement(WebDriverBy::tagName('body'));
        $moveValue = 100;

        //右への移動
        $startX = $elements->getLocation()->getX();
        for ($i = 0; $i < $moveValue; $i++) {
            $body->sendKeys(array(WebDriverKeys::ARROW_RIGHT));
        }
        $movedRightX = $elements->getLocation()->getX();
        $this->assertEquals($movedRightX, $startX + $moveValue);

        //左への移動
        $startX = $movedRightX;
        for ($i = 0; $i < $moveValue; $i++) {
            $body->sendKeys(array(WebDriverKeys::ARROW_LEFT));
        }
        $movedLeftX = $elements->getLocation()->getX();
        $this->assertEquals($movedLeftX, $startX - $moveValue);

        //上への移動
        $startY = $elements->getLocation()->getY();
        for ($i = 0; $i < $moveValue; $i++) {
            $body->sendKeys(array(WebDriverKeys::ARROW_UP));
        }
        $movedRightY = $elements->getLocation()->getY();
        $this->assertEquals($movedRightY, $startY - $moveValue);

        //下への移動
        $startY = $movedRightY;
        for ($i = 0; $i < $moveValue; $i++) {
            $body->sendKeys(array(WebDriverKeys::ARROW_DOWN));
        }
        $movedLeftY = $elements->getLocation()->getY();
        $this->assertEquals($movedLeftY, $startY + $moveValue);
    }

    //パーツの移動 マウス
    public function dt_parts_move_by_mouse($elements)
    {
        //パーツのある場所をクリックさせる。
        $action = $this->webDriver->action();
        $action->moveToElement($elements);
        $canvasArea = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('#dtMainArea'))
            )
        );
//        $canvasArea->click();

//
//        //位置の移動が来ないので保留
//        $x_offset = 100;
//        $y_offset = 100;
//        $action->MoveToElement($canvasArea);
//        $action->clickAndHold($canvasArea);
//
//        $action.Build();
//        $action.MoveToElement($elements, 100, 100)
//        .ClickAndHold()
//        .MoveByOffset(100, 0)
//        .Release();
//        $action.Perform();
//

        for ($i = 0; $i < $x_offset; $i++) {
            $action->moveByOffset($elements->getLocation()->getX()+5,0)->perform();
        }
        var_dump("---------------------------------");
        for ($i = 0; $i < $y_offset; $i++) {
            $action->moveByOffset(0,$elements->getLocation()->getY()+5)->perform();
        }
//        $action->release();
//        .clickAndHold(dragElement).moveToElement(dropElement).build();
    }

    //配置したパーツをマウスから選択する場合はパーツのある場所がクリックされる必要がある。
    //※クリックイベントはmainAreaに対して送る。（パーツではない）
    public function dt_parts_center_click($elements)
    {
        $action = $this->webDriver->action();
        $action->moveToElement($elements);
        $canvasArea = $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('#dtMainArea'))
            )
        );
        $canvasArea->click();
    }

//モーダル
    //モーダル内のＯＫボタンを押す
    public function dt_modal_select_ok()
    {
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.modalSelectOkBtn'))

            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.modalSelectOkBtn'))->click();
    }

    //モーダル内のＮＧボタンを押す
    public function dt_modal_select_ng()
    {
        $this->webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOf(
                $this->webDriver->findElement(WebDriverBy::cssSelector('.modalSelectNgBtn'))

            )
        );
        $this->webDriver->findElement(WebDriverBy::cssSelector('.modalSelectNgBtn'))->click();
    }

//モーダル
    //
    ///util

    ///要素の有無を調べてクリック
//    public function getOpPcUtil()
//    {
//        $btn = $this->webDriver->wait(10, 500)->until(
//            WebDriverExpectedCondition::visibilityOf($this->webDriver->findElement(WebDriverBy::className("dtBackBtn")))
//        );
//        $btn->click();
//    }

    function uploadImage($imgName)
    {
        // getting the input element
        $fileInput = $this->webDriver->findElement(WebDriverBy::id('dtImageUploadFile'));

        // set the file detector
        $fileInput->setFileDetector(new LocalFileDetector());

        // upload the file and submit the form
        $fileInput->sendKeys(self::UPLOADPATH.$imgName);
    }

    //ターゲットに$cssSelectorに指定された要素がない場合にクリックする。
    function ifOffCilckTarget($target, $cssSelector)
    {
//        if(!$target->findElements(WebDriverBy::cssSelector($cssSelector))){
        $target->click();
//        }
    }

    function waitVisible($target)
    {
        $this->webDriver->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOf($target)
        );
        return true;
    }

    //イベント送出のためにdisplay:noneの非表示項目を表示させる。（＋表示形式を他の要素に関係ないfixedに設定。+opacityで見えないようにする。）
    function clickableElement($id){
        $jsFunc ="var target = document.getElementById('". $id."');target.style.display='block';target.style.opacity='0';";
        var_dump($jsFunc);
        $this->webDriver->executeScript($jsFunc);
    }
}
