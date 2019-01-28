<?php

namespace AutoTest\Base;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverDimension;
use PHPUnit\Framework\TestCase;

class Op_Base_TestCase extends TestCase
{
    /**
     * @var RemoteWebDriver
     */
    protected $webDriver;
    protected $webDriverType = "c";//ブラウザによって切り替える 'c' => 'chrome', 'p' => 'phantomjs'
    protected $uaType = "pc";//sp or pc
    protected $isHeadless = true;//ローカルでheadlessで起動する際はここをtrueにしたうえで起動コマンドに--headlessを含める

    public function setUp()
    {
        parent::setUp();
        $brouzerTypes = array('c' => 'chrome', 'p' => 'phantomjs');//, 'o' => '増えたら追加していく'

        switch ($brouzerTypes[$this->webDriverType]) {
            case "phantomjs":
                //　起動時のコマンド:  phantomjs --webdriver=localhost:4444
                $this->setPhantomjs();
                break;
            case "chrome":
                //　起動時のコマンド: chromedriver --headless --disable-gpu --remote-debugging-port=9222
                // ※上のコマンド中のchromedriverはエイリアス名　エイリアスを作成していない場合はchromedriver.exeの実行可能なパスに差し替え
                $this->setChrome();
                break;
        }

        // タイムアウトを設定(タイムアウトしたらエラーとなる)
        $this->webDriver->manage()->timeouts()->implicitlyWait(15);
    }

    //  phantom js
    public function setPhantomjs()
    {
        $capabilities = array(
            WebDriverCapabilityType::BROWSER_NAME => WebDriverBrowserType::PHANTOMJS,
            WebDriverCapabilityType::PLATFORM => WebDriverPlatform::ANY,
            'phantomjs.page.settings.userAgent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:25.0) Gecko/20100101 Firefox/25.0',
        );
        $this->webDriver = RemoteWebDriver::create('http://127.0.0.1:4444', $capabilities);
        $this->webDriver->manage()->window()->setSize(new WebDriverDimension(1024, 768));
    }

    //　Chrome
    public function setChrome()
    {
        $capabilities = DesiredCapabilities::chrome();
        $options = new ChromeOptions();

        //端末による差分、ブラウザサイズ、UAの指定。
        switch ($this->uaType){
            case "sp":{
                $options->addArguments(["--window-size=375,668"]);
                $ua = $this->getUaDetail("sp");
                break;
            }
            case "pc":{
                $options->addArguments(["--window-size=1280,1024"]);
                $ua = $this->getUaDetail("pc");
                break;
            }
            default:{
                var_dump("uaに存在しない値が設定されています。uaはspかpcで設定してください。");
            }
        }
        $options->addArguments(['--user-agent='.$ua]);

        //lang
        $options->addArguments(['--lang=ja']);

        if($this->isHeadless){
            $options->addArguments(["--headless", "--disable-gpu"]);
        }

        // Chromeクラッシュの回避策として
        $options->addArguments(["--no-sandbox"]);

        //ホスト　（ポートはコマンドプロンプトに出た情報から変更?）
        $host = '127.0.0.1:9515'; //'http://localhost:4444' //'http://127.0.0.1:4444/wd/hub'//'127.0.0.1:9515'

        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities->setPlatform("Linux");

        $this->webDriver = RemoteWebDriver::create($host, $capabilities);
    }

    //uaの詳細の指定 : 試したい端末を追加して、$uaDetailで指定する。
    //uaの情報： https://developers.whatismybrowser.com/useragents/explore/software_name/chrome/
    public function getUaDetail($hardwareType){
        $uaDetail = null;//Chrome44OnLinux , Chrome30OnAndroid_KitKat 等指定したデバイスにあるものを設定。
        $hardwareTypeList = array(
            'pc'=>array(
                'Chrome44OnLinux'=>'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36',//Chrome 44 on Linux
                ''=>'',
            ),
            'sp'=>array(
                'Chrome30OnAndroid_KitKat'=>  'Mozilla/5.0 (Linux; Android 4.4.2; XMP-6250 Build/HAWK) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Safari/537.36 ADAPI/2.0 (UUID:9e7df0ed-2a5c-4a19-bec7-2cc54800f99d) RK3188-ADAPI/1.2.84.533 (MODEL:XMP-6250)',//Chrome 30 on Android (KitKat) userAgent偽装でのスマホのデバッグ
                ''=>'',
            )
        );
        $targetList = $hardwareTypeList[$hardwareType];
        $ua = $uaDetail ? $targetList[$uaDetail] : $targetList[key($targetList)];//uaの指定がないときは最初の指定を使う。
        return $ua;
    }

    public function tearDown()
    {
        if ($this->webDriver != null) {
            $this->webDriver->close();
        }
        parent::tearDown();
    }
}